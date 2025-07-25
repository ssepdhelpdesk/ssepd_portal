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
        Schema::create('ngo_registrations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ngo_org_id');
            $table->tinyInteger('ngo_registration_type')->nullable();
            $table->bigInteger('ngo_category');
            $table->string('ngo_org_name');
            $table->string('ngo_org_pan');
            $table->string('ngo_org_pan_file')->nullable();
            $table->string('ngo_org_email');
            $table->string('ngo_org_phone');
            $table->string('ngo_org_website')->nullable();
            $table->bigInteger('ngo_registered_with')->nullable();
            $table->string('ngo_other_reg_act')->nullable();
            $table->bigInteger('ngo_type_of_vo_or_ngo')->nullable();
            $table->string('ngo_reg_no')->nullable();
            $table->string('ngo_system_gen_reg_no')->nullable();
            $table->string('ngo_file_rc')->nullable();
            $table->date('ngo_date_of_registration')->nullable();
            $table->date('ngo_date_of_registration_validity')->nullable();
            $table->string('nature_of_organisation', 250)->nullable();
            $table->string('nature_of_organisation_other')->nullable();
            $table->bigInteger('ngo_organisation_type')->nullable();
            $table->string('ngo_file_byelaws')->nullable();
            $table->string('ngo_parent_organisation')->nullable();
            $table->boolean('ngo_reg_velidity_available')->nullable();
            $table->tinyInteger('ngo_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->string('pin', 6)->nullable();
            $table->string('ngo_postal_address_at')->nullable();
            $table->string('ngo_postal_address_post')->nullable();
            $table->string('ngo_postal_address_via')->nullable();
            $table->string('ngo_postal_address_ps')->nullable();
            $table->string('ngo_postal_address_district')->nullable();
            $table->string('ngo_postal_address_pin', 6)->nullable();
            $table->string('is_active')->default('active');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by')->nullable();
            $table->string('ngo_file_beneficiary')->nullable();
            $table->integer('bank_account_type_1')->nullable();
            $table->string('bank_account_holder_name_1')->nullable();
            $table->string('bank_account_holder_name_2')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->unsignedBigInteger('ifsc_code')->nullable();
            $table->string('bank_account_file')->nullable();
            $table->string('ngo_additional_docs_file')->nullable();
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('application_stage_id');
            $table->foreignId('user_table_id');
            $table->bigInteger('no_of_form_completed');
            $table->string('dsso_assigned')->nullable();
            $table->tinyInteger('dsso_remarks_type')->nullable();
            $table->foreignId('dsso_created_by')->nullable();
            $table->date('dsso_created_date')->nullable();
            $table->time('dsso_created_time')->nullable();
            $table->date('dsso_updated_date')->nullable();
            $table->time('dsso_updated_time')->nullable();
            $table->longText('dsso_remark')->nullable();
            $table->string('dsso_inspection_report_file')->nullable();
            $table->string('collector_assigned')->nullable();
            $table->tinyInteger('collector_remarks_type')->nullable();
            $table->foreignId('collector_created_by')->nullable();
            $table->date('collector_created_date')->nullable();
            $table->time('collector_created_time')->nullable();
            $table->date('collector_updated_date')->nullable();
            $table->time('collector_updated_time')->nullable();
            $table->longText('collector_remark')->nullable();
            $table->string('collector_inspection_report_file')->nullable();
            $table->string('ho_assigned')->nullable();
            $table->tinyInteger('ho_remarks_type')->nullable();
            $table->foreignId('ho_created_by')->nullable();
            $table->date('ho_created_date')->nullable();
            $table->time('ho_created_time')->nullable();
            $table->date('ho_updated_date')->nullable();
            $table->time('ho_updated_time')->nullable();
            $table->longText('ho_remark')->nullable();
            $table->string('ho_inspection_report_file')->nullable();
            $table->string('bo_assigned')->nullable();
            $table->tinyInteger('bo_remarks_type')->nullable();
            $table->foreignId('bo_created_by')->nullable();
            $table->date('bo_created_date')->nullable();
            $table->time('bo_created_time')->nullable();
            $table->date('bo_updated_date')->nullable();
            $table->time('bo_updated_time')->nullable();
            $table->longText('bo_remark')->nullable();
            $table->string('bo_inspection_report_file')->nullable();
            $table->string('director_assigned')->nullable();
            $table->tinyInteger('director_remarks_type')->nullable();
            $table->foreignId('director_created_by')->nullable();
            $table->date('director_created_date')->nullable();
            $table->time('director_created_time')->nullable();
            $table->date('director_updated_date')->nullable();
            $table->time('director_updated_time')->nullable();
            $table->longText('director_remark')->nullable();
            $table->string('director_inspection_report_file')->nullable();
            $table->date('ngo_approved_from_date')->nullable();
            $table->date('ngo_approved_validity_date')->nullable();
            $table->string('admin_assigned')->nullable();
            $table->tinyInteger('admin_remarks_type')->nullable();
            $table->foreignId('admin_created_by')->nullable();
            $table->date('admin_created_date')->nullable();
            $table->time('admin_created_time')->nullable();
            $table->date('admin_updated_date')->nullable();
            $table->time('admin_updated_time')->nullable();
            $table->longText('admin_remark')->nullable();
            $table->string('admin_inspection_report_file')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngo_registrations');
    }
};
