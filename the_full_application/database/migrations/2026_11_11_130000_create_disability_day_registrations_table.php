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
        if (!Schema::hasTable('disability_day_registrations')) {
            Schema::create('disability_day_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('registration_no', 64)->unique();
                $table->string('organization_name', 255);
                $table->string('contact_person', 255);
                $table->string('email', 150)->index();
                $table->string('phone', 20)->index();
                $table->text('purpose');
                $table->string('at', 255);
                $table->string('po', 255);
                $table->string('ps', 255);
                $table->unsignedBigInteger('district_id')->nullable()->index();
                $table->string('district_name', 100)->nullable();
                $table->unsignedBigInteger('state_id')->nullable()->index();
                $table->string('state_name', 100)->nullable();
                $table->string('pin', 10);
                $table->timestamp('applied_at')->nullable()->index();
                $table->string('allotted_stall', 100)->nullable();
                $table->tinyInteger('status')->default(1)->comment('0 = Inactive, 1 = Active');
                $table->string('financial_year', 20)->nullable()->index();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['email', 'financial_year'], 'disability_day_reg_email_fy_unique');
            });
        } else {
            Schema::table('disability_day_registrations', function (Blueprint $table) {
                if (!Schema::hasColumn('disability_day_registrations', 'status')) {
                    $table->tinyInteger('status')->default(1)->after('allotted_stall')->comment('0 = Inactive, 1 = Active');
                }
                if (!Schema::hasColumn('disability_day_registrations', 'financial_year')) {
                    $table->string('financial_year', 20)->nullable()->after('status')->index();
                }
            });

            try {
                Schema::table('disability_day_registrations', function (Blueprint $table) {
                    $table->unique(['email', 'financial_year'], 'disability_day_reg_email_fy_unique');
                });
            } catch (\Throwable $e) {
                // Unique key may already exist
            }
        }

        if (!Schema::hasTable('disability_day_stall_allotments')) {
            Schema::create('disability_day_stall_allotments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('registration_id')->index();
                $table->integer('stall_number')->index();
                $table->string('stall_name', 100)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('registration_id')
                      ->references('id')
                      ->on('disability_day_registrations')
                      ->onDelete('cascade');

                $table->unique(['stall_number'], 'unique_disability_stall_no');
                $table->unique(['registration_id', 'stall_number'], 'unique_disability_reg_stall');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disability_day_stall_allotments');
        Schema::dropIfExists('disability_day_registrations');
    }
};

