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
        Schema::create('special_schools', function (Blueprint $table) {
            $table->id();
            $table->integer('management_id')->index('idx_management_id');
            $table->string('special_school_management_name', 255);
            $table->integer('special_school_id')->index('idx_special_school');
            $table->string('special_school_name', 255);
            $table->string('school_system_gen_reg_no', 255);
            $table->date('school_establishment_date');
            $table->integer('school_category');
            $table->integer('school_type');
            $table->string('act_reg_no');
            $table->string('file_act_reg');
            $table->string('school_email_id');
            $table->string('school_mobile_no');
            $table->tinyInteger('school_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->string('pin', 6)->nullable();
            $table->string('school_postal_address_at')->nullable();
            $table->string('school_postal_address_post')->nullable();
            $table->string('school_postal_address_via')->nullable();
            $table->string('school_postal_address_ps')->nullable();
            $table->string('school_postal_address_district')->nullable();
            $table->string('school_postal_address_pin', 6)->nullable();
            $table->string('is_active')->default('active');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by')->nullable();
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('application_stage_id');
            $table->foreignId('user_table_id');
            $table->bigInteger('no_of_form_completed');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_schools');
    }
};
