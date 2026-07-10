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
        Schema::create('nsap_dump18_june2026_data', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('state_code')->nullable();
            $table->string('district_code')->nullable();
            $table->string('sub_district_municipal_area_code')->nullable();
            $table->string('gram_panchayat_ward_code')->nullable();
            $table->string('village_code')->nullable();
            $table->string('scheme_code')->nullable();
            $table->string('sanction_order_no')->nullable();
            $table->string('applicant_name')->nullable();
            $table->string('contact_person_mobile')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->string('category_code')->nullable();
            $table->string('state_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('sub_district_municipal_area_name')->nullable();
            $table->string('rural_urban_area')->nullable();
            $table->string('gram_panchayat_ward_name')->nullable();
            $table->string('village_name')->nullable();
            $table->string('full_add')->nullable();
            $table->string('creation_date')->nullable();
            $table->string('sanction_date')->nullable();
            $table->string('bank_po_account_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('status')->nullable();
            $table->string('uid_ref_value')->nullable();
            
            $table->bigInteger('state_id')->nullable()->index();
            $table->string('district')->nullable();
            $table->bigInteger('district_id')->nullable()->index();
            $table->string('area')->nullable();
            $table->string('block_or_ulb')->nullable();
            $table->string('block_id')->nullable()->index();
            $table->string('municipality_id')->nullable()->index();
            $table->string('sub_district_municipality')->nullable();
            $table->string('gram_panchayat_ward')->nullable();
            $table->string('gp_id')->nullable()->index();
            $table->string('ward_id')->nullable()->index();
            $table->string('village')->nullable();
            $table->string('village_id')->nullable()->index();
            $table->string('pin', 6)->nullable()->index();
            $table->string('postal_address_at')->nullable();
            $table->string('postal_address_post')->nullable();
            $table->string('postal_address_via')->nullable();
            $table->string('postal_address_ps')->nullable();
            $table->string('postal_address_district')->nullable();
            $table->string('postal_address_pin', 6)->nullable()->index();
            $table->date('dob')->nullable();
            $table->string('age_by_user')->nullable();
            $table->string('gender_by_user')->nullable();
            $table->string('pincode')->nullable();
            $table->string('father_husband_name')->nullable();
            $table->string('caste')->nullable();
            $table->string('bpl_id')->nullable();
            $table->string('bpl_member_id')->nullable();
            $table->string('ration_card_no')->nullable();
            $table->string('epic_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('disbursement_upto')->nullable();
            $table->string('bank_account_file')->nullable();
            $table->string('disbursement_mode')->nullable();
            $table->string('beneficiary_no')->nullable();
            $table->string('name_as_per_aadhaar')->nullable();
            $table->string('aadhar_verified')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->bigInteger('aadhaar_no_by_user')->nullable()->unique();
            $table->string('aadhaar_hash', 64)->nullable()->unique()->index();
            $table->text('aadhaar_encrypted')->nullable();
            $table->tinyInteger('verified_aadhar')->default(0)->comment('0=Not Verified, 1=Verified');
            $table->string('verified_aadhar_remarks')->nullable();
            $table->tinyInteger('marked_for_dbt')->default(0)->comment('0=Not Verified, 1=Verified');
            $table->string('is_active')->default('active')->index();
            $table->integer('db_status')->default(1)->index();
            $table->enum('status_db', ['Active', 'Inactive'])->default('Active')->index();
            $table->string('created_by')->default(0)->index();
            $table->foreignId('created_by_user_table_id')->default(1)->index();
            $table->date('created_date')->nullable()->useCurrent();
            $table->time('created_time')->nullable()->useCurrent();
            $table->foreignId('updated_by')->nullable()->index();
            $table->date('updated_date')->nullable()->index();
            $table->time('updated_time')->nullable();
            $table->index('state');
            $table->index('district');
            $table->index('scheme_code');
            $table->index('beneficiary_no');
            $table->index('aadhar_no');
            $table->index('mobile_no');
            $table->timestamps();
        });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nsap_dump18_june2026_data');
    }
};
