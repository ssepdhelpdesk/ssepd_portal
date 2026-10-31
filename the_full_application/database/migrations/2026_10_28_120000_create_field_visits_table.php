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
        Schema::create('field_visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('visit_reference_no', 50)->unique('uq_field_visits_ref');
            
            // Relational link to Visit Type Master
            $table->unsignedBigInteger('field_visit_type_id');
            $table->foreign('field_visit_type_id', 'fk_fv_visit_type')
                  ->references('id')
                  ->on('field_visit_types')
                  ->onDelete('restrict');

            $table->date('visit_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'closed'])->default('submitted');
            $table->string('visit_purpose', 255);

            // Office / Institution Details
            $table->string('institution_type', 50); // SPECIAL_SCHOOL, DDRC, PIA_INSTITUTE, GOVT_OFFICE, OTHER
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->string('institution_name', 255);
            $table->string('organization_code', 100)->nullable();

            // Location hierarchy references (Domain codes matching SSEPD masters)
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('block_id')->nullable();
            $table->bigInteger('municipality_id')->nullable();
            $table->bigInteger('gp_id')->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->text('institution_address')->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('contact_number', 25)->nullable();

            // Inspection Observations and Findings
            $table->text('areas_inspected')->nullable();
            $table->longText('key_observations')->nullable();
            $table->longText('findings')->nullable();
            $table->longText('issues_identified')->nullable();
            $table->longText('recommendations')->nullable();
            $table->longText('corrective_actions')->nullable();
            $table->boolean('is_follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->longText('overall_remarks')->nullable();

            // Overall Visit Geolocation Summary (acquired at site)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('gps_accuracy', 8, 2)->nullable();
            $table->timestamp('location_captured_at')->nullable();
            $table->text('location_address')->nullable();

            // Visiting Officer attribution (Mandatory user_table_id rule)
            $table->bigInteger('visiting_officer_id');

            // Audit Fields (Mandatory user_table_id rule)
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Performance Indexes
            $table->index('visit_date', 'idx_fv_visit_date');
            $table->index('status', 'idx_fv_status');
            $table->index('institution_type', 'idx_fv_inst_type');
            $table->index('district_id', 'idx_fv_district_id');
            $table->index('block_id', 'idx_fv_block_id');
            $table->index('municipality_id', 'idx_fv_municipality_id');
            $table->index('visiting_officer_id', 'idx_fv_officer');
            $table->index('created_by', 'idx_fv_created_by');
            $table->index('deleted_at', 'idx_fv_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_visits');
    }
};
