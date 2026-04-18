<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('resident_data_updates')) {
            Schema::create('resident_data_updates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
                $table->string('rt_number', 5)->default('3');
                $table->string('requested_name')->nullable();
                $table->string('requested_wa', 20)->nullable();
                $table->json('requested_family_members')->nullable();
                $table->string('notes')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->string('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_data_updates');
    }
};
