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
        Schema::create('pension_disbursement_authorities', function (Blueprint $table) {
            $table->id();
            $table->string('for_the_month')->nullable();
            $table->string('authority_name');
            $table->string('authority_mobile_no');
            $table->string('authority_email_id')->nullable();
            $table->string('disbursement_month');
            $table->bigInteger('authority_designation');
            $table->tinyInteger('staff_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->string('is_active')->default('active');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by');
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('user_table_id');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pension_disbursement_authorities');
    }
};
