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
        Schema::create('ngo_part_three_act_registrations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ngo_org_id');
            $table->foreignId('ngo_tbl_id')->constrained('ngo_registrations');
            $table->string('ngo_system_gen_reg_no', 255);
            $table->string('authority_one')->nullable();
            $table->string('regd_no_one')->nullable();
            $table->date('regd_date_one')->nullable();
            $table->date('validity_date_one')->nullable();
            $table->string('regd_certificate_file_one')->nullable();
            $table->string('regd_certificate_file_one_path')->nullable();
            $table->string('authority_two')->nullable();
            $table->string('regd_no_two')->nullable();
            $table->date('regd_date_two')->nullable();
            $table->date('validity_date_two')->nullable();
            $table->string('regd_certificate_file_two')->nullable();
            $table->string('regd_certificate_file_two_path')->nullable();
            $table->string('authority_three')->nullable();
            $table->string('regd_no_three')->nullable();
            $table->date('regd_date_three')->nullable();
            $table->date('validity_date_three')->nullable();
            $table->string('regd_certificate_file_three')->nullable();
            $table->string('regd_certificate_file_three_path')->nullable();
            $table->string('authority_four')->nullable();
            $table->string('regd_no_four')->nullable();
            $table->date('regd_date_four')->nullable();
            $table->date('validity_date_four')->nullable();
            $table->string('regd_certificate_file_four')->nullable();
            $table->string('regd_certificate_file_four_path')->nullable();
            $table->string('authority_five')->nullable();
            $table->string('regd_no_five')->nullable();
            $table->date('regd_date_five')->nullable();
            $table->date('validity_date_five')->nullable();
            $table->string('regd_certificate_file_five')->nullable();
            $table->string('regd_certificate_file_five_path')->nullable();
            $table->string('authority_six')->nullable();
            $table->string('regd_no_six')->nullable();
            $table->date('regd_date_six')->nullable();
            $table->date('validity_date_six')->nullable();
            $table->string('regd_certificate_file_six')->nullable();
            $table->string('regd_certificate_file_six_path')->nullable();
            $table->string('authority_seven')->nullable();
            $table->string('regd_no_seven')->nullable();
            $table->date('regd_date_seven')->nullable();
            $table->date('validity_date_seven')->nullable();
            $table->string('regd_certificate_file_seven')->nullable();
            $table->string('regd_certificate_file_seven_path')->nullable();
            $table->string('authority_eight')->nullable();
            $table->string('regd_no_eight')->nullable();
            $table->date('regd_date_eight')->nullable();
            $table->date('validity_date_eight')->nullable();
            $table->string('regd_certificate_file_eight')->nullable();
            $table->string('regd_certificate_file_eight_path')->nullable();
            $table->string('authority_nine')->nullable();
            $table->string('regd_no_nine')->nullable();
            $table->date('regd_date_nine')->nullable();
            $table->date('validity_date_nine')->nullable();
            $table->string('regd_certificate_file_nine')->nullable();
            $table->string('regd_certificate_file_nine_path')->nullable();
            $table->string('authority_other_act')->nullable();
            $table->string('authority_other')->nullable();
            $table->string('regd_no_other')->nullable();
            $table->date('regd_date_other')->nullable();
            $table->date('validity_date_other')->nullable();
            $table->string('regd_certificate_file_other')->nullable();
            $table->string('regd_certificate_file_other_path')->nullable();
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
        Schema::dropIfExists('ngo_part_three_act_registrations');
    }
};
