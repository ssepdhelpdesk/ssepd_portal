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
        Schema::create('monthly_pension_disbursemenets', function (Blueprint $table) {
            $table->id();
            $table->string('for_the_month');
            $table->date('disbursement_start_date')->nullable();
            $table->date('disbursement_end_date')->nullable();
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
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_pension_disbursemenets');
    }
};
