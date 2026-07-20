<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 执行迁移。
     * 将管理员用户名和邮箱调整为必填字段。
     * @return void
     */
    public function up(): void {
        Schema::table( 'admin_users', function ( Blueprint $table ): void {
            $table->string( 'name' )->nullable( false )->comment( '用户名' )->change();
            $table->string( 'email' )->nullable( false )->comment( '邮箱' )->change();
        } );
    }

    /**
     * 回滚迁移。
     * 恢复管理员用户名和邮箱允许为空。
     * @return void
     */
    public function down(): void {
        Schema::table( 'admin_users', function ( Blueprint $table ): void {
            $table->string( 'name' )->nullable()->comment( '用户名' )->change();
            $table->string( 'email' )->nullable()->comment( '邮箱' )->change();
        } );
    }
};
