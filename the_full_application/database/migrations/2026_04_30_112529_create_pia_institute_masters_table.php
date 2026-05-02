<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pia_institute_masters', function (Blueprint $table) {

            $table->id();

            $table->string('excel_institute_name',250)->nullable();
            $table->bigInteger('excel_institute_id')->index();
            $table->bigInteger('excel_institute_master_id')->nullable()->index();
            $table->string('excel_institute_type')->nullable();
            $table->bigInteger('excel_institute_type_id')->nullable()->index();
            $table->string('excel_institute_user_id')->nullable();
            $table->string('excel_institute_system_email_id')->nullable();
            $table->string('excel_institute_email_id')->nullable();
            $table->string('excel_pia_name')->nullable();
            $table->bigInteger('excel_pia_id')->nullable()->index();
            $table->string('excel_district_name')->nullable();
            $table->string('excel_district_id')->nullable();
            $table->string('excel_approved_strength_of_inmates')->nullable();
            $table->longText('excel_institute_address')->nullable();
            $table->string('excel_nodal_officer_name')->nullable();
            $table->string('excel_nodal_officer_contact_number')->nullable();

            $table->bigInteger('user_table_id')->nullable()->index();

            $table->tinyInteger('which_govt')->default(1)->comment('1=GOO, 2=GOI');
            $table->tinyInteger('basic_details_completed')->default(0)->comment('0=NO, 1=YES');

            $table->string('pia_name')->nullable();
            $table->bigInteger('pia_id')->nullable()->index();
            $table->string('pia_nodal_officer_name')->nullable();
            $table->string('pia_nodal_officer_contact_no')->nullable();
            $table->string('pia_system_gen_reg_no')->nullable()->index();
            $table->string('institute_system_gen_reg_no')->nullable()->index();

            $table->string('institute_name',250)->nullable()->index();
            $table->bigInteger('institute_id')->index();
            $table->bigInteger('institute_master_id')->nullable()->index();
            $table->bigInteger('institute_type')->nullable()->index();
            $table->bigInteger('institute_type_id')->nullable()->index();
            $table->string('institute_user_id')->nullable();
            $table->string('institute_system_email_id')->nullable();
            $table->string('institute_email_id')->nullable();

            $table->string('district_name')->nullable();
            $table->string('approved_strength_of_inmates')->nullable();
            $table->longText('institute_address')->nullable();
            $table->string('nodal_officer_name')->nullable();
            $table->string('nodal_officer_contact_number')->nullable();
            $table->bigInteger('nodal_officer_designation')->nullable();
            $table->date('date_of_registration')->nullable()->index();
            $table->string('registration_no')->nullable()->index();
            $table->string('registration_certificate')->nullable();
            $table->string('grantee_code')->nullable();

            $table->integer('address_type')->index();
            $table->bigInteger('state_id')->nullable()->index();
            $table->bigInteger('district_id')->index();
            $table->string('block_id')->nullable()->index();
            $table->string('municipality_id')->nullable()->index();
            $table->string('gp_id')->nullable()->index();
            $table->string('village_id')->nullable()->index();
            $table->string('ward_id')->nullable()->index();

            $table->string('pin', 6)->nullable()->index();
            $table->string('pia_postal_address_at')->nullable();
            $table->string('pia_postal_address_post')->nullable();
            $table->string('pia_postal_address_via')->nullable();
            $table->string('pia_postal_address_ps')->nullable();
            $table->string('pia_postal_address_district')->nullable();
            $table->string('pia_postal_address_pin', 6)->nullable()->index();

            $table->enum('status', ['Active', 'Inactive'])->default('Active')->index();
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

            /*
            |--------------------------------------------------------------------------
            | Composite Indexes (Performance Critical)
            |--------------------------------------------------------------------------
            */

            $table->index(['state_id', 'district_id']);
            $table->index(['district_id', 'block_id']);
            $table->index(['block_id', 'gp_id']);
            $table->index(['gp_id', 'village_id']);

            $table->index(['status', 'is_active', 'db_status']);

            $table->index(['pia_id', 'institute_id']);
            $table->index(['user_table_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pia_institute_masters');
    }
};