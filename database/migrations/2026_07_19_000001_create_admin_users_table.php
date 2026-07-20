<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 执行迁移。
     */
    public function up(): void
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->unique()->comment('用户名');
            $table->string('email')->nullable()->unique()->comment('邮箱');
            $table->boolean('status')->default(true)->comment('状态：1启用，0禁用');
            $table->unsignedInteger('level')->default(1)->comment('级别');
            $table->string('password')->comment('密码');
            $table->string('avatar')->nullable()->comment('头像');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * 回滚迁移。
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
