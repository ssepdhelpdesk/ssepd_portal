<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MunicipalityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipalities = [
            ['municipality_id' => 1, 'municipality_name' => 'Sambalpur Municipal Corporation', 'district_id' => 2401, 'is_active' => 'active', 'subdivision_id' => 30, 'nic_municipality_code' => 24010002, 'ulb_type' => 2],
            ['municipality_id' => 2, 'municipality_name' => 'Raurkela', 'district_id' => 2402, 'is_active' => 'Inactive', 'subdivision_id' => 38, 'nic_municipality_code' => 0, 'ulb_type' => 1],
            ['municipality_id' => 3, 'municipality_name' => 'Anandapur Municipality', 'district_id' => 2403, 'is_active' => 'active', 'subdivision_id' => 35, 'nic_municipality_code' => 24030001, 'ulb_type' => 1],
            ['municipality_id' => 4, 'municipality_name' => 'Barbil Municipality', 'district_id' => 2403, 'is_active' => 'active', 'subdivision_id' => 36, 'nic_municipality_code' => 24030004, 'ulb_type' => 1],
            ['municipality_id' => 5, 'municipality_name' => 'Baripada Municipality', 'district_id' => 2404, 'is_active' => 'active', 'subdivision_id' => 15, 'nic_municipality_code' => 24040001, 'ulb_type' => 1],
            ['municipality_id' => 6, 'municipality_name' => 'Rairangpur Municipality', 'district_id' => 2404, 'is_active' => 'active', 'subdivision_id' => 14, 'nic_municipality_code' => 24040002, 'ulb_type' => 1],
            ['municipality_id' => 7, 'municipality_name' => 'Balasore Municipality', 'district_id' => 2405, 'is_active' => 'active', 'subdivision_id' => 7, 'nic_municipality_code' => 24050003, 'ulb_type' => 1],
            ['municipality_id' => 8, 'municipality_name' => 'Choudwar Municipality', 'district_id' => 2406, 'is_active' => 'active', 'subdivision_id' => 1, 'nic_municipality_code' => 24060004, 'ulb_type' => 1],
            ['municipality_id' => 9, 'municipality_name' => 'Dhenkanal Municipality', 'district_id' => 2407, 'is_active' => 'active', 'subdivision_id' => 18, 'nic_municipality_code' => 24070005, 'ulb_type' => 1],
            ['municipality_id' => 10, 'municipality_name' => 'Phulbani Municipality', 'district_id' => 2408, 'is_active' => 'active', 'subdivision_id' => 57, 'nic_municipality_code' => 24080002, 'ulb_type' => 1],
            ['municipality_id' => 11, 'municipality_name' => 'Balangir Municipalities', 'district_id' => 2409, 'is_active' => 'active', 'subdivision_id' => 25, 'nic_municipality_code' => 24090002, 'ulb_type' => 1],
            ['municipality_id' => 12, 'municipality_name' => 'Bhawanipatna Municipality', 'district_id' => 2410, 'is_active' => 'active', 'subdivision_id' => 43, 'nic_municipality_code' => 24100003, 'ulb_type' => 1],
            ['municipality_id' => 13, 'municipality_name' => 'Jeypore Municipality', 'district_id' => 2411, 'is_active' => 'active', 'subdivision_id' => 51, 'nic_municipality_code' => 24110001, 'ulb_type' => 1],
            ['municipality_id' => 14, 'municipality_name' => 'Koraput Municipality', 'district_id' => 2411, 'is_active' => 'active', 'subdivision_id' => 50, 'nic_municipality_code' => 24110003, 'ulb_type' => 1],
            ['municipality_id' => 15, 'municipality_name' => 'Berhampur Municipality', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 46, 'nic_municipality_code' => 24120003, 'ulb_type' => 2],
            ['municipality_id' => 16, 'municipality_name' => 'Puri Municipality', 'district_id' => 2413, 'is_active' => 'active', 'subdivision_id' => 10, 'nic_municipality_code' => 24130005, 'ulb_type' => 1],
            ['municipality_id' => 17, 'municipality_name' => 'Bargarh Municipality', 'district_id' => 2414, 'is_active' => 'active', 'subdivision_id' => 33, 'nic_municipality_code' => 24140002, 'ulb_type' => 1],
            ['municipality_id' => 18, 'municipality_name' => 'Belpahar Municipality', 'district_id' => 2415, 'is_active' => 'active', 'subdivision_id' => 41, 'nic_municipality_code' => 24150001, 'ulb_type' => 1],
            ['municipality_id' => 19, 'municipality_name' => 'Brajrajnagar Municipality', 'district_id' => 2415, 'is_active' => 'active', 'subdivision_id' => 41, 'nic_municipality_code' => 24150002, 'ulb_type' => 1],
            ['municipality_id' => 20, 'municipality_name' => 'Deogarh Municipality', 'district_id' => 2416, 'is_active' => 'active', 'subdivision_id' => 42, 'nic_municipality_code' => 24160001, 'ulb_type' => 1],
            ['municipality_id' => 21, 'municipality_name' => 'Basudevpur Municipality', 'district_id' => 2417, 'is_active' => 'active', 'subdivision_id' => 9, 'nic_municipality_code' => 24170001, 'ulb_type' => 1],
            ['municipality_id' => 22, 'municipality_name' => 'Bhadrak Municipality', 'district_id' => 2417, 'is_active' => 'active', 'subdivision_id' => 9, 'nic_municipality_code' => 24170002, 'ulb_type' => 1],
            ['municipality_id' => 23, 'municipality_name' => 'Kendrapara Municipality', 'district_id' => 2418, 'is_active' => 'active', 'subdivision_id' => 6, 'nic_municipality_code' => 24180001, 'ulb_type' => 1],
            ['municipality_id' => 24, 'municipality_name' => 'Pattamundai Municipality', 'district_id' => 2418, 'is_active' => 'active', 'subdivision_id' => 6, 'nic_municipality_code' => 24180002, 'ulb_type' => 1],
            ['municipality_id' => 25, 'municipality_name' => 'Jagatsinghpur Municipality', 'district_id' => 2419, 'is_active' => 'active', 'subdivision_id' => 5, 'nic_municipality_code' => 24190002, 'ulb_type' => 1],
            ['municipality_id' => 26, 'municipality_name' => 'Paradeep Municipality', 'district_id' => 2419, 'is_active' => 'active', 'subdivision_id' => 5, 'nic_municipality_code' => 24190001, 'ulb_type' => 1],
            ['municipality_id' => 27, 'municipality_name' => 'Jajpur Muncipality', 'district_id' => 2420, 'is_active' => 'active', 'subdivision_id' => 4, 'nic_municipality_code' => 24200001, 'ulb_type' => 1],
            ['municipality_id' => 28, 'municipality_name' => 'Vyasanagar Muncipality', 'district_id' => 2420, 'is_active' => 'active', 'subdivision_id' => 4, 'nic_municipality_code' => 24200002, 'ulb_type' => 1],
            ['municipality_id' => 29, 'municipality_name' => 'Angul Municipality', 'district_id' => 2421, 'is_active' => 'active', 'subdivision_id' => 21, 'nic_municipality_code' => 24210001, 'ulb_type' => 1],
            ['municipality_id' => 30, 'municipality_name' => 'Notified Area Council,Daspalla', 'district_id' => 2422, 'is_active' => 'active', 'subdivision_id' => 13, 'nic_municipality_code' => 24220004, 'ulb_type' => 1],
            ['municipality_id' => 31, 'municipality_name' => 'Bhubaneswar Municipal Corporation', 'district_id' => 2423, 'is_active' => 'active', 'subdivision_id' => 11, 'nic_municipality_code' => 24230002, 'ulb_type' => 2],
            ['municipality_id' => 32, 'municipality_name' => 'Jatni Municipal Council', 'district_id' => 2423, 'is_active' => 'active', 'subdivision_id' => 11, 'nic_municipality_code' => 24230003, 'ulb_type' => 1],
            ['municipality_id' => 33, 'municipality_name' => 'Paralakhemundi Municipality', 'district_id' => 2424, 'is_active' => 'active', 'subdivision_id' => 49, 'nic_municipality_code' => 24240002, 'ulb_type' => 1],
            ['municipality_id' => 34, 'municipality_name' => 'Boudh N', 'district_id' => 2426, 'is_active' => 'active', 'subdivision_id' => 58, 'nic_municipality_code' => 24260001, 'ulb_type' => 1],
            ['municipality_id' => 35, 'municipality_name' => 'Sonepur Municipality', 'district_id' => 2427, 'is_active' => 'active', 'subdivision_id' => 28, 'nic_municipality_code' => 24270003, 'ulb_type' => 1],
            ['municipality_id' => 36, 'municipality_name' => 'Notified Area Council,Khariar', 'district_id' => 2428, 'is_active' => 'active', 'subdivision_id' => 45, 'nic_municipality_code' => 24280004, 'ulb_type' => 1],
            ['municipality_id' => 37, 'municipality_name' => 'Rayagada Municipality', 'district_id' => 2429, 'is_active' => 'active', 'subdivision_id' => 53, 'nic_municipality_code' => 24290003, 'ulb_type' => 1],
            ['municipality_id' => 38, 'municipality_name' => 'Municipality Nabarangpur', 'district_id' => 2430, 'is_active' => 'active', 'subdivision_id' => 55, 'nic_municipality_code' => 24300001, 'ulb_type' => 1],
            ['municipality_id' => 39, 'municipality_name' => 'Malkangiri Municipality', 'district_id' => 2431, 'is_active' => 'active', 'subdivision_id' => 52, 'nic_municipality_code' => 24310003, 'ulb_type' => 1],
            ['municipality_id' => 40, 'municipality_name' => 'Raurkela Municipality', 'district_id' => 2402, 'is_active' => 'active', 'subdivision_id' => 38, 'nic_municipality_code' => 24020003, 'ulb_type' => 2],
            ['municipality_id' => 41, 'municipality_name' => 'Sundargarh Municipality', 'district_id' => 2402, 'is_active' => 'active', 'subdivision_id' => 39, 'nic_municipality_code' => 24020004, 'ulb_type' => 1],
            ['municipality_id' => 42, 'municipality_name' => 'Rajagangapur Municipality', 'district_id' => 2402, 'is_active' => 'active', 'subdivision_id' => 39, 'nic_municipality_code' => 24020002, 'ulb_type' => 1],
            ['municipality_id' => 43, 'municipality_name' => 'Biramitrapur', 'district_id' => 2402, 'is_active' => 'active', 'subdivision_id' => 38, 'nic_municipality_code' => 24020001, 'ulb_type' => 1],
            ['municipality_id' => 44, 'municipality_name' => 'Jaleswar Muncipality', 'district_id' => 2405, 'is_active' => 'active', 'subdivision_id' => 7, 'nic_municipality_code' => 24050006, 'ulb_type' => 1],
            ['municipality_id' => 45, 'municipality_name' => 'Municipality Umerkote', 'district_id' => 2430, 'is_active' => 'active', 'subdivision_id' => 55, 'nic_municipality_code' => 24300002, 'ulb_type' => 1],
            ['municipality_id' => 46, 'municipality_name' => 'Notified Area Council,Khariar Road', 'district_id' => 2428, 'is_active' => 'active', 'subdivision_id' => 45, 'nic_municipality_code' => 24280001, 'ulb_type' => 1],
            ['municipality_id' => 47, 'municipality_name' => 'Notified Area Council,Odagaon', 'district_id' => 2422, 'is_active' => 'active', 'subdivision_id' => 13, 'nic_municipality_code' => 24220006, 'ulb_type' => 1],
            ['municipality_id' => 48, 'municipality_name' => 'Athamallik N', 'district_id' => 2421, 'is_active' => 'active', 'subdivision_id' => 23, 'nic_municipality_code' => 24210002, 'ulb_type' => 1],
            ['municipality_id' => 49, 'municipality_name' => 'Talcher M', 'district_id' => 2421, 'is_active' => 'active', 'subdivision_id' => 22, 'nic_municipality_code' => 24210003, 'ulb_type' => 1],
            ['municipality_id' => 50, 'municipality_name' => 'Niligiri N', 'district_id' => 2405, 'is_active' => 'active', 'subdivision_id' => 8, 'nic_municipality_code' => 24050001, 'ulb_type' => 1],
            ['municipality_id' => 51, 'municipality_name' => 'Soro M', 'district_id' => 2405, 'is_active' => 'active', 'subdivision_id' => 7, 'nic_municipality_code' => 24050002, 'ulb_type' => 1],
            ['municipality_id' => 52, 'municipality_name' => 'ATTABIRA NAC', 'district_id' => 2414, 'is_active' => 'active', 'subdivision_id' => 33, 'nic_municipality_code' => 24140004, 'ulb_type' => 1],
            ['municipality_id' => 53, 'municipality_name' => 'BARPALI NAC', 'district_id' => 2414, 'is_active' => 'active', 'subdivision_id' => 33, 'nic_municipality_code' => 24140001, 'ulb_type' => 1],
            ['municipality_id' => 54, 'municipality_name' => 'PADAMPUR NAC', 'district_id' => 2414, 'is_active' => 'active', 'subdivision_id' => 34, 'nic_municipality_code' => 24140003, 'ulb_type' => 1],
            ['municipality_id' => 55, 'municipality_name' => 'Titilgarh M', 'district_id' => 2409, 'is_active' => 'active', 'subdivision_id' => 27, 'nic_municipality_code' => 24090001, 'ulb_type' => 1],
            ['municipality_id' => 56, 'municipality_name' => 'Kantabanji N', 'district_id' => 2409, 'is_active' => 'active', 'subdivision_id' => 27, 'nic_municipality_code' => 24090006, 'ulb_type' => 1],
            ['municipality_id' => 57, 'municipality_name' => 'Patnagarh N', 'district_id' => 2409, 'is_active' => 'active', 'subdivision_id' => 26, 'nic_municipality_code' => 24090003, 'ulb_type' => 1],
            ['municipality_id' => 58, 'municipality_name' => 'Tusra N', 'district_id' => 2409, 'is_active' => 'active', 'subdivision_id' => 25, 'nic_municipality_code' => 24090005, 'ulb_type' => 1],
            ['municipality_id' => 59, 'municipality_name' => 'Banki N', 'district_id' => 2406, 'is_active' => 'active', 'subdivision_id' => 3, 'nic_municipality_code' => 24060002, 'ulb_type' => 1],
            ['municipality_id' => 60, 'municipality_name' => 'Athagarh N', 'district_id' => 2406, 'is_active' => 'active', 'subdivision_id' => 2, 'nic_municipality_code' => 24060001, 'ulb_type' => 1],
            ['municipality_id' => 61, 'municipality_name' => 'Cuttack M', 'district_id' => 2406, 'is_active' => 'active', 'subdivision_id' => 1, 'nic_municipality_code' => 24060003, 'ulb_type' => 2],
            ['municipality_id' => 62, 'municipality_name' => 'Bhuban N', 'district_id' => 2407, 'is_active' => 'active', 'subdivision_id' => 20, 'nic_municipality_code' => 24070001, 'ulb_type' => 1],
            ['municipality_id' => 63, 'municipality_name' => 'Kamakhyanagar N', 'district_id' => 2407, 'is_active' => 'active', 'subdivision_id' => 20, 'nic_municipality_code' => 24070003, 'ulb_type' => 1],
            ['municipality_id' => 64, 'municipality_name' => 'Hindol N', 'district_id' => 2407, 'is_active' => 'active', 'subdivision_id' => 19, 'nic_municipality_code' => 24070004, 'ulb_type' => 1],
            ['municipality_id' => 65, 'municipality_name' => 'Kashinagar N', 'district_id' => 2424, 'is_active' => 'active', 'subdivision_id' => 49, 'nic_municipality_code' => 24240001, 'ulb_type' => 1],
            ['municipality_id' => 66, 'municipality_name' => 'Belaguntha N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 48, 'nic_municipality_code' => 24120002, 'ulb_type' => 1],
            ['municipality_id' => 67, 'municipality_name' => 'Bhanjagar N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 48, 'nic_municipality_code' => 24120004, 'ulb_type' => 1],
            ['municipality_id' => 68, 'municipality_name' => 'Buguda N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 48, 'nic_municipality_code' => 24120018, 'ulb_type' => 1],
            ['municipality_id' => 69, 'municipality_name' => 'Chartapur N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120005, 'ulb_type' => 1],
            ['municipality_id' => 70, 'municipality_name' => 'Chikiti N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 46, 'nic_municipality_code' => 24120006, 'ulb_type' => 1],
            ['municipality_id' => 71, 'municipality_name' => 'Digapahandi N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 46, 'nic_municipality_code' => 24120007, 'ulb_type' => 1],
            ['municipality_id' => 72, 'municipality_name' => 'Ganjam N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120008, 'ulb_type' => 1],
            ['municipality_id' => 73, 'municipality_name' => 'Gopalpur N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 46, 'nic_municipality_code' => 24120021, 'ulb_type' => 1],
            ['municipality_id' => 74, 'municipality_name' => 'Hinjilicut M', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120010, 'ulb_type' => 1],
            ['municipality_id' => 75, 'municipality_name' => 'K.S.Nagar N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120011, 'ulb_type' => 1],
            ['municipality_id' => 76, 'municipality_name' => 'Khallikote N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120012, 'ulb_type' => 1],
            ['municipality_id' => 77, 'municipality_name' => 'Kodal N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120013, 'ulb_type' => 1],
            ['municipality_id' => 78, 'municipality_name' => 'Polosara N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120014, 'ulb_type' => 1],
            ['municipality_id' => 79, 'municipality_name' => 'Purusottampur N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120020, 'ulb_type' => 1],
            ['municipality_id' => 80, 'municipality_name' => 'Rambha N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 47, 'nic_municipality_code' => 24120016, 'ulb_type' => 1],
            ['municipality_id' => 81, 'municipality_name' => 'Sorada N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 48, 'nic_municipality_code' => 24120017, 'ulb_type' => 1],
            ['municipality_id' => 82, 'municipality_name' => 'Balliguda N', 'district_id' => 2408, 'is_active' => 'active', 'subdivision_id' => 56, 'nic_municipality_code' => 24080003, 'ulb_type' => 1],
            ['municipality_id' => 83, 'municipality_name' => 'G.Udayagiri N', 'district_id' => 2408, 'is_active' => 'active', 'subdivision_id' => 56, 'nic_municipality_code' => 24080001, 'ulb_type' => 1],
            ['municipality_id' => 84, 'municipality_name' => 'CHAMPUA N', 'district_id' => 2403, 'is_active' => 'active', 'subdivision_id' => 36, 'nic_municipality_code' => 24030005, 'ulb_type' => 1],
            ['municipality_id' => 85, 'municipality_name' => 'KEONJHAR M', 'district_id' => 2403, 'is_active' => 'active', 'subdivision_id' => 37, 'nic_municipality_code' => 24030003, 'ulb_type' => 1],
            ['municipality_id' => 86, 'municipality_name' => 'Balugaon N', 'district_id' => 2423, 'is_active' => 'active', 'subdivision_id' => 12, 'nic_municipality_code' => 24230001, 'ulb_type' => 1],
            ['municipality_id' => 87, 'municipality_name' => 'Banapur N', 'district_id' => 2423, 'is_active' => 'active', 'subdivision_id' => 12, 'nic_municipality_code' => 24230005, 'ulb_type' => 1],
            ['municipality_id' => 88, 'municipality_name' => 'Khordha M', 'district_id' => 2423, 'is_active' => 'active', 'subdivision_id' => 12, 'nic_municipality_code' => 24230004, 'ulb_type' => 1],
            ['municipality_id' => 89, 'municipality_name' => 'Kotpad N', 'district_id' => 2411, 'is_active' => 'active', 'subdivision_id' => 51, 'nic_municipality_code' => 24110002, 'ulb_type' => 1],
            ['municipality_id' => 90, 'municipality_name' => 'Sunabeda M', 'district_id' => 2411, 'is_active' => 'active', 'subdivision_id' => 50, 'nic_municipality_code' => 24110005, 'ulb_type' => 1],
            ['municipality_id' => 91, 'municipality_name' => 'Karanjia N', 'district_id' => 2404, 'is_active' => 'active', 'subdivision_id' => 17, 'nic_municipality_code' => 24040004, 'ulb_type' => 1],
            ['municipality_id' => 92, 'municipality_name' => 'Udala N', 'district_id' => 2404, 'is_active' => 'active', 'subdivision_id' => 16, 'nic_municipality_code' => 24040003, 'ulb_type' => 1],
            ['municipality_id' => 93, 'municipality_name' => 'Khandapada N', 'district_id' => 2422, 'is_active' => 'active', 'subdivision_id' => 13, 'nic_municipality_code' => 24220001, 'ulb_type' => 1],
            ['municipality_id' => 94, 'municipality_name' => 'Nayagarh N', 'district_id' => 2422, 'is_active' => 'active', 'subdivision_id' => 13, 'nic_municipality_code' => 24220002, 'ulb_type' => 1],
            ['municipality_id' => 95, 'municipality_name' => 'Ranpur N', 'district_id' => 2422, 'is_active' => 'active', 'subdivision_id' => 13, 'nic_municipality_code' => 24220003, 'ulb_type' => 1],
            ['municipality_id' => 96, 'municipality_name' => 'Nuapada N', 'district_id' => 2428, 'is_active' => 'active', 'subdivision_id' => 45, 'nic_municipality_code' => 24280002, 'ulb_type' => 1],
            ['municipality_id' => 97, 'municipality_name' => 'Konark N', 'district_id' => 2413, 'is_active' => 'active', 'subdivision_id' => 10, 'nic_municipality_code' => 24130001, 'ulb_type' => 1],
            ['municipality_id' => 98, 'municipality_name' => 'Nimapara N', 'district_id' => 2413, 'is_active' => 'active', 'subdivision_id' => 10, 'nic_municipality_code' => 24130002, 'ulb_type' => 1],
            ['municipality_id' => 99, 'municipality_name' => 'Pipili N', 'district_id' => 2413, 'is_active' => 'active', 'subdivision_id' => 10, 'nic_municipality_code' => 24130003, 'ulb_type' => 1],
            ['municipality_id' => 100, 'municipality_name' => 'Gunupur M', 'district_id' => 2429, 'is_active' => 'active', 'subdivision_id' => 54, 'nic_municipality_code' => 24290002, 'ulb_type' => 1],
            ['municipality_id' => 101, 'municipality_name' => 'Gudari N', 'district_id' => 2429, 'is_active' => 'active', 'subdivision_id' => 54, 'nic_municipality_code' => 24290001, 'ulb_type' => 1],
            ['municipality_id' => 102, 'municipality_name' => 'Kuchinda N', 'district_id' => 2401, 'is_active' => 'active', 'subdivision_id' => 32, 'nic_municipality_code' => 24010005, 'ulb_type' => 1],
            ['municipality_id' => 103, 'municipality_name' => 'Rairakhol N', 'district_id' => 2401, 'is_active' => 'active', 'subdivision_id' => 31, 'nic_municipality_code' => 24010004, 'ulb_type' => 1],
            ['municipality_id' => 104, 'municipality_name' => 'Tarbha N', 'district_id' => 2427, 'is_active' => 'active', 'subdivision_id' => 28, 'nic_municipality_code' => 24270001, 'ulb_type' => 1],
            ['municipality_id' => 105, 'municipality_name' => 'Binka N', 'district_id' => 2427, 'is_active' => 'active', 'subdivision_id' => 28, 'nic_municipality_code' => 24270002, 'ulb_type' => 1],
            ['municipality_id' => 106, 'municipality_name' => 'Aska N', 'district_id' => 2412, 'is_active' => 'active', 'subdivision_id' => 48, 'nic_municipality_code' => 24120001, 'ulb_type' => 1],
            ['municipality_id' => 107, 'municipality_name' => 'Balimela N', 'district_id' => 2431, 'is_active' => 'active', 'subdivision_id' => 52, 'nic_municipality_code' => 24310001, 'ulb_type' => 1],
            ['municipality_id' => 108, 'municipality_name' => 'JODA M', 'district_id' => 2403, 'is_active' => 'active', 'subdivision_id' => 36, 'nic_municipality_code' => 24030002, 'ulb_type' => 1],
            ['municipality_id' => 109, 'municipality_name' => 'Jharsuguda ULB', 'district_id' => 2415, 'is_active' => 'active', 'subdivision_id' => 41, 'nic_municipality_code' => 24150003, 'ulb_type' => 1],
            ['municipality_id' => 110, 'municipality_name' => 'Kesinga N', 'district_id' => 2410, 'is_active' => 'active', 'subdivision_id' => 43, 'nic_municipality_code' => 24100002, 'ulb_type' => 1],
            ['municipality_id' => 111, 'municipality_name' => 'Junagarh N', 'district_id' => 2410, 'is_active' => 'active', 'subdivision_id' => 44, 'nic_municipality_code' => 24100001, 'ulb_type' => 1],
            ['municipality_id' => 112, 'municipality_name' => 'Dh.Garh N', 'district_id' => 2410, 'is_active' => 'active', 'subdivision_id' => 44, 'nic_municipality_code' => 24100004, 'ulb_type' => 1],
            ['municipality_id' => 113, 'municipality_name' => 'BIJEPUR NAC', 'district_id' => 2414, 'is_active' => 'active', 'subdivision_id' => 34, 'nic_municipality_code' => 24140005, 'ulb_type' => 1],
            ['municipality_id' => 114, 'municipality_name' => 'CHANDABALI NAC', 'district_id' => 2417, 'is_active' => 'active', 'subdivision_id' => 9, 'nic_municipality_code' => 24170003, 'ulb_type' => 1],
            ['municipality_id' => 115, 'municipality_name' => 'DHAMNAGAR NAC', 'district_id' => 2417, 'is_active' => 'active', 'subdivision_id' => 9, 'nic_municipality_code' => 24170004, 'ulb_type' => 1],
        ];

        $municipalityData = [];
        
        foreach ($municipalities as $municipality) {
            $municipalityData[] = array_merge($municipality, [
                'state_id' => 228,
                'is_iap' => '1',
                'status' => 1,
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        DB::table('municipalities')->insert($municipalityData);
    }
}
