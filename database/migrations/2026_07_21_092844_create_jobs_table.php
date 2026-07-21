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
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('队列任务ID');
            $table->string('queue')->index()->comment('队列名称');
            $table->longText('payload')->comment('任务载荷');
            $table->unsignedTinyInteger('attempts')->comment('已尝试执行次数');
            $table->unsignedInteger('reserved_at')->nullable()->comment('任务保留时间戳');
            $table->unsignedInteger('available_at')->comment('任务可执行时间戳');
            $table->unsignedInteger('created_at')->comment('任务创建时间戳');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
