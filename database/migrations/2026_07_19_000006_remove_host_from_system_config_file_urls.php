<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * 执行迁移。
     * 移除图片和文件配置链接中的 Host，仅保留公开路径。
     * @return void
     */
    public function up(): void {
        $configs = DB::table( 'system_config' )
            ->whereIn( 'type', ['image', 'file'] )
            ->whereNotNull( 'value' )
            ->get( ['id', 'value'] );
        foreach ( $configs as $config ) {
            $value = trim( (string) $config->value );
            if ( filter_var( $value, FILTER_VALIDATE_URL ) === false ) { continue; }
            $path = parse_url( $value, PHP_URL_PATH );
            if ( ! is_string( $path ) || $path === '' ) { continue; }
            DB::table( 'system_config' )->where( 'id', $config->id )->update( ['value' => $path] );
        }
    }

    /**
     * 回滚迁移。
     * 使用当前应用地址恢复完整公开链接。
     * @return void
     */
    public function down(): void {
        $host = rtrim( (string) config( 'app.url' ), '/' );
        if ( $host === '' ) { return; }
        $configs = DB::table( 'system_config' )
            ->whereIn( 'type', ['image', 'file'] )
            ->where( 'value', 'like', '/storage/system_file/%' )
            ->get( ['id', 'value'] );
        foreach ( $configs as $config ) {
            DB::table( 'system_config' )
                ->where( 'id', $config->id )
                ->update( ['value' => $host . '/' . ltrim( (string) $config->value, '/' )] );
        }
    }
};
