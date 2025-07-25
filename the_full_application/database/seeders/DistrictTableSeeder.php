<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DistrictTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            ['district_id' => 2401, 'district_name' => 'SAMBALPUR'],
            ['district_id' => 2402, 'district_name' => 'SUNDARGARH'],
            ['district_id' => 2403, 'district_name' => 'KENDUJHAR'],
            ['district_id' => 2404, 'district_name' => 'MAYURBHANJ'],
            ['district_id' => 2405, 'district_name' => 'BALESHWAR'],
            ['district_id' => 2406, 'district_name' => 'CUTTACK'],
            ['district_id' => 2407, 'district_name' => 'DHENKANAL'],
            ['district_id' => 2408, 'district_name' => 'KANDHAMAL'],
            ['district_id' => 2409, 'district_name' => 'BOLANGIR'],
            ['district_id' => 2410, 'district_name' => 'KALAHANDI'],
            ['district_id' => 2411, 'district_name' => 'KORAPUT'],
            ['district_id' => 2412, 'district_name' => 'GANJAM'],
            ['district_id' => 2413, 'district_name' => 'PURI'],
            ['district_id' => 2414, 'district_name' => 'BARGARH'],
            ['district_id' => 2415, 'district_name' => 'JHARSUGUDA'],
            ['district_id' => 2416, 'district_name' => 'DEOGARH'],
            ['district_id' => 2417, 'district_name' => 'BHADRAK'],
            ['district_id' => 2418, 'district_name' => 'KENDRAPARA'],
            ['district_id' => 2419, 'district_name' => 'JAGATSINGHAPUR'],
            ['district_id' => 2420, 'district_name' => 'JAJPUR'],
            ['district_id' => 2421, 'district_name' => 'ANGUL'],
            ['district_id' => 2422, 'district_name' => 'NAYAGARH'],
            ['district_id' => 2423, 'district_name' => 'KHORDHA'],
            ['district_id' => 2424, 'district_name' => 'GAJAPATI'],
            ['district_id' => 2426, 'district_name' => 'BOUDH'],
            ['district_id' => 2427, 'district_name' => 'SONEPUR'],
            ['district_id' => 2428, 'district_name' => 'NUAPADA'],
            ['district_id' => 2429, 'district_name' => 'RAYAGADA'],
            ['district_id' => 2430, 'district_name' => 'NABARANGAPUR'],
            ['district_id' => 2431, 'district_name' => 'MALKANGIRI'],
        ];

        $districtData = [];
        
        foreach ($districts as $district) {
            $districtData[] = array_merge($district, [
                'state_id' => 228,
                'is_iap' => '1',
                'is_active' => 'active',
                'status' => 1,
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        DB::table('districts')->insert($districtData);
    }
}
