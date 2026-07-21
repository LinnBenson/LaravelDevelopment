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
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('失败任务ID');
            $table->string('uuid')->unique()->comment('失败任务唯一标识');
            $table->text('connection')->comment('队列连接名称');
            $table->text('queue')->comment('队列名称');
            $table->longText('payload')->comment('任务载荷');
            $table->longText('exception')->comment('异常信息');
            $table->timestamp('failed_at')->useCurrent()->comment('失败时间');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};
