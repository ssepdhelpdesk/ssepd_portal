<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FieldVisitPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'field-visit-access',
            'field-visit-list',
            'field-visit-show',
            'field-visit-create',
            'field-visit-edit',
            'field-visit-delete',
            'field-visit-submit',
            'field-visit-evidence-view',
            'field-visit-evidence-upload',
            'field-visit-evidence-delete',
            'field-visit-export',
            'field-visit-report-access',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        // Assign all permissions to SuperAdmin and Admin
        $superAdmin = Role::where('name', 'SuperAdmin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        $admin = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        // Assign field-visit-report-access to field & department roles
        $reportRoles = [
            'BSSO',
            'MEO',
            'BDO',
            'SSSO',
            'DSSO',
            'SubCollector',
            'Collector',
            'HO',
            'BO',
            'Director',
            'Secretary',
            'PensionRole',
        ];

        foreach ($reportRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo('field-visit-report-access');
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
