<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 执行迁移。
     * 创建系统配置表。
     * @return void
     */
    public function up(): void {
        Schema::create( 'system_config', function ( Blueprint $table ): void {
            $table->id();
            $table->string( 'category', 32 )->index()->comment( '类别' );
            $table->string( 'type', 32 )->comment( '类型' );
            $table->string( 'key', 191 )->unique()->comment( '键名' );
            $table->longText( 'value' )->nullable()->comment( '值' );
            $table->unsignedInteger( 'index' )->default( 0 )->index()->comment( '排序' );
            $table->timestamps();
        } );
    }

    /**
     * 回滚迁移。
     * 删除系统配置表。
     * @return void
     */
    public function down(): void {
        Schema::dropIfExists( 'system_config' );
    }
};
