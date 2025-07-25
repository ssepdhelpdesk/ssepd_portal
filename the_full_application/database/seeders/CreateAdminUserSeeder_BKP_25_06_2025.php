<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $SuperAdminUser = User::create([
            'name' => 'Waris Patel', 
            'email' => 'super_admin@gmail.com',
            'user_id' => 'suraj7196',
            'password' => bcrypt('123456'),
            'email_verified_at' => Carbon::now('Asia/Kolkata')
        ]);

        $AdminUser = User::create([
            'name' => 'Suraj Prasim Patel', 
            'email' => 'admin@gmail.com',
            'user_id' => 'admin7196',
            'password' => bcrypt('123456'),
            'email_verified_at' => Carbon::now('Asia/Kolkata')
        ]);

        /*$BssoUser = User::create([
            'name' => 'BSSO User', 
            'email' => 'bsso@gmail.com',
            'user_id' => 'bsso7196',
            'password' => bcrypt('123456'),
            'email_verified_at' => Carbon::now('Asia/Kolkata')
        ]);*/

        $role_super_admin = Role::create(['name' => 'SuperAdmin']);
        $role_admin = Role::create(['name' => 'Admin']);
        $role_peo = Role::create(['name' => 'PEO']);
        $role_bsso = Role::create(['name' => 'BSSO']);
        $role_meo = Role::create(['name' => 'MEO']);
        $role_bdo = Role::create(['name' => 'BDO']);
        $role_tahasildar = Role::create(['name' => 'Tahasildar']);
        $role_ssso = Role::create(['name' => 'SSSO']);
        $role_dsso = Role::create(['name' => 'DSSO']);
        $role_subcollector = Role::create(['name' => 'SubCollector']);
        $role_collector = Role::create(['name' => 'Collector']);
        $role_ho = Role::create(['name' => 'HO']);
        $role_bo = Role::create(['name' => 'BO']);
        $role_director = Role::create(['name' => 'Director']);
        $role_secretary = Role::create(['name' => 'Secretary']);
        $role_ngo = Role::create(['name' => 'Ngo']);
        $role_institute = Role::create(['name' => 'INSTITUTE']);
        $role_health_consultant = Role::create(['name' => 'HEALTH_CONSULTANT']);
        $role_tech_operator = Role::create(['name' => 'TECH_OPERATOR']);
        $role_tech_operator = Role::create(['name' => 'DPM']);

        $permissions = Permission::pluck('id','id')->all();       

        $bssoPermissions = [
            'my-profile-access',
            'my-profile-list',
            'my-profile-show',
            'my-profile-create',
            'my-profile-edit',
            'ngo-access',
            'ngo-list',
            'ngo-show',
        ];

        $ngoPermissions = [
            'my-profile-access',
            'my-profile-list',
            'my-profile-show',
            'my-profile-create',
            'my-profile-edit',
            'ngo-access',
            'ngo-list',
            'ngo-show',
            'ngo-create',
            'ngo-edit',
        ];

        $requiredPermissions = [
            'my-profile-access',
            'my-profile-list',
            'my-profile-show',
            'my-profile-create',
            'my-profile-edit',
            'ngo-access',
            'ngo-list',
            'ngo-show',
            'ngo-edit',
            'ngo-approve-form',
        ];

        $role_super_admin->syncPermissions($permissions);
        $role_admin->syncPermissions($permissions);
        //$role_bsso->syncPermissions($bssoPermissions);
        $role_ngo->syncPermissions($ngoPermissions);

        $role_dsso->syncPermissions($requiredPermissions);
        $role_collector->syncPermissions($requiredPermissions);
        $role_ho->syncPermissions($requiredPermissions);
        $role_bo->syncPermissions($requiredPermissions);
        $role_director->syncPermissions($requiredPermissions);

        $SuperAdminUser->assignRole([$role_super_admin->id]);
        $AdminUser->assignRole([$role_admin->id]);
        //$BssoUser->assignRole([$role_bsso->id]);        
    }
}