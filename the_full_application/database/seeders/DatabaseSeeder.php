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
            WardMasterSeeder::class,
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

        DB::table('grampanchayats')->whereIn('gp_id', ['2411001006', '2418009014', '2431014012', '2431014013', '2431014014', '2401001001', '2401001002', '2401001005', '2401001006', '2401001007', '2401001013', '2401001015', '2401002008', '2401002010', '2401002011', '2401002015', '2401002017', '2408021001', '2409021012', '2407015028', '2428001023', '2428005019', '2410008008', '2431014011', '2403011004', '2418004019', '2418004012', '2418004032', '2418004036', '2418005029', '2418006018', '2402013010', '2402012006', '2423003017', '2420004041', '2414004002', '2414004017', '2414006007', '2414007007', '2422003018', '2422003019', '2422005021', '2422005034', '2422008028', '2431008020', '2431010001', '2431011001', '2417002023', '2417002024', '2417003020', '2417005001', '2417005002', '2417005028', '2417007007', '2417007030', '2417007031'])->update(['is_active' => 'Inactive']);
        
        DB::table('ward_master')->whereIn('ward_code', ['24110005026', '24110005027', '240100040015', '241900020022', '241900020023'])->update(['is_active' => '0']);
    }
}


