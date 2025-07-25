<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $allRoles = [
            'SuperAdmin',
            'Admin',
            'PEO',
            'BSSO',
            'MEO',
            'BDO',
            'Tahasildar',
            'SSSO',
            'DSSO',
            'SubCollector',
            'Collector',
            'HO',
            'BO',
            'Director',
            'Secretary',
            'Ngo',
            'INSTITUTE',
            'HEALTH_CONSULTANT',
            'TECH_OPERATOR',
            'DPM',
            'User',
            'SpecialSchool',
        ];

        // ✅ Use names instead of IDs
        $allPermissionNames = Permission::pluck('name')->all();

        $rolePermissions = [
            'SuperAdmin' => $allPermissionNames,
            'Admin' => $allPermissionNames,

            'SpecialSchool' => [
                'my-profile-access', 'my-profile-list', 'my-profile-show', 'my-profile-create', 'my-profile-edit',
                'ngo-access', 'ngo-list', 'ngo-show', 'ngo-create', 'ngo-edit',
                'special-school-access', 'special-school-list', 'special-school-show', 'special-school-create', 'special-school-edit', 'special-school-delete',
            ],

            'Ngo' => [
                'my-profile-access', 'my-profile-list', 'my-profile-show', 'my-profile-create', 'my-profile-edit',
                'ngo-access', 'ngo-list', 'ngo-show', 'ngo-create', 'ngo-edit',
                'special-school-access', 'special-school-list', 'special-school-show', 'special-school-create', 'special-school-edit', 'special-school-delete', 'special-school-approve-form',
            ],

            'DSSO' => [
                'my-profile-access', 'my-profile-list', 'my-profile-show', 'my-profile-create', 'my-profile-edit',
                'ngo-access', 'ngo-list', 'ngo-show', 'ngo-edit', 'ngo-approve-form',
                'special-school-access', 'special-school-list', 'special-school-show', 'special-school-create', 'special-school-edit', 'special-school-delete', 'special-school-approve-form',
            ],

            'Collector' => [
                'my-profile-access', 'my-profile-list', 'my-profile-show', 'my-profile-create', 'my-profile-edit',
                'ngo-access', 'ngo-list', 'ngo-show', 'ngo-edit', 'ngo-approve-form',
                'special-school-access', 'special-school-list', 'special-school-show', 'special-school-create', 'special-school-edit', 'special-school-delete', 'special-school-approve-form',
            ],

            'HO' => [
                'my-profile-access', 'my-profile-list', 'my-profile-show', 'my-profile-create', 'my-profile-edit',
                'ngo-access', 'ngo-list', 'ngo-show', 'ngo-edit', 'ngo-approve-form',
                'special-school-access', 'special-school-list', 'special-school-show', 'special-school-create', 'special-school-edit', 'special-school-delete', 'special-school-approve-form',
            ],

            'BO' => [
                'my-profile-access', 'my-profile-list', 'my-profile-show', 'my-profile-create', 'my-profile-edit',
                'ngo-access', 'ngo-list', 'ngo-show', 'ngo-edit', 'ngo-approve-form',
                'special-school-access', 'special-school-list', 'special-school-show', 'special-school-create', 'special-school-edit', 'special-school-delete', 'special-school-approve-form',
            ],

            'Director' => [
                'my-profile-access', 'my-profile-list', 'my-profile-show', 'my-profile-create', 'my-profile-edit',
                'ngo-access', 'ngo-list', 'ngo-show', 'ngo-edit', 'ngo-approve-form',
                'special-school-access', 'special-school-list', 'special-school-show', 'special-school-create', 'special-school-edit', 'special-school-delete', 'special-school-approve-form',
            ],

            'Secretary' => [
                'my-profile-access', 'my-profile-list', 'my-profile-show', 'my-profile-create', 'my-profile-edit',
                'ngo-access', 'ngo-list', 'ngo-show', 'ngo-edit', 'ngo-approve-form',
                'special-school-access', 'special-school-list', 'special-school-show', 'special-school-create', 'special-school-edit', 'special-school-delete', 'special-school-approve-form',
            ],
        ];

        foreach ($allRoles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if (array_key_exists($roleName, $rolePermissions)) {
                $permissions = $rolePermissions[$roleName];
                $permissionModels = Permission::whereIn('name', $permissions)->get();
                $role->syncPermissions($permissionModels);
            }
        }
    }
}
