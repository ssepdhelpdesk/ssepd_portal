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
        Schema::create('habitations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pid');
            $table->bigInteger('habitation_code')->nullable();
            $table->string('habitation_name',250)->nullable();
            $table->bigInteger('village_code')->nullable();
            $table->bigInteger('gram_panchayat_code')->nullable();
            $table->bigInteger('block_code')->nullable();
            $table->bigInteger('district_code')->nullable();
            $table->bigInteger('state_code')->nullable();
            $table->boolean('is_active')->default(1)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitations');
    }
};
