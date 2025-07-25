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

class NgoPartThreeActRegistrationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ngopartthreeactregistrations = [
            ['ngo_org_id' => 3,  'ngo_tbl_id' => 3,  'ngo_system_gen_reg_no' => 'SSEPD/NGO/25/11/2024/23373',  'authority_one' => NULL,  'regd_no_one' => NULL,  'regd_date_one' => NULL,  'validity_date_one' => NULL,  'regd_certificate_file_one' => NULL,  'regd_certificate_file_one_path' => NULL,  'authority_two' => NULL,  'regd_no_two' => NULL,  'regd_date_two' => NULL,  'validity_date_two' => NULL,  'regd_certificate_file_two' => NULL,  'regd_certificate_file_two_path' => NULL,  'authority_three' => NULL,  'regd_no_three' => NULL,  'regd_date_three' => NULL,  'validity_date_three' => NULL,  'regd_certificate_file_three' => NULL,  'regd_certificate_file_three_path' => NULL,  'authority_four' => NULL,  'regd_no_four' => NULL,  'regd_date_four' => NULL,  'validity_date_four' => NULL,  'regd_certificate_file_four' => NULL,  'regd_certificate_file_four_path' => NULL,  'authority_five' => NULL,  'regd_no_five' => NULL,  'regd_date_five' => NULL,  'validity_date_five' => NULL,  'regd_certificate_file_five' => NULL,  'regd_certificate_file_five_path' => NULL,  'authority_six' => NULL,  'regd_no_six' => NULL,  'regd_date_six' => NULL,  'validity_date_six' => NULL,  'regd_certificate_file_six' => NULL,  'regd_certificate_file_six_path' => NULL,  'authority_seven' => NULL,  'regd_no_seven' => NULL,  'regd_date_seven' => NULL,  'validity_date_seven' => NULL,  'regd_certificate_file_seven' => NULL,  'regd_certificate_file_seven_path' => NULL,  'authority_eight' => NULL,  'regd_no_eight' => NULL,  'regd_date_eight' => NULL,  'validity_date_eight' => NULL,  'regd_certificate_file_eight' => NULL,  'regd_certificate_file_eight_path' => NULL,  'authority_nine' => NULL,  'regd_no_nine' => NULL,  'regd_date_nine' => NULL,  'validity_date_nine' => NULL,  'regd_certificate_file_nine' => NULL,  'regd_certificate_file_nine_path' => NULL,  'authority_other_act' => 'Societies-Registration-Act-1860',  'authority_other' => 'ADDITIONAL DISTRICT MAGISTRATE',  'regd_no_other' => '4030-160 OF 1992-1993',  'regd_date_other' => '1992-09-18',  'validity_date_other' => '2025-05-20',  'regd_certificate_file_other' => 'ngo_regd_certificate_file_other_SR53akZqSNtVXx3YSKCdIjycLHxoDhLB5M0BUnZ1.pdf',  'regd_certificate_file_other_path' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/ngo_regd_certificate_file_other_SR53akZqSNtVXx3YSKCdIjycLHxoDhLB5M0BUnZ1.pdf',  'created_date' => '2025-05-20',  'created_time' => '13:12:51',  'created_by' => 9587],
        ];
        $ngopartthreeactregistrationsData = [];

        foreach ($ngopartthreeactregistrations as $ngoactregistration) {
            $ngopartthreeactregistrationsData[] = array_merge($ngoactregistration, [
                'status' => 1,
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        DB::table('ngo_part_three_act_registrations')->insert($ngopartthreeactregistrationsData);
  }
}
