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
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary()->comment('任务批次ID');
            $table->string('name')->comment('任务批次名称');
            $table->integer('total_jobs')->comment('任务总数');
            $table->integer('pending_jobs')->comment('待处理任务数');
            $table->integer('failed_jobs')->comment('失败任务数');
            $table->longText('failed_job_ids')->comment('失败任务ID列表');
            $table->mediumText('options')->nullable()->comment('任务批次选项');
            $table->integer('cancelled_at')->nullable()->comment('取消时间戳');
            $table->integer('created_at')->comment('创建时间戳');
            $table->integer('finished_at')->nullable()->comment('完成时间戳');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_batches');
    }
};
