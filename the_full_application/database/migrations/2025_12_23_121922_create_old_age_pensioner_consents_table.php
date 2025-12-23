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
        Schema::create('old_age_pensioner_consents', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_name')->index();
            $table->string('updated_scheme_name')->index();
            $table->string('name_of_the_beneficiary')->index();
            $table->string('father_or_husband_name')->nullable();
            $table->string('date_of_birth')->index();
            $table->string('age')->index();
            $table->string('gender')->index();
            $table->string('udid_no')->nullable()->index();
            $table->string('disability_category')->nullable()->index();
            $table->string('disability_percentage')->nullable()->index();
            $table->bigInteger('pension_amount')->nullable();
            $table->integer('address_type')->index();
            $table->bigInteger('state_id')->nullable()->index();
            $table->string('district')->index();
            $table->bigInteger('district_id')->index();
            $table->string('block_or_ulb')->nullable();
            $table->string('block_id')->nullable()->index();
            $table->string('municipality_id')->nullable()->index();
            $table->string('block_or_ulb_id')->nullable()->index();
            $table->string('gp_or_ward')->nullable();
            $table->string('gp_id')->nullable()->index();
            $table->string('ward_id')->nullable()->index();
            $table->string('gp_or_ward_id')->nullable()->index();
            $table->string('village')->nullable();
            $table->string('village_id')->nullable()->index();
            $table->string('pin', 6)->nullable()->index();
            $table->string('postal_address_at')->nullable();
            $table->string('postal_address_post')->nullable();
            $table->string('postal_address_via')->nullable();
            $table->string('postal_address_ps')->nullable();
            $table->string('postal_address_district')->nullable();
            $table->string('postal_address_pin', 6)->nullable()->index();
            $table->string('is_active')->default('active')->index();
            $table->bigInteger('aadhaar_no')->nullable()->unique();
            $table->string('nsap_sanction_order_no')->nullable()->index();
            $table->string('sub_collector_sanction_order_no')->nullable();
            $table->string('pension_month')->nullable()->index();
            $table->date('discontinued_date')->nullable()->index();
            $table->date('discontinued_system_gen_date')->nullable();
            $table->time('discontinued_system_gen_time')->nullable();
            $table->string('discontinued_reason')->nullable()->index();
            $table->string('discontinued_by')->nullable()->index();
            $table->integer('db_status')->default(1)->index();
            $table->integer('scheme_migration_id')->nullable()->index();
            $table->string('scheme_migration_remarks')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->index();
            $table->string('created_by')->nullable();
            $table->foreignId('created_by_user_table_id')->index();
            $table->date('created_date')->index();
            $table->time('created_time');
            $table->foreignId('updated_by')->nullable()->index();
            $table->date('updated_date')->nullable()->index();
            $table->time('updated_time')->nullable();
            $table->timestamps();

            $table->index(['scheme_name', 'status'], 'idx_scheme_status');
            $table->index(['district_id', 'block_or_ulb_id'], 'idx_district_block');
            $table->index(['block_or_ulb_id', 'gp_or_ward_id'], 'idx_block_gp');
            $table->index(['gp_or_ward_id', 'village_id'], 'idx_gp_village');
            $table->index(['scheme_migration_id', 'db_status'], 'idx_migration_status');
            $table->index(['is_active', 'status'], 'idx_active_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_age_pensioner_consents');
    }
};
