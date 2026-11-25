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
        // 1. Participant Registrations Table
        Schema::create('disability_day_participant_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no', 64)->unique();
            $table->string('participant_name', 255);
            $table->unsignedBigInteger('institute_id')->nullable()->index();
            $table->string('school_name', 255)->index();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('tshirt_size', 10);
            
            // Address Details (Village vs Municipality)
            $table->tinyInteger('address_type')->comment('1: Village (Block), 2: Municipality (ULB)');
            $table->bigInteger('state_id')->nullable()->index();
            $table->string('state_name', 100)->nullable();
            $table->bigInteger('district_id')->nullable()->index();
            $table->string('district_name', 100)->nullable();
            $table->bigInteger('block_id')->nullable()->index();
            $table->string('block_name', 100)->nullable();
            $table->bigInteger('gp_id')->nullable()->index();
            $table->string('gp_name', 100)->nullable();
            $table->bigInteger('village_id')->nullable()->index();
            $table->string('village_name', 100)->nullable();
            $table->bigInteger('municipality_id')->nullable()->index();
            $table->string('municipality_name', 100)->nullable();
            $table->string('pin', 10);
            
            // Postal Address Details
            $table->string('at', 255)->nullable();
            $table->string('post', 255)->nullable();
            $table->string('ps', 255)->nullable();
            $table->string('postal_pin', 10)->nullable();
            
            // Escort & Participation Details
            $table->unsignedInteger('number_of_escorts')->default(0);
            $table->string('participation_in', 50)->index();
            $table->string('participation_other', 255)->nullable();
            $table->json('attending_dates')->nullable();

            // Transportation & Venue Arrival Details
            $table->string('transport_mode', 100)->nullable()->index('dd_part_transport_mode_idx');
            $table->string('transport_mode_other', 255)->nullable();
            $table->date('expected_arrival_date')->nullable()->index('dd_part_arrival_date_idx');
            $table->time('expected_arrival_time')->nullable();
            
            // System metadata & Status
            $table->timestamp('applied_at')->nullable()->index();
            $table->tinyInteger('status')->default(1)->comment('1: Active / Registered');
            $table->string('financial_year', 20)->nullable()->index();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Escorts Relational Table
        Schema::create('disability_day_escorts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participant_registration_id')->index();
            $table->unsignedInteger('escort_index')->default(1);
            $table->string('name', 255);
            $table->string('mobile', 15);
            $table->string('email', 150)->nullable();
            $table->string('relationship', 100);
            $table->timestamps();

            $table->foreign('participant_registration_id')
                ->references('id')
                ->on('disability_day_participant_registrations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disability_day_escorts');
        Schema::dropIfExists('disability_day_participant_registrations');
    }
};
