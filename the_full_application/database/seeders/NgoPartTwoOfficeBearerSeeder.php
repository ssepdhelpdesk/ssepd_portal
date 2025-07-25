<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class NgoPartTwoOfficeBearerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ngoparttwobearerregistrations = [
            ['ngo_org_id' => 3, 'ngo_tbl_id' => 3, 'ngo_system_gen_reg_no' => 'SSEPD/NGO/25/11/2024/23373', 'office_bearer_name' => 'Harish Chandra Das', 'office_bearer_gender' => 1, 'office_bearer_email' => 'harish.asmsbp@gmail.com', 'office_bearer_phone' => '9776922208', 'office_bearer_designation' => 1, 'office_bearer_key_designation' => 1, 'office_bearer_date_of_association' => '2014-01-09', 'office_bearer_pan' => 'CNEPP6647M', 'office_bearer_pan_file' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/j7jtLmPMV1ne0hFDvv5Ml0Rw1qn2qcJGOtNTuVc9.pdf', 'office_bearer_name_as_aadhar' => 'Harish Chandra Das', 'office_bearer_dob' => '1958-02-01', 'office_bearer_aadhar' => '381669807370', 'office_bearer_aadhar_file' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/ilbBbsGIjMS3RPHGWUukOCVPDmfz4Kw3r1dyZ4JH.pdf', 'want_to_add_another_bearer' => 1, 'created_date' => '2024-11-27', 'created_time' => '17:46:08', 'created_by' => 9587],
            ['ngo_org_id' => 3, 'ngo_tbl_id' => 3, 'ngo_system_gen_reg_no' => 'SSEPD/NGO/25/11/2024/23373', 'office_bearer_name' => 'Golaka Behera', 'office_bearer_gender' => 1, 'office_bearer_email' => 'golaka.asmsbp@gmail.com', 'office_bearer_phone' => '9861922208', 'office_bearer_designation' => 4, 'office_bearer_key_designation' => 2, 'office_bearer_date_of_association' => '2014-01-09', 'office_bearer_pan' => 'CNEPP6647N', 'office_bearer_pan_file' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/j70Hng2sqkHdfapDF0jdhTTsm1nkG2bhtGnDtcYrb1.pdf', 'office_bearer_name_as_aadhar' => 'Golaka Behera', 'office_bearer_dob' => '1984-12-09', 'office_bearer_aadhar' => '661669807556', 'office_bearer_aadhar_file' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/0Hng2sqkHdfapDF0jdhTTsm1nkG2bhtGnDtcYrb3.pdf', 'want_to_add_another_bearer' => 1, 'created_date' => '2024-11-27', 'created_time' => '17:46:08', 'created_by' => 9587],
            ['ngo_org_id' => 3, 'ngo_tbl_id' => 3, 'ngo_system_gen_reg_no' => 'SSEPD/NGO/25/11/2024/23373', 'office_bearer_name' => 'Narayani Singh', 'office_bearer_gender' => 2, 'office_bearer_email' => 'narayani.asmsbp@gmail.com', 'office_bearer_phone' => '8456922208', 'office_bearer_designation' => 8, 'office_bearer_key_designation' => 3, 'office_bearer_date_of_association' => '2014-01-09', 'office_bearer_pan' => 'CNEPP6647J', 'office_bearer_pan_file' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/fzQyHSoG1MERuv17lEyqMVu7aAl3iiOnGDkKWeF8.pdf', 'office_bearer_name_as_aadhar' => 'Narayani SIngh', 'office_bearer_dob' => '1972-08-10', 'office_bearer_aadhar' => '981669808459', 'office_bearer_aadhar_file' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/fzQyHSoG1MERuv17lEyqMVu7aAl3iiOnGDkKWeF23.pdf', 'want_to_add_another_bearer' => 1, 'created_date' => '2024-11-27', 'created_time' => '17:46:08', 'created_by' => 9587],
        ];

        $ngoparttwobearerregistrationsData = [];

        foreach ($ngoparttwobearerregistrations as $ngobearerregistration) {
            $ngoparttwobearerregistrationsData[] = array_merge($ngobearerregistration, [
                'status' => 1,
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        DB::table('ngo_part_two_office_bearers')->insert($ngoparttwobearerregistrationsData);
    }
}
