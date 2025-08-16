<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = [
            14005, 14006, 14007, 14008, 14009, 14010, 14011, 14012, 14013, 14014,
            14015, 14016, 14017, 14018, 14019, 14020, 14021, 14022, 14023, 14024,
            14025, 14026, 14027, 14028, 14029, 14030, 14031, 14032, 14033, 14034,
            14035, 14036, 14037, 14038, 14039, 14040, 14041, 14042, 14043, 14044,
            14045, 14046, 14047, 14048, 14049, 14050, 14051, 14052, 14053, 14054,
            14055, 14056, 14057, 14058, 14059, 14060, 14061, 14062, 14063, 14064,
            14065, 14066, 14067, 14068, 14069, 14070, 14071, 14072, 14073, 14074,
            14075, 14076, 14077, 14078, 14079, 14080, 14081, 14082, 14083, 14084,
            14085, 14086, 14087, 14088, 14089, 14090, 14091, 14092, 14093, 14094,
            14095, 14096, 14097, 14098, 14099, 14100, 14101, 14102, 14103, 14104,
            14105, 14106, 14107, 14108,
        ];

        DB::table('users')
        ->whereIn('user_table_id', $userIds)
        ->update([
            'role_id' => 22,
            'role_name' => 'SpecialSchool'
        ]);

        $DdrcUserIds = [
            14248, 14249, 14250, 14251, 14252, 14253, 14254, 14255, 14256, 14257,
        ];

        DB::table('users')
        ->whereIn('user_table_id', $DdrcUserIds)
        ->update([
            'role_id' => 23,
            'role_name' => 'DDRC'
        ]);

        $ArcUserIds = [
            14240, 14241, 14242, 14243,
        ];

        DB::table('users')
        ->whereIn('user_table_id', $ArcUserIds)
        ->update([
            'role_id' => 24,
            'role_name' => 'ARC'
        ]);

        $ddrcMapping = [
            'DDRC_BHADRAK'     => 2417,
            'DDRC_DHENKANAL'   => 2407,
            'DDRC_GANJAM'      => 2412,
            'DDRC_KALAHANDI'   => 2410,
            'DDRC_KANDHAMAL'   => 2408,
            'DDRC_KHORDHA'     => 2423,
            'DDRC_KORAPUT'     => 2411,
            'DDRC_MAYURBHANJ'  => 2404,
            'DDRC_NABARANGAPUR'=> 2430,
            'DDRC_SAMBALPUR'   => 2401,
        ];

        foreach ($ddrcMapping as $userId => $districtId) {
            DB::table('users')
            ->where('user_id', $userId)
            ->update([
                'posted_district' => $districtId,
                'role_id' => 23,
                'role_name' => 'DDRC',
            ]);
        }
    }
}
