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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('municipality_id');
            $table->string('municipality_name');
            $table->bigInteger('district_id');
            $table->bigInteger('state_id')->default(228);
            $table->bigInteger('is_mining')->nullable();
            $table->bigInteger('subdivision_id');
            $table->bigInteger('nic_municipality_code');
            $table->bigInteger('ulb_type');
            $table->string('is_iap', 1)->default('1');
            $table->string('is_active')->default('active');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
