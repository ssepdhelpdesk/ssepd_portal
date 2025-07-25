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
        Schema::create('ngo_part_two_office_bearers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ngo_org_id');
            $table->foreignId('ngo_tbl_id')->constrained('ngo_registrations');
            $table->string('ngo_system_gen_reg_no', 255);
            $table->string('office_bearer_name', 255);
            $table->enum('office_bearer_gender', ['1', '2', '3']);
            $table->string('office_bearer_email', 255);
            $table->string('office_bearer_phone', 15);
            $table->string('office_bearer_designation', 255);
            $table->string('office_bearer_key_designation', 255);
            $table->date('office_bearer_date_of_association');
            $table->string('office_bearer_pan', 10);
            $table->string('office_bearer_pan_file', 255);
            $table->string('office_bearer_name_as_aadhar', 255);
            $table->date('office_bearer_dob');
            $table->string('office_bearer_aadhar');
            $table->string('office_bearer_aadhar_file');
            $table->string('want_to_add_another_bearer');
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
        Schema::dropIfExists('ngo_part_two_office_bearers');
    }
};
