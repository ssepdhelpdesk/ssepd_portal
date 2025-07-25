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
            'user_table_id' => '1',
            'name' => 'Waris Patel', 
            'email' => 'super_admin@gmail.com',
            'user_id' => 'suraj7196',
            'password' => bcrypt('123456'),
            'profile_photo' => 'https://png.pngtree.com/png-clipart/20191121/original/pngtree-user-icon-isolated-on-abstract-background-png-image_5150068.jpg',
            'profile_photo_path' => 'https://png.pngtree.com/png-clipart/20191121/original/pngtree-user-icon-isolated-on-abstract-background-png-image_5150068.jpg',
            'email_verified_at' => Carbon::now('Asia/Kolkata'),
            'created_by' => 1,
            'created_at' => Carbon::now('Asia/Kolkata'),
            'updated_at' => Carbon::now('Asia/Kolkata'),
            'role_id' => '1',
            'role_name' => 'SuperAdmin'
        ]);

        $AdminUser = User::create([
            'user_table_id' => '2',
            'name' => 'Suraj Prasim Patel', 
            'email' => 'admin@gmail.com',
            'user_id' => 'admin7196',
            'password' => bcrypt('123456'),
            'profile_photo' => 'https://png.pngtree.com/png-clipart/20191121/original/pngtree-user-icon-isolated-on-abstract-background-png-image_5150068.jpg',
            'profile_photo_path' => 'https://png.pngtree.com/png-clipart/20191121/original/pngtree-user-icon-isolated-on-abstract-background-png-image_5150068.jpg',
            'email_verified_at' => Carbon::now('Asia/Kolkata'),
            'created_by' => 1,
            'created_at' => Carbon::now('Asia/Kolkata'),
            'updated_at' => Carbon::now('Asia/Kolkata'),
            'role_id' => '2',
            'role_name' => 'Admin'
        ]);

        $role_super_admin = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $role_admin = Role::firstOrCreate(['name' => 'Admin']);

        $SuperAdminUser->assignRole('SuperAdmin');
        $AdminUser->assignRole('Admin');
    }
}