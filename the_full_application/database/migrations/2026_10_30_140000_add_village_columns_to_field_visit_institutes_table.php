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
        Schema::table('field_visit_institutes', function (Blueprint $table) {
            if (!Schema::hasColumn('field_visit_institutes', 'village_id')) {
                $table->bigInteger('village_id')->nullable()->after('gp_name')->index('idx_fvi_village_id');
            }
            if (!Schema::hasColumn('field_visit_institutes', 'village_name')) {
                $table->string('village_name', 100)->nullable()->after('village_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_visit_institutes', function (Blueprint $table) {
            if (Schema::hasColumn('field_visit_institutes', 'village_name')) {
                $table->dropColumn('village_name');
            }
            if (Schema::hasColumn('field_visit_institutes', 'village_id')) {
                $table->dropIndex('idx_fvi_village_id');
                $table->dropColumn('village_id');
            }
        });
    }
};

