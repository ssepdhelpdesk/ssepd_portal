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
        Schema::create('pension_funds_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('for_the_month')->nullable();
            $table->integer('mbpy_oap_below_80_years')->nullable();
            $table->bigInteger('funds_mbpy_oap_below_80_years')->default(0);
            $table->integer('mbpy_oap_above_80_years')->nullable();
            $table->bigInteger('funds_mbpy_oap_above_80_years')->default(0);
            $table->integer('mbpy_wp')->nullable();
            $table->bigInteger('funds_mbpy_wp')->default(0);
            $table->integer('mbpy_dp')->nullable();
            $table->bigInteger('funds_mbpy_dp')->default(0);
            $table->integer('mbpy_sdp_below_80_percent')->nullable();
            $table->bigInteger('funds_mbpy_sdp_below_80_percent')->default(0);
            $table->integer('mbpy_sdp_above_80_percent')->nullable();
            $table->bigInteger('funds_mbpy_sdp_above_80_percent')->default(0);
            $table->integer('mbpy_sdoap')->nullable();
            $table->bigInteger('funds_mbpy_sdoap')->default(0);
            $table->integer('mbpy_clp')->nullable();
            $table->bigInteger('funds_mbpy_clp')->default(0);
            $table->integer('mbpy_wp_aids')->nullable();
            $table->bigInteger('funds_mbpy_wp_aids')->default(0);
            $table->integer('mbpy_dp_aids')->nullable();
            $table->bigInteger('funds_mbpy_dp_aids')->default(0);
            $table->integer('mbpy_unmarried_women')->nullable();
            $table->bigInteger('funds_mbpy_unmarried_women')->default(0);
            $table->integer('mbpy_orphan_due_to_covide')->nullable();
            $table->bigInteger('funds_mbpy_orphan_due_to_covide')->default(0);
            $table->integer('mbpy_widow_due_to_covid')->nullable();
            $table->bigInteger('funds_mbpy_widow_due_to_covid')->default(0);
            $table->integer('mbpy_divorce_or_destitute')->nullable();
            $table->bigInteger('funds_mbpy_divorce_or_destitute')->default(0);
            $table->integer('mbpy_transgender')->nullable();
            $table->bigInteger('funds_mbpy_transgender')->default(0);
            $table->bigInteger('death_reported')->default(0)->nullable();
            $table->bigInteger('mbpy_total_beneficiaries')->default(0);
            $table->bigInteger('funds_mbpy_total_beneficiaries')->default(0);
            $table->bigInteger('no_of_normal_pensioners')->nullable();
            $table->bigInteger('no_of_ep_pensioners')->nullable();
            $table->string('mbpy_bank_account_number')->nullable();
            $table->string('mbpy_bank_ifsc_code')->nullable();
            $table->tinyInteger('address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->string('municipality_id')->nullable();
            $table->string('block_id')->nullable();
            $table->string('gp_id')->nullable();
            $table->string('village_id')->nullable();
            $table->string('pin', 6)->nullable();
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
        Schema::dropIfExists('pension_funds_requirements');
    }
};
