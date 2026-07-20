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
        if ( Schema::hasColumn('users', 'avatar') ) { return; }

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password')->comment('头像');
        });
    }

    /**
     * 回滚迁移。
     */
    public function down(): void
    {
        if ( !Schema::hasColumn('users', 'avatar') ) { return; }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};
