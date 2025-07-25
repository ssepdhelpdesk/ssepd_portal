<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApplicationStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applicationstages = [
            ['stage_id' => 1, 'stage_name' => 'Pending for final submit'],
            ['stage_id' => 2, 'stage_name' => 'Application Applied Successfully'],
            ['stage_id' => 3, 'stage_name' => 'Forwarded to BSSO'],
            ['stage_id' => 4, 'stage_name' => 'Forwarded to BDO'],
            ['stage_id' => 5, 'stage_name' => 'Forwarded to Tahasildar'],
            ['stage_id' => 6, 'stage_name' => 'Forwarded to SSSO'],
            ['stage_id' => 7, 'stage_name' => 'Forwarded to DSSO'],
            ['stage_id' => 8, 'stage_name' => 'Forwarded to SubCollector'],
            ['stage_id' => 9, 'stage_name' => 'Forwarded to Collector'],
            ['stage_id' => 10, 'stage_name' => 'Forwarded to HO'],
            ['stage_id' => 11, 'stage_name' => 'Forwarded to BO'],
            ['stage_id' => 12, 'stage_name' => 'Forwarded to Director'],
            ['stage_id' => 13, 'stage_name' => 'Forwarded to Secretary'],
            ['stage_id' => 14, 'stage_name' => 'Rejected by BSSO'],
            ['stage_id' => 15, 'stage_name' => 'Rejected by BDO'],
            ['stage_id' => 16, 'stage_name' => 'Rejected by Tahasildar'],
            ['stage_id' => 17, 'stage_name' => 'Rejected by SSSO'],
            ['stage_id' => 18, 'stage_name' => 'Rejected by DSSO'],
            ['stage_id' => 19, 'stage_name' => 'Rejected by SubCollector'],
            ['stage_id' => 20, 'stage_name' => 'Rejected by Collector'],
            ['stage_id' => 21, 'stage_name' => 'Rejected by HO'],
            ['stage_id' => 22, 'stage_name' => 'Rejected by BO'],
            ['stage_id' => 23, 'stage_name' => 'Rejected by Director'],
            ['stage_id' => 24, 'stage_name' => 'Rejected by Secretary'],
            ['stage_id' => 25, 'stage_name' => 'Approved'],
            ['stage_id' => 26, 'stage_name' => 'Reverted by BSSO'],
            ['stage_id' => 27, 'stage_name' => 'Reverted by BDO'],
            ['stage_id' => 28, 'stage_name' => 'Reverted by Tahasildar'],
            ['stage_id' => 29, 'stage_name' => 'Reverted by SSSO'],
            ['stage_id' => 30, 'stage_name' => 'Reverted by DSSO'],
            ['stage_id' => 31, 'stage_name' => 'Reverted by SubCollector'],
            ['stage_id' => 32, 'stage_name' => 'Reverted by Collector'],
            ['stage_id' => 33, 'stage_name' => 'Reverted by HO'],
            ['stage_id' => 34, 'stage_name' => 'Reverted by BO'],
            ['stage_id' => 35, 'stage_name' => 'Reverted by Director'],
            ['stage_id' => 36, 'stage_name' => 'Reverted by Secretary'],
            ['stage_id' => 37, 'stage_name' => 'Application updated by User'],
        ];

        $applicationstagesData = [];
        
        foreach ($applicationstages as $applicationstage) {
            $applicationstagesData[] = array_merge($applicationstage, [
                'status' => 1,
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata'),
            ]);
        }

        DB::table('application_stages')->insert($applicationstagesData);
    }
}
