<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration {
    /**
     * 执行迁移。
     * 将已有图片和文件配置的相对路径转换为公开链接。
     * @return void
     */
    public function up(): void {
        $configs = DB::table( 'system_config' )
            ->whereIn( 'type', ['image', 'file'] )
            ->whereNotNull( 'value' )
            ->get( ['id', 'value'] );
        foreach ( $configs as $config ) {
            $value = trim( (string) $config->value );
            if ( $value === '' || preg_match( '#^(?:https?://|/)#i', $value ) === 1 ) { continue; }
            DB::table( 'system_config' )
                ->where( 'id', $config->id )
                ->update( ['value' => Storage::disk( 'public' )->url( $value )] );
        }
    }

    /**
     * 回滚迁移。
     * 将当前 public 磁盘链接恢复为相对路径。
     * @return void
     */
    public function down(): void {
        $basePath = rtrim( (string) parse_url( Storage::disk( 'public' )->url( '' ), PHP_URL_PATH ), '/' );
        $configs = DB::table( 'system_config' )
            ->whereIn( 'type', ['image', 'file'] )
            ->whereNotNull( 'value' )
            ->get( ['id', 'value'] );
        foreach ( $configs as $config ) {
            $valuePath = (string) parse_url( (string) $config->value, PHP_URL_PATH );
            if ( $basePath === '' || ! str_starts_with( $valuePath, "{$basePath}/" ) ) { continue; }
            DB::table( 'system_config' )
                ->where( 'id', $config->id )
                ->update( ['value' => ltrim( substr( $valuePath, strlen( $basePath ) ), '/' )] );
        }
    }
};
