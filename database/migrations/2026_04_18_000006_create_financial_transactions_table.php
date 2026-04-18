<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('financial_transactions')) {
            Schema::create('financial_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('rt_number', 5)->default('3');
                $table->date('transaction_date');
                $table->string('description');
                $table->enum('type', ['pemasukan', 'pengeluaran']);
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('category')->nullable();
                $table->string('receipt_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
