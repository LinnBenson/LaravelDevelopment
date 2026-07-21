<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->comment('会话ID');
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('用户ID');
            $table->string('ip_address', 45)->nullable()->comment('客户端IP地址');
            $table->text('user_agent')->nullable()->comment('客户端用户代理');
            $table->longText('payload')->comment('序列化会话数据');
            $table->integer('last_activity')->index()->comment('最后活动时间戳');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
