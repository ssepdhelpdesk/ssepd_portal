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
Schema::create('ssepd_pensions', function (Blueprint $table) {
$table->id();
$table->uuid('uuid')->nullable()->unique()->index();

/**
* Application Identifiers
*/
$table->bigInteger('mbpy_application_id')->nullable()->unique()->index();
$table->bigInteger('nsap_application_id')->nullable()->unique()->index();
$table->bigInteger('mbpy_application_migration_Id')->nullable();
$table->bigInteger('nsap_application_migration_Id')->nullable();
$table->bigInteger('mbpy_id')->nullable();
$table->bigInteger('nsap_id')->nullable();

/**
* Scheme / Stage / Status
*/
$table->bigInteger('pension_scheme_id')->nullable();
$table->bigInteger('mbpy_scheme_id')->nullable();
$table->bigInteger('nsap_scheme_id')->nullable();
$table->bigInteger('stage_id')->nullable();
$table->string('status', 100)->nullable();
$table->string('application_active', 50)->nullable()->default('ACTIVE');
$table->tinyInteger('verify_for_sanction')->nullable()->default(0);
$table->tinyInteger('which_govt')->default(0)->comment('0=Default, 1=GOO, 2=GOI');

/**
* Applicant Personal Details
*/
$table->string('applicant_name', 100)->nullable();
$table->string('father_husband_name', 100)->nullable();
$table->string('father_name', 55)->nullable();
$table->string('mother_name', 55)->nullable();
$table->string('first_name', 100)->nullable();
$table->string('middle_name', 100)->nullable();
$table->string('last_name', 100)->nullable();
$table->date('applicant_dob')->nullable();
$table->integer('age')->nullable();
$table->integer('applicant_age')->nullable();
$table->bigInteger('gender_id')->nullable();
$table->bigInteger('caste_id')->nullable();

/**
* Contact Details
*/
$table->string('mobile_number', 20)->nullable();

/**
* Address / Location Details
*/
$table->bigInteger('district_id')->nullable();
$table->bigInteger('subdivision_id')->nullable();
$table->bigInteger('block_id')->nullable();
$table->bigInteger('gp_id')->nullable();
$table->bigInteger('village_id')->nullable();
$table->bigInteger('municipality_id')->nullable();
$table->bigInteger('ward_id')->nullable();
$table->bigInteger('habitation_id')->nullable();
$table->bigInteger('address_type')->nullable();
$table->bigInteger('ulb_type_id')->nullable();

$table->string('district_name', 100)->nullable();
$table->string('block_name', 100)->nullable();
$table->string('municipality_name', 100)->nullable();
$table->string('gp_name', 100)->nullable();
$table->string('ward_name', 50)->nullable();
$table->string('ward_number', 50)->nullable();
$table->string('city_town_name', 50)->nullable();
$table->string('house_plot_number', 50)->nullable();
$table->string('pin_number', 50)->nullable();

/**
* Aadhaar/ Bank / Payment Details
*/
$table->bigInteger('withdraw_through_id')->nullable();
$table->string('uid_number', 100)->nullable();
$table->string('aadhar_number', 50)->nullable();
$table->bigInteger('aadhaar_no_by_user')->nullable()->unique();
$table->string('aadhaar_hash', 64)->nullable()->unique()->index();
$table->text('aadhaar_encrypted')->nullable();
$table->tinyInteger('verified_aadhar')->default(0)->comment('0=Not Verified, 1=Verified');
$table->string('verified_aadhar_remarks')->nullable();
$table->bigInteger('bank_tbl_id')->nullable();
$table->string('bank_account_po_number', 100)->nullable();
$table->string('bank_po_account_number', 50)->nullable();
$table->string('ifsc_code', 100)->nullable();
$table->double('sanction_amount')->nullable()->default(0);

/**
* Application Numbers / Orders
*/
$table->string('application_number', 200)->nullable();
$table->string('sanction_order_number', 200)->nullable();
$table->string('mbpy_sanction_order_no', 100)->nullable();
$table->string('nsap_sanction_order_no', 100)->nullable();

/**
* Dates (Sanction / Disbursement / Pension)
*/
$table->date('sanction_date')->nullable();
$table->date('disbursement_date')->nullable();
$table->date('pension_with_effect_from')->nullable();

/**
* Disability Details
*/
$table->bigInteger('disability_type_id')->nullable();
$table->integer('disability_percentage')->default(0);
$table->string('disability_document_path', 500)->nullable();
$table->string('disability_type_Condition_date', 100)->nullable();

/**
* BPL / Ration Card Details
*/
$table->string('bpl_number', 50)->nullable();
$table->string('bpl_scan_copy_path', 300)->nullable();
$table->string('ration_card_no', 300)->nullable();
$table->string('ration_card_scan_copy_path', 350)->nullable();

/**
* Document Uploads
*/
$table->string('upload_image', 500)->nullable();
$table->string('upload_signature_thumb', 500)->nullable();
$table->string('aadhar_scan_copy_path', 500)->nullable();
$table->string('age_proof_scan_copy_Path', 300)->nullable();
$table->string('income_certificate_path', 300)->nullable();
$table->string('additional_certificate_path', 300)->nullable();
$table->string('death_certificate_path', 300)->nullable();
$table->string('attach_Addtional_Document', 100)->nullable();
$table->string('guardian_type', 350)->nullable();
$table->string('tg_certf_path', 200)->nullable();
$table->string('tg_reg_no', 50)->nullable();
$table->string('self_declartion_certificate', 100)->nullable();
$table->string('misa_certificate', 100)->nullable();

/**
* Verification / Flags
*/
$table->tinyInteger('is_aadhar_verify')->nullable()->default(0);
$table->string('verification_report', 500)->nullable();
$table->string('udid_cerificateNo', 30)->nullable();
$table->string('pensioner_sanctionId', 30)->nullable();

/**
* Remarks / Rejection / Invalid
*/
$table->string('rejection_message', 500)->nullable();
$table->string('rejected_aadhar', 300)->nullable();
$table->string('without_bpl_remark', 400)->nullable();
$table->string('remark', 500)->nullable();
$table->text('invalid_reason')->nullable();

/**
* Official / Collector Section
*/
$table->string('payable_at', 300)->nullable();
$table->string('sanctioned_for_life_time', 300)->nullable();
$table->string('case_record_number', 300)->nullable();
$table->string('serial_number_of_application', 300)->nullable();
$table->string('sub_collector_signature', 500)->nullable();
$table->string('sub_collector_signature_updated_date', 300)->nullable();

/**
* System Tracking / Digitization
*/
$table->bigInteger('digitization_status')->nullable();
$table->string('subhadra_id', 200)->nullable();

/**
* Declaration Flags
*/
$table->string('declaration_1', 10)->nullable()->default('N');
$table->string('declaration_2', 10)->nullable()->default('N');
$table->string('declaration_3', 10)->nullable()->default('N');
$table->string('declaration_4', 10)->nullable()->default('N');
$table->string('declaration_5', 10)->nullable()->default('N');

/**
* Audit Fields
*/
$table->string('is_active')->default('active')->index();
$table->integer('db_status')->default(1)->index();
$table->bigInteger('created_by')->nullable();
$table->date('created_date')->nullable()->useCurrent();
$table->date('created_on')->nullable();
$table->time('created_time')->nullable()->useCurrent();
$table->foreignId('created_by_user_table_id')->default(1)->index();
$table->bigInteger('updated_by')->nullable();
$table->date('updated_date')->nullable()->index();
$table->date('updated_on')->nullable();
$table->time('updated_time')->nullable();
$table->timestamps();

/**
* Indexes
*/
$table->index('mbpy_application_id', 'idx_mbpy_application_id');
$table->index('nsap_application_id', 'idx_nsap_application_id');
$table->index('uuid', 'idx_uuid');

$table->index('pension_scheme_id', 'idx_pension_scheme_id');
$table->index('mbpy_scheme_id', 'idx_mbpy_scheme_id');
$table->index('nsap_scheme_id', 'idx_nsap_scheme_id');
$table->index('stage_id', 'idx_stage_id');
$table->index('status', 'idx_status');

$table->index('district_id', 'idx_district_id');
$table->index('subdivision_id', 'idx_subdivision_id');
$table->index('block_id', 'idx_block_id');
$table->index('gp_id', 'idx_gp_id');
$table->index('village_id', 'idx_village_id');
$table->index('municipality_id', 'idx_municipality_id');
$table->index('ward_id', 'idx_ward_id');
$table->index('habitation_id', 'idx_habitation_id');
$table->index('digitization_status', 'idx_digitization_status');

$table->index('aadhar_number', 'idx_aadhar_number');
$table->index('application_number', 'idx_application_number');
$table->index('sanction_order_number', 'idx_sanction_order_number');

$table->index('udid_cerificateNo', 'idx_udid_cerificateNo');
$table->index('pensioner_sanctionId', 'idx_pensioner_sanctionId');

$table->index('subhadra_id', 'idx_subhadra_id');
$table->index('ward_number', 'idx_ward_number');

$table->index('created_on', 'idx_created_on');
$table->index('updated_on', 'idx_updated_on');
$table->index('updated_date', 'idx_updated_date');

$table->index('rejected_aadhar', 'idx_rejected_aadhar');
$table->index('verify_for_sanction', 'idx_verify_for_sanction');

$table->index(['district_id', 'nsap_scheme_id', 'status'], 'idx_nsap_count1');
$table->index(['block_id', 'nsap_scheme_id', 'status', 'stage_id'], 'idx_nsap_block_count');
$table->index(['block_id', 'nsap_scheme_id', 'status', 'nsap_application_id'], 'idx_nsap_block_scheme_status_id');

$table->index(['ward_id', 'habitation_id', 'digitization_status'], 'idx_ward_hab_digit');
$table->index(['case_record_number', 'serial_number_of_application'], 'idx_case_serial');

});
}

/**
* Reverse the migrations.
*/
public function down(): void
{
Schema::dropIfExists('ssepd_pensions');
}
};
