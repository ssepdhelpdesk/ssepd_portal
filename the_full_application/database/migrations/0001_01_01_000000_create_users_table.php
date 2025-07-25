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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_table_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('user_id')->unique();
            $table->string('mobile_no')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->timestamp('email_verified_at')->nullable()->useCurrent();;
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes')->nullable();
            $table->string('password');
            $table->string('address', 500)->nullable();
            $table->timestamp('entry_dt')->nullable()->useCurrent();
            $table->tinyInteger('is_active')->default(1)->nullable();
            $table->binary('created_by', 50)->nullable();
            $table->string('posted_state')->nullable();
            $table->string('posted_district')->nullable();
            $table->string('posted_subdiv')->nullable();
            $table->string('posted_block')->nullable();
            $table->string('posted_municipality')->nullable();
            $table->string('posted_gp')->nullable();
            $table->string('posted_village')->nullable();
            $table->string('posted_address')->nullable();
            $table->string('posted_tahasil')->nullable();
            $table->date('ngo_approve_date')->nullable();
            $table->string('posted_disability_department_section')->nullable();
            $table->string('otp_for_forgot_password', 50)->nullable();
            $table->tinyInteger('account_non_locked')->default(0)->nullable();
            $table->integer('failed_login_cnt')->default(0);
            $table->string('additnl_para', 50)->nullable();
            $table->tinyInteger('allow_multiple_sessions')->default(0)->nullable();
            $table->tinyInteger('is_logged_in')->default(0)->nullable();
            $table->timestamp('last_req_time')->nullable()->useCurrent();;
            $table->tinyInteger('enabled')->default(1)->nullable();
            $table->tinyInteger('is_doctor')->default(0)->nullable();
            $table->string('otp_for_issuing_disability_id_card', 20)->nullable();
            $table->tinyInteger('is_survey_consultant')->default(0)->nullable();
            $table->string('competent_authority_designation', 100)->nullable();
            $table->string('mci_crr_regd_no', 100)->nullable();
            $table->string('doctor_qualification', 100)->nullable();
            $table->string('medical_institute_id')->nullable();
            $table->timestamp('otp_for_forgot_password_duration')->nullable()->useCurrent();;
            $table->timestamp('otp_for_issuing_disability_id_card_duration')->nullable()->useCurrent();;
            $table->string('posted_zone')->nullable();
            $table->string('role_id')->nullable();
            $table->string('role_name')->nullable();
            $table->string('department_section_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
