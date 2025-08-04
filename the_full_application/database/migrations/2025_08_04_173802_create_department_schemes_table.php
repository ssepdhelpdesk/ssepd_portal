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
        Schema::create('department_schemes', function (Blueprint $table) {
            $table->id();
            $table->integer('scheme_id');
            $table->string('scheme_name');
            $table->string('sub_scheme_id')->nullable();
            $table->string('sub_scheme_name')->nullable();
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
        Schema::dropIfExists('department_schemes');
    }
};
