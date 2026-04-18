<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('rt_number')->default('1')->after('id');
            $table->string('block', 10);
            $table->string('number', 10);
            $table->string('owner_name');
            $table->string('wa_number', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['block', 'number', 'rt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
