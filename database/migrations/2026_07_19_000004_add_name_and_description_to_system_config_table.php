<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 执行迁移。
     * 为系统配置增加名称和描述，并使用键名补全已有配置名称。
     * @return void
     */
    public function up(): void {
        Schema::table( 'system_config', function ( Blueprint $table ): void {
            $table->string( 'name', 191 )->nullable()->after( 'type' )->comment( '名称' );
            $table->text( 'description' )->nullable()->after( 'value' )->comment( '描述' );
        } );
        DB::table( 'system_config' )->whereNull( 'name' )->update( ['name' => DB::raw( '`key`' )] );
    }

    /**
     * 回滚迁移。
     * 删除系统配置名称和描述字段。
     * @return void
     */
    public function down(): void {
        Schema::table( 'system_config', function ( Blueprint $table ): void {
            $table->dropColumn( ['name', 'description'] );
        } );
    }
};
