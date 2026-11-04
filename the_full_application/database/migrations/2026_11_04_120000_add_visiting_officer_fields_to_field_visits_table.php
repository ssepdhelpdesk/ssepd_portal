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
            if (!Schema::hasColumn('field_visits', 'visiting_officer_name')) {
                $table->string('visiting_officer_name', 255)->nullable()->after('visiting_officer_id');
            }
            if (!Schema::hasColumn('field_visits', 'visiting_officer_designation')) {
                $table->string('visiting_officer_designation', 255)->nullable()->after('visiting_officer_name');
            }
            if (!Schema::hasColumn('field_visits', 'other_designation')) {
                $table->string('other_designation', 255)->nullable()->after('visiting_officer_designation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('field_visits', 'visiting_officer_name')) {
                $columns[] = 'visiting_officer_name';
            }
            if (Schema::hasColumn('field_visits', 'visiting_officer_designation')) {
                $columns[] = 'visiting_officer_designation';
            }
            if (Schema::hasColumn('field_visits', 'other_designation')) {
                $columns[] = 'other_designation';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

