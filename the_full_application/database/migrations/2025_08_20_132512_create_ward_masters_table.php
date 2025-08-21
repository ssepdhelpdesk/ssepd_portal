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
        Schema::create('ward_masters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ward_code');
            $table->string('ward_name');
            $table->string('rural_urban_area');
            $table->bigInteger('municipal_area_code');
            $table->bigInteger('district_code');
            $table->bigInteger('state_code');
            $table->bigInteger('zone_id')->nullable();
            $table->bigInteger('state_id')->default(228);
            $table->string('is_active')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ward_masters');
    }
};
