<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tahasil;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TahasilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahasils = [
            ['tahasil_id' => 1, 'tahasil_name' => 'Angul', 'district_id' => 2421],
            ['tahasil_id' => 2, 'tahasil_name' => 'Athamallik', 'district_id' => 2421],
            ['tahasil_id' => 3, 'tahasil_name' => 'Kaniha', 'district_id' => 2421],
            ['tahasil_id' => 4, 'tahasil_name' => 'Kishore Nagar', 'district_id' => 2421],
            ['tahasil_id' => 5, 'tahasil_name' => 'Chhendipada', 'district_id' => 2421],
            ['tahasil_id' => 6, 'tahasil_name' => 'Talcher', 'district_id' => 2421],
            ['tahasil_id' => 7, 'tahasil_name' => 'Pallahada', 'district_id' => 2421],
            ['tahasil_id' => 8, 'tahasil_name' => 'Banarpala', 'district_id' => 2421],
            ['tahasil_id' => 9, 'tahasil_name' => 'Athagarh', 'district_id' => 2406],
            ['tahasil_id' => 10, 'tahasil_name' => 'Cuttack', 'district_id' => 2406],
            ['tahasil_id' => 11, 'tahasil_name' => 'Kantapada', 'district_id' => 2406],
            ['tahasil_id' => 12, 'tahasil_name' => 'Kisan Nagar', 'district_id' => 2406],
            ['tahasil_id' => 13, 'tahasil_name' => 'Tangi Choudwar', 'district_id' => 2406],
            ['tahasil_id' => 14, 'tahasil_name' => 'Damapada', 'district_id' => 2406],
            ['tahasil_id' => 15, 'tahasil_name' => 'Tigiria', 'district_id' => 2406],
            ['tahasil_id' => 16, 'tahasil_name' => 'Niali', 'district_id' => 2406],
            ['tahasil_id' => 17, 'tahasil_name' => 'Narasinghapur', 'district_id' => 2406],
            ['tahasil_id' => 18, 'tahasil_name' => 'Nischintakoili', 'district_id' => 2406],
            ['tahasil_id' => 19, 'tahasil_name' => 'Badamba', 'district_id' => 2406],
            ['tahasil_id' => 20, 'tahasil_name' => 'Banki', 'district_id' => 2406],
            ['tahasil_id' => 21, 'tahasil_name' => 'Barang', 'district_id' => 2406],
            ['tahasil_id' => 22, 'tahasil_name' => 'Mahanga', 'district_id' => 2406],
            ['tahasil_id' => 23, 'tahasil_name' => 'Salepur', 'district_id' => 2406],
            ['tahasil_id' => 24, 'tahasil_name' => 'K.Nuagaon', 'district_id' => 2408],
            ['tahasil_id' => 25, 'tahasil_name' => 'Kandhamal', 'district_id' => 2408],
            ['tahasil_id' => 26, 'tahasil_name' => 'Kotagarh', 'district_id' => 2408],
            ['tahasil_id' => 27, 'tahasil_name' => 'Khajuripada', 'district_id' => 2408],
            ['tahasil_id' => 28, 'tahasil_name' => 'Ghumusura Udayagiri', 'district_id' => 2408],
            ['tahasil_id' => 29, 'tahasil_name' => 'Chakapada', 'district_id' => 2408],
            ['tahasil_id' => 30, 'tahasil_name' => 'Tikabali', 'district_id' => 2408],
            ['tahasil_id' => 31, 'tahasil_name' => 'Tumudibandh', 'district_id' => 2408],
            ['tahasil_id' => 32, 'tahasil_name' => 'Daringbadi', 'district_id' => 2408],
            ['tahasil_id' => 33, 'tahasil_name' => 'Phiringia', 'district_id' => 2408],
            ['tahasil_id' => 34, 'tahasil_name' => 'Baliguda', 'district_id' => 2408],
            ['tahasil_id' => 35, 'tahasil_name' => 'Raikia', 'district_id' => 2408],
            ['tahasil_id' => 36, 'tahasil_name' => 'Karlamunda', 'district_id' => 2410],
            ['tahasil_id' => 37, 'tahasil_name' => 'Kalampur', 'district_id' => 2410],
            ['tahasil_id' => 38, 'tahasil_name' => 'Kalahandi', 'district_id' => 2410],
            ['tahasil_id' => 39, 'tahasil_name' => 'Kesinga', 'district_id' => 2410],
            ['tahasil_id' => 40, 'tahasil_name' => 'Koksara', 'district_id' => 2410],
            ['tahasil_id' => 41, 'tahasil_name' => 'Golamunda', 'district_id' => 2410],
            ['tahasil_id' => 42, 'tahasil_name' => 'Junagarh', 'district_id' => 2410],
            ['tahasil_id' => 43, 'tahasil_name' => 'Jaipatana', 'district_id' => 2410],
            ['tahasil_id' => 44, 'tahasil_name' => 'Thuamul Rampur', 'district_id' => 2410],
            ['tahasil_id' => 45, 'tahasil_name' => 'Dharmagarh', 'district_id' => 2410],
            ['tahasil_id' => 46, 'tahasil_name' => 'Lanjigarh', 'district_id' => 2410],
            ['tahasil_id' => 47, 'tahasil_name' => 'Narla', 'district_id' => 2410],
            ['tahasil_id' => 48, 'tahasil_name' => 'Madanpur Rampur', 'district_id' => 2410],
            ['tahasil_id' => 49, 'tahasil_name' => 'Anandapur', 'district_id' => 2403],
            ['tahasil_id' => 50, 'tahasil_name' => 'Ghatagaon', 'district_id' => 2403],
            ['tahasil_id' => 51, 'tahasil_name' => 'Champua', 'district_id' => 2403],
            ['tahasil_id' => 52, 'tahasil_name' => 'Ghasipura', 'district_id' => 2403],
            ['tahasil_id' => 53, 'tahasil_name' => 'Jhumpura', 'district_id' => 2403],
            ['tahasil_id' => 54, 'tahasil_name' => 'Telakoi', 'district_id' => 2403],
            ['tahasil_id' => 55, 'tahasil_name' => 'Patna', 'district_id' => 2403],
            ['tahasil_id' => 56, 'tahasil_name' => 'Barbil', 'district_id' => 2403],
            ['tahasil_id' => 57, 'tahasil_name' => 'Bansapal', 'district_id' => 2403],
            ['tahasil_id' => 58, 'tahasil_name' => 'Sadar', 'district_id' => 2403],
            ['tahasil_id' => 59, 'tahasil_name' => 'Saharapada', 'district_id' => 2403],
            ['tahasil_id' => 60, 'tahasil_name' => 'Hatadihi', 'district_id' => 2403],
            ['tahasil_id' => 61, 'tahasil_name' => 'Harichandanpur', 'district_id' => 2403],
            ['tahasil_id' => 62, 'tahasil_name' => '"Aali   "', 'district_id' => 2418],
            ['tahasil_id' => 63, 'tahasil_name' => 'Kendrapada', 'district_id' => 2418],
            ['tahasil_id' => 64, 'tahasil_name' => 'Garadapur', 'district_id' => 2418],
            ['tahasil_id' => 65, 'tahasil_name' => 'Derabisa', 'district_id' => 2418],
            ['tahasil_id' => 66, 'tahasil_name' => 'Pattamundai', 'district_id' => 2418],
            ['tahasil_id' => 67, 'tahasil_name' => 'Raja Nagar', 'district_id' => 2418],
            ['tahasil_id' => 68, 'tahasil_name' => 'Marshaghai', 'district_id' => 2418],
            ['tahasil_id' => 69, 'tahasil_name' => 'Mahakalapada', 'district_id' => 2418],
            ['tahasil_id' => 70, 'tahasil_name' => 'Rajkanika', 'district_id' => 2418],
            ['tahasil_id' => 71, 'tahasil_name' => 'Kundura', 'district_id' => 2411],
            ['tahasil_id' => 72, 'tahasil_name' => 'Kotpad', 'district_id' => 2411],
            ['tahasil_id' => 73, 'tahasil_name' => 'Koraput', 'district_id' => 2411],
            ['tahasil_id' => 74, 'tahasil_name' => 'Jeypore', 'district_id' => 2411],
            ['tahasil_id' => 75, 'tahasil_name' => 'Dasamanthapur', 'district_id' => 2411],
            ['tahasil_id' => 76, 'tahasil_name' => 'Nandapur', 'district_id' => 2411],
            ['tahasil_id' => 77, 'tahasil_name' => 'Narayanpatana', 'district_id' => 2411],
            ['tahasil_id' => 78, 'tahasil_name' => 'Bandhugaon', 'district_id' => 2411],
            ['tahasil_id' => 79, 'tahasil_name' => 'Pattangi', 'district_id' => 2411],
            ['tahasil_id' => 80, 'tahasil_name' => 'Baipariguda ', 'district_id' => 2411],
            ['tahasil_id' => 81, 'tahasil_name' => 'Borigumma', 'district_id' => 2411],
            ['tahasil_id' => 82, 'tahasil_name' => 'Lakshmipur', 'district_id' => 2411],
            ['tahasil_id' => 83, 'tahasil_name' => 'Semiliguda', 'district_id' => 2411],
            ['tahasil_id' => 84, 'tahasil_name' => 'Machkund', 'district_id' => 2411],
            ['tahasil_id' => 85, 'tahasil_name' => 'Khordha', 'district_id' => 2423],
            ['tahasil_id' => 86, 'tahasil_name' => 'Balianta', 'district_id' => 2423],
            ['tahasil_id' => 87, 'tahasil_name' => 'Balipatna', 'district_id' => 2423],
            ['tahasil_id' => 88, 'tahasil_name' => 'Banapur', 'district_id' => 2423],
            ['tahasil_id' => 89, 'tahasil_name' => 'Begunia', 'district_id' => 2423],
            ['tahasil_id' => 90, 'tahasil_name' => 'Bhubaneswar', 'district_id' => 2423],
            ['tahasil_id' => 91, 'tahasil_name' => 'Bolagarh', 'district_id' => 2423],
            ['tahasil_id' => 92, 'tahasil_name' => 'Chilika', 'district_id' => 2423],
            ['tahasil_id' => 93, 'tahasil_name' => 'Jatni', 'district_id' => 2423],
            ['tahasil_id' => 94, 'tahasil_name' => 'Tangi', 'district_id' => 2423],
            ['tahasil_id' => 95, 'tahasil_name' => 'Aska', 'district_id' => 2412],
            ['tahasil_id' => 96, 'tahasil_name' => 'Kukudakhandi', 'district_id' => 2412],
            ['tahasil_id' => 97, 'tahasil_name' => 'Kanisi', 'district_id' => 2412],
            ['tahasil_id' => 98, 'tahasil_name' => 'Kabisuryanagar', 'district_id' => 2412],
            ['tahasil_id' => 99, 'tahasil_name' => 'Kodala', 'district_id' => 2412],
            ['tahasil_id' => 100, 'tahasil_name' => 'Khallikote', 'district_id' => 2412],
            ['tahasil_id' => 101, 'tahasil_name' => 'Ganjam', 'district_id' => 2412],
            ['tahasil_id' => 102, 'tahasil_name' => 'Chikiti', 'district_id' => 2412],
            ['tahasil_id' => 103, 'tahasil_name' => 'Chhatrapur', 'district_id' => 2412],
            ['tahasil_id' => 104, 'tahasil_name' => 'Jagannathprasad', 'district_id' => 2412],
            ['tahasil_id' => 105, 'tahasil_name' => 'Digapahandi', 'district_id' => 2412],
            ['tahasil_id' => 106, 'tahasil_name' => 'Dharakot', 'district_id' => 2412],
            ['tahasil_id' => 107, 'tahasil_name' => 'Purushottampur', 'district_id' => 2412],
            ['tahasil_id' => 108, 'tahasil_name' => 'Patrapur', 'district_id' => 2412],
            ['tahasil_id' => 109, 'tahasil_name' => '"Polasara  "', 'district_id' => 2412],
            ['tahasil_id' => 110, 'tahasil_name' => 'Buguda', 'district_id' => 2412],
            ['tahasil_id' => 111, 'tahasil_name' => '"  Berhampur"', 'district_id' => 2412],
            ['tahasil_id' => 112, 'tahasil_name' => 'Belaguntha', 'district_id' => 2412],
            ['tahasil_id' => 113, 'tahasil_name' => 'Sanakhemundi', 'district_id' => 2412],
            ['tahasil_id' => 114, 'tahasil_name' => 'Sorada', 'district_id' => 2412],
            ['tahasil_id' => 115, 'tahasil_name' => '"Hinjilicut    "', 'district_id' => 2412],
            ['tahasil_id' => 116, 'tahasil_name' => 'Seragada', 'district_id' => 2412],
            ['tahasil_id' => 117, 'tahasil_name' => 'Ghumusara', 'district_id' => 2412],
            ['tahasil_id' => 118, 'tahasil_name' => 'Kashinagar', 'district_id' => 2424],
            ['tahasil_id' => 119, 'tahasil_name' => 'Guma', 'district_id' => 2424],
            ['tahasil_id' => 120, 'tahasil_name' => 'Nuagada', 'district_id' => 2424],
            ['tahasil_id' => 121, 'tahasil_name' => 'Parlakhemundi', 'district_id' => 2424],
            ['tahasil_id' => 122, 'tahasil_name' => 'Mohana', 'district_id' => 2424],
            ['tahasil_id' => 123, 'tahasil_name' => 'Ramagiri Udayagiri', 'district_id' => 2424],
            ['tahasil_id' => 124, 'tahasil_name' => 'Rayagada', 'district_id' => 2424],
            ['tahasil_id' => 125, 'tahasil_name' => 'Erasama', 'district_id' => 2419],
            ['tahasil_id' => 126, 'tahasil_name' => 'Balikuda', 'district_id' => 2419],
            ['tahasil_id' => 127, 'tahasil_name' => 'Kujang', 'district_id' => 2419],
            ['tahasil_id' => 128, 'tahasil_name' => 'Jagatsinghpur', 'district_id' => 2419],
            ['tahasil_id' => 129, 'tahasil_name' => 'Biridi', 'district_id' => 2419],
            ['tahasil_id' => 130, 'tahasil_name' => 'Raghunathpur', 'district_id' => 2419],
            ['tahasil_id' => 131, 'tahasil_name' => 'Tirtol ', 'district_id' => 2419],
            ['tahasil_id' => 132, 'tahasil_name' => 'Naugaon', 'district_id' => 2419],
            ['tahasil_id' => 133, 'tahasil_name' => 'Jharsuguda', 'district_id' => 2415],
            ['tahasil_id' => 134, 'tahasil_name' => 'Kirmira', 'district_id' => 2415],
            ['tahasil_id' => 135, 'tahasil_name' => 'Kolabira', 'district_id' => 2415],
            ['tahasil_id' => 136, 'tahasil_name' => 'Lakhanpur', 'district_id' => 2415],
            ['tahasil_id' => 137, 'tahasil_name' => ' Laikera', 'district_id' => 2415],
            ['tahasil_id' => 138, 'tahasil_name' => 'Odapada', 'district_id' => 2407],
            ['tahasil_id' => 139, 'tahasil_name' => 'Kankadahad', 'district_id' => 2407],
            ['tahasil_id' => 140, 'tahasil_name' => 'Kamakhyanagar ', 'district_id' => 2407],
            ['tahasil_id' => 141, 'tahasil_name' => 'Gondia', 'district_id' => 2407],
            ['tahasil_id' => 142, 'tahasil_name' => 'Dhenkanal ', 'district_id' => 2407],
            ['tahasil_id' => 143, 'tahasil_name' => 'Parjanga', 'district_id' => 2407],
            ['tahasil_id' => 144, 'tahasil_name' => 'Bhuban', 'district_id' => 2407],
            ['tahasil_id' => 145, 'tahasil_name' => 'Hindol', 'district_id' => 2407],
            ['tahasil_id' => 146, 'tahasil_name' => 'Deogarh', 'district_id' => 2416],
            ['tahasil_id' => 147, 'tahasil_name' => 'Barkote', 'district_id' => 2416],
            ['tahasil_id' => 148, 'tahasil_name' => 'Reamal', 'district_id' => 2416],
            ['tahasil_id' => 149, 'tahasil_name' => 'Nuapada', 'district_id' => 2428],
            ['tahasil_id' => 150, 'tahasil_name' => 'Komana', 'district_id' => 2428],
            ['tahasil_id' => 151, 'tahasil_name' => 'Boden', 'district_id' => 2428],
            ['tahasil_id' => 152, 'tahasil_name' => 'Kharial', 'district_id' => 2428],
            ['tahasil_id' => 153, 'tahasil_name' => 'Sunapalli', 'district_id' => 2428],
            ['tahasil_id' => 154, 'tahasil_name' => 'Umarkote ', 'district_id' => 2430],
            ['tahasil_id' => 155, 'tahasil_name' => 'Chandahandi', 'district_id' => 2430],
            ['tahasil_id' => 156, 'tahasil_name' => 'Nabarangpur', 'district_id' => 2430],
            ['tahasil_id' => 157, 'tahasil_name' => 'Tentulikhunti', 'district_id' => 2430],
            ['tahasil_id' => 158, 'tahasil_name' => 'Nandahandi', 'district_id' => 2430],
            ['tahasil_id' => 159, 'tahasil_name' => 'Kodinga', 'district_id' => 2430],
            ['tahasil_id' => 160, 'tahasil_name' => 'Raighar', 'district_id' => 2430],
            ['tahasil_id' => 161, 'tahasil_name' => 'Dabugaon', 'district_id' => 2430],
            ['tahasil_id' => 162, 'tahasil_name' => 'Papadahandi', 'district_id' => 2430],
            ['tahasil_id' => 163, 'tahasil_name' => 'Jharigaon', 'district_id' => 2430],
            ['tahasil_id' => 164, 'tahasil_name' => 'Odagaon', 'district_id' => 2422],
            ['tahasil_id' => 165, 'tahasil_name' => 'Khandapada ', 'district_id' => 2422],
            ['tahasil_id' => 166, 'tahasil_name' => 'Bhapur', 'district_id' => 2422],
            ['tahasil_id' => 167, 'tahasil_name' => 'Daspalla', 'district_id' => 2422],
            ['tahasil_id' => 168, 'tahasil_name' => 'Gania', 'district_id' => 2422],
            ['tahasil_id' => 169, 'tahasil_name' => 'Ranpur', 'district_id' => 2422],
            ['tahasil_id' => 170, 'tahasil_name' => 'Nuagaon', 'district_id' => 2422],
            ['tahasil_id' => 171, 'tahasil_name' => 'Nayagarh', 'district_id' => 2422],
            ['tahasil_id' => 172, 'tahasil_name' => 'Astarang', 'district_id' => 2413],
            ['tahasil_id' => 173, 'tahasil_name' => 'Kanas', 'district_id' => 2413],
            ['tahasil_id' => 174, 'tahasil_name' => 'Krushnaprasad', 'district_id' => 2413],
            ['tahasil_id' => 175, 'tahasil_name' => 'Kakatpur', 'district_id' => 2413],
            ['tahasil_id' => 176, 'tahasil_name' => 'Gopa', 'district_id' => 2413],
            ['tahasil_id' => 177, 'tahasil_name' => 'Delanga', 'district_id' => 2413],
            ['tahasil_id' => 178, 'tahasil_name' => 'Pipili', 'district_id' => 2413],
            ['tahasil_id' => 179, 'tahasil_name' => 'Nimapada ', 'district_id' => 2413],
            ['tahasil_id' => 180, 'tahasil_name' => 'Puri ', 'district_id' => 2413],
            ['tahasil_id' => 181, 'tahasil_name' => 'Brahmagiri', 'district_id' => 2413],
            ['tahasil_id' => 182, 'tahasil_name' => 'Sakhigopal', 'district_id' => 2413],
            ['tahasil_id' => 183, 'tahasil_name' => 'Attabira', 'district_id' => 2414],
            ['tahasil_id' => 184, 'tahasil_name' => 'Ambabhona', 'district_id' => 2414],
            ['tahasil_id' => 185, 'tahasil_name' => 'Gaisilet', 'district_id' => 2414],
            ['tahasil_id' => 186, 'tahasil_name' => 'Jharbandh', 'district_id' => 2414],
            ['tahasil_id' => 187, 'tahasil_name' => 'Padampur', 'district_id' => 2414],
            ['tahasil_id' => 188, 'tahasil_name' => 'Paikmal', 'district_id' => 2414],
            ['tahasil_id' => 189, 'tahasil_name' => 'Bijepur', 'district_id' => 2414],
            ['tahasil_id' => 190, 'tahasil_name' => 'Bargarh', 'district_id' => 2414],
            ['tahasil_id' => 191, 'tahasil_name' => 'Barapali ', 'district_id' => 2414],
            ['tahasil_id' => 192, 'tahasil_name' => 'Bheden', 'district_id' => 2414],
            ['tahasil_id' => 193, 'tahasil_name' => 'Sohella', 'district_id' => 2414],
            ['tahasil_id' => 194, 'tahasil_name' => 'Bhatli', 'district_id' => 2414],
            ['tahasil_id' => 195, 'tahasil_name' => 'Agalpur', 'district_id' => 2409],
            ['tahasil_id' => 196, 'tahasil_name' => 'Kantabanji ', 'district_id' => 2409],
            ['tahasil_id' => 197, 'tahasil_name' => 'Khaprakhol', 'district_id' => 2409],
            ['tahasil_id' => 198, 'tahasil_name' => 'Titilagarh ', 'district_id' => 2409],
            ['tahasil_id' => 199, 'tahasil_name' => 'Deogaon', 'district_id' => 2409],
            ['tahasil_id' => 200, 'tahasil_name' => 'Tusara', 'district_id' => 2409],
            ['tahasil_id' => 201, 'tahasil_name' => 'Puintala', 'district_id' => 2409],
            ['tahasil_id' => 202, 'tahasil_name' => 'Patnagarh ', 'district_id' => 2409],
            ['tahasil_id' => 203, 'tahasil_name' => 'Bangomunda', 'district_id' => 2409],
            ['tahasil_id' => 204, 'tahasil_name' => 'Bolangir', 'district_id' => 2409],
            ['tahasil_id' => 205, 'tahasil_name' => 'Belpada', 'district_id' => 2409],
            ['tahasil_id' => 206, 'tahasil_name' => 'Muribahal', 'district_id' => 2409],
            ['tahasil_id' => 207, 'tahasil_name' => 'Loisingha', 'district_id' => 2409],
            ['tahasil_id' => 208, 'tahasil_name' => 'Saintala', 'district_id' => 2409],
            ['tahasil_id' => 209, 'tahasil_name' => 'Oupada', 'district_id' => 2405],
            ['tahasil_id' => 210, 'tahasil_name' => 'Khaira', 'district_id' => 2405],
            ['tahasil_id' => 211, 'tahasil_name' => 'Jaleswar', 'district_id' => 2405],
            ['tahasil_id' => 212, 'tahasil_name' => 'Nilagiri ', 'district_id' => 2405],
            ['tahasil_id' => 213, 'tahasil_name' => 'Basta ', 'district_id' => 2405],
            ['tahasil_id' => 214, 'tahasil_name' => 'Baliapal', 'district_id' => 2405],
            ['tahasil_id' => 215, 'tahasil_name' => 'Baleshwar', 'district_id' => 2405],
            ['tahasil_id' => 216, 'tahasil_name' => 'Bahanaga ', 'district_id' => 2405],
            ['tahasil_id' => 217, 'tahasil_name' => 'Bhogarai', 'district_id' => 2405],
            ['tahasil_id' => 218, 'tahasil_name' => 'Remuna', 'district_id' => 2405],
            ['tahasil_id' => 219, 'tahasil_name' => 'Soro', 'district_id' => 2405],
            ['tahasil_id' => 220, 'tahasil_name' => 'Similia', 'district_id' => 2405],
            ['tahasil_id' => 221, 'tahasil_name' => 'Kantamal', 'district_id' => 2426],
            ['tahasil_id' => 222, 'tahasil_name' => 'Boudh ', 'district_id' => 2426],
            ['tahasil_id' => 223, 'tahasil_name' => 'Harbhanga', 'district_id' => 2426],
            ['tahasil_id' => 224, 'tahasil_name' => 'Chandabali', 'district_id' => 2417],
            ['tahasil_id' => 225, 'tahasil_name' => 'Tihidi ', 'district_id' => 2417],
            ['tahasil_id' => 226, 'tahasil_name' => 'Dhamnagar', 'district_id' => 2417],
            ['tahasil_id' => 227, 'tahasil_name' => 'Banta', 'district_id' => 2417],
            ['tahasil_id' => 228, 'tahasil_name' => 'Basudevpur', 'district_id' => 2417],
            ['tahasil_id' => 229, 'tahasil_name' => 'Bhandaripokhari', 'district_id' => 2417],
            ['tahasil_id' => 230, 'tahasil_name' => 'Bhadrak ', 'district_id' => 2417],
            ['tahasil_id' => 231, 'tahasil_name' => 'Udala', 'district_id' => 2404],
            ['tahasil_id' => 232, 'tahasil_name' => 'Kaptipada ', 'district_id' => 2404],
            ['tahasil_id' => 233, 'tahasil_name' => 'Kusumi ', 'district_id' => 2404],
            ['tahasil_id' => 234, 'tahasil_name' => 'Karanjia', 'district_id' => 2404],
            ['tahasil_id' => 235, 'tahasil_name' => 'Gopabandhunagar', 'district_id' => 2404],
            ['tahasil_id' => 236, 'tahasil_name' => 'Koliana', 'district_id' => 2404],
            ['tahasil_id' => 237, 'tahasil_name' => 'Khunta', 'district_id' => 2404],
            ['tahasil_id' => 238, 'tahasil_name' => 'Thakuramunda', 'district_id' => 2404],
            ['tahasil_id' => 239, 'tahasil_name' => 'Tiringa', 'district_id' => 2404],
            ['tahasil_id' => 240, 'tahasil_name' => 'Bijatala', 'district_id' => 2404],
            ['tahasil_id' => 241, 'tahasil_name' => 'Badasahi ', 'district_id' => 2404],
            ['tahasil_id' => 242, 'tahasil_name' => 'Bisoi', 'district_id' => 2404],
            ['tahasil_id' => 243, 'tahasil_name' => 'Bahalda', 'district_id' => 2404],
            ['tahasil_id' => 244, 'tahasil_name' => 'Bangriposi', 'district_id' => 2404],
            ['tahasil_id' => 245, 'tahasil_name' => 'Baripada ', 'district_id' => 2404],
            ['tahasil_id' => 246, 'tahasil_name' => 'Betnoti', 'district_id' => 2404],
            ['tahasil_id' => 247, 'tahasil_name' => 'Morada', 'district_id' => 2404],
            ['tahasil_id' => 248, 'tahasil_name' => 'Jashipur', 'district_id' => 2404],
            ['tahasil_id' => 249, 'tahasil_name' => 'Jamda', 'district_id' => 2404],
            ['tahasil_id' => 250, 'tahasil_name' => 'Rairangpur', 'district_id' => 2404],
            ['tahasil_id' => 251, 'tahasil_name' => 'Raruan', 'district_id' => 2404],
            ['tahasil_id' => 252, 'tahasil_name' => 'Rasgobindapur', 'district_id' => 2404],
            ['tahasil_id' => 253, 'tahasil_name' => 'Sukruli', 'district_id' => 2404],
            ['tahasil_id' => 254, 'tahasil_name' => 'Shamakhunta', 'district_id' => 2404],
            ['tahasil_id' => 255, 'tahasil_name' => 'Suliapada', 'district_id' => 2404],
            ['tahasil_id' => 256, 'tahasil_name' => 'Saraskana', 'district_id' => 2404],
            ['tahasil_id' => 257, 'tahasil_name' => 'Kudumulugumma', 'district_id' => 2431],
            ['tahasil_id' => 258, 'tahasil_name' => 'Kalimela', 'district_id' => 2431],
            ['tahasil_id' => 259, 'tahasil_name' => 'Khairaput', 'district_id' => 2431],
            ['tahasil_id' => 260, 'tahasil_name' => 'Chitrakonda ', 'district_id' => 2431],
            ['tahasil_id' => 261, 'tahasil_name' => 'Mathili', 'district_id' => 2431],
            ['tahasil_id' => 262, 'tahasil_name' => 'Malkangiri ', 'district_id' => 2431],
            ['tahasil_id' => 263, 'tahasil_name' => 'Motu', 'district_id' => 2431],
            ['tahasil_id' => 264, 'tahasil_name' => 'Dasarathpur ', 'district_id' => 2420],
            ['tahasil_id' => 265, 'tahasil_name' => 'Danagadi', 'district_id' => 2420],
            ['tahasil_id' => 266, 'tahasil_name' => 'Dharmasala', 'district_id' => 2420],
            ['tahasil_id' => 267, 'tahasil_name' => 'Binjharpur', 'district_id' => 2420],
            ['tahasil_id' => 268, 'tahasil_name' => 'Vyasanagar ', 'district_id' => 2420],
            ['tahasil_id' => 269, 'tahasil_name' => 'Bari', 'district_id' => 2420],
            ['tahasil_id' => 270, 'tahasil_name' => 'Jajpur ', 'district_id' => 2420],
            ['tahasil_id' => 271, 'tahasil_name' => 'Rasulpur ', 'district_id' => 2420],
            ['tahasil_id' => 272, 'tahasil_name' => 'Sukinda', 'district_id' => 2420],
            ['tahasil_id' => 273, 'tahasil_name' => 'Kalyansinghpur', 'district_id' => 2429],
            ['tahasil_id' => 274, 'tahasil_name' => '"Kashipur  "', 'district_id' => 2429],
            ['tahasil_id' => 275, 'tahasil_name' => 'Kolnara', 'district_id' => 2429],
            ['tahasil_id' => 276, 'tahasil_name' => 'Gudari', 'district_id' => 2429],
            ['tahasil_id' => 277, 'tahasil_name' => '"Gunupur   "', 'district_id' => 2429],
            ['tahasil_id' => 278, 'tahasil_name' => 'Chandrapur', 'district_id' => 2429],
            ['tahasil_id' => 279, 'tahasil_name' => 'Padmapur', 'district_id' => 2429],
            ['tahasil_id' => 280, 'tahasil_name' => 'Bissam Cuttack', 'district_id' => 2429],
            ['tahasil_id' => 281, 'tahasil_name' => 'Muniguda', 'district_id' => 2429],
            ['tahasil_id' => 282, 'tahasil_name' => 'Ramanaguda', 'district_id' => 2429],
            ['tahasil_id' => 283, 'tahasil_name' => 'Rayagada', 'district_id' => 2429],
            ['tahasil_id' => 285, 'tahasil_name' => 'Kuchinda ', 'district_id' => 2401],
            ['tahasil_id' => 286, 'tahasil_name' => 'Naktideul', 'district_id' => 2401],
            ['tahasil_id' => 287, 'tahasil_name' => 'Jamankira', 'district_id' => 2401],
            ['tahasil_id' => 288, 'tahasil_name' => 'Bamra', 'district_id' => 2401],
            ['tahasil_id' => 289, 'tahasil_name' => 'Maneswar ', 'district_id' => 2401],
            ['tahasil_id' => 290, 'tahasil_name' => 'Jujumura', 'district_id' => 2401],
            ['tahasil_id' => 291, 'tahasil_name' => 'Rengali', 'district_id' => 2401],
            ['tahasil_id' => 292, 'tahasil_name' => 'Rairakhol', 'district_id' => 2401],
            ['tahasil_id' => 293, 'tahasil_name' => 'Sambalpur', 'district_id' => 2401],
            ['tahasil_id' => 294, 'tahasil_name' => 'Kutra', 'district_id' => 2402],
            ['tahasil_id' => 295, 'tahasil_name' => 'Koira', 'district_id' => 2402],
            ['tahasil_id' => 296, 'tahasil_name' => 'Gurundia', 'district_id' => 2402],
            ['tahasil_id' => 297, 'tahasil_name' => 'Tangarpali', 'district_id' => 2402],
            ['tahasil_id' => 298, 'tahasil_name' => 'Baragaon', 'district_id' => 2402],
            ['tahasil_id' => 299, 'tahasil_name' => 'Bonai', 'district_id' => 2402],
            ['tahasil_id' => 300, 'tahasil_name' => 'Biramitrapur ', 'district_id' => 2402],
            ['tahasil_id' => 301, 'tahasil_name' => 'Bisra ', 'district_id' => 2402],
            ['tahasil_id' => 302, 'tahasil_name' => 'Balisankara', 'district_id' => 2402],
            ['tahasil_id' => 303, 'tahasil_name' => 'Rourkela ', 'district_id' => 2402],
            ['tahasil_id' => 304, 'tahasil_name' => 'Rajgangpur', 'district_id' => 2402],
            ['tahasil_id' => 305, 'tahasil_name' => 'Lahunipada', 'district_id' => 2402],
            ['tahasil_id' => 306, 'tahasil_name' => 'Lathikata ', 'district_id' => 2402],
            ['tahasil_id' => 307, 'tahasil_name' => 'Lephripada ', 'district_id' => 2402],
            ['tahasil_id' => 308, 'tahasil_name' => 'Sundargarh', 'district_id' => 2402],
            ['tahasil_id' => 309, 'tahasil_name' => 'Subdega', 'district_id' => 2402],
            ['tahasil_id' => 310, 'tahasil_name' => 'Hemgiri', 'district_id' => 2402],
            ['tahasil_id' => 311, 'tahasil_name' => 'Panposh', 'district_id' => 2402],
            ['tahasil_id' => 312, 'tahasil_name' => 'Ullunda', 'district_id' => 2427],
            ['tahasil_id' => 313, 'tahasil_name' => 'Tarava', 'district_id' => 2427],
            ['tahasil_id' => 314, 'tahasil_name' => 'Binika', 'district_id' => 2427],
            ['tahasil_id' => 315, 'tahasil_name' => 'Biramaharajpur', 'district_id' => 2427],
            ['tahasil_id' => 316, 'tahasil_name' => 'Rampur', 'district_id' => 2427],
            ['tahasil_id' => 317, 'tahasil_name' => 'Subarnapur', 'district_id' => 2427],
            ['tahasil_id' => 318, 'tahasil_name' => 'Darpana', 'district_id' => 2420],
            ['tahasil_id' => 319, 'tahasil_name' => 'Kuarmunda', 'district_id' => 2402],
        ];

        $tahasilData = [];
        
        foreach ($tahasils as $tahasil) {
            $tahasilData[] = array_merge($tahasil, [
                'is_active' => 'active',
                'status' => 1,
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        DB::table('tahasils')->insert($tahasilData);
    }
}
