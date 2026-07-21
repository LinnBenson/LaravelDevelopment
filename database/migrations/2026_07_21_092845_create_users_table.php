<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('UID');
            $table->unsignedBigInteger('agent')->default(0)->index()->comment('上级代理管理员ID');
            $table->string('name')->nullable()->unique()->comment('用户名');
            $table->string('email')->nullable()->unique()->comment('邮箱');
            $table->string('phone')->nullable()->unique()->comment('电话');
            $table->boolean('status')->default(true)->comment('状态：1启用，0禁用');
            $table->unsignedInteger('level')->default(1)->comment('级别');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('password')->comment('密码');
            $table->string('avatar')->nullable()->comment('头像');
            $table->rememberToken()->comment('登录记忆令牌');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement('ALTER TABLE `'.DB::getTablePrefix().'users` AUTO_INCREMENT = 2088;');
        DB::table('users')->insert([
            'name' => 'test',
            'email' => 'test@test.com',
            'phone' => '1 1234567890',
            'status' => true,
            'level' => 10,
            'nickname' => '测试用户',
            'password' => Hash::make('testuser'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
