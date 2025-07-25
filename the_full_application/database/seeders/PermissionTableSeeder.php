<?php

namespace Database\Seeders;
  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
  
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
           'role-access',
           'role-list',
           'role-show',
           'role-create',
           'role-edit',
           'role-delete',
           'permission-access',
           'permission-list',
           'permission-show',
           'permission-create',
           'permission-edit',
           'permission-delete',
           'user-access',
           'user-list',
           'user-show',
           'user-create',
           'user-edit',
           'user-delete',
           'my-profile-access',
           'my-profile-list',
           'my-profile-show',
           'my-profile-create',
           'my-profile-edit',
           'my-profile-delete',
           'location-access',
           'location-list',
           'location-show',
           'location-create',
           'location-edit',
           'location-delete',
           'product-access',
           'product-list',
           'product-show',
           'product-create',
           'product-edit',
           'product-delete',
           'ngo-access',
           'ngo-list',
           'ngo-show',
           'ngo-create',
           'ngo-edit',
           'ngo-delete',
           'ngo-approve-form',
           'special-school-access',
           'special-school-list',
           'special-school-show',
           'special-school-create',
           'special-school-edit',
           'special-school-delete',
           'special-school-approve-form'
        ];
        
        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}