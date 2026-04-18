<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->string('rt_number')->default('1')->after('id');
            $table->tinyInteger('month');           // 1–12
            $table->smallInteger('year');
            $table->decimal('income', 15, 2)->default(0);
            $table->decimal('expense', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('receipt_image')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year', 'rt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
