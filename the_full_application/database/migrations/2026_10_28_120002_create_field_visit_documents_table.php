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
        Schema::create('field_visit_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('field_visit_id');
            $table->foreign('field_visit_id', 'fk_fvd_visit')
                  ->references('id')
                  ->on('field_visits')
                  ->onDelete('cascade');

            $table->string('document_name', 255);
            $table->string('file_path', 255);
            $table->string('original_filename', 255);
            $table->unsignedBigInteger('file_size'); // Size in bytes
            $table->string('mime_type', 100);
            $table->string('file_checksum', 64); // SHA-256 hash of original file

            // Audit attribution (Mandatory user_table_id rule)
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();

            // Performance Indexes
            $table->index('field_visit_id', 'idx_fvd_visit_id');
            $table->index('created_by', 'idx_fvd_created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_visit_documents');
    }
};
