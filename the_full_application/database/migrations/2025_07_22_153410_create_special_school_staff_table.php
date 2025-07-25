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
        Schema::create('special_school_staff', function (Blueprint $table) {
            $table->id();
            $table->integer('management_id')->index('idx_management_id');
            $table->string('special_school_management_name', 255);
            $table->integer('special_school_id')->index('idx_special_school');
            $table->string('special_school_name', 255);
            $table->string('school_system_gen_reg_no', 255);
            $table->string('special_school_staff_name');
            $table->date('staff_engagement_date');
            $table->unsignedTinyInteger('staff_designation');
            $table->unsignedTinyInteger('staff_employment_type');
            $table->string('highest_qualification');
            $table->decimal('basic_remuneration', 10, 2);
            $table->string('special_school_staff_aadhar_no', 12);
            $table->string('special_school_file_staff_aadhar');
            $table->string('staff_email_id')->nullable();
            $table->string('staff_mob_no', 10);
            $table->date('staff_date_of_birth');
            $table->string('file_staff_image');
            $table->unsignedTinyInteger('disability_type')->nullable();
            $table->string('udid_no')->nullable();
            $table->string('file_udid_certificate')->nullable();            
            $table->tinyInteger('staff_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->string('pin', 6)->nullable();
            $table->string('staff_postal_address_at')->nullable();
            $table->string('staff_postal_address_post')->nullable();
            $table->string('staff_postal_address_via')->nullable();
            $table->string('staff_postal_address_ps')->nullable();
            $table->string('staff_postal_address_district')->nullable();
            $table->string('staff_postal_address_pin', 6)->nullable();
            $table->string('is_active')->default('active');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by');
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('application_stage_id');
            $table->foreignId('user_table_id');
            $table->bigInteger('no_of_form_completed');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_school_staff');
    }
};
