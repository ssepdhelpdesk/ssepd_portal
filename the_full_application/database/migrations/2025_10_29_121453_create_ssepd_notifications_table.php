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
        Schema::create('ssepd_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('message');
            $table->string('type')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('read_status')->default(false)->index();
            $table->timestamp('read_at')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('for_the_month')->nullable();
            $table->string('for_which_page')->nullable();
            $table->foreignId('for_which_user_table_id')->nullable();
            $table->tinyInteger('staff_address_type')->nullable()->comment('1=Block, 2=ULB');
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('village_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->enum('is_active', ['active', 'inactive'])->default('active');
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Deleted/Inactive');
            $table->date('created_date');
            $table->time('created_time');
            $table->foreignId('created_by');
            $table->date('updated_date')->nullable();
            $table->time('updated_time')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('user_table_id')->nullable();

            $table->index(['district_id', 'block_id', 'municipality_id', 'gp_id'], 'location_index');
            $table->index(['for_which_page', 'for_the_month'], 'page_month_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssepd_notifications');
    }
};
