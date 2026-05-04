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
        Schema::create('pia_institute_benf_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name_of_the_beneficiary');
            $table->string('benf_system_gen_reg_no')->nullable()->index();
            $table->string('father_or_husband_name');
            $table->date('date_of_birth')->index();
            $table->integer('age');
            $table->string('beneficiary_mobile', 10)->index();
            $table->integer('gender')->index();
            $table->string('aadhaar_no', 12);
            $table->boolean('verified_aadhar')->default(0)->index();
            $table->text('verified_aadhar_remarks')->nullable();
            $table->string('aadhaar_hash', 64)->nullable()->unique()->index();
            $table->text('aadhaar_encrypted')->nullable();
            $table->string('aadhaar_file');
            $table->string('beneficiary_file');
            $table->date('date_of_joining')->index();
            $table->tinyInteger('benf_continued_discontinued')->default(0)->comment('0=Continued, 1=Discontinued');
            $table->date('date_of_discontinue')->nullable()->index();
            $table->string('bank_ac_no')->index();
            $table->bigInteger('bank_ifsc')->index();
            $table->string('beneficiary_bank_file');
            $table->tinyInteger('is_disabled')->default(0)->index();
            $table->string('udid_no')->nullable()->index();
            $table->string('beneficiary_udid_file')->nullable();
            $table->string('disability_category')->nullable();
            $table->tinyInteger('benf_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->string('pin', 6)->nullable();
            $table->string('benf_postal_address_at')->nullable();
            $table->string('benf_postal_address_post')->nullable();
            $table->string('benf_postal_address_via')->nullable();
            $table->string('benf_postal_address_ps')->nullable();
            $table->string('benf_postal_address_district')->nullable();
            $table->string('benf_postal_address_pin', 6)->nullable();
            $table->foreignId('pia_institute_master_institute_id')->nullable();
            $table->foreignId('pia_institute_master_institute_type_id')->nullable();
            $table->string('is_active')->default('active');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by');
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('user_table_id');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pia_institute_benf_details');
    }
};
