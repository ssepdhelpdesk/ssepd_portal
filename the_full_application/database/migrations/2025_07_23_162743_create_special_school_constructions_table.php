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
        Schema::create('special_school_constructions', function (Blueprint $table) {
            $table->id();
            $table->integer('management_id')->index('idx_management_id');
            $table->string('special_school_management_name', 255);
            $table->integer('special_school_id')->index('idx_special_school');
            $table->string('special_school_name', 255);
            $table->string('school_system_gen_reg_no', 255);
            $table->tinyInteger('new_or_existing')->default(0);
            $table->string('file_construction_image_1');
            $table->decimal('latitude_1', 10, 7);
            $table->decimal('longitude_1', 10, 7);
            $table->string('file_construction_image_2');
            $table->decimal('latitude_2', 10, 7);
            $table->decimal('longitude_2', 10, 7);
            $table->string('file_construction_image_3');
            $table->decimal('latitude_3', 10, 7);
            $table->decimal('longitude_3', 10, 7);
            $table->string('file_construction_image_4');
            $table->decimal('latitude_4', 10, 7);
            $table->decimal('longitude_4', 10, 7);
            $table->string('file_construction_image_5');
            $table->decimal('latitude_5', 10, 7);
            $table->decimal('longitude_5', 10, 7);
            $table->longText('any_remarks', 255)->nullable();
            $table->tinyInteger('phase_no')->default(0);
            $table->decimal('system_stored_latitude', 10, 7);
            $table->decimal('system_stored_longitude', 10, 7);
            $table->tinyInteger('school_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('ward_id')->nullable();
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
            $table->tinyInteger('approve_status')->default(0);
            $table->string('approver_remarks')->nullable();
            $table->date('approved_date')->nullable();
            $table->bigInteger('no_of_phase_approved');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by');
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->bigInteger('no_of_image_uploaded');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('which_govt')->default(1)->comment('1 = State, 2 = National');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_school_constructions');
    }
};
