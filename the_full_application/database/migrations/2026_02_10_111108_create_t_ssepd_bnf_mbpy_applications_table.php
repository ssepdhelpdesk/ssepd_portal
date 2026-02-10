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
        Schema::create('t_ssepd_bnf_mbpy_application', function (Blueprint $table) {

            // ==============================
            // TABLE SETTINGS
            // ==============================
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            // ==============================
            // PRIMARY / UNIQUE IDENTIFIERS
            // ==============================
            $table->id();
            $table->bigInteger('mbpy_application_id')->nullable()->unique()->index();
            $table->uuid('uuid')->nullable()->unique()->index();

            // ==============================
            // SCHEME / STAGE / STATUS DETAILS
            // ==============================
            $table->bigInteger('pension_scheme_id')->nullable();
            $table->bigInteger('stage_id')->nullable();
            $table->string('status', 100)->nullable();
            $table->bigInteger('mbpy_scheme_id')->nullable();

            // ==============================
            // APPLICANT PERSONAL DETAILS
            // ==============================
            $table->string('applicant_name', 100)->nullable();
            $table->string('father_husband_name', 100)->nullable();
            $table->date('applicant_dob')->nullable();
            $table->integer('applicant_age')->nullable();
            $table->bigInteger('gender_id')->nullable();
            $table->bigInteger('caste_id')->nullable();

            // ==============================
            // ADDRESS / LOCATION DETAILS
            // ==============================
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('subdivision_id')->nullable();
            $table->bigInteger('address_type')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('ulb_type_id')->nullable();
            $table->string('city_town_name', 50)->nullable();
            $table->string('ward_number', 50)->nullable();
            $table->string('house_plot_number', 50)->nullable();
            $table->string('pin_number', 50)->nullable();

            // ==============================
            // DECLARATION DETAILS
            // ==============================
            $table->string('declaration_1', 10)->nullable()->default('N');
            $table->string('declaration_2', 10)->nullable()->default('N');
            $table->string('declaration_3', 10)->nullable()->default('N');
            $table->string('declaration_4', 10)->nullable()->default('N');
            $table->string('declaration_5', 10)->nullable()->default('N');

            // ==============================
            // DISABILITY DETAILS
            // ==============================
            $table->bigInteger('disability_type_id')->nullable();
            $table->string('disability_document_path', 500)->nullable();
            $table->integer('disability_percentage')->default(0);
            $table->string('disability_type_Condition_date', 100)->nullable();

            // ==============================
            // BPL / REJECTION DETAILS
            // ==============================
            $table->string('bpl_number', 50)->nullable();
            $table->string('rejection_message', 500)->nullable();
            $table->string('rejected_aadhar', 300)->nullable();

            // ==============================
            // IDENTITY / CONTACT DETAILS
            // ==============================
            $table->string('aadhar_number', 50)->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('uid_number', 100)->nullable();
            $table->tinyInteger('is_aadhar_verify')->nullable()->default(0);

            // ==============================
            // UPLOADS / DOCUMENTS
            // ==============================
            $table->string('upload_signature_thumb', 500)->nullable();
            $table->string('upload_image', 500)->nullable();
            $table->string('aadhar_scan_copy_path', 500)->nullable();
            $table->string('death_certificate_path', 300)->nullable();
            $table->string('additional_certificate_path', 300)->nullable();
            $table->string('income_certificate_path', 300)->nullable();
            $table->string('age_proof_scan_copy_Path', 300)->nullable();
            $table->string('tg_certf_path', 200)->nullable();
            $table->string('self_declartion_certificate', 100)->nullable();
            $table->string('misa_certificate', 100)->nullable();

            // ==============================
            // APPLICATION / SANCTION DETAILS
            // ==============================
            $table->string('application_number', 200)->nullable();
            $table->date('sanction_date')->nullable();
            $table->string('sanction_order_number', 200)->nullable();
            $table->double('sanction_amount')->nullable()->default(0);
            $table->string('payable_at', 100)->nullable();
            $table->string('sanctioned_for_life_time', 50)->nullable();
            $table->date('disbursement_date')->nullable();
            $table->string('mbpy_sanction_order_no', 100)->nullable();

            // ==============================
            // BANK DETAILS
            // ==============================
            $table->string('bank_po_account_number', 50)->nullable();
            $table->string('ifsc_code', 50)->nullable();

            // ==============================
            // CASE / REMARK DETAILS
            // ==============================
            $table->string('case_record_number', 200)->nullable();
            $table->string('serial_number_of_application', 50)->nullable();
            $table->string('remark', 500)->nullable();
            $table->string('sub_collector_signature', 500)->nullable();
            $table->date('sub_collector_signature_updated_date')->nullable();
            $table->string('invalid_reason', 500)->nullable();

            // ==============================
            // NAME BREAKUP DETAILS
            // ==============================
            $table->string('father_name', 55)->nullable();
            $table->string('mother_name', 55)->nullable();
            $table->string('verification_report', 500)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();

            // ==============================
            // GEO TAGGING / DIGITIZATION DETAILS
            // ==============================
            $table->bigInteger('ward_id')->nullable();
            $table->bigInteger('habitation_id')->nullable();
            $table->bigInteger('digitization_status')->nullable();

            // ==============================
            // MIGRATION / OTHER DETAILS
            // ==============================
            $table->bigInteger('mbpy_id')->nullable();
            $table->bigInteger('nsap_application_migration_Id')->nullable();

            // ==============================
            // GOVT / ACTIVE FLAGS
            // ==============================
            $table->string('application_active', 50)->nullable()->default('ACTIVE');
            $table->tinyInteger('verify_for_sanction')->nullable()->default(0);
            $table->tinyInteger('which_govt')->default(1)->comment('1=GOO, 2=GOI');
            $table->string('is_active')->default('active')->index();
            $table->integer('db_status')->default(1)->index();

            // ==============================
            // TG DETAILS
            // ==============================
            $table->string('tg_reg_no', 50)->nullable();

            // ==============================
            // LOCATION NAME FIELDS
            // ==============================
            $table->string('district_name', 100)->nullable();
            $table->string('block_name', 100)->nullable();
            $table->string('municipality_name', 100)->nullable();
            $table->string('gp_name', 100)->nullable();

            // ==============================
            // UDID / PENSIONER DETAILS
            // ==============================
            $table->string('udid_cerificateNo', 30)->nullable();
            $table->string('pensioner_sanctionId', 30)->nullable();

            // ==============================
            // CREATED / UPDATED TRACKING
            // ==============================
            $table->bigInteger('created_by')->nullable();
            $table->date('created_on')->nullable();
            $table->foreignId('created_by_user_table_id')->default(1)->index();
            $table->date('created_date')->nullable()->useCurrent();
            $table->time('created_time')->nullable()->useCurrent();

            $table->bigInteger('updated_by')->nullable();
            $table->date('updated_on')->nullable();
            $table->date('updated_date')->nullable()->index();
            $table->time('updated_time')->nullable();

            // ==============================
            // SUBHADRA + TIMESTAMPS
            // ==============================
            $table->string('subhadra_id', 200)->nullable();
            $table->timestamps();

            // ==============================
            // INDEXES
            // ==============================
            $table->index('district_id', 'idx_district_id');
            $table->index('block_id', 'idx_block_id');
            $table->index('mbpy_scheme_id', 'idx_mbpy_scheme_id');
            $table->index('stage_id', 'idx_stage_id');
            $table->index('status', 'idx_status');
            $table->index('aadhar_number', 'idx_aadhar_number');
            $table->index('application_number', 'idx_application_number');
            $table->index('sanction_order_number', 'idx_sanction_order_number');
            $table->index('ward_id', 'idx_ward_id');
            $table->index('habitation_id', 'idx_habitation_id');
            $table->index('digitization_status', 'idx_digitization_status');
            $table->index('created_on', 'idx_created_on');

            $table->index(['district_id', 'mbpy_scheme_id', 'status'], 'idx_mbpy_count1');
            $table->index(['block_id', 'mbpy_scheme_id', 'status', 'stage_id'], 'idx_mbpy_block_count');
            $table->index(['block_id', 'mbpy_scheme_id', 'status', 'mbpy_application_id'], 'idx_mbpy_block_scheme_status_id');

            $table->index('pensioner_sanctionId', 'idx_pensioner_sanctionId');
            $table->index('udid_cerificateNo', 'idx_udid_cerificateNo');
            $table->index('subhadra_id', 'idx_subhadra_id');
            $table->index('ward_number', 'idx_ward_number');

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
        Schema::dropIfExists('t_ssepd_bnf_mbpy_application');
    }
};
