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
        Schema::create('ddrc_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_table_id')->unique('uq_user_table');
            $table->string('ddrc_name', 255);
            $table->string('ddrc_system_gen_reg_no', 255);
            $table->string('file_geo_tagged_image', 255);
            $table->decimal('ddrc_latitude', 10, 7);
            $table->decimal('ddrc_longitude', 10, 7);
            $table->decimal('system_stored_latitude', 10, 7);
            $table->decimal('system_stored_longitude', 10, 7);
            $table->tinyInteger('ddrc_address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->string('pin', 6)->nullable();
            $table->string('ddrc_postal_address_at')->nullable();
            $table->string('ddrc_postal_address_post')->nullable();
            $table->string('ddrc_postal_address_via')->nullable();
            $table->string('ddrc_postal_address_ps')->nullable();
            $table->string('ddrc_postal_address_district')->nullable();
            $table->string('ddrc_postal_address_pin', 6)->nullable();
            $table->string('is_active')->default('active');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by')->nullable();
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ddrc_details');
    }
};
