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
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ipv4_address')->nullable();
            $table->string('ipv6_address')->nullable();
            $table->string('device_type');
            $table->timestamp('login_date_time')->nullable();
            $table->string('login_time')->nullable();
            $table->longText('login_location')->nullable();
            $table->timestamp('logout_date_time')->nullable();
            $table->string('logout_time')->nullable();
            $table->string('session_duration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
