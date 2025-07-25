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
        Schema::create('ngo_part_six_financial_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ngo_org_id');
            $table->foreignId('ngo_tbl_id')->constrained('ngo_registrations');
            $table->string('ngo_system_gen_reg_no', 255);
            $table->string('financial_status_financial_year_1')->nullable();
            $table->string('financial_status_receipt_price_1', 255)->nullable();
            $table->string('financial_status_payment_1', 255)->nullable();
            $table->string('financial_status_surplus_1', 255)->nullable();
            $table->string('financial_status_audit_file_1')->nullable();
            $table->string('financial_status_it_return_file_1')->nullable();
            $table->string('financial_status_financial_year_2')->nullable();
            $table->string('financial_status_receipt_price_2', 255)->nullable();
            $table->string('financial_status_payment_2', 255)->nullable();
            $table->string('financial_status_surplus_2', 255)->nullable();
            $table->string('financial_status_audit_file_2')->nullable();
            $table->string('financial_status_it_return_file_2')->nullable();
            $table->string('financial_status_financial_year_3')->nullable();
            $table->string('financial_status_receipt_price_3', 255)->nullable();
            $table->string('financial_status_payment_3', 255)->nullable();
            $table->string('financial_status_surplus_3', 255)->nullable();
            $table->string('financial_status_audit_file_3')->nullable();
            $table->string('financial_status_it_return_file_3')->nullable();
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
        Schema::dropIfExists('ngo_part_six_financial_statuses');
    }
};
