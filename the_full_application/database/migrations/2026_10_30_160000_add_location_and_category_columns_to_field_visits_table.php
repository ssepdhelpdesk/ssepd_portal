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
        Schema::table('field_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('field_visits', 'address_type')) {
                $table->tinyInteger('address_type')->nullable()->after('institution_type');
            }
            if (!Schema::hasColumn('field_visits', 'category_id')) {
                $table->integer('category_id')->nullable()->after('institution_type');
            }
            if (!Schema::hasColumn('field_visits', 'village_id')) {
                $table->bigInteger('village_id')->nullable()->after('gp_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('field_visits', 'address_type')) {
                $columnsToDrop[] = 'address_type';
            }
            if (Schema::hasColumn('field_visits', 'category_id')) {
                $columnsToDrop[] = 'category_id';
            }
            if (Schema::hasColumn('field_visits', 'village_id')) {
                $columnsToDrop[] = 'village_id';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

