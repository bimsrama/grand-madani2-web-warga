<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('rt_number')->default('1')->after('id');
            $table->string('name');
            $table->string('location');
            $table->text('embed_url');              // iframe src or HLS/RTSP embed URL
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot: which residents have view-access to which cameras
        Schema::create('camera_resident', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_id')->constrained('cameras')->cascadeOnDelete();
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['camera_id', 'resident_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_resident');
        Schema::dropIfExists('cameras');
    }
};
