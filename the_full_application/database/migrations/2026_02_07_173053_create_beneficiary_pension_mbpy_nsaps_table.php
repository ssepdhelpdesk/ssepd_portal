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
    Schema::create('beneficiary_pension_mbpy_nsaps', function (Blueprint $table) {
        $table->id();
/* ========================
* Primary / Common
* ======================== */
$table->uuid('uuid')->unique()->index()->comment('Public identifier');

$table->bigInteger('nsap_application_id')->nullable();
$table->bigInteger('mbpy_application_id')->nullable();

$table->bigInteger('pension_scheme_id')->nullable();
$table->bigInteger('stage_id')->nullable();
$table->string('status')->nullable();

$table->bigInteger('nsap_scheme_id')->nullable();
$table->bigInteger('mbpy_scheme_id')->nullable();

/* ========================
* Applicant Details
* ======================== */
$table->string('applicant_name')->nullable();
$table->string('father_husband_name')->nullable();
$table->string('father_name')->nullable();
$table->string('mother_name')->nullable();

$table->string('first_name')->nullable();
$table->string('middle_name')->nullable();
$table->string('last_name')->nullable();

$table->date('applicant_dob')->nullable();
$table->integer('age')->nullable();
$table->integer('applicant_age')->nullable();
$table->bigInteger('gender_id')->nullable();

/* ========================
* Address / Location
* ======================== */
$table->bigInteger('address_type')->nullable();

$table->bigInteger('district_id')->nullable();
$table->string('district_name')->nullable();

$table->bigInteger('subdivision_id')->nullable();
$table->bigInteger('block_id')->nullable();
$table->string('block_name')->nullable();

$table->bigInteger('gp_id')->nullable();
$table->string('gp_name')->nullable();

$table->bigInteger('village_id')->nullable();
$table->bigInteger('municipality_id')->nullable();
$table->string('municipality_name')->nullable();

$table->bigInteger('habitation_id')->nullable();
$table->bigInteger('ward_id')->nullable();
$table->string('ward_name')->nullable();
$table->string('ward_number')->nullable();

$table->bigInteger('ulb_type_id')->nullable();
$table->string('city_town_name')->nullable();
$table->string('house_plot_number')->nullable();
$table->string('pin_number')->nullable();            

/* ========================
* Identity / Bank
* ======================== */
$table->string('aadhar_number')->nullable();
$table->string('uid_number')->nullable();
$table->string('ifsc_code')->nullable();
$table->string('bank_account_po_number')->nullable();
$table->string('bank_po_account_number')->nullable();

/* ========================
* Certificates / Uploads
* ======================== */
$table->string('upload_image')->nullable();
$table->string('upload_signature_thumb')->nullable();

$table->string('aadhar_scan_copy_path')->nullable();
$table->string('age_proof_scan_copy_Path')->nullable();
$table->string('income_certificate_path')->nullable();
$table->string('additional_certificate_path')->nullable();
$table->string('death_certificate_path')->nullable();
$table->string('ration_card_scan_copy_path')->nullable();
$table->string('bpl_scan_copy_path')->nullable();
$table->string('attach_Addtional_Document')->nullable();

$table->string('self_declartion_certificate')->nullable();
$table->string('misa_certificate')->nullable();
$table->string('tg_certf_path')->nullable();
$table->string('tg_reg_no')->nullable();

/* ========================
* Disability / Pension
* ======================== */
$table->bigInteger('disability_type_id')->nullable();
$table->integer('disability_percentage')->default(0);
$table->string('disability_document_path')->nullable();
$table->string('disability_type_Condition_date')->nullable();
$table->string('udid_cerificateNo')->nullable();

/* ========================
* BPL / Ration
* ======================== */
$table->string('bpl_number')->nullable();
$table->string('ration_card_no')->nullable();
$table->string('without_bpl_remark')->nullable();

/* ========================
* Sanction / Payment
* ======================== */
$table->date('sanction_date')->nullable();
$table->string('sanction_order_number')->nullable();
$table->string('nsap_sanction_order_no')->nullable();
$table->string('mbpy_sanction_order_no')->nullable();

$table->double('sanction_amount')->default(0);
$table->string('payable_at')->nullable();
$table->string('sanctioned_for_life_time')->nullable();
$table->date('pension_with_effect_from')->nullable();
$table->date('disbursement_date')->nullable();
$table->string('pensioner_sanctionId')->nullable();

/* ========================
* Workflow / Status
* ======================== */
$table->string('application_number')->nullable();
$table->string('serial_number_of_application')->nullable();
$table->string('case_record_number')->nullable();

$table->string('rejection_message')->nullable();
$table->longText('invalid_reason')->nullable();
$table->string('remark')->nullable();

$table->string('verification_report')->nullable();
$table->tinyInteger('verify_for_sanction')->default(0);

$table->tinyInteger('is_aadhar_verify')->default(0);
$table->string('rejected_aadhar')->nullable();

$table->bigInteger('withdraw_through_id')->nullable();
$table->bigInteger('digitization_status')->nullable();
$table->string('application_active')->default('ACTIVE');

/* ========================
* Declarations
* ======================== */
$table->string('declaration_1')->default('N');
$table->string('declaration_2')->default('N');
$table->string('declaration_3')->default('N');
$table->string('declaration_4')->default('N');
$table->string('declaration_5')->default('N');

/* ========================
* Audit
* ======================== */
$table->bigInteger('created_by')->nullable();
$table->date('created_on')->nullable();
$table->bigInteger('updated_by')->nullable();
$table->date('updated_on')->nullable();

$table->string('sub_collector_signature')->nullable();
$table->date('sub_collector_signature_updated_date')->nullable();

$table->bigInteger('nsap_application_migration_Id')->nullable();
$table->bigInteger('mbpy_application_migration_Id')->nullable();
$table->string('subhadra_id')->nullable();
$table->timestamps();

/* ========================
* Indexes (Important ones)
* ======================== */
$table->index('status');
$table->index('aadhar_number');
$table->index('application_number');
$table->index('district_id');
$table->index('subdivision_id');
$table->index('block_id');
$table->index('gp_id');
$table->index('village_id');
$table->index('municipality_id');
$table->index('ward_id');
$table->index('habitation_id');            
$table->index('digitization_status');
$table->index('pensioner_sanctionId');
$table->index('udid_cerificateNo');
});
}

/**
* Reverse the migrations.
*/
public function down(): void
{
    Schema::dropIfExists('beneficiary_pension_mbpy_nsaps');
}
};
