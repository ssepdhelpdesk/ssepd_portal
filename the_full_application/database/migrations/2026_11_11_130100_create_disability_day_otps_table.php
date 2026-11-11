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
        if (!Schema::hasTable('disability_day_otps')) {
            Schema::create('disability_day_otps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('registration_id')->index();
                $table->string('email', 150)->index();
                $table->string('otp_hash', 255);
                $table->timestamp('expires_at')->index();
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamp('verified_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->foreign('registration_id')
                      ->references('id')
                      ->on('disability_day_registrations')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disability_day_otps');
    }
};

