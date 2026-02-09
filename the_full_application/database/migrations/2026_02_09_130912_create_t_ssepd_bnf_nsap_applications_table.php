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
        Schema::create('t_ssepd_bnf_nsap_application', function (Blueprint $table) {

            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->id();
            $table->bigInteger('nsap_application_id')->nullable();
            $table->uuid('uuid')->nullable()->unique()->index();
            $table->bigInteger('pension_scheme_id')->nullable();
            $table->bigInteger('stage_id')->nullable();
            $table->string('status', 100)->nullable();
            $table->bigInteger('nsap_scheme_id')->nullable();
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

            $table->string('declaration_1', 10)->nullable()->default('N');
            $table->string('declaration_2', 10)->nullable()->default('N');
            $table->string('declaration_3', 10)->nullable()->default('N');
            $table->string('declaration_4', 10)->nullable()->default('N');
            $table->string('declaration_5', 10)->nullable()->default('N');

            $table->bigInteger('disability_type_id')->nullable();
            $table->string('disability_document_path', 500)->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->string('bpl_number', 50)->nullable();

            $table->integer('disability_percentage')->default(0);

            $table->bigInteger('withdraw_through_id')->nullable();
            $table->string('rejection_message', 500)->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->string('sanction_order_number', 100)->nullable();
            $table->string('application_number', 100)->nullable();
            $table->bigInteger('gender_id')->nullable();
            $table->integer('age')->nullable();

            $table->string('district_name', 50)->nullable();
            $table->string('block_name', 50)->nullable();
            $table->string('municipality_name', 50)->nullable();
            $table->string('gp_name', 50)->nullable();
            $table->string('ward_name', 50)->nullable();

            $table->date('sanction_date')->nullable();
            $table->date('disbursement_date')->nullable();

            $table->double('sanction_amount')->nullable()->default(0);

            $table->string('uid_number', 100)->nullable();
            $table->string('bank_account_po_number', 100)->nullable();
            $table->string('ifsc_code', 100)->nullable();
            $table->string('aadhar_number', 50)->nullable();
            $table->string('upload_image', 500)->nullable();

            $table->integer('applicant_age')->nullable();

            $table->string('death_certificate_path', 300)->nullable();
            $table->string('additional_certificate_path', 300)->nullable();
            $table->string('income_certificate_path', 300)->nullable();
            $table->string('age_proof_scan_copy_Path', 300)->nullable();
            $table->string('bpl_scan_copy_path', 300)->nullable();
            $table->string('aadhar_scan_copy_path', 300)->nullable();

            $table->string('payable_at', 300)->nullable();
            $table->string('sanctioned_for_life_time', 300)->nullable();
            $table->string('case_record_number', 300)->nullable();
            $table->string('serial_number_of_application', 300)->nullable();
            $table->string('remark', 300)->nullable();
            $table->string('sub_collector_signature', 300)->nullable();
            $table->string('sub_collector_signature_updated_date', 300)->nullable();

            $table->string('ration_card_no', 300)->nullable();
            $table->string('ration_card_scan_copy_path', 350)->nullable();
            $table->string('upload_signature_thumb', 350)->nullable();
            $table->string('guardian_type', 350)->nullable();

            $table->string('rejected_aadhar', 300)->nullable();
            $table->string('without_bpl_remark', 400)->nullable();

            $table->bigInteger('habitation_id')->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->bigInteger('digitization_status')->nullable();

            $table->string('nsap_sanction_order_no', 100)->nullable();

            $table->text('invalid_reason')->nullable();

            $table->date('pension_with_effect_from')->nullable();
            $table->string('attach_Addtional_Document', 100)->nullable();
            $table->string('verification_report', 100)->nullable();

            $table->bigInteger('nsap_id')->nullable();
            $table->string('disability_type_Condition_date', 100)->nullable();
            $table->bigInteger('mbpy_application_migration_Id')->nullable();

            $table->tinyInteger('is_aadhar_verify')->nullable()->default(1);

            $table->string('udid_cerificateNo', 30)->nullable();
            $table->string('pensioner_sanctionId', 30)->nullable();

            $table->tinyInteger('verify_for_sanction')->nullable()->default(0);
            $table->tinyInteger('which_govt')->default(1)->comment('1=GOO, 2=GOI');
            $table->string('is_active')->default('active')->index();
            $table->integer('db_status')->default(1)->index();
            $table->bigInteger('created_by')->nullable();
            $table->date('created_on')->nullable();            
            $table->foreignId('created_by_user_table_id')->default(1)->index();
            $table->date('created_date')->nullable()->useCurrent();
            $table->time('created_time')->nullable()->useCurrent();
            $table->bigInteger('updated_by')->nullable();
            $table->date('updated_on')->nullable();
            $table->date('updated_date')->nullable()->index();
            $table->time('updated_time')->nullable();
            $table->timestamps();

            // Indexes as per DDL
            $table->index('nsap_application_id');
            $table->index('district_id', 't_ssepd_bnf_nsap_application_district_id_IDX');
            $table->index('digitization_status', 't_ssepd_bnf_nsap_application_fk_1');

            $table->index(['habitation_id', 'ward_id', 'digitization_status'], 'habitation_id');

            $table->index([
                'pension_scheme_id',
                'stage_id',
                'status',
                'nsap_scheme_id',
                'district_id',
                'subdivision_id',
                'address_type',
                'block_id',
                'gp_id',
                'village_id',
                'caste_id',
                'municipality_id',
                'gender_id'
            ], 'pension_scheme_id');

            $table->index([
                'stage_id',
                'nsap_scheme_id',
                'district_id',
                'subdivision_id',
                'address_type',
                'block_id',
                'gp_id',
                'village_id',
                'gender_id',
                'habitation_id',
                'ward_id',
                'nsap_sanction_order_no'
            ], 'stage_id');

            $table->index([
                'block_id',
                'gp_id',
                'village_id',
                'applicant_dob',
                'caste_id',
                'created_by',
                'created_on',
                'aadhar_number',
                'upload_image',
                'applicant_age',
                'income_certificate_path',
                'age_proof_scan_copy_Path',
                'bpl_scan_copy_path'
            ], 'block_id');

            $table->index([
                'verification_report',
                'is_aadhar_verify',
                'udid_cerificateNo',
                'pensioner_sanctionId'
            ], 'verification_report');

            $table->index([
                'aadhar_number',
                'upload_image',
                'death_certificate_path',
                'aadhar_scan_copy_path',
                'rejected_aadhar',
                'ward_id',
                'digitization_status'
            ], 'aadhar_number');

            $table->index('rejected_aadhar', 'rejected_aadhar');

            $table->index(['mbpy_application_migration_Id', 'pensioner_sanctionId'], 'mbpy_application_migration_Id');

            $table->index('status', 'status');         

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_ssepd_bnf_nsap_application');
    }
};