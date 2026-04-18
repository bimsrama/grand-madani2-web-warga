<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            // Cek kolom sebelum ditambah — aman dijalankan berulang
            if (!Schema::hasColumn('residents', 'pin')) {
                $table->string('pin', 60)->nullable()->after('wa_number');
            }
            if (!Schema::hasColumn('residents', 'family_members')) {
                $table->json('family_members')->nullable()->after('pin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('residents', 'pin'))            $cols[] = 'pin';
            if (Schema::hasColumn('residents', 'family_members')) $cols[] = 'family_members';
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
