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
        Schema::create('special_school_mappings', function (Blueprint $table) {
            $table->id();
            $table->integer('management_id')->index('idx_management_id');
            $table->string('management_name', 255);
            $table->integer('special_school_id')->unique('uq_special_school');
            $table->string('special_school_name', 255);
            $table->integer('district_id')->index('idx_district_id');
            $table->string('district_name', 100);
            $table->integer('user_table_id')->unique('uq_user_table');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_school_mappings');
    }
};
