<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // hasTable = aman dijalankan berulang, skip jika tabel sudah ada
        if (!Schema::hasTable('community_widgets')) {
            Schema::create('community_widgets', function (Blueprint $table) {
                $table->id();
                $table->string('rt_number', 5)->default('3');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('thumbnail_path')->nullable();
                $table->string('external_link')->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('community_widgets');
    }
};
