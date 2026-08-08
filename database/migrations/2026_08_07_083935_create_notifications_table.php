<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 执行数据库迁移。
     * 创建系统通知表。
     */
    public function up(): void {
        Schema::create( 'notifications', function ( Blueprint $table ) {
            $table->uuid( 'id' )->primary()->comment( '通知ID' );
            $table->string( 'type' )->comment( '通知类型' );
            $table->string( 'notifiable_type' )->comment( '接收者模型类型' );
            $table->unsignedBigInteger( 'notifiable_id' )->comment( '接收者ID' );
            $table->text( 'data' )->comment( '通知数据' );
            $table->timestamp( 'read_at' )->nullable()->comment( '阅读时间' );
            $table->timestamp( 'created_at' )->nullable()->comment( '创建时间' );
            $table->timestamp( 'updated_at' )->nullable()->comment( '更新时间' );
            $table->index( ['notifiable_type', 'notifiable_id'] );
        } );
    }

    /**
     * 回滚数据库迁移。
     * 删除系统通知表。
     */
    public function down(): void {
        Schema::dropIfExists( 'notifications' );
    }
};
