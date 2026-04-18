<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_items', function (Blueprint $table) {
            $table->id();
            $table->string('rt_number')->default('1')->after('id');
            $table->enum('category', ['preloved', 'jasa']); // preloved goods or service directory
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 15, 2)->nullable(); // null = negotiable / quote for jasa
            $table->string('contact_name');
            $table->string('contact_wa', 20);
            $table->string('image')->nullable();          // storage path
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_items');
    }
};
