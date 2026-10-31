<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FieldVisitType;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FieldVisitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds idempotently.
     */
    public function run(): void
    {
        // 1. Seed module permissions idempotently
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'field-visit-type-access',
            'field-visit-type-list',
            'field-visit-type-show',
            'field-visit-type-create',
            'field-visit-type-edit',
            'field-visit-type-delete',
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
            ?? User::first()?->user_table_id 
            ?? 1;

        // 4. Seed initial standard Visit Types idempotently
        $visitTypes = [
            [
                'name'        => 'Inspection',
                'code'        => 'INSPECTION',
                'description' => 'Formal regulatory or compliance inspection of departmental facilities, institutes, and field centers.',
                'is_active'   => true,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Inquiry',
                'code'        => 'INQUIRY',
                'description' => 'Fact-finding inquiry into specific complaints, allegations, irregularities, or special references.',
                'is_active'   => true,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Monitoring Visit',
                'code'        => 'MONITORING',
                'description' => 'Routine monitoring and operational supervision of departmental schemes, centers, and beneficiaries.',
                'is_active'   => true,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Follow-up Visit',
                'code'        => 'FOLLOW_UP',
                'description' => 'Verification of compliance or rectification progress following previous inspection recommendations.',
                'is_active'   => true,
                'sort_order'  => 4,
            ],
            [
                'name'        => 'Review Visit',
                'code'        => 'REVIEW',
                'description' => 'Scheduled institutional review by departmental officers to assess performance and resource delivery.',
                'is_active'   => true,
                'sort_order'  => 5,
            ],
            [
                'name'        => 'Verification Visit',
                'code'        => 'VERIFICATION',
                'description' => 'On-ground verification of infrastructure, beneficiary lists, civil works, or physical assets.',
                'is_active'   => true,
                'sort_order'  => 6,
            ],
            [
                'name'        => 'Official Visit',
                'code'        => 'OFFICIAL',
                'description' => 'Executive, protocol, or ceremonial visits conducted by senior dignitaries and departmental heads.',
                'is_active'   => true,
                'sort_order'  => 7,
            ],
            [
                'name'        => 'Other',
                'code'        => 'OTHER',
                'description' => 'Ad-hoc or emergency field visits not covered under standard categories.',
                'is_active'   => true,
                'sort_order'  => 8,
            ],
        ];

        foreach ($visitTypes as $type) {
            FieldVisitType::updateOrCreate(
                ['code' => $type['code']],
                [
                    'name'        => $type['name'],
                    'description' => $type['description'],
                    'is_active'   => $type['is_active'],
                    'sort_order'  => $type['sort_order'],
                    'created_by'  => $auditUserTableId,
                ]
            );
        }
    }
}
