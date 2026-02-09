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
        
// ====== MBPY Columns ======
        $table->bigInteger('mbpy_application_id')->nullable();
        $table->bigInteger('pension_scheme_id')->nullable();
        $table->bigInteger('stage_id')->nullable();
        $table->string('status', 100)->nullable();
        $table->bigInteger('mbpy_scheme_id')->nullable();
        $table->string('applicant_name', 100)->nullable();
        $table->string('father_husband_name', 100)->nullable();
        $table->bigInteger('district_id')->nullable();
        $table->bigInteger('subdivision_id')->nullable();
        $table->bigInteger('address_type')->nullable();
        $table->bigInteger('block_id')->nullable();
        $table->bigInteger('gp_id')->nullable();
        $table->bigInteger('village_id')->nullable();
        $table->date('applicant_dob')->nullable();
        $table->bigInteger('caste_id')->nullable();
        $table->bigInteger('created_by')->nullable();
        $table->date('created_on')->nullable();
        $table->bigInteger('updated_by')->nullable();
        $table->date('updated_on')->nullable();
        $table->string('declaration_1', 10)->nullable()->default('N');
        $table->string('declaration_2', 10)->nullable()->default('N');
        $table->string('declaration_3', 10)->nullable()->default('N');
        $table->bigInteger('disability_type_id')->nullable();
        $table->string('disability_document_path', 500)->nullable();
        $table->bigInteger('municipality_id')->nullable();
        $table->string('rejection_message', 500)->nullable();
        $table->integer('applicant_age')->nullable();
        $table->string('aadhar_number', 50)->nullable();
        $table->string('mobile_number', 15)->nullable();
        $table->bigInteger('ulb_type_id')->nullable();
        $table->string('city_town_name', 50)->nullable();
        $table->string('ward_number', 50)->nullable();
        $table->string('house_plot_number', 50)->nullable();
        $table->string('pin_number', 50)->nullable();
        $table->string('upload_signature_thumb', 500)->nullable();
        $table->string('upload_image', 500)->nullable();
        $table->string('application_number', 200)->nullable();
        $table->bigInteger('gender_id')->nullable();
        $table->string('aadhar_scan_copy_path', 500)->nullable();
        $table->date('sanction_date')->nullable();
        $table->string('sanction_order_number', 200)->nullable();
        $table->double('sanction_amount')->nullable()->default(0);
        $table->string('payable_at', 100)->nullable();
        $table->string('sanctioned_for_life_time', 50)->nullable();
        $table->string('case_record_number', 200)->nullable();
        $table->string('serial_number_of_application', 50)->nullable();
        $table->string('remark', 500)->nullable();
        $table->string('sub_collector_signature', 500)->nullable();
        $table->date('sub_collector_signature_updated_date')->nullable();
        $table->string('application_active', 50)->nullable()->default('ACTIVE');
        $table->date('disbursement_date')->nullable();
        $table->string('uid_number', 100)->nullable();
        $table->string('bank_po_account_number', 50)->nullable();
        $table->string('ifsc_code', 50)->nullable();
        $table->string('district_name', 100)->nullable();
        $table->string('block_name', 100)->nullable();
        $table->string('municipality_name', 100)->nullable();
        $table->string('gp_name', 100)->nullable();
        $table->integer('disability_percentage')->default(0);
        $table->string('death_certificate_path', 300)->nullable();
        $table->string('additional_certificate_path', 300)->nullable();
        $table->string('income_certificate_path', 300)->nullable();
        $table->string('age_proof_scan_copy_Path', 300)->nullable();
        $table->string('rejected_aadhar', 300)->nullable();
        $table->string('tg_certf_path', 200)->nullable();
        $table->string('tg_reg_no', 50)->nullable();
        $table->string('self_declartion_certificate', 100)->nullable();
        $table->string('father_name', 55)->nullable();
        $table->string('mother_name', 55)->nullable();
        $table->string('verification_report', 500)->nullable();
        $table->string('first_name', 100)->nullable();
        $table->string('middle_name', 100)->nullable();
        $table->string('last_name', 100)->nullable();
        $table->bigInteger('ward_id')->nullable();
        $table->bigInteger('habitation_id')->nullable();
        $table->bigInteger('digitization_status')->nullable();
        $table->string('mbpy_sanction_order_no', 100)->nullable();
        $table->string('invalid_reason', 500)->nullable();
        $table->bigInteger('id')->nullable();
        $table->string('disability_type_Condition_date', 100)->nullable();
        $table->bigInteger('nsap_application_migration_Id')->nullable();
        $table->tinyInteger('is_aadhar_verify')->nullable()->default(0);
        $table->string('misa_certificate', 100)->nullable();
        $table->string('udid_cerificateNo', 30)->nullable();
        $table->string('pensioner_sanctionId', 30)->nullable();
        $table->tinyInteger('verify_for_sanction')->nullable()->default(0);
        $table->string('subhadra_id', 200)->nullable();

            // ====== NSAP Columns ======
        $table->bigInteger('nsap_application_id')->nullable();
        $table->bigInteger('nsap_scheme_id')->nullable();
        $table->string('declaration_4', 10)->nullable()->default('N');
        $table->string('declaration_5', 10)->nullable()->default('N');
        $table->string('bpl_number', 50)->nullable();
        $table->bigInteger('withdraw_through_id')->nullable();
        $table->integer('age')->nullable();
        $table->string('ward_name', 50)->nullable();
        $table->string('bank_account_po_number', 100)->nullable();
        $table->string('bpl_scan_copy_path', 300)->nullable();
        $table->string('ration_card_no', 300)->nullable();
        $table->string('ration_card_scan_copy_path', 350)->nullable();
        $table->string('guardian_type', 350)->nullable();
        $table->string('without_bpl_remark', 400)->nullable();
        $table->string('nsap_sanction_order_no', 100)->nullable();
        $table->date('pension_with_effect_from')->nullable();
        $table->string('attach_Addtional_Document', 100)->nullable();
        $table->bigInteger('mbpy_application_migration_Id')->nullable();
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
