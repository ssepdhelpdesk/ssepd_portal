<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NgoCategory;

class NgoCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ngocategories = [
            'RPwD Act',
            'Senior Citizen Act'
        ];

        foreach ($ngocategories as $ngocategory) {
             NgoCategory::create(['ngo_category_name' => $ngocategory]);
        }
    }
}
