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

            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            /**
             * Primary + Identifiers
             */
            $table->id();
            $table->bigInteger('nsap_application_id')->nullable()->unique()->index();
            $table->uuid('uuid')->nullable()->unique()->index();

            /**
             * Scheme / Stage / Status
             */
            $table->bigInteger('pension_scheme_id')->nullable();
            $table->bigInteger('stage_id')->nullable();
            $table->string('status', 100)->nullable();
            $table->bigInteger('nsap_scheme_id')->nullable();

            /**
             * Applicant Basic Details
             */
            $table->string('applicant_name', 100)->nullable();
            $table->string('father_husband_name', 100)->nullable();
            $table->date('applicant_dob')->nullable();
            $table->integer('age')->nullable();
            $table->integer('applicant_age')->nullable();
            $table->bigInteger('gender_id')->nullable();
            $table->bigInteger('caste_id')->nullable();

            /**
             * Address / Location (IDs)
             */
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('subdivision_id')->nullable();
            $table->bigInteger('address_type')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('habitation_id')->nullable();
            $table->bigInteger('ward_id')->nullable();

            /**
             * Address / Location (Names)
             */
            $table->string('district_name', 50)->nullable();
            $table->string('block_name', 50)->nullable();
            $table->string('municipality_name', 50)->nullable();
            $table->string('gp_name', 50)->nullable();
            $table->string('ward_name', 50)->nullable();

            /**
             * Declaration Flags
             */
            $table->string('declaration_1', 10)->nullable()->default('N');
            $table->string('declaration_2', 10)->nullable()->default('N');
            $table->string('declaration_3', 10)->nullable()->default('N');
            $table->string('declaration_4', 10)->nullable()->default('N');
            $table->string('declaration_5', 10)->nullable()->default('N');

            /**
             * Disability / Verification Related
             */
            $table->bigInteger('disability_type_id')->nullable();
            $table->integer('disability_percentage')->default(0);
            $table->string('disability_document_path', 500)->nullable();
            $table->string('disability_type_Condition_date', 100)->nullable();
            $table->string('verification_report', 100)->nullable();
            $table->tinyInteger('is_aadhar_verify')->nullable()->default(1);

            /**
             * BPL / Ration Related
             */
            $table->string('subhadra_id', 300)->nullable();
            $table->string('bpl_number', 50)->nullable();
            $table->string('ration_card_no', 300)->nullable();
            $table->string('ration_card_scan_copy_path', 350)->nullable();
            $table->string('without_bpl_remark', 400)->nullable();

            /**
             * Application Numbers / Sanction Info
             */
            $table->string('application_number', 100)->nullable();
            $table->string('sanction_order_number', 100)->nullable();
            $table->string('nsap_sanction_order_no', 100)->nullable();
            $table->date('sanction_date')->nullable();
            $table->date('disbursement_date')->nullable();
            $table->double('sanction_amount')->nullable()->default(0);
            $table->date('pension_with_effect_from')->nullable();

            /**
             * Payment / Bank Details
             */
            $table->bigInteger('withdraw_through_id')->nullable();
            $table->string('uid_number', 100)->nullable();
            $table->string('bank_account_po_number', 100)->nullable();
            $table->string('ifsc_code', 100)->nullable();
            $table->string('payable_at', 300)->nullable();

            /**
             * Identification / Contact
             */
            $table->string('mobile_number', 20)->nullable();
            $table->string('aadhar_number', 50)->nullable();
            $table->string('aadhar_scan_copy_path', 300)->nullable();
            $table->string('rejected_aadhar', 300)->nullable();

            /**
             * Uploaded Files / Documents
             */
            $table->string('upload_image', 500)->nullable();
            $table->string('upload_signature_thumb', 350)->nullable();
            $table->string('death_certificate_path', 300)->nullable();
            $table->string('additional_certificate_path', 300)->nullable();
            $table->string('income_certificate_path', 300)->nullable();
            $table->string('age_proof_scan_copy_Path', 300)->nullable();
            $table->string('bpl_scan_copy_path', 300)->nullable();
            $table->string('attach_Addtional_Document', 100)->nullable();

            /**
             * Case / Remarks / Officer Details
             */
            $table->string('sanctioned_for_life_time', 300)->nullable();
            $table->string('case_record_number', 300)->nullable();
            $table->string('serial_number_of_application', 300)->nullable();
            $table->string('remark', 300)->nullable();
            $table->string('rejection_message', 500)->nullable();
            $table->string('sub_collector_signature', 300)->nullable();
            $table->string('sub_collector_signature_updated_date', 300)->nullable();
            $table->text('invalid_reason')->nullable();
            $table->string('guardian_type', 350)->nullable();

            /**
             * Migration / Mapping / Extra IDs
             */
            $table->bigInteger('nsap_id')->nullable();
            $table->bigInteger('mbpy_application_migration_Id')->nullable();
            $table->bigInteger('digitization_status')->nullable();
            $table->string('udid_cerificateNo', 30)->nullable();
            $table->string('pensioner_sanctionId', 30)->nullable();

            /**
             * Workflow / Active Status Flags
             */
            $table->tinyInteger('verify_for_sanction')->nullable()->default(0);
            $table->tinyInteger('which_govt')->default(2)->comment('1=GOO, 2=GOI');
            $table->string('is_active')->default('active')->index();
            $table->integer('db_status')->default(1)->index();

            /**
             * Audit / Created Updated Info
             */
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

            /**
             * Indexes
             */
            $table->index('district_id', 'idx_district_id');
            $table->index('block_id', 'idx_block_id');
            $table->index('nsap_scheme_id', 'idx_nsap_scheme_id');
            $table->index('stage_id', 'idx_stage_id');
            $table->index('status', 'idx_status');
            $table->index('aadhar_number', 'idx_aadhar_number');
            $table->index('application_number', 'idx_application_number');
            $table->index('sanction_order_number', 'idx_sanction_order_number');
            $table->index('ward_id', 'idx_ward_id');
            $table->index('habitation_id', 'idx_habitation_id');
            $table->index('digitization_status', 'idx_digitization_status');
            $table->index('created_on', 'idx_created_on');
            $table->index(['district_id', 'nsap_scheme_id', 'status'], 'idx_nsap_count1');
            $table->index(['block_id', 'nsap_scheme_id', 'status', 'stage_id'], 'idx_nsap_block_count');
            $table->index(['block_id', 'nsap_scheme_id', 'status', 'nsap_application_id'], 'idx_nsap_block_scheme_status_id');
            $table->index('pensioner_sanctionId', 'idx_pensioner_sanctionId');
            $table->index('udid_cerificateNo', 'idx_udid_cerificateNo');
            $table->index('subhadra_id', 'idx_subhadra_id');
            $table->index(['ward_id', 'habitation_id', 'digitization_status'], 'idx_ward_hab_digit');
            $table->index(['case_record_number', 'serial_number_of_application'], 'idx_case_serial');
            $table->index('rejected_aadhar', 'idx_rejected_aadhar');
            $table->index('verify_for_sanction', 'idx_verify_for_sanction');
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
