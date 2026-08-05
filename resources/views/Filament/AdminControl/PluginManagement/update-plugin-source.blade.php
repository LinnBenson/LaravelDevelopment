{{-- 插件更新来源 --}}
<div class="plugin-update-source">
    <span>更新来源</span>
    <code>{{ $source }}</code>
</div>

<style>
    /* 插件更新来源链接 */
    .plugin-update-source {
        display: flex;
        width: 100%;
        min-width: 0;
        padding: 0.75rem;
        border: 1px solid rgba(107, 114, 128, 0.18);
        border-radius: 0.625rem;
        background: rgba(107, 114, 128, 0.06);
        flex-direction: column;
        gap: 0.375rem;
    }
    .plugin-update-source span {
        color: #9ca3af;
        font-size: 0.6875rem;
        font-weight: 600;
    }
    .plugin-update-source code {
        display: block;
        width: 100%;
        min-width: 0;
        overflow-wrap: anywhere;
        color: inherit;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.75rem;
        line-height: 1.5;
        white-space: normal;
        word-break: break-word;
    }
</style>
