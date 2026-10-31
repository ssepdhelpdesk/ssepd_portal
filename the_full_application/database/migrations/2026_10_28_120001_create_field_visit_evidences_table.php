<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('field_visit_evidences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('field_visit_id');
            $table->foreign('field_visit_id', 'fk_fve_visit')
                  ->references('id')
                  ->on('field_visits')
                  ->onDelete('cascade');

            $table->string('photo_path', 255);
            $table->string('thumbnail_path', 255)->nullable();
            $table->string('original_filename', 255);
            $table->unsignedBigInteger('file_size'); // Size in bytes
            $table->string('mime_type', 100);
            $table->string('file_checksum', 64); // SHA-256 hash of original uploaded image bytes

            // Geolocation and Capture Metadata
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('gps_accuracy', 8, 2)->nullable();
            $table->timestamp('location_reading_time')->nullable();
            $table->timestamp('client_capture_time')->nullable();
            $table->string('capture_source', 50)->default('DEVICE_CAMERA'); // DEVICE_CAMERA or MANUAL_UPLOAD
            $table->string('caption', 255)->nullable();

            // Audit attribution (Mandatory user_table_id rule)
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();

            // Performance Indexes
            $table->index('field_visit_id', 'idx_fve_visit_id');
            $table->index('created_by', 'idx_fve_created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_visit_evidences');
    }
};
