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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('block_id');
            $table->string('block_name');
            $table->bigInteger('district_id');
            $table->bigInteger('state_id')->default(228);
            $table->bigInteger('is_mining')->nullable();
            $table->bigInteger('subdivision_id')->nullable();
            $table->bigInteger('nsap_block_code')->nullable();
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
        Schema::dropIfExists('blocks');
    }
};
