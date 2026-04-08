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
        Schema::create('daily_pension_disbursements', function (Blueprint $table) {
            $table->id();
            $table->string('for_the_month');
            $table->date('disbursement_start_date')->nullable();
            $table->date('disbursement_end_date')->nullable();
            $table->bigInteger('mbpy_oap_below_80_years')->nullable();
            $table->bigInteger('funds_mbpy_oap_below_80_years')->default(0);
            $table->bigInteger('mbpy_oap_above_80_years')->nullable();
            $table->bigInteger('funds_mbpy_oap_above_80_years')->default(0);
            $table->bigInteger('mbpy_wp')->nullable();
            $table->bigInteger('funds_mbpy_wp')->default(0);
            $table->bigInteger('mbpy_dp')->nullable();
            $table->bigInteger('funds_mbpy_dp')->default(0);
            $table->bigInteger('mbpy_sdp_below_80_percent')->nullable();
            $table->bigInteger('funds_mbpy_sdp_below_80_percent')->default(0);
            $table->bigInteger('mbpy_sdp_above_80_percent')->nullable();
            $table->bigInteger('funds_mbpy_sdp_above_80_percent')->default(0);
            $table->bigInteger('mbpy_sdoap')->nullable();
            $table->bigInteger('funds_mbpy_sdoap')->default(0);
            $table->bigInteger('mbpy_clp')->nullable();
            $table->bigInteger('funds_mbpy_clp')->default(0);
            $table->bigInteger('mbpy_wp_aids')->nullable();
            $table->bigInteger('funds_mbpy_wp_aids')->default(0);
            $table->bigInteger('mbpy_dp_aids')->nullable();
            $table->bigInteger('funds_mbpy_dp_aids')->default(0);
            $table->bigInteger('mbpy_unmarried_women')->nullable();
            $table->bigInteger('funds_mbpy_unmarried_women')->default(0);
            $table->bigInteger('mbpy_orphan_due_to_covide')->nullable();
            $table->bigInteger('funds_mbpy_orphan_due_to_covide')->default(0);
            $table->bigInteger('mbpy_widow_due_to_covid')->nullable();
            $table->bigInteger('funds_mbpy_widow_due_to_covid')->default(0);
            $table->bigInteger('mbpy_divorce_or_destitute')->nullable();
            $table->bigInteger('funds_mbpy_divorce_or_destitute')->default(0);
            $table->bigInteger('mbpy_transgender')->nullable();
            $table->bigInteger('death_reported')->nullable();
            $table->bigInteger('funds_mbpy_transgender')->default(0);
            $table->bigInteger('mbpy_total_beneficiaries')->default(0);
            $table->bigInteger('funds_mbpy_total_beneficiaries')->default(0);
            $table->bigInteger('no_of_normal_pensioners')->nullable();
            $table->bigInteger('no_of_ep_pensioners')->nullable();            
            $table->tinyInteger('staff_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->string('disbursement_started')->default(0);
            $table->string('is_active')->default('active');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by');
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->tinyInteger('approve_status')->default(0);
            $table->bigInteger('approved_by')->nullable();
            $table->dateTime('approved_date_time')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_pension_disbursements');
    }
};
