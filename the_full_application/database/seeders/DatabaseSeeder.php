<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
            RolePermissionSeeder::class,
            CreateAdminUserSeeder::class,
            SsepdUserTableSeeder::class,
            ModelHasRolesSeeder::class,
            StateTableSeeder::class,
            DistrictTableSeeder::class,
            SubdivisionTableSeeder::class,
            BlockTableSeeder::class,
            MunicipalityTableSeeder::class,
            TahasilTableSeeder::class,
            GrampanchayatTableSeeder::class,
            VillageTableSeederOne::class,
            VillageTableSeederTwo::class,
            VillageTableSeederThree::class,
            VillageTableSeederFour::class,
            VillageTableSeederFive::class,
            ApplicationStageSeeder::class,
            BankMasterSeeder::class,
            NgoCategorySeeder::class,
            GenderSeeder::class,
            UpdateUserRolesSeeder::class,
            /*NgoRegistrationTableSeeder::class,
            NgoPartTwoOfficeBearerSeeder::class,
            NgoPartThreeActRegistrationsSeeder::class,*/
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_id' => 'waris7196',
        ]);

        DB::table('blocks')->whereIn('block_id', [
            2405200, 2406050, 2406998, 2406999, 2413090,
            2431009, 2431100, 2431101, 2431102, 2431103, 2431104
        ])->update(['is_active' => 'Inactive']);

        DB::table('municipalities')->whereIn('municipality_id', [2])->update(['is_active' => 'Inactive']);
    }
}
