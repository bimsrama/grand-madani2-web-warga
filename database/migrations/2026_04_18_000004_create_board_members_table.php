<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('board_members')) {
            Schema::create('board_members', function (Blueprint $table) {
                $table->id();
                $table->string('rt_number', 5)->default('3');
                $table->string('name');
                $table->string('role');
                $table->string('photo_path')->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('board_members');
    }
};
