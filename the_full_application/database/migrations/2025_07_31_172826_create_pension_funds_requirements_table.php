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
            $table->integer('mbpy_oap_below_80_years')->nullable();
            $table->integer('mbpy_oap_above_80_years')->nullable();
            $table->integer('mbpy_wp')->nullable();
            $table->integer('mbpy_dp')->nullable();
            $table->integer('mbpy_sdp_below_80_percent')->nullable();
            $table->integer('mbpy_sdp_above_80_percent')->nullable();
            $table->integer('mbpy_sdoap')->nullable();
            $table->integer('mbpy_clp')->nullable();
            $table->integer('mbpy_wp_aids')->nullable();
            $table->integer('mbpy_dp_aids')->nullable();
            $table->integer('mbpy_unmarried_women')->nullable();
            $table->integer('mbpy_orphan_due_to_covide')->nullable();
            $table->integer('mbpy_widow_due_to_covid')->nullable();
            $table->integer('mbpy_divorce_or_destitute')->nullable();
            $table->integer('mbpy_transgender')->nullable();
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
