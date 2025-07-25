<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = [
            'Male',
            'Female',
            'Other'
        ];

        foreach ($genders as $gender) {
             Gender::create(['gender_name' => $gender]);
        }
    }
}
