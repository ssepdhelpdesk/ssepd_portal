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
        Schema::create('field_visit_institutes', function (Blueprint $table) {
            $table->id();
            $table->string('institute_code', 50)->unique('uq_fvi_code');
            $table->string('institute_name', 255)->index('idx_fvi_name');
            
            // Institutional Category mapping from Excel Sheet1 (Col 2 & 3)
            $table->unsignedInteger('category_id')->index('idx_fvi_cat_id');
            $table->string('category_name', 100)->index('idx_fvi_cat_name');
            
            // Managing NGO / Organization details from Excel Sheet1 (Col 4 & 5)
            $table->unsignedBigInteger('management_id')->nullable()->index('idx_fvi_mgmt_id');
            $table->string('management_name', 255)->nullable();
            
            // Excel Source Traceability Identifiers (Col 1 & 6)
            $table->unsignedBigInteger('source_institute_id')->nullable()->index('idx_fvi_src_id');
            $table->unsignedBigInteger('legacy_sub_id')->nullable();
            
            // Location Hierarchy References (Col 8 & 9 and sub-district domain fields)
            $table->tinyInteger('address_type')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->index('idx_fvi_dist_id');
            $table->string('district_name', 100)->nullable();
            $table->bigInteger('block_id')->nullable()->index('idx_fvi_block_id');
            $table->string('block_name', 100)->nullable();
            $table->bigInteger('municipality_id')->nullable()->index('idx_fvi_muni_id');
            $table->string('municipality_name', 100)->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->string('gp_name', 100)->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->string('ward_name', 100)->nullable();
            
            // Physical Address and Contact Information
            $table->text('address')->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('contact_number', 25)->nullable();
            $table->string('email', 150)->nullable();
            
            // Operational Status and Scheme Funding (Col 10 & 11)
            $table->tinyInteger('status')->default(1)->index('idx_fvi_status'); // 1 = Active, 0 = Inactive
            $table->boolean('is_active')->default(true)->index('idx_fvi_is_active');
            $table->tinyInteger('which_govt')->default(1)->index('idx_fvi_which_govt'); // 1 = Govt. of Odisha, 2 = Govt. of India
            
            $table->text('remarks')->nullable();
            
            // Mandatory Audit columns storing the authenticated user's user_table_id value.
            // In accordance with portal architectural standards, user_table_id is indexed without
            // a DB-level foreign key to the users table.
            $table->bigInteger('created_by')->nullable()->index('idx_fvi_created_by');
            $table->bigInteger('updated_by')->nullable()->index('idx_fvi_updated_by');
            $table->bigInteger('deleted_by')->nullable()->index('idx_fvi_deleted_by');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_visit_institutes');
    }
};
