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
        Schema::create('application_stage_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('department_scheme_id');
            $table->string('model_name')->nullable();
            $table->bigInteger('model_table_id')->nullable();
            $table->bigInteger('initial_model_table_id')->nullable();
            $table->bigInteger('stage_id');            
            $table->string('stage_name');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by')->nullable();
            $table->longText('created_by_remarks')->nullable();
            $table->string('created_by_inspection_report_file')->nullable();
            $table->string('created_by_ip_v_four')->nullable();
            $table->string('created_by_ip_v_six')->nullable();
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->string('updated_by_remarks')->nullable();
            $table->string('updated_by_ip_v_four')->nullable();
            $table->string('updated_by_ip_v_six')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_stage_histories');
    }
};
