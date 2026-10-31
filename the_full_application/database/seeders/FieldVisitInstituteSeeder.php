<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FieldVisitInstitute;
use App\Models\User;
use App\Models\District;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;
use Exception;

class FieldVisitInstituteSeeder extends Seeder
{
    /**
     * Run the database seeds idempotently.
     */
    public function run(): void
    {
        // 1. Seed module permissions idempotently
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'field-visit-institute-access',
            'field-visit-institute-list',
            'field-visit-institute-show',
            'field-visit-institute-create',
            'field-visit-institute-edit',
            'field-visit-institute-delete',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        // 2. Grant permissions to SuperAdmin and Admin roles if they exist
        foreach (['SuperAdmin', 'Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }

        // 3. Resolve system admin user_table_id for audit tracking
        $auditUserTableId = User::where('user_table_id', 1)->value('user_table_id') 
            ?? User::where('id', 1)->value('user_table_id')
            ?? 1;

        // 4. Locate Excel Source File
        $possiblePaths = [
            base_path('MyWorkingFiles/All Analysis Files By Antigravity/ssepd_institute_master_for_field_visit.xlsx'),
            base_path('MyWorkingFiles/All Analysis Files By Antigravity/ssepd_institute_master_for_field_visit.xls'),
            database_path('seeders/data/ssepd_institute_master_for_field_visit.xlsx'),
        ];

        $filePath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $filePath = $path;
                break;
            }
        }

        if (!$filePath) {
            throw new Exception("Excel source file 'ssepd_institute_master_for_field_visit.xlsx' not found in expected locations.");
        }

        if ($this->command) {
            $this->command->info("Loading Excel workbook: " . $filePath);
        }

        // Preload districts for name normalization and fast lookup
        $dbDistricts = District::all()->keyBy('district_id');

        // Pre-check: If any manually created record (source_institute_id IS NULL) collides with Excel codes (INST-0001 to INST-0440),
        // safely reassign its code to INST-{id} so it preserves uniqueness.
        $conflictingManuals = FieldVisitInstitute::whereNull('source_institute_id')->get();
        foreach ($conflictingManuals as $manualRecord) {
            if (preg_match('/^INST-(\d+)$/', $manualRecord->institute_code, $matches)) {
                $codeNum = (int)$matches[1];
                if ($codeNum <= 440) {
                    $newManualCode = 'INST-' . str_pad($manualRecord->id, 4, '0', STR_PAD_LEFT);
                    $manualRecord->update(['institute_code' => $newManualCode]);
                    if ($this->command) {
                        $this->command->info("Safely reassigned manual record ID {$manualRecord->id} code to {$newManualCode} to avoid Excel collision.");
                    }
                }
            }
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Sheet1') 
            ?? $spreadsheet->getSheetByName('sheet1') 
            ?? $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $insertedCount = 0;
        $updatedCount  = 0;
        $skippedCount  = 0;
        $failedCount   = 0;
        $totalProcessed = 0;
        $importedSourceIds = [];

        // Process inside transaction for database integrity
        DB::beginTransaction();

        try {
            for ($row = 2; $row <= $highestRow; $row++) {
                $c1  = trim((string)$sheet->getCell([1, $row])->getFormattedValue());
                $c2  = trim((string)$sheet->getCell([2, $row])->getFormattedValue());
                $c3  = trim((string)$sheet->getCell([3, $row])->getFormattedValue());
                $c4  = trim((string)$sheet->getCell([4, $row])->getFormattedValue());
                $c5  = trim((string)$sheet->getCell([5, $row])->getFormattedValue());
                $c6  = trim((string)$sheet->getCell([6, $row])->getFormattedValue());
                $c7  = trim((string)$sheet->getCell([7, $row])->getFormattedValue());
                $c8  = trim((string)$sheet->getCell([8, $row])->getFormattedValue());
                $c9  = trim((string)$sheet->getCell([9, $row])->getFormattedValue());
                $c10 = trim((string)$sheet->getCell([10, $row])->getFormattedValue());
                $c11 = trim((string)$sheet->getCell([11, $row])->getFormattedValue());

                // Skip remnant / completely empty rows
                if ($c7 === '' && $c2 === '') {
                    $skippedCount++;
                    if ($this->command) {
                        $this->command->warn("Row $row skipped: empty institute name and category (cell A: '$c1').");
                    }
                    continue;
                }

                if ($c7 === '') {
                    $failedCount++;
                    if ($this->command) {
                        $this->command->error("Row $row failed: missing institute name.");
                    }
                    continue;
                }

                // Determine Source ID & Sub ID from Excel Column F (institute_master_id) or Column A (id)
                if ($c6 !== '') {
                    $sourceId = (int)$c6;
                    $subId    = $c1 !== '' ? (int)$c1 : $sourceId;
                } elseif ($c1 !== '') {
                    $sourceId = (int)$c1;
                    $subId    = $sourceId;
                } else {
                    $sourceId = 440 + $row;
                    $subId    = $sourceId;
                }

                $instituteCode = 'INST-' . str_pad($sourceId, 4, '0', STR_PAD_LEFT);
                $importedSourceIds[] = $sourceId;

                $categoryId   = (int)$c2;
                $categoryName = $c3;
                $mgmtId       = $c4 !== '' ? (int)$c4 : null;
                $mgmtName     = $c5;
                $instName     = $c7;
                $districtId   = (int)$c8;
                $districtName = $c9;
                if (isset($dbDistricts[$districtId])) {
                    $districtName = $dbDistricts[$districtId]->district_name ?? $districtName;
                }

                $status    = ($c10 === '0') ? 0 : 1;
                $isActive  = ($status === 1);
                $whichGovt = ($c11 === '2') ? 2 : 1;

                // Lookup existing record by source_institute_id or institute_code
                $existing = FieldVisitInstitute::where('source_institute_id', $sourceId)->first()
                    ?: FieldVisitInstitute::where('institute_code', $instituteCode)->first();

                if ($existing) {
                    $existing->update([
                        'institute_name'      => $instName,
                        'institute_code'      => $instituteCode,
                        'category_id'         => $categoryId,
                        'category_name'       => $categoryName,
                        'management_id'       => $mgmtId,
                        'management_name'     => $mgmtName,
                        'source_institute_id' => $sourceId,
                        'legacy_sub_id'       => $subId,
                        'district_id'         => $districtId,
                        'district_name'       => $districtName,
                        'status'              => $status,
                        'is_active'           => $isActive,
                        'which_govt'          => $whichGovt,
                        'updated_by'          => $auditUserTableId,
                    ]);
                    $updatedCount++;
                } else {
                    FieldVisitInstitute::create([
                        'institute_code'      => $instituteCode,
                        'institute_name'      => $instName,
                        'category_id'         => $categoryId,
                        'category_name'       => $categoryName,
                        'management_id'       => $mgmtId,
                        'management_name'     => $mgmtName,
                        'source_institute_id' => $sourceId,
                        'legacy_sub_id'       => $subId,
                        'district_id'         => $districtId,
                        'district_name'       => $districtName,
                        'status'              => $status,
                        'is_active'           => $isActive,
                        'which_govt'          => $whichGovt,
                        'created_by'          => $auditUserTableId,
                        'updated_by'          => null,
                    ]);
                    $insertedCount++;
                }

                $totalProcessed++;
            }

            // Prune any previously imported records that are NOT in the updated Excel file
            // (strictly preserving manual records where source_institute_id IS NULL)
            $staleInstitutes = FieldVisitInstitute::whereNotNull('source_institute_id')
                ->whereNotIn('source_institute_id', $importedSourceIds)
                ->get();

            foreach ($staleInstitutes as $stale) {
                $linkedCount = DB::table('field_visits')->where('institution_id', $stale->id)->count();
                if ($linkedCount > 0) {
                    if ($this->command) {
                        $this->command->warn("Cannot remove stale institute ID {$stale->id} ({$stale->institute_name}): referenced by $linkedCount Field Visit(s).");
                    }
                } else {
                    $stale->delete();
                    if ($this->command) {
                        $this->command->info("Removed obsolete Excel record ID {$stale->id} (Code: {$stale->institute_code}).");
                    }
                }
            }

            DB::commit();

            if ($this->command) {
                $this->command->info("=== Field Visit Institute Seeder Completed Successfully ===");
                $this->command->info("Total Valid Processed: $totalProcessed");
                $this->command->info("Inserted Records:      $insertedCount");
                $this->command->info("Updated Records:       $updatedCount");
                $this->command->info("Skipped Rows:          $skippedCount");
                $this->command->info("Failed Records:        $failedCount");
                $this->command->info("Total in DB:           " . FieldVisitInstitute::count());
            }
        } catch (Exception $e) {
            DB::rollBack();
            if ($this->command) {
                $this->command->error("Seeder failed: " . $e->getMessage());
            }
            throw $e;
        }
    }
}
