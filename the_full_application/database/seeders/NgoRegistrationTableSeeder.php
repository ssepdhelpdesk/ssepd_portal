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

class NgoRegistrationTableSeeder extends Seeder
{
/**
* Run the database seeds.
*/
public function run(): void
{
    $ngoregistrations = [
        ['ngo_org_id' => 1, 'ngo_category' => 1, 'ngo_org_name' => 'Aama katha', 'ngo_org_pan' => 'CNEPP6647M', 'ngo_org_pan_file' => 'ngo_files/SSEPD_NGO_21_11_2024_12867/y3LldDyauxeJqwuUKP3DeLGEJ8BPfp2KqqPNuqHi.pdf', 'ngo_org_email' => 'aamakatha@gmail.com', 'ngo_org_phone' => '7008731607', 'ngo_org_website' => 'www.aamakatha.com',  'ngo_registered_with' => 2, 'ngo_other_reg_act' => NULL, 'ngo_type_of_vo_or_ngo' => 3, 'ngo_reg_no' => 'AHTTA73', 'ngo_system_gen_reg_no' => 'SSEPD/NGO/21/11/2024/12867', 'ngo_file_rc' => 'ngo_files/SSEPD_NGO_21_11_2024_12867/M4kJTez4BZVK10W018OfxZPvc7Zjd65UxZxplYO4.pdf', 'ngo_date_of_registration' => '2024-07-23', 'nature_of_organisation' => '2,6', 'nature_of_organisation_other' => 'Other Org 1', 'ngo_organisation_type' => 1, 'ngo_file_byelaws' => 'ngo_files/SSEPD_NGO_21_11_2024_12867/M4kJTez4BZVK10W018OfxZPvc7Zjd65UxZxplYO4.pdf', 'ngo_parent_organisation' => 'mata rani', 'ngo_reg_velidity_available' => 1, 'ngo_address_type' => 1, 'state_id' => 228, 'district_id' => 2417, 'block_id' => 2417002, 'gp_id' => 2417002026, 'village_id' => 394309, 'pin' => 751001, 'ngo_postal_address_at' => 'BOXMA', 'ngo_postal_address_post' => 'JAMANKIRA', 'ngo_postal_address_ps' => 'KUCHINDA', 'ngo_postal_address_pin' => 768222, 'created_date' => '2024-11-21', 'created_time' => '17:28:45', 'user_table_id' => 9585],
        ['ngo_org_id' => 2, 'ngo_category' => 1, 'ngo_org_name' => 'RANKANIDHI BLIND, DEAF AND DUMB VIDYALAYA', 'ngo_org_pan' => 'AABAR2437G', 'ngo_org_pan_file' => 'ngo_files/SSEPD_NGO_22_11_2024_29783/WalrTqMNSoopTFy0WieZNjbTnkKiB8aL8ckfuo4u.pdf', 'ngo_org_email' => 'anadich.2017@gmail.com', 'ngo_org_phone' => '9438253907', 'ngo_org_website' => 'www.rsbddbdk.blogspot.com',  'ngo_registered_with' => 2, 'ngo_other_reg_act' => NULL, 'ngo_type_of_vo_or_ngo' => 3, 'ngo_reg_no' => 'BLS2142105198889', 'ngo_system_gen_reg_no' => 'SSEPD/NGO/22/11/2024/29783', 'ngo_file_rc' => 'ngo_files/SSEPD_NGO_22_11_2024_29783/vsM2sq5p9Dw6crQNbIO8i62KnVlhijcaTItRH06K.pdf', 'ngo_date_of_registration' => '1988-07-18', 'nature_of_organisation' => '5,6', 'nature_of_organisation_other' => 'Other Org 2', 'ngo_organisation_type' => 2, 'ngo_file_byelaws' => 'ngo_files/SSEPD_NGO_21_11_2024_12867/M4kJTez4BZVK10W018OfxZPvc7Zjd65UxZxplYO4.pdf', 'ngo_parent_organisation' => 'ADDITINAL RESISTAR OF SOCIETIES', 'ngo_reg_velidity_available' => 1, 'ngo_address_type' => 1, 'state_id' => 228, 'district_id' => 2417, 'block_id' => 2417002, 'gp_id' => 2417002004, 'village_id' => 394859, 'pin' => 756114, 'ngo_postal_address_at' => 'GOPALPUR', 'ngo_postal_address_post' => 'GANIJANGA', 'ngo_postal_address_ps' => 'BONTH', 'ngo_postal_address_pin' => 756114, 'created_date' => '2024-11-22', 'created_time' => '13:20:38', 'user_table_id' => 9586],
        ['ngo_org_id' => 3, 'ngo_category' => 1, 'ngo_org_name' => 'ADARSHA SHISHU MANDIR', 'ngo_org_pan' => 'AAAAA5306N', 'ngo_org_pan_file' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/0Hng2sqkHdfapDF0jdhTTsm1nkG2bhtGnDtcYrb6.pdf', 'ngo_org_email' => 'asm2005smailbox@rediffmail.com', 'ngo_org_phone' => '6632411000', 'ngo_org_website' => 'www.asmodisha.org',  'ngo_registered_with' => 10, 'ngo_other_reg_act' => 'ODISHA ASSOCIATION', 'ngo_type_of_vo_or_ngo' => 2, 'ngo_reg_no' => '4786/107-1994-95', 'ngo_system_gen_reg_no' => 'SSEPD/NGO/25/11/2024/23373', 'ngo_file_rc' => 'ngo_files/SSEPD_NGO_25_11_2024_23373/fzQyHSoG1MERuv17lEyqMVu7aAl3iiOnGDkKWeFQ.pdf', 'ngo_date_of_registration' => '1995-02-07', 'nature_of_organisation' => '3,6', 'nature_of_organisation_other' => 'Other Org 3', 'ngo_organisation_type' => 1, 'ngo_file_byelaws' => 'ngo_files/SSEPD_NGO_21_11_2024_12867/M4kJTez4BZVK10W018OfxZPvc7Zjd65UxZxplYO4.pdf', 'ngo_parent_organisation' => 'ADARSHA SHISHU MANDIR', 'ngo_reg_velidity_available' => 1, 'ngo_address_type' => 1, 'state_id' => 228, 'district_id' => 2401, 'block_id' => 2401028, 'gp_id' => 2401028002, 'village_id' => 382300, 'pin' => 768005, 'ngo_postal_address_at' => 'Netaji Nagar, NSCB College Road', 'ngo_postal_address_post' => 'Dhanupali', 'ngo_postal_address_ps' => 'Dhanupali', 'ngo_postal_address_pin' => 768005, 'created_date' => '2024-11-25', 'created_time' => '11:43:13', 'user_table_id' => 9587],
    ];

    $ngoregistrationsData = [];

    foreach ($ngoregistrations as $ngoregistration) {
        $userId = 'ngo_' . last(explode('/', $ngoregistration['ngo_system_gen_reg_no']));

        $formCompleted = 1;
        if ($ngoregistration['ngo_org_id'] == 3) {
        $formCompleted = 3;
    }

        $ngoregistrationsData[] = array_merge($ngoregistration, [
            'created_by' => 1,
            'application_stage_id' => 1,
            'no_of_form_completed' => $formCompleted,
            'status' => 1,
            'created_at' => Carbon::now('Asia/Kolkata'),
            'updated_at' => Carbon::now('Asia/Kolkata'),
        ]);

        $user = User::create([
            'name' => $ngoregistration['ngo_org_name'],
            'email' => $ngoregistration['ngo_org_email'],
            'user_id' => $userId,
            'password' => Hash::make('123456'),
            'mobile_no' => $ngoregistration['ngo_org_phone'],
            'email_verified_at' =>  now()->setTimezone('Asia/Kolkata')->toDateTimeString(),
            'created_at' => Carbon::now('Asia/Kolkata'),
            'updated_at' => Carbon::now('Asia/Kolkata'),
        ]);

        $role = Role::find(15);
        if ($role) {
            $user->assignRole($role->name);
        } else {
            throw new Exception("Role with ID 15 does not exist.");
        }
    }

    DB::table('ngo_registrations')->insert($ngoregistrationsData);
}
}
