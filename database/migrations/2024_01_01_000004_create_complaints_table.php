<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('rt_number')->default('1')->after('id');
            $table->string('reporter_name');
            $table->string('reporter_address');
            $table->string('category');             // Kebersihan, Keamanan, Fasilitas, dll.
            $table->text('description');
            $table->enum('status', ['waiting', 'processing', 'done'])->default('waiting');
            $table->text('admin_response')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('complaints');
    }
};
