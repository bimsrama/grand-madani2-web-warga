<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ipl_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('ipl_transactions', 'biaya_sampah')) {
                $table->decimal('biaya_sampah', 15, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('ipl_transactions', 'biaya_keamanan')) {
                $table->decimal('biaya_keamanan', 15, 2)->default(0)->after('biaya_sampah');
            }
            if (!Schema::hasColumn('ipl_transactions', 'kas_rt')) {
                $table->decimal('kas_rt', 15, 2)->default(0)->after('biaya_keamanan');
            }
            if (!Schema::hasColumn('ipl_transactions', 'dana_sosial')) {
                $table->decimal('dana_sosial', 15, 2)->default(0)->after('kas_rt');
            }
            if (!Schema::hasColumn('ipl_transactions', 'kas_rw')) {
                $table->decimal('kas_rw', 15, 2)->default(0)->after('dana_sosial');
            }
            if (!Schema::hasColumn('ipl_transactions', 'magic_link_token')) {
                $table->string('magic_link_token', 64)->nullable()->after('invoice_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ipl_transactions', function (Blueprint $table) {
            $cols = [];
            foreach (['biaya_sampah','biaya_keamanan','kas_rt','dana_sosial','kas_rw','magic_link_token'] as $col) {
                if (Schema::hasColumn('ipl_transactions', $col)) $cols[] = $col;
            }
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
