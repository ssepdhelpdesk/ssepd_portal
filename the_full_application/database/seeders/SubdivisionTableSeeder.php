<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subdivision;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubdivisionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subdivisions = [
            ['subdivision_id' => 1, 'subdivision_name' => 'CUTTACK', 'district_id' => '2406', 'district_name' => 'CUTTACK'],
            ['subdivision_id' => 2, 'subdivision_name' => 'ATHGARH', 'district_id' => '2406', 'district_name' => 'CUTTACK'],
            ['subdivision_id' => 3, 'subdivision_name' => 'BANKI', 'district_id' => '2406', 'district_name' => 'CUTTACK'],
            ['subdivision_id' => 4, 'subdivision_name' => 'JAJPUR', 'district_id' => '2420', 'district_name' => 'JAJPUR'],
            ['subdivision_id' => 5, 'subdivision_name' => 'JAGATSINGHPUR', 'district_id' => '2419', 'district_name' => 'JAGATSINGHAPUR'],
            ['subdivision_id' => 6, 'subdivision_name' => 'KENDRAPARA', 'district_id' => '2418', 'district_name' => 'KENDRAPARA'],
            ['subdivision_id' => 7, 'subdivision_name' => 'BALASORE', 'district_id' => '2405', 'district_name' => 'BALESHWAR'],
            ['subdivision_id' => 8, 'subdivision_name' => 'NILGIRI', 'district_id' => '2405', 'district_name' => 'BALESHWAR'],
            ['subdivision_id' => 9, 'subdivision_name' => 'BHADRAK', 'district_id' => '2417', 'district_name' => 'BHADRAK'],
            ['subdivision_id' => 10, 'subdivision_name' => 'PURI', 'district_id' => '2413', 'district_name' => 'PURI'],
            ['subdivision_id' => 11, 'subdivision_name' => 'BHUBANESWAR', 'district_id' => '2423', 'district_name' => 'KHORDHA'],
            ['subdivision_id' => 12, 'subdivision_name' => 'KHURDA', 'district_id' => '2423', 'district_name' => 'KHORDHA'],
            ['subdivision_id' => 13, 'subdivision_name' => 'NAYAGARH', 'district_id' => '2422', 'district_name' => 'NAYAGARH'],
            ['subdivision_id' => 14, 'subdivision_name' => 'BAMANGHATI', 'district_id' => '2404', 'district_name' => 'MAYURBHANJ'],
            ['subdivision_id' => 15, 'subdivision_name' => 'BARIPADA', 'district_id' => '2404', 'district_name' => 'MAYURBHANJ'],
            ['subdivision_id' => 16, 'subdivision_name' => 'KAPTIPADA', 'district_id' => '2404', 'district_name' => 'MAYURBHANJ'],
            ['subdivision_id' => 17, 'subdivision_name' => 'PANCHPIR', 'district_id' => '2404', 'district_name' => 'MAYURBHANJ'],
            ['subdivision_id' => 18, 'subdivision_name' => 'DHENKANAL SADAR', 'district_id' => '2407', 'district_name' => 'DHENKANAL'],
            ['subdivision_id' => 19, 'subdivision_name' => 'HINDOL', 'district_id' => '2407', 'district_name' => 'DHENKANAL'],
            ['subdivision_id' => 20, 'subdivision_name' => 'KAMAKHYA -NAGAR', 'district_id' => '2407', 'district_name' => 'DHENKANAL'],
            ['subdivision_id' => 21, 'subdivision_name' => 'ANGUL', 'district_id' => '2421', 'district_name' => 'ANGUL'],
            ['subdivision_id' => 22, 'subdivision_name' => 'TALCHER', 'district_id' => '2421', 'district_name' => 'ANGUL'],
            ['subdivision_id' => 23, 'subdivision_name' => 'ATHMALLIK', 'district_id' => '2421', 'district_name' => 'ANGUL'],
            ['subdivision_id' => 24, 'subdivision_name' => 'PALLAHARA', 'district_id' => '2421', 'district_name' => 'ANGUL'],
            ['subdivision_id' => 25, 'subdivision_name' => 'BOLANGIR', 'district_id' => '2409', 'district_name' => 'BOLANGIR'],
            ['subdivision_id' => 26, 'subdivision_name' => 'PATNAGARH', 'district_id' => '2409', 'district_name' => 'BOLANGIR'],
            ['subdivision_id' => 27, 'subdivision_name' => 'TITILAGARH', 'district_id' => '2409', 'district_name' => 'BOLANGIR'],
            ['subdivision_id' => 28, 'subdivision_name' => 'SONEPUR', 'district_id' => '2427', 'district_name' => 'SONEPUR'],
            ['subdivision_id' => 29, 'subdivision_name' => 'BIRMAHARAJPUR', 'district_id' => '2427', 'district_name' => 'SONEPUR'],
            ['subdivision_id' => 30, 'subdivision_name' => 'SAMBALPUR', 'district_id' => '2401', 'district_name' => 'SAMBALPUR'],
            ['subdivision_id' => 31, 'subdivision_name' => 'RAIRAKHOL', 'district_id' => '2401', 'district_name' => 'SAMBALPUR'],
            ['subdivision_id' => 32, 'subdivision_name' => 'KUCHINDA', 'district_id' => '2401', 'district_name' => 'SAMBALPUR'],
            ['subdivision_id' => 33, 'subdivision_name' => 'BARGARH', 'district_id' => '2414', 'district_name' => 'BARGARH'],
            ['subdivision_id' => 34, 'subdivision_name' => 'PADMAPUR', 'district_id' => '2414', 'district_name' => 'BARGARH'],
            ['subdivision_id' => 35, 'subdivision_name' => 'ANANDAPUR', 'district_id' => '2403', 'district_name' => 'KENDUJHAR'],
            ['subdivision_id' => 36, 'subdivision_name' => 'CHAMPUA', 'district_id' => '2403', 'district_name' => 'KENDUJHAR'],
            ['subdivision_id' => 37, 'subdivision_name' => 'KEONJHAR', 'district_id' => '2403', 'district_name' => 'KENDUJHAR'],
            ['subdivision_id' => 38, 'subdivision_name' => 'PANPOSH', 'district_id' => '2402', 'district_name' => 'SUNDARGARH'],
            ['subdivision_id' => 39, 'subdivision_name' => 'SUNDARGARH', 'district_id' => '2402', 'district_name' => 'SUNDARGARH'],
            ['subdivision_id' => 40, 'subdivision_name' => 'BONAI', 'district_id' => '2402', 'district_name' => 'SUNDARGARH'],
            ['subdivision_id' => 41, 'subdivision_name' => 'JHARSUGUDA', 'district_id' => '2415', 'district_name' => 'JHARSUGUDA'],
            ['subdivision_id' => 42, 'subdivision_name' => 'DEOGARH', 'district_id' => '2416', 'district_name' => 'DEOGARH'],
            ['subdivision_id' => 43, 'subdivision_name' => 'BHAWANIPATNA', 'district_id' => '2410', 'district_name' => 'KALAHANDI'],
            ['subdivision_id' => 44, 'subdivision_name' => 'DHARMAGARH', 'district_id' => '2410', 'district_name' => 'KALAHANDI'],
            ['subdivision_id' => 45, 'subdivision_name' => 'NUAPADA', 'district_id' => '2428', 'district_name' => 'NUAPADA'],
            ['subdivision_id' => 46, 'subdivision_name' => 'BERHAMPUR', 'district_id' => '2412', 'district_name' => 'GANJAM'],
            ['subdivision_id' => 47, 'subdivision_name' => 'CHHATRAPUR', 'district_id' => '2412', 'district_name' => 'GANJAM'],
            ['subdivision_id' => 48, 'subdivision_name' => 'BHANJANAGAR', 'district_id' => '2412', 'district_name' => 'GANJAM'],
            ['subdivision_id' => 49, 'subdivision_name' => 'PARALA-KHEMUNDI', 'district_id' => '2424', 'district_name' => 'GAJAPATI'],
            ['subdivision_id' => 50, 'subdivision_name' => 'KORAPUT', 'district_id' => '2411', 'district_name' => 'KORAPUT'],
            ['subdivision_id' => 51, 'subdivision_name' => 'JEYPORE', 'district_id' => '2411', 'district_name' => 'KORAPUT'],
            ['subdivision_id' => 52, 'subdivision_name' => 'MALKANGIRI', 'district_id' => '2431', 'district_name' => 'MALKANGIRI'],
            ['subdivision_id' => 53, 'subdivision_name' => 'RAYAGADA', 'district_id' => '2429', 'district_name' => 'RAYAGADA'],
            ['subdivision_id' => 54, 'subdivision_name' => 'GUNUPUR', 'district_id' => '2429', 'district_name' => 'RAYAGADA'],
            ['subdivision_id' => 55, 'subdivision_name' => 'NAWRANGPUR', 'district_id' => '2430', 'district_name' => 'NABARANGAPUR'],
            ['subdivision_id' => 56, 'subdivision_name' => 'BALIGUDA', 'district_id' => '2408', 'district_name' => 'KANDHAMAL'],
            ['subdivision_id' => 57, 'subdivision_name' => 'KONDHMAL', 'district_id' => '2408', 'district_name' => 'KANDHAMAL'],
            ['subdivision_id' => 58, 'subdivision_name' => 'BOUDH', 'district_id' => '2426', 'district_name' => 'BOUDH'],
        ];

        $subdivisionData = [];
        
        foreach ($subdivisions as $subdivision) {
            $subdivisionData[] = array_merge($subdivision, [
                'state_id' => 228,
                'is_active' => 'active',
                'status' => 1,
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        DB::table('subdivisions')->insert($subdivisionData);
    }
}
