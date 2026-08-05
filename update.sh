#!/usr/bin/env bash

set -Eeuo pipefail

project_root="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
config_file="${project_root}/config/update.json"
workspace="${project_root}/update_tmp"
backup_root="${project_root}/storage/app/update-backups/$(date '+%Y%m%d-%H%M%S')"
declare -a update_files=()
declare -a replaced_files=()
declare -a new_files=()
update_finished=false
workspace_created=false

cleanup() {
    if [[ "${workspace_created}" == true && -d "${workspace}" ]]; then
        rm -rf "${workspace}"
    fi
}

rollback() {
    local exit_code=$?
    trap - ERR INT TERM
    if [[ "${update_finished}" == false && ${#replaced_files[@]} -gt 0 ]]; then
        echo "更新失败，正在恢复本地文件……" >&2
        for file in "${new_files[@]}"; do
            rm -f "${project_root}/${file}"
        done
        for file in "${replaced_files[@]}"; do
            mkdir -p "$(dirname "${project_root}/${file}")"
            cp -a "${backup_root}/${file}" "${project_root}/${file}"
        done
    fi
    cleanup
    exit "${exit_code}"
}

trap rollback ERR INT TERM
trap cleanup EXIT

if [[ -e "${workspace}" || -L "${workspace}" ]]; then
    echo "临时更新目录已存在，请确认并手动处理：${workspace}" >&2
    exit 1
fi
mkdir "${workspace}"
workspace_created=true

if [[ ! -f "${config_file}" ]]; then
    echo "更新配置不存在：${config_file}" >&2
    exit 1
fi

mapfile -t encoded_config < <(php -r '
    $json = file_get_contents( $argv[1] );
    $config = is_string( $json ) ? json_decode( $json, true ) : null;
    if ( !is_array( $config ) || json_last_error() !== JSON_ERROR_NONE ) { exit( 1 ); }
    $source = $config["source"] ?? null;
    $files = $config["files"] ?? null;
    if ( !is_string( $source ) || trim( $source ) === "" || !is_array( $files ) ) { exit( 2 ); }
    echo base64_encode( trim( $source ) ).PHP_EOL;
    foreach ( $files as $file ) {
        if ( !is_string( $file ) || trim( $file ) === "" ) { exit( 3 ); }
        echo base64_encode( trim( $file ) ).PHP_EOL;
    }
' "${config_file}")

if [[ ${#encoded_config[@]} -lt 2 ]]; then
    echo "config/update.json 格式错误，或未配置 source 和至少一个 files 项。" >&2
    exit 1
fi

source_url="$(printf '%s' "${encoded_config[0]}" | base64 --decode)"
for encoded_file in "${encoded_config[@]:1}"; do
    file="$(printf '%s' "${encoded_file}" | base64 --decode)"
    if [[ "${file}" == /* || "${file}" == .git || "${file}" == .git/* || "${file}" =~ (^|/)\.\.(/|$) ]]; then
        echo "拒绝不安全的更新路径：${file}" >&2
        exit 1
    fi
    update_files+=( "${file}" )
done

echo "正在从 ${source_url} 获取更新……"
git clone --depth 1 --quiet "${source_url}" "${workspace}/remote"

for file in "${update_files[@]}"; do
    remote_file="${workspace}/remote/${file}"
    local_file="${project_root}/${file}"
    resolved_local="$(realpath -m "${local_file}")"
    if [[ "${resolved_local}" != "${project_root}/"* || ! -f "${remote_file}" || -L "${remote_file}" ]]; then
        echo "远程文件不存在、不是普通文件或本地路径不安全：${file}" >&2
        exit 1
    fi
    if [[ ( -e "${local_file}" || -L "${local_file}" ) && ( ! -f "${local_file}" || -L "${local_file}" ) ]]; then
        echo "本地目标不是普通文件，拒绝覆盖：${file}" >&2
        exit 1
    fi
done

echo "将更新以下文件："
printf '  - %s\n' "${update_files[@]}"
if [[ "${1:-}" != "--yes" ]]; then
    read -r -p "确认覆盖本地文件？[y/N] " confirmation
    if [[ ! "${confirmation}" =~ ^[Yy]$ ]]; then
        echo "已取消更新。"
        exit 0
    fi
fi

mkdir -p "${backup_root}"
for file in "${update_files[@]}"; do
    remote_file="${workspace}/remote/${file}"
    local_file="${project_root}/${file}"
    if [[ -e "${local_file}" || -L "${local_file}" ]]; then
        mkdir -p "$(dirname "${backup_root}/${file}")"
        cp -a "${local_file}" "${backup_root}/${file}"
        replaced_files+=( "${file}" )
    else
        new_files+=( "${file}" )
    fi
    mkdir -p "$(dirname "${local_file}")"
    temporary_file="$(mktemp "$(dirname "${local_file}")/.update.XXXXXX")"
    cp "${remote_file}" "${temporary_file}"
    chmod --reference="${remote_file}" "${temporary_file}"
    mv -f "${temporary_file}" "${local_file}"
    chown -R www:www "${local_file}"
    chmod -R 775 "${local_file}"
done

update_finished=true
echo "更新完成，共覆盖 ${#update_files[@]} 个文件。"
echo "本地备份：${backup_root}"
