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
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disability_day_registrations');
    }
};

