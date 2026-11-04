<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FieldVisitInstitute;
use App\Models\User;
use App\Models\District;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
        // safely reassign its code to INST-{id} so it preserves uniqueness without data loss.
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
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Dynamic Header-to-Column Mapping
        // In the Excel Sheet1:
        // Col A (1): id
        // Col B (2): institute_master_id (1st occurrence = category_id, 1-13)
        // Col C (3): institute_master_name (1st occurrence = category_name)
        // Col D (4): management_id
        // Col E (5): management_name
        // Col F (6): institute_master_id (2nd occurrence = source_institute_id, 1-440)
        // Col G (7): institute_master_name (2nd occurrence = institute_name)
        // Col H (8): district_id
        // Col I (9): district_name
        // Col J (10): status
        // Col K (11): which_govt
        $headerOccurrences = [];
        $colMap = [
            'id'                  => 1,
            'category_id'         => 2,
            'category_name'       => 3,
            'management_id'       => 4,
            'management_name'     => 5,
            'source_institute_id' => 6,
            'institute_name'      => 7,
            'district_id'         => 8,
            'district_name'       => 9,
            'status'              => 10,
            'which_govt'          => 11,
        ];

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $headerText = strtolower(trim((string)$sheet->getCell([$col, 1])->getValue()));
            if ($headerText === '') continue;

            $headerOccurrences[$headerText] = ($headerOccurrences[$headerText] ?? 0) + 1;
            $occ = $headerOccurrences[$headerText];

            if ($headerText === 'id') {
                $colMap['id'] = $col;
            } elseif ($headerText === 'category_id') {
                $colMap['category_id'] = $col;
            } elseif ($headerText === 'category_name') {
                $colMap['category_name'] = $col;
            } elseif ($headerText === 'institute_master_id') {
                if ($occ === 1) {
                    $colMap['category_id'] = $col;
                } else {
                    $colMap['source_institute_id'] = $col;
                }
            } elseif ($headerText === 'institute_master_name') {
                if ($occ === 1) {
                    $colMap['category_name'] = $col;
                } else {
                    $colMap['institute_name'] = $col;
                }
            } elseif ($headerText === 'management_id') {
                $colMap['management_id'] = $col;
            } elseif ($headerText === 'management_name') {
                $colMap['management_name'] = $col;
            } elseif ($headerText === 'district_id') {
                $colMap['district_id'] = $col;
            } elseif ($headerText === 'district_name') {
                $colMap['district_name'] = $col;
            } elseif ($headerText === 'status') {
                $colMap['status'] = $col;
            } elseif ($headerText === 'which_govt') {
                $colMap['which_govt'] = $col;
            }
        }

        $insertedCount = 0;
        $updatedCount  = 0;
        $unchangedCount = 0;
        $skippedCount  = 0;
        $failedCount   = 0;
        $totalProcessed = 0;
        $duplicateSourceIdsCount = 0;
        $missingDistrictMappingCount = 0;
        $importedSourceIds = [];
        $seenSourceIdsInExcel = [];

        // Process inside transaction for database integrity
        DB::beginTransaction();

        try {
            for ($row = 2; $row <= $highestRow; $row++) {
                // Read row cells using mapped columns
                $rawId       = trim((string)$sheet->getCell([$colMap['id'], $row])->getFormattedValue());
                $rawCatId    = trim((string)$sheet->getCell([$colMap['category_id'], $row])->getFormattedValue());
                $rawCatName  = trim((string)$sheet->getCell([$colMap['category_name'], $row])->getFormattedValue());
                $rawMgmtId   = trim((string)$sheet->getCell([$colMap['management_id'], $row])->getFormattedValue());
                $rawMgmtName = trim((string)$sheet->getCell([$colMap['management_name'], $row])->getFormattedValue());
                $rawSrcId    = trim((string)$sheet->getCell([$colMap['source_institute_id'], $row])->getFormattedValue());
                $rawInstName = trim((string)$sheet->getCell([$colMap['institute_name'], $row])->getFormattedValue());
                $rawDistId   = trim((string)$sheet->getCell([$colMap['district_id'], $row])->getFormattedValue());
                $rawDistName = trim((string)$sheet->getCell([$colMap['district_name'], $row])->getFormattedValue());
                $rawStatus   = trim((string)$sheet->getCell([$colMap['status'], $row])->getFormattedValue());
                $rawGovt     = trim((string)$sheet->getCell([$colMap['which_govt'], $row])->getFormattedValue());

                // Check for completely blank row
                if ($rawInstName === '' && $rawCatName === '' && $rawCatId === '' && $rawSrcId === '') {
                    $skippedCount++;
                    if ($this->command) {
                        $this->command->warn("Row $row skipped: empty row.");
                    }
                    continue;
                }

                // Validation 1: Institute Name is required
                if ($rawInstName === '') {
                    $failedCount++;
                    if ($this->command) {
                        $this->command->error("Row $row failed: missing institute name.");
                    }
                    continue;
                }

                // Validation 2: Category ID and Name
                $categoryId = (int)$rawCatId;
                if ($categoryId <= 0) {
                    $failedCount++;
                    if ($this->command) {
                        $this->command->error("Row $row failed: invalid category_id ('$rawCatId').");
                    }
                    continue;
                }
                $categoryName = $rawCatName;

                // Validation 3: Source ID & Sub ID
                $sourceId = $rawSrcId !== '' ? (int)$rawSrcId : ($rawId !== '' ? (int)$rawId : null);
                if (!$sourceId || $sourceId <= 0) {
                    $failedCount++;
                    if ($this->command) {
                        $this->command->error("Row $row failed: invalid source_institute_id.");
                    }
                    continue;
                }

                // Validation 4: Duplicate Source ID check within Excel
                if (isset($seenSourceIdsInExcel[$sourceId])) {
                    $duplicateSourceIdsCount++;
                    $failedCount++;
                    if ($this->command) {
                        $this->command->error("Row $row failed: duplicate source_institute_id $sourceId (first seen in Row {$seenSourceIdsInExcel[$sourceId]}).");
                    }
                    continue;
                }
                $seenSourceIdsInExcel[$sourceId] = $row;
                $importedSourceIds[] = $sourceId;

                $subId = $rawId !== '' ? (int)$rawId : $sourceId;
                $instituteCode = 'INST-' . str_pad($sourceId, 4, '0', STR_PAD_LEFT);

                // Validation 5: District ID in District master
                $districtId = (int)$rawDistId;
                if (!isset($dbDistricts[$districtId])) {
                    $missingDistrictMappingCount++;
                    $failedCount++;
                    if ($this->command) {
                        $this->command->error("Row $row failed: district_id $districtId not found in districts table.");
                    }
                    continue;
                }
                $districtName = $dbDistricts[$districtId]->district_name ?? $rawDistName;

                // Management details
                $mgmtId   = $rawMgmtId !== '' ? (int)$rawMgmtId : null;
                $mgmtName = $rawMgmtName !== '' ? $rawMgmtName : null;

                // Operational Status and Scheme Funding
                $status    = ($rawStatus === '0') ? 0 : 1;
                $isActive  = ($status === 1);
                $whichGovt = ($rawGovt === '2') ? 2 : 1;

                // Lookup existing record by source_institute_id or institute_code
                $existing = FieldVisitInstitute::where('source_institute_id', $sourceId)->first()
                    ?: FieldVisitInstitute::where('institute_code', $instituteCode)->first();

                if ($existing) {
                    // Check if any attributes changed
                    $hasChanges = (
                        $existing->institute_name !== $rawInstName ||
                        $existing->institute_code !== $instituteCode ||
                        (int)$existing->category_id !== (int)$categoryId ||
                        $existing->category_name !== $categoryName ||
                        (int)$existing->management_id !== (int)$mgmtId ||
                        $existing->management_name !== $mgmtName ||
                        (int)$existing->source_institute_id !== (int)$sourceId ||
                        (int)$existing->legacy_sub_id !== (int)$subId ||
                        (int)$existing->district_id !== (int)$districtId ||
                        $existing->district_name !== $districtName ||
                        (int)$existing->status !== (int)$status ||
                        (bool)$existing->is_active !== (bool)$isActive ||
                        (int)$existing->which_govt !== (int)$whichGovt
                    );

                    if ($hasChanges) {
                        $existing->update([
                            'institute_name'      => $rawInstName,
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
                        $unchangedCount++;
                    }
                } else {
                    FieldVisitInstitute::create([
                        'institute_code'      => $instituteCode,
                        'institute_name'      => $rawInstName,
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

            // SAFETY ENHANCEMENT:
            // Do NOT automatically delete database records missing from Excel.
            // Identify and report them separately to prevent accidental data loss or foreign key breakage.
            $missingFromExcel = FieldVisitInstitute::whereNotNull('source_institute_id')
                ->whereNotIn('source_institute_id', $importedSourceIds)
                ->get();
            $missingFromExcelCount = $missingFromExcel->count();

            if ($missingFromExcelCount > 0 && $this->command) {
                $this->command->warn("Identified $missingFromExcelCount database record(s) with source_institute_id not present in the revised Excel workbook (preserved safely without deletion):");
                foreach ($missingFromExcel as $missingRec) {
                    $linkedVisits = DB::table('field_visits')->where('institution_id', $missingRec->id)->count();
                    $this->command->line("  - ID: {$missingRec->id} | Code: {$missingRec->institute_code} | Name: {$missingRec->institute_name} | Linked Visits: {$linkedVisits}");
                }
            }

            DB::commit();

            $totalInDb = FieldVisitInstitute::count();

            if ($this->command) {
                $this->command->info("=== Field Visit Institute Seeder Completed Successfully ===");
                $this->command->table(
                    ['Metric', 'Count'],
                    [
                        ['Total Excel Rows (Data)', $highestRow - 1],
                        ['Valid Records',           $totalProcessed],
                        ['Inserted',                $insertedCount],
                        ['Updated',                 $updatedCount],
                        ['Unchanged',               $unchangedCount],
                        ['Skipped',                 $skippedCount],
                        ['Failed',                  $failedCount],
                        ['Duplicate Source IDs',    $duplicateSourceIdsCount],
                        ['Missing District Mapping', $missingDistrictMappingCount],
                        ['Missing from Excel',      $missingFromExcelCount],
                        ['Total Database Records',  $totalInDb],
                    ]
                );
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
