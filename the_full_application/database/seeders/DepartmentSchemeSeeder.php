<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Scheme;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $department_schemes = [
            ['scheme_id' => 1, 'scheme_name' => 'NGO'],
            ['scheme_id' => 2, 'scheme_name' => 'Special School'],
            ['scheme_id' => 3, 'scheme_name' => 'MBPY'],
            ['scheme_id' => 4, 'scheme_name' => 'NSAP'],
            ['scheme_id' => 5, 'scheme_name' => 'DDRC'],
            ['scheme_id' => 6, 'scheme_name' => 'ARC'],
        ];

        $departmentSchemeData = [];
        
        foreach ($department_schemes as $Scheme) {
            $departmentSchemeData[] = array_merge($Scheme, [
                'is_active' => 'active',
                'status' => 1,
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        DB::table('department_schemes')->insert($departmentSchemeData);
    }
}
