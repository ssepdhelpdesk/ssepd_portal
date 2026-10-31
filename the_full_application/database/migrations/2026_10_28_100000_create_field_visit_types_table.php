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
        Schema::create('field_visit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique('uq_field_visit_types_name');
            $table->string('code', 50)->unique('uq_field_visit_types_code');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index('idx_fvt_is_active');
            $table->integer('sort_order')->default(0)->index('idx_fvt_sort_order');
            
            // Audit columns storing the authenticated user's user_table_id value.
            // Following project conventions, user_table_id is indexed without a DB-level
            // foreign key constraint to users table.
            $table->bigInteger('created_by')->nullable()->index('idx_fvt_created_by');
            $table->bigInteger('updated_by')->nullable()->index('idx_fvt_updated_by');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_visit_types');
    }
};
