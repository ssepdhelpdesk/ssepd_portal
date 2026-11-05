<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FieldVisitDemoDataSeeder extends Seeder
{
    /**
     * Seed exactly 1,000 realistic, internally consistent Field Visit records.
     *
     * Strict Constraints:
     * - Only transactional data into `field_visits`.
     * - Zero fake records or physical files in `field_visit_evidences` or `field_visit_documents`.
     * - Strictly preserves existing non-demo records.
     * - Idempotent: Skips execution if 1,000 demo records exist. If partial run exists, cleans up and re-seeds.
     * - Visiting officer and creator attribution strictly mapped to existing users' `user_table_id`.
     */
    public function run(): void
    {
        $this->command?->info('FieldVisitDemoDataSeeder: Initializing 1,000 Field Visit demo records generator...');

        // 1. Idempotency Check: Count existing demo records with prefix 'FV-DEMO-%'
        $existingDemoCount = DB::table('field_visits')
            ->where('visit_reference_no', 'like', 'FV-DEMO-%')
            ->count();

        if ($existingDemoCount === 1000) {
            $this->command?->info("FieldVisitDemoDataSeeder: Exactly 1,000 demo records already exist. Seeder is idempotent, no changes made.");
            return;
        }

        if ($existingDemoCount > 0) {
            $this->command?->warn("FieldVisitDemoDataSeeder: Found partial demo dataset ({$existingDemoCount} records). Cleaning up old demo records before seeding fresh 1,000...");
            DB::table('field_visits')->where('visit_reference_no', 'like', 'FV-DEMO-%')->delete();
        }

        // 2. Preload Reference & Lookup Masters into Memory
        $districts = DB::table('districts')
            ->where('status', 1)
            ->get()
            ->keyBy('district_id');

        $blocks = DB::table('blocks')
            ->where('status', 1)
            ->where('is_active', '!=', 'Inactive')
            ->get()
            ->groupBy('district_id');

        $munis = DB::table('municipalities')
            ->where('status', 1)
            ->where('is_active', '!=', 'Inactive')
            ->get()
            ->groupBy('district_id');

        $gps = DB::table('grampanchayats')
            ->where('status', 1)
            ->get()
            ->groupBy('block_id');

        $villages = DB::table('villages')
            ->select('village_id', 'gp_id')
            ->get()
            ->groupBy('gp_id');

        $wards = DB::table('ward_masters')
            ->select('ward_code', 'municipal_area_code')
            ->get()
            ->groupBy('municipal_area_code');

        $visitTypes = DB::table('field_visit_types')
            ->where('is_active', 1)
            ->get()
            ->values();

        if ($visitTypes->isEmpty()) {
            $this->command?->error("FieldVisitDemoDataSeeder: No active field visit types found in field_visit_types table.");
            return;
        }

        $institutes = DB::table('field_visit_institutes')
            ->where('is_active', 1)
            ->get()
            ->groupBy('district_id');

        // Preload active officers with user_table_id
        $users = DB::table('users')
            ->select('id', 'name', 'user_table_id', 'role_id', 'posted_district', 'posted_block', 'posted_municipality')
            ->whereNotNull('user_table_id')
            ->where('is_active', 1)
            ->whereIn('role_id', ['1', '2', '4', '5', '6', '8', '9', '10', '11', '12', '14', '15', '25'])
            ->get();

        $bssoUsers      = $users->where('role_id', '4')->values();
        $bdoUsers       = $users->where('role_id', '6')->values();
        $meoUsers       = $users->where('role_id', '5')->values();
        $dssoUsers      = $users->where('role_id', '9')->values();
        $collectorUsers = $users->where('role_id', '11')->values();
        $subdivUsers    = $users->whereIn('role_id', ['8', '10'])->values();
        $stateUsers     = $users->whereIn('role_id', ['1', '2', '12', '14', '15', '25'])->values();

        // Fallback user if pools are empty
        $fallbackUser = $users->first() ?? (object)[
            'id' => 1,
            'name' => 'System Admin',
            'user_table_id' => 1,
            'role_id' => '1',
            'posted_district' => 2401,
            'posted_block' => null,
            'posted_municipality' => null,
        ];

        // 3. District Centroids (Latitude, Longitude) for Odisha
        $districtCentroids = [
            2401 => [21.4669, 83.9812], // SAMBALPUR
            2402 => [22.1200, 84.0300], // Sundaragada
            2403 => [21.6289, 85.5817], // KENDUJHAR
            2404 => [21.9322, 86.7360], // MAYURBHANJ
            2405 => [21.4934, 86.9135], // BALESHWAR
            2406 => [20.4625, 85.8828], // Kataka
            2407 => [20.6625, 85.5960], // DHENKANAL
            2408 => [20.4700, 84.2300], // Kandhamala
            2409 => [20.7100, 83.4800], // Balangir
            2410 => [19.9075, 83.1644], // KALAHANDI
            2411 => [18.8135, 82.7123], // KORAPUT
            2412 => [19.3550, 84.9850], // GANJAM
            2413 => [19.8135, 85.8312], // PURI
            2414 => [21.3333, 83.6167], // Baragada
            2415 => [21.8550, 84.0080], // JHARSUGUDA
            2416 => [21.5300, 84.7300], // Debagada
            2417 => [21.0574, 86.4950], // BHADRAK
            2418 => [20.5000, 86.4200], // Kendrapada
            2419 => [20.2600, 86.1700], // JAGATSINGHAPUR
            2420 => [20.8500, 86.3300], // JAJPUR
            2421 => [20.8400, 85.1000], // Anugola
            2422 => [20.1300, 85.1000], // Nayagada
            2423 => [20.2961, 85.8245], // KHORDHA
            2424 => [18.7700, 84.0900], // GAJAPATI
            2426 => [20.8400, 84.3200], // BOUDH
            2427 => [20.8400, 83.9200], // Sonpur
            2428 => [20.8300, 82.5200], // NUAPADA
            2429 => [19.1700, 83.4200], // RAYAGADA
            2430 => [19.2300, 82.5500], // NABARANGAPUR
            2431 => [18.3500, 81.9000], // MALKANGIRI
        ];

        // 4. Realistic Text Narratives
        $purposes = [
            'Annual statutory inspection of infrastructure and beneficiary amenities',
            'Surprise quality assessment and physical verification of resident rehabilitation services',
            'Routine quarterly monitoring of meal quality, hygiene, and medical support systems',
            'Follow-up review on previous audit compliance and infrastructure upgrades',
            'Evaluation of special education facilities, assistive aids, and learning materials',
            'Verification of beneficiary attendance, stipend disbursements, and biometric records',
            'Assessment of vocational training equipment and therapeutic rehabilitation units',
            'Inquiry regarding grievance redressal, emergency preparedness, and fire safety protocols',
            'Review of DDRC outreach camp logistics and artificial limb distribution',
            'Inspection of senior citizen healthcare facilities, barrier-free access, and sanitation',
            'State flagship scheme monitoring and disability certificate issuance progress',
            'Comprehensive review of grant-in-aid utilization and institutional governance',
        ];

        $areasList = [
            'Classrooms, computer lab, kitchen, dining hall, boys & girls hostels, washrooms, medical dispensary',
            'Therapy unit, speech therapy room, prosthetic workshop, sensory park, administrative office',
            'Dormitories, emergency medical room, kitchen store, recreation center, compound boundary wall',
            'Physiotherapy ward, clinical assessment room, counseling center, beneficiary record room',
            'Kitchen pantry, solar water heaters, RO water filtration plant, fire extinguishers, first aid posts',
            'Activity hall, vocational skill center, craft workshops, assistive device storage, pharmacy',
            'Sanitary blocks, accessible ramps with handrails, biometric entry-exit gates, CCTV surveillance room',
            'Store room, ration stock ledger, medical inspection register, staff attendance ledger',
        ];

        // Key observations strictly under 500 characters
        $observations = [
            'Premises found neat and orderly. Assistive aids distributed on schedule. Staff attendance register up to date with 92% present. RO water plant operating smoothly. 4 fire extinguishers inspected and found serviced recently.',
            'Special educational sessions active during inspection. 48 out of 52 enrolled children present. Nutrition program adhering strictly to state dietary charts. Adequate lighting and ventilation in all residential rooms.',
            'Physiotherapy equipment operational. Patient logbook systematically maintained with daily progress entries. Ramp accessibility installed at primary entrances. Minor repair needed on the southern compound wall.',
            'Facility maintaining satisfactory hygiene standards. Therapeutic sessions being conducted as per calendar. Biometric device operational. First-aid medicine stock verified and found valid with no expired medicines.',
            'Rehabilitation services proceeding as per scheme norms. Beneficiary feedback gathered during interaction was overwhelmingly positive. Digital records updated on the SSEPD state MIS portal.',
            'General upkeep satisfactory. Meal quality checked and found nutritious. Barrier-free toilets clean and functional. Need observed for additional sensory learning toys in primary deaf and blind classes.',
            'Vocational training unit functioning actively. 24 trainees engaged in tailoring and bookbinding. Electricity backup generator in good working order. Resident medical attendant on duty during visit.',
            'Clinical assessment records maintained systematically. Assistive devices fitment registers verified. Drinking water test reports within prescribed potability parameters.',
        ];

        $findingsList = [
            'Institutional infrastructure complies with standard operating guidelines. Staff to beneficiary ratio maintained within approved parameters. Accounts and ration stock records match physical inventory.',
            'Significant improvement observed in therapeutic intervention tracking. Beneficiaries expressed high satisfaction regarding food quality and recreational facilities. Digital case sheets implemented.',
            'All safety guidelines adhered to. Solar water system operational. Minor plastering work pending in auxiliary store room. Vocational training outputs exhibit commendable quality.',
            'Beneficiary enrolment increased by 14% over previous quarter. Special educators utilize modern teaching aids effectively. Sanitary disposal systems conform to local municipal standards.',
            'Early intervention therapy yielding positive developmental milestones for children under 10. Audiometry equipment calibrated and operational. Outreach camp registrations progressing satisfactorily.',
        ];

        $issuesList = [
            'Minor leakage observed in the hostel roof corner during monsoon. Handrail near west ramp requires repainting.',
            'Two desktop computers in the ICT lab need OS update and antivirus renewal. Additional wheelchair required in OPD.',
            'Ration stock buffer is adequate for 3 weeks; replenishment indent pending with civil supplies coordinator.',
            'Need for dedicated speech therapist on alternate Saturdays to handle increased weekend OPD caseload.',
            'Fire drill mock demonstration overdue by 2 months; annual refilling due in upcoming quarter.',
            'Signage in Braille requires minor touch-up near library entrance; boundary wall needs solar lighting fixtures.',
            'Minor drainage clogging noticed near back kitchen wash yard; plumbing contractor notified for clearing.',
        ];

        $recommendationsList = [
            'Authorize minor repair funds for hostel roof maintenance before upcoming monsoon season.',
            'Procure two additional ergonomic wheelchairs and update ICT lab hardware through district welfare contingency fund.',
            'Liaise with District Social Security Office to expedite delivery of pending assistive aid consignments.',
            'Engage a visiting clinical psychologist on contract basis for bi-weekly adolescent counseling sessions.',
            'Conduct mandatory disaster management mock drill within 15 days in coordination with local fire station.',
            'Install solar streetlights along inner walkway and refresh Braille directional signage throughout campus.',
            'Sanction masonry maintenance for boundary wall and expedite clearance of back yard drainage line.',
        ];

        $correctiveActionsList = [
            'Institution head instructed to issue work order for roof sealing within 7 business days.',
            'Administrative officer tasked with vendor coordination for computer service and software renewal.',
            'Indent forwarded to DSSO office with copy to district collectorate for expedited ration allotment.',
            'Roster finalized for visiting therapist; notification issued on institutional notice board.',
            'Fire drill scheduled for the upcoming second Saturday under supervision of local station officer.',
            'Maintenance contractor given 10-day deadline to finish ramp handrail coating and campus lighting.',
            'Local municipal sanitation squad scheduled for drain desilting by end of the week.',
        ];

        $remarksList = [
            'Overall administration of the institution is commendable. Management shows genuine dedication to inmate welfare and state scheme implementation. Recommended for continuation of standard grant-in-aid.',
            'Satisfactory progress noted across all parameters. The center serves as an impactful community resource for PwDs in the block. Sustained monitoring advised.',
            'Highly organized facility with dedicated instructional staff. Timely compliance on previous recommendations noted. Special commendation for their inclusive vocational training initiatives.',
            'Good operational efficiency and compassionate beneficiary care observed. Institution maintains transparent documentation and strong community engagement.',
            'The institution fulfills essential social security mandates effectively. Periodic clinical supervision by district medical team is strongly encouraged.',
        ];

        $contactPersons = [
            'Pradeep Kumar Mishra', 'Sunita Jena', 'Ramesh Chandra Mohapatra', 'Debabrata Nayak',
            'Dr. Alok Ranjan Sahoo', 'Manoranjan Das', 'Sujata Pattnaik', 'Subhashree Mohanty',
            'Bikram Keshari Rout', 'Pravat Kumar Barik', 'Ananya Tripathy', 'Deepak Kumar Sahu',
            'Santosh Kumar Behera', 'Minati Pradhan', 'Tapas Kumar Swain', 'Priyanka Das',
        ];

        $designationMap = [
            '1'  => ['Secretary', 'Director'],
            '2'  => ['Deputy Secretary', 'Under Secretary'],
            '14' => ['Director'],
            '15' => ['Secretary'],
            '12' => ['Assistant Director (Law)', 'Desk Officer', 'Section Officer (SO)', 'OSD'],
            '11' => ['Collector'],
            '9'  => ['DSSO'],
            '8'  => ['Social Welfare Officer'],
            '10' => ['Sub-Collector'],
            '4'  => ['BSSO'],
            '6'  => ['BSSO', 'Social Welfare Officer'],
            '5'  => ['Other'], // other_designation = 'Municipal Executive Officer'
            '25' => ['Assistant Section Officer (ASO)'],
        ];

        $allDistrictsList = $districts->values();
        $records = [];

        // 5. Build exactly 1,000 records
        for ($i = 1; $i <= 1000; $i++) {
            $refNo = 'FV-DEMO-' . str_pad($i, 4, '0', STR_PAD_LEFT);

            // Date distribution: past 368 days evenly spread across calendar
            $dayOffset = (int)(($i - 1) * (368 / 1000));
            $visitDateObj = Carbon::parse('2025-11-01')->addDays($dayOffset);
            $visitDate = $visitDateObj->toDateString();

            // Time distribution: 09:00 to 15:30 start; 1.5 to 3.5 hrs duration
            $startHours = [9, 10, 11, 14];
            $startMins  = [0, 15, 30, 45];
            $sh = $startHours[$i % count($startHours)];
            $sm = $startMins[($i * 3) % count($startMins)];
            $durationMinutes = 90 + (($i * 17) % 120);
            $startTime = sprintf('%02d:%02d:00', $sh, $sm);
            $endTimeObj = Carbon::parse("$visitDate $startTime")->addMinutes($durationMinutes);
            $endTime = $endTimeObj->format('H:i:s');

            // Status distribution: 65% submitted, 15% reviewed, 15% closed, 5% draft
            $mod20 = $i % 20;
            if ($mod20 === 0) {
                $status = 'draft';
            } elseif (in_array($mod20, [1, 2, 3])) {
                $status = 'reviewed';
            } elseif (in_array($mod20, [4, 5, 6])) {
                $status = 'closed';
            } else {
                $status = 'submitted';
            }

            // Follow-up requirement: ~28%
            $isFollowUp = in_array($i % 7, [1, 2]) ? 1 : 0;
            $followUpDate = null;
            if ($isFollowUp) {
                $followUpDays = 20 + (($i * 13) % 45); // 20 to 65 days
                $followUpDate = $visitDateObj->copy()->addDays($followUpDays)->toDateString();
            }

            // Visit Type (IDs 1-8 evenly cycled)
            $vType = $visitTypes[($i - 1) % $visitTypes->count()];
            $typeId = (int)$vType->id;

            // Determine Visiting Officer / Creator role tier
            // 45% Block/ULB level, 25% District level, 15% Subdiv, 15% State level
            $tierMod = $i % 100;
            if ($tierMod < 35 && $bssoUsers->isNotEmpty()) {
                $user = $bssoUsers[($i * 7) % $bssoUsers->count()];
                $tier = 'bsso';
            } elseif ($tierMod < 45 && $meoUsers->isNotEmpty()) {
                $user = $meoUsers[($i * 5) % $meoUsers->count()];
                $tier = 'meo';
            } elseif ($tierMod < 60 && $dssoUsers->isNotEmpty()) {
                $user = $dssoUsers[($i * 3) % $dssoUsers->count()];
                $tier = 'dsso';
            } elseif ($tierMod < 70 && $collectorUsers->isNotEmpty()) {
                $user = $collectorUsers[($i * 2) % $collectorUsers->count()];
                $tier = 'collector';
            } elseif ($tierMod < 85 && $subdivUsers->isNotEmpty()) {
                $user = $subdivUsers[($i * 3) % $subdivUsers->count()];
                $tier = 'subdiv';
            } else {
                $user = $stateUsers->isNotEmpty()
                    ? $stateUsers[($i * 2) % $stateUsers->count()]
                    : $fallbackUser;
                $tier = 'state';
            }

            $officerUserTableId = (int)$user->user_table_id;
            $officerName = $user->name;
            $roleIdStr = (string)$user->role_id;
            $possibleDesigs = $designationMap[$roleIdStr] ?? ['Other'];
            $officerDesig = $possibleDesigs[($i * 3) % count($possibleDesigs)];
            $otherDesig = ($officerDesig === 'Other') ? 'Municipal Executive Officer' : null;

            // Resolve Location Hierarchy & Address Type
            $districtId = null;
            $blockId    = null;
            $gpId       = null;
            $villageId  = null;
            $muniId     = null;
            $wardId     = null;
            $addressType = null;

            if ($tier === 'bsso') {
                $addressType = 1; // Rural
                $userBlockId = (int)$user->posted_block;
                $blockObj = DB::table('blocks')->where('block_id', $userBlockId)->first();
                if ($blockObj) {
                    $districtId = (int)$blockObj->district_id;
                    $blockId = $userBlockId;
                } else {
                    $districtId = 2401;
                    $blockId = 2401001;
                }
            } elseif ($tier === 'meo') {
                $addressType = 2; // Urban
                $userMuniId = (int)$user->posted_municipality;
                $muniObj = DB::table('municipalities')->where('municipality_id', $userMuniId)->first();
                if ($muniObj) {
                    $districtId = (int)$muniObj->district_id;
                    $muniId = $userMuniId;
                } else {
                    $districtId = 2401;
                    $muniId = 1;
                }
            } elseif ($tier === 'dsso' || $tier === 'collector') {
                $districtId = !empty($user->posted_district) ? (int)$user->posted_district : 2401;
                $addressType = ($i % 3 === 0) ? 2 : 1; // 67% rural, 33% urban
            } else {
                // Subdiv or State level: distribute across all 30 districts
                $distObj = $allDistrictsList[($i - 1) % $allDistrictsList->count()];
                $districtId = (int)$distObj->district_id;
                $addressType = ($i % 4 === 0) ? 2 : 1; // 75% rural, 25% urban
            }

            // Resolve subordinate geo fields
            if ($addressType === 1) {
                // Rural: district -> block -> GP -> village
                if (empty($blockId)) {
                    $distBlocks = $blocks->get($districtId);
                    if ($distBlocks && $distBlocks->isNotEmpty()) {
                        $blockId = (int)$distBlocks[($i * 3) % $distBlocks->count()]->block_id;
                    } else {
                        $blockId = 2401001;
                    }
                }

                $blockGps = $gps->get($blockId);
                if ($blockGps && $blockGps->isNotEmpty()) {
                    $gpObj = $blockGps[($i * 5) % $blockGps->count()];
                    $gpId = (int)$gpObj->gp_id;
                } else {
                    $gpId = 2401001001;
                }

                $gpVillages = $villages->get($gpId);
                if ($gpVillages && $gpVillages->isNotEmpty()) {
                    $vilObj = $gpVillages[($i * 7) % $gpVillages->count()];
                    $villageId = (int)$vilObj->village_id;
                } else {
                    $villageId = 649625;
                }
                $muniId = null;
                $wardId = null;
            } else {
                // Urban: district -> municipality -> ward
                if (empty($muniId)) {
                    $distMunis = $munis->get($districtId);
                    if ($distMunis && $distMunis->isNotEmpty()) {
                        $muniId = (int)$distMunis[($i * 3) % $distMunis->count()]->municipality_id;
                    } else {
                        $muniId = 1;
                    }
                }

                $muniWards = $wards->get($muniId);
                if ($muniWards && $muniWards->isNotEmpty()) {
                    $wardObj = $muniWards[($i * 5) % $muniWards->count()];
                    $wardId = (int)$wardObj->ward_code;
                } else {
                    $wardId = 240100020001;
                }
                $blockId = null;
                $gpId = null;
                $villageId = null;
            }

            // Resolve Institute in this district
            $distInstitutes = $institutes->get($districtId);
            if ($distInstitutes && $distInstitutes->isNotEmpty()) {
                $inst = $distInstitutes[($i * 3) % $distInstitutes->count()];
            } else {
                $inst = DB::table('field_visit_institutes')->first();
            }

            $instituteId   = (int)$inst->id;
            $instituteName = $inst->institute_name;
            $categoryId    = (int)$inst->category_id;
            $categoryName  = $inst->category_name;
            $orgCode       = $inst->institute_code;

            // Contact info
            $contactPerson = $contactPersons[($i * 7) % count($contactPersons)];
            $contactNumber = '9' . str_pad((string)((861000000 + ($i * 7393)) % 1000000000), 9, '0', STR_PAD_LEFT);

            // Text narratives
            $purpose   = $purposes[($i * 2) % count($purposes)];
            $areas     = $areasList[($i * 3) % count($areasList)];
            $obs       = $observations[($i * 5) % count($observations)];
            $findings  = $findingsList[($i * 3) % count($findingsList)];
            $issues    = $issuesList[($i * 2) % count($issuesList)];
            $recomms   = $recommendationsList[($i * 4) % count($recommendationsList)];
            $actions   = $correctiveActionsList[($i * 3) % count($correctiveActionsList)];
            $remarks   = $remarksList[($i * 2) % count($remarksList)];

            $locTypeDesc = ($addressType === 1) ? "GP Area, Block Office Road" : "Ward Area, Municipal Circle";
            $distName    = $districts->get($districtId)->district_name ?? 'Odisha';
            $instAddress = "At: {$instituteName}, {$locTypeDesc}, District: {$distName}, Odisha - 75" . sprintf('%04d', ($districtId % 1000) * 10 + 1);

            // Geolocation coordinates (District Centroid + realistic jitter)
            $centroid  = $districtCentroids[$districtId] ?? [20.2961, 85.8245];
            $jitterLat = (($i * 13) % 160 - 80) / 1000.0;
            $jitterLon = (($i * 17) % 160 - 80) / 1000.0;
            $lat = round($centroid[0] + $jitterLat, 7);
            $lon = round($centroid[1] + $jitterLon, 7);
            $accuracy = round(3.5 + (($i * 7) % 120) / 10.0, 2);
            $locCapturedAt = "$visitDate $startTime";
            $locAddress = "Near {$instituteName}, {$distName}, Odisha, GPS Geofence Verified";

            // Attribution strictly to user_table_id
            $createdBy = $officerUserTableId;
            $updatedBy = null;
            if ($status === 'reviewed' || $status === 'closed') {
                $supervisorId = $dssoUsers->isNotEmpty() ? $dssoUsers->first()->user_table_id : $officerUserTableId;
                $updatedBy = (int)$supervisorId;
            }

            $createdAt = "$visitDate $endTime";
            $updatedAt = ($status === 'reviewed' || $status === 'closed')
                ? Carbon::parse($createdAt)->addDays(2)->format('Y-m-d H:i:s')
                : $createdAt;

            $records[] = [
                'visit_reference_no'          => $refNo,
                'field_visit_type_id'         => $typeId,
                'visit_date'                  => $visitDate,
                'start_time'                  => $startTime,
                'end_time'                    => $endTime,
                'status'                      => $status,
                'visit_purpose'               => $purpose,
                'institution_type'            => $categoryName,
                'category_id'                 => $categoryId,
                'address_type'                => $addressType,
                'institution_id'              => $instituteId,
                'institution_name'            => $instituteName,
                'organization_code'           => $orgCode,
                'district_id'                 => $districtId,
                'block_id'                    => $blockId,
                'municipality_id'             => $muniId,
                'gp_id'                       => $gpId,
                'village_id'                  => $villageId,
                'ward_id'                     => $wardId,
                'institution_address'         => $instAddress,
                'contact_person'              => $contactPerson,
                'contact_number'              => $contactNumber,
                'areas_inspected'             => $areas,
                'key_observations'            => $obs,
                'findings'                    => $findings,
                'issues_identified'           => $issues,
                'recommendations'             => $recomms,
                'corrective_actions'          => $actions,
                'is_follow_up_required'       => $isFollowUp,
                'follow_up_date'              => $followUpDate,
                'overall_remarks'             => $remarks,
                'latitude'                    => $lat,
                'longitude'                   => $lon,
                'gps_accuracy'                => $accuracy,
                'location_captured_at'        => $locCapturedAt,
                'location_address'            => $locAddress,
                'visiting_officer_id'         => $officerUserTableId,
                'visiting_officer_name'       => $officerName,
                'visiting_officer_designation'=> $officerDesig,
                'other_designation'           => $otherDesig,
                'created_by'                  => $createdBy,
                'updated_by'                  => $updatedBy,
                'deleted_by'                  => null,
                'deleted_at'                  => null,
                'created_at'                  => $createdAt,
                'updated_at'                  => $updatedAt,
            ];
        }

        // 6. Batch Insert into field_visits in a DB Transaction
        $this->command?->info('FieldVisitDemoDataSeeder: Inserting 1,000 records in batches...');
        
        DB::beginTransaction();
        try {
            $chunks = array_chunk($records, 200);
            foreach ($chunks as $chunkIndex => $chunk) {
                DB::table('field_visits')->insert($chunk);
                $this->command?->line('  - Chunk ' . ($chunkIndex + 1) . ' / ' . count($chunks) . ' inserted (' . count($chunk) . ' rows).');
            }
            DB::commit();
            $this->command?->info('FieldVisitDemoDataSeeder: Successfully inserted exactly 1,000 Field Visit records!');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command?->error('FieldVisitDemoDataSeeder: Insertion failed: ' . $e->getMessage());
            throw $e;
        }
    }
}

