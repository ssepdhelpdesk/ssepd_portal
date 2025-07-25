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
        Schema::create('ngo_part_six_asset_organizations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ngo_org_id');
            $table->foreignId('ngo_tbl_id')->constrained('ngo_registrations');
            $table->string('ngo_system_gen_reg_no', 255);
            $table->string('land_no_of_unit')->nullable();
            $table->enum('land_permanent_or_rental', ['1', '2'])->nullable();
            $table->string('land_no_of_unit_file')->nullable();
            $table->string('building_no_of_unit')->nullable();
            $table->enum('building_permanent_or_rental', ['1', '2'])->nullable();
            $table->string('building_no_of_unit_file')->nullable();
            $table->string('vehicles_no_of_unit')->nullable();
            $table->enum('vehicles_permanent_or_rental', ['1', '2'])->nullable();
            $table->string('vehicles_no_of_unit_file')->nullable();
            $table->string('equipment_no_of_unit')->nullable();
            $table->enum('equipment_permanent_or_rental', ['1', '2'])->nullable();
            $table->string('equipment_no_of_unit_file')->nullable();
            $table->string('others_no_of_unit')->nullable();
            $table->enum('others_permanent_or_rental', ['1', '2'])->nullable();
            $table->string('others_no_of_unit_file')->nullable();
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by');
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngo_part_six_asset_organizations');
    }
};
