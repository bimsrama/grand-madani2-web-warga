<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipl_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('rt_number')->default('1')->after('id');
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
            $table->tinyInteger('month');                    // 1–12
            $table->smallInteger('year');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('status', ['lunas', 'belum'])->default('belum');
            $table->timestamp('paid_at')->nullable();
            $table->string('invoice_path')->nullable();      // PDF path in storage
            $table->timestamps();

            $table->unique(['resident_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipl_transactions');
    }
};
