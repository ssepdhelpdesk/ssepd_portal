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
        Schema::create('bank_masters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bank_id');
            $table->string('bank_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_ifsc')->nullable();
            $table->string('address')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->string('nsap_branch_code')->nullable();
            $table->bigInteger('bank_type')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_masters');
    }
};
