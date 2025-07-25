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
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('village_id');
            $table->string('village_name');
            $table->bigInteger('gp_id');
            $table->bigInteger('block_id');
            $table->bigInteger('district_id');
            $table->bigInteger('state_id')->default(228);
            $table->bigInteger('subdivision_id')->nullable();
            $table->bigInteger('nic_village_id')->nullable();
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
        Schema::dropIfExists('villages');
    }
};
