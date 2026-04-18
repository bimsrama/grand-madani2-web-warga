<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only target users table, other core tables are handled in original migrations
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'rt_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('rt_number')->default('1')->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('rt_number');
            });
        }
    }
};
