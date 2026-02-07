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
        Schema::create('pension_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->integer('scheme_id')->index();
            $table->string('scheme_name')->index();
            $table->string('scheme_type')->index();
            $table->string('office_section')->nullable()->index();
            $table->string('office_sub_section')->nullable()->index();
            $table->tinyInteger('which_govt')->default(1)->comment('1=GOO, 2=GOI');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->index();
            $table->string('is_active')->default('active')->index();
            $table->integer('db_status')->default(1)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pension_types');
    }
};
