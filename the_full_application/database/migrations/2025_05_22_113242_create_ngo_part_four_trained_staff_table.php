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
        Schema::create('ngo_part_four_trained_staff', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ngo_org_id');
            $table->foreignId('ngo_tbl_id')->constrained('ngo_registrations');
            $table->string('ngo_system_gen_reg_no', 255);
            $table->string('staff_name');
            $table->string('staff_designation');
            $table->string('staff_role');
            $table->tinyInteger('staff_category')->nullable();
            $table->tinyInteger('staff_category_type')->nullable();
            $table->string('staff_qualification')->nullable();
            $table->date('staff_date_of_joining')->nullable();
            $table->string('staff_aadhar_number', 12)->nullable();
            $table->string('staff_mob_no')->nullable();
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
        Schema::dropIfExists('ngo_part_four_trained_staff');
    }
};
