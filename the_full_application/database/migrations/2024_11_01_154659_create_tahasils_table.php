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
        Schema::create('tahasils', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tahasil_id');
            $table->string('tahasil_name', 200)->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('is_active', 50)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahasils');
    }
};
