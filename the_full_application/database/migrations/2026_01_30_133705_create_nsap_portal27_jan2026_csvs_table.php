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
        Schema::create('nsap_portal27_jan2026_csvs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('state')->nullable();
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

            $table->string('applicant_name')->nullable();
            $table->longText('address')->nullable();
            $table->string('scheme')->nullable();
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('pincode')->nullable();

            $table->string('father_husband_name')->nullable();
            $table->string('caste')->nullable();
            $table->string('bpl_id')->nullable();
            $table->string('bpl_member_id')->nullable();

            $table->string('sanction_order_no')->nullable();
            $table->string('ration_card_no')->nullable();
            $table->string('epic_no')->nullable();
            $table->string('mobile_no')->nullable();

            $table->string('sanction_date')->nullable();
            $table->string('disbursement_upto')->nullable();

            $table->string('bank_po_account')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('disbursement_mode')->nullable();

            $table->string('beneficiary_no')->nullable();
            $table->string('name_as_per_aadhaar')->nullable();
            $table->string('aadhar_verified')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->bigInteger('aadhaar_no_by_user')->nullable()->unique();
            $table->string('aadhaar_hash', 64)->nullable()->unique()->index();
            $table->text('aadhaar_encrypted')->nullable();

            $table->string('is_active')->default('active')->index();
            $table->integer('db_status')->default(1)->index();
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->index();
            $table->string('created_by')->default(1)->index();
            $table->foreignId('created_by_user_table_id')->default(1)->index();
            $table->date('created_date')->nullable()->useCurrent();
            $table->time('created_time')->nullable()->useCurrent();
            $table->foreignId('updated_by')->nullable()->index();
            $table->date('updated_date')->nullable()->index();
            $table->time('updated_time')->nullable();

            $table->index('state');
            $table->index('district');
            $table->index('scheme');
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
        Schema::dropIfExists('nsap_portal27_jan2026_csvs');
    }
};
