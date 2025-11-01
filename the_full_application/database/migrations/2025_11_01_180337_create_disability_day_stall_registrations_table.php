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
        Schema::create('disability_day_stall_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_the_organization');
            $table->string('contact_person_name');
            $table->string('email');
            $table->string('phone_number');
            $table->longText('purpose_of_requirement_of_stall');
            $table->longText('organization_address');
            $table->integer('allotted_stall_no')->nullable();
            $table->tinyInteger('staff_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->string('pin', 6)->nullable();
            $table->string('is_active')->default('active');
            $table->date('created_date');
            $table->time('created_time');
            $table->integer('created_by');
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->integer('updated_by')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disability_day_stall_registrations');
    }
};
