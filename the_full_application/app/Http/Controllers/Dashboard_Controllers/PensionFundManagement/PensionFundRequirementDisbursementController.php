<?php

namespace App\Http\Controllers\Dashboard_Controllers\PensionFundManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PensionFundsRequirement;
use App\Models\PensionFundRequirementDates;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class PensionFundRequirementDisbursementController extends Controller
{
    public function pension_fund_requirement_report_of_district(Request $request)
    {
        try {
            \Log::info('District Fund Report Request', $request->all());

            $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
            ->where('is_active', 'active')
            ->orderBy('id', 'desc')
            ->get();

            $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

            \Log::info('Selected Month: '.$month);

            $activeConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
            ->where('for_the_month', $month)
            ->first();

            if (!$activeConfig) {

                \Log::warning('Submission dates not configured for month: '.$month);

                return redirect()->back()->with('error', 'Submission dates are not configured for the selected month.');
            }

            $approve_status = $request->approve_status;

            \Log::info('Approve Status: '.$approve_status);

            $user = auth()->user();
            $userRole = $user->role_id;

            \Log::info('User Role: '.$userRole);

            $blockIds = collect();
            $municipalityIds = collect();

            if (in_array($userRole, [1,2,12,13,14,15,25])) {

                $blockIds = Block::where('is_active','active')->pluck('block_id');
                $municipalityIds = Municipality::where('is_active','active')->pluck('municipality_id');

            } elseif (in_array($userRole, [4,6])) {

                $blockIds = collect([$user->posted_block]);

            } elseif ($userRole == 5) {

                $municipalityIds = collect([$user->posted_municipality]);

            } elseif (in_array($userRole, [8,10])) {

                $blockIds = Block::where('subdivision_id',$user->posted_subdiv)
                ->where('is_active','active')
                ->pluck('block_id');

                $municipalityIds = Municipality::where('subdivision_id',$user->posted_subdiv)
                ->where('is_active','active')
                ->pluck('municipality_id');

            } elseif (in_array($userRole, [9,11])) {

                $blockIds = Block::where('district_id',$user->posted_district)
                ->where('is_active','active')
                ->pluck('block_id');

                $municipalityIds = Municipality::where('district_id',$user->posted_district)
                ->where('is_active','active')
                ->pluck('municipality_id');
            }

            \Log::info('Block Count: '.$blockIds->count());
            \Log::info('Municipality Count: '.$municipalityIds->count());

            $fields = [
                'mbpy_oap_below_80_years' => 1000,
                'mbpy_oap_above_80_years' => 3500,
                'mbpy_wp' => 1000,
                'mbpy_dp' => 1000,
                'mbpy_sdp_below_80_percent' => 1200,
                'mbpy_sdp_above_80_percent' => 3500,
                'mbpy_sdoap' => 3500,
                'mbpy_clp' => 1000,
                'mbpy_wp_aids' => 1000,
                'mbpy_dp_aids' => 1000,
                'mbpy_unmarried_women' => 1000,
                'mbpy_orphan_due_to_covide' => 1000,
                'mbpy_widow_due_to_covid' => 1000,
                'mbpy_divorce_or_destitute' => 1000,
                'mbpy_transgender' => 1000,
            ];

            if ($request->ajax()) {

                \Log::info('AJAX Request Detected');

                if ($approve_status == 3) {

                    \Log::info('Fetching Data Not Provided Districts');

                    $zeroFields = collect($fields)->keys()
                    ->map(fn($f) => "0 as $f")
                    ->implode(',');

                    $query = DB::table('districts as d')
                    ->selectRaw("d.district_name, $zeroFields")
                    ->leftJoin('pension_funds_requirements as pfr', function($join) use ($month){
                        $join->on('d.district_id','=','pfr.district_id')
                        ->where('pfr.for_the_month',$month);
                    })
                    ->whereNull('pfr.id');

                    $totalsRaw = (object) array_fill_keys(array_keys($fields), 0);

                } else {

                    \Log::info('Fetching Approved/Pending District Data');

                    $sumFields = collect($fields)->keys()
                    ->map(fn($f) => "COALESCE(SUM(pfr.$f),0) as $f")
                    ->implode(',');

                    $query = DB::table('pension_funds_requirements as pfr')
                    ->selectRaw("d.district_name, $sumFields")
                    ->leftJoin('districts as d','pfr.district_id','=','d.district_id')
                    ->where('pfr.for_the_month',$month)
                    ->where('pfr.approve_status',$approve_status)
                    ->where('pfr.status',1)
                    ->where(function($q) use ($blockIds,$municipalityIds){
                        $q->whereIn('pfr.block_id',$blockIds)
                        ->orWhereIn('pfr.municipality_id',$municipalityIds);
                    })
                    ->groupBy('d.district_name');

                    $totalsRaw = DB::table('pension_funds_requirements as pfr')
                    ->selectRaw($sumFields)
                    ->where('pfr.for_the_month',$month)
                    ->where('pfr.approve_status',$approve_status)
                    ->where('pfr.status',1)
                    ->where(function($q) use ($blockIds,$municipalityIds){
                        $q->whereIn('pfr.block_id',$blockIds)
                        ->orWhereIn('pfr.municipality_id',$municipalityIds);
                    })
                    ->first();
                }

                \Log::info('Totals Raw', (array)$totalsRaw);

                $totals = [];
                $totalBeneficiaries = 0;
                $totalFund = 0;

                foreach ($fields as $field => $rate) {

                    $value = $totalsRaw->$field ?? 0;

                    $totals[$field] = $value;
                    $totals["funds_$field"] = $value * $rate;

                    $totalBeneficiaries += $value;
                    $totalFund += ($value * $rate);
                }

                $totals['total_beneficiaries'] = $totalBeneficiaries;
                $totals['total_fund'] = $totalFund;

                \Log::info('Calculated Totals', $totals);

                $dt = DataTables::of($query)
                ->addIndexColumn()

                ->filter(function ($query) use ($request) {

                    if ($request->has('search') && $request->search['value']) {

                        $keyword = $request->search['value'];

                        \Log::info('Search Keyword: '.$keyword);

                        $query->where(function ($q) use ($keyword) {

                            $q->where('d.district_name', 'like', "%{$keyword}%");

                        });
                    }
                });

                foreach ($fields as $field => $rate) {

                    $dt->addColumn("funds_$field", fn($row) => ($row->$field ?? 0) * $rate);
                }

                $dt->addColumn('total_beneficiaries', function($row) use ($fields){

                    return collect($fields)->keys()->sum(fn($f) => $row->$f ?? 0);
                });

                $dt->addColumn('total_fund', function($row) use ($fields){

                    return collect($fields)
                    ->map(fn($rate, $field) => ($row->$field ?? 0) * $rate)
                    ->sum();
                });

                \Log::info('Returning DataTables Response');

                return $dt->with(['totals'=>$totals])->make(true);
            }

            return view(
                'dashboard.pension.pension_fund_management.pension_fund_requirement_report_of_district',
                compact('dateConfig','month','approve_status','fields')
            );

        } catch (\Exception $e) {

            \Log::error('District Fund Report Error: '.$e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => $e->getMessage()
            ],500);
        }
    }

    public function pension_fund_requirement_report_of_block_ulb(Request $request)
    {
        try {

            \Log::info('Block/ULB Fund Report Request', $request->all());

            $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
            ->where('is_active', 'active')
            ->orderBy('id', 'desc')
            ->get();

            $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

            \Log::info('Selected Month: '.$month);

            $activeConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
            ->where('for_the_month', $month)
            ->first();

            if (!$activeConfig) {

                \Log::warning('Submission dates not configured for month: '.$month);

                return redirect()->back()->with('error', 'Submission dates are not configured for the selected month.');
            }

            $approve_status = $request->approve_status;

            \Log::info('Approve Status: '.$approve_status);

            $user = auth()->user();
            $userRole = $user->role_id;

            \Log::info('User Role: '.$userRole);

            $blockIds = collect();
            $municipalityIds = collect();

            if (in_array($userRole, [1,2,12,13,14,15,25])) {

                $blockIds = Block::where('is_active','active')->pluck('block_id');
                $municipalityIds = Municipality::where('is_active','active')->pluck('municipality_id');

            } elseif (in_array($userRole, [4,6])) {

                $blockIds = collect([$user->posted_block]);

            } elseif ($userRole == 5) {

                $municipalityIds = collect([$user->posted_municipality]);

            } elseif (in_array($userRole, [8,10])) {

                $blockIds = Block::where('subdivision_id',$user->posted_subdiv)->where('is_active','active')->pluck('block_id');
                $municipalityIds = Municipality::where('subdivision_id',$user->posted_subdiv)->where('is_active','active')->pluck('municipality_id');

            } elseif (in_array($userRole, [9,11])) {

                $blockIds = Block::where('district_id',$user->posted_district)->where('is_active','active')->pluck('block_id');
                $municipalityIds = Municipality::where('district_id',$user->posted_district)->where('is_active','active')->pluck('municipality_id');
            }

            \Log::info('Block Count: '.$blockIds->count());
            \Log::info('Municipality Count: '.$municipalityIds->count());

            $fields = [
                'mbpy_oap_below_80_years' => 1000,
                'mbpy_oap_above_80_years' => 3500,
                'mbpy_wp' => 1000,
                'mbpy_dp' => 1000,
                'mbpy_sdp_below_80_percent' => 1200,
                'mbpy_sdp_above_80_percent' => 3500,
                'mbpy_sdoap' => 3500,
                'mbpy_clp' => 1000,
                'mbpy_wp_aids' => 1000,
                'mbpy_dp_aids' => 1000,
                'mbpy_unmarried_women' => 1000,
                'mbpy_orphan_due_to_covide' => 1000,
                'mbpy_widow_due_to_covid' => 1000,
                'mbpy_divorce_or_destitute' => 1000,
                'mbpy_transgender' => 1000,
            ];

            if ($request->ajax()) {

                \Log::info('AJAX Request Detected');

                if ($approve_status == 3) {

                    \Log::info('Fetching Data Not Provided Block/ULB');

                    $zeroFields = collect($fields)->keys()
                    ->map(fn($f) => "0 as $f")
                    ->implode(',');

                    $blocksQuery = DB::table('blocks as b')
                    ->selectRaw("d.district_name, b.block_name, NULL as municipality_name, $zeroFields")
                    ->leftJoin('districts as d','b.district_id','=','d.district_id')
                    ->leftJoin('pension_funds_requirements as pfr', function($join) use ($month){
                        $join->on('b.block_id','=','pfr.block_id')
                        ->where('pfr.for_the_month',$month);
                    })
                    ->whereNull('pfr.id')
                    ->whereIn('b.block_id',$blockIds);

                    $ulbQuery = DB::table('municipalities as m')
                    ->selectRaw("d.district_name, NULL as block_name, m.municipality_name, $zeroFields")
                    ->leftJoin('districts as d','m.district_id','=','d.district_id')
                    ->leftJoin('pension_funds_requirements as pfr', function($join) use ($month){
                        $join->on('m.municipality_id','=','pfr.municipality_id')
                        ->where('pfr.for_the_month',$month);
                    })
                    ->whereNull('pfr.id')
                    ->whereIn('m.municipality_id',$municipalityIds);

                    $query = $blocksQuery->unionAll($ulbQuery);

                    $totalsRaw = (object) array_fill_keys(array_keys($fields), 0);

                } else {

                    \Log::info('Fetching Approved/Pending Block/ULB Data');

                    $sumFields = collect($fields)->keys()
                    ->map(fn($f) => "COALESCE(SUM(pfr.$f),0) as $f")
                    ->implode(',');

                    $query = DB::table('pension_funds_requirements as pfr')
                    ->selectRaw("d.district_name, b.block_name, m.municipality_name, $sumFields")
                    ->leftJoin('districts as d','pfr.district_id','=','d.district_id')
                    ->leftJoin('blocks as b','pfr.block_id','=','b.block_id')
                    ->leftJoin('municipalities as m','pfr.municipality_id','=','m.municipality_id')
                    ->where('pfr.for_the_month',$month)
                    ->where('pfr.approve_status',$approve_status)
                    ->where('pfr.status',1)
                    ->where(function($q) use ($blockIds,$municipalityIds){
                        $q->whereIn('pfr.block_id',$blockIds)
                        ->orWhereIn('pfr.municipality_id',$municipalityIds);
                    })
                    ->groupBy('d.district_name','b.block_name','m.municipality_name');

                    $totalsRaw = DB::table('pension_funds_requirements as pfr')
                    ->selectRaw($sumFields)
                    ->where('pfr.for_the_month',$month)
                    ->where('pfr.approve_status',$approve_status)
                    ->where('pfr.status',1)
                    ->where(function($q) use ($blockIds,$municipalityIds){
                        $q->whereIn('pfr.block_id',$blockIds)
                        ->orWhereIn('pfr.municipality_id',$municipalityIds);
                    })
                    ->first();
                }

                \Log::info('Totals Raw', (array)$totalsRaw);

                $totals = [];
                $totalBeneficiaries = 0;
                $totalFund = 0;

                foreach ($fields as $field => $rate) {

                    $value = $totalsRaw->$field ?? 0;

                    $totals[$field] = $value;
                    $totals["funds_$field"] = $value * $rate;

                    $totalBeneficiaries += $value;
                    $totalFund += ($value * $rate);
                }

                $totals['total_beneficiaries'] = $totalBeneficiaries;
                $totals['total_fund'] = $totalFund;

                \Log::info('Calculated Totals', $totals);

                $dt = DataTables::of($query)

                ->addIndexColumn()

                ->addColumn('area_name', fn($row) => $row->block_name ?? $row->municipality_name)

                ->filter(function ($query) use ($request) {

                    if ($request->has('search') && $request->search['value']) {

                        $keyword = $request->search['value'];

                        \Log::info('Search Keyword: '.$keyword);

                        $query->where(function ($q) use ($keyword) {
                            $q->where('d.district_name', 'like', "%{$keyword}%")
                            ->orWhere('b.block_name', 'like', "%{$keyword}%")
                            ->orWhere('m.municipality_name', 'like', "%{$keyword}%");
                        });
                    }
                });

                foreach ($fields as $field => $rate) {
                    $dt->addColumn("funds_$field", fn($row) => ($row->$field ?? 0) * $rate);
                }

                $dt->addColumn('total_beneficiaries', function($row) use ($fields){
                    return collect($fields)->keys()->sum(fn($f) => $row->$f ?? 0);
                });

                $dt->addColumn('total_fund', function($row) use ($fields){
                    return collect($fields)
                    ->map(fn($rate, $field) => ($row->$field ?? 0) * $rate)
                    ->sum();
                });

                \Log::info('Returning DataTables Response');

                return $dt->with(['totals'=>$totals])->make(true);
            }

            return view(
                'dashboard.pension.pension_fund_management.pension_fund_requirement_report_of_block_ulb',
                compact('dateConfig','month','approve_status','fields')
            );

        } catch (\Exception $e) {

            \Log::error('Block/ULB Fund Report Error: '.$e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => $e->getMessage()
            ],500);
        }
    }

    public function pension_dpr_report_of_district(Request $request)
    {
        try {
            \Log::info('District DPR Report Request', $request->all());

            $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
            ->where('is_active', 'active')
            ->orderBy('id', 'desc')
            ->get();

            $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

            \Log::info('Selected Month: '.$month);

            $activeConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
            ->where('for_the_month', $month)
            ->first();

            if (!$activeConfig) {

                \Log::warning('Submission dates not configured for month: '.$month);

                return redirect()->back()->with('error', 'Submission dates are not configured for the selected month.');
            }

            $approve_status = $request->approve_status;

            \Log::info('Approve Status: '.$approve_status);

            $user = auth()->user();
            $userRole = $user->role_id;

            \Log::info('User Role: '.$userRole);

            $blockIds = collect();
            $municipalityIds = collect();

            if (in_array($userRole, [1,2,12,13,14,15,25])) {

                $blockIds = Block::where('is_active','active')->pluck('block_id');
                $municipalityIds = Municipality::where('is_active','active')->pluck('municipality_id');

            } elseif (in_array($userRole, [4,6])) {

                $blockIds = collect([$user->posted_block]);

            } elseif ($userRole == 5) {

                $municipalityIds = collect([$user->posted_municipality]);

            } elseif (in_array($userRole, [8,10])) {

                $blockIds = Block::where('subdivision_id',$user->posted_subdiv)
                ->where('is_active','active')
                ->pluck('block_id');

                $municipalityIds = Municipality::where('subdivision_id',$user->posted_subdiv)
                ->where('is_active','active')
                ->pluck('municipality_id');

            } elseif (in_array($userRole, [9,11])) {

                $blockIds = Block::where('district_id',$user->posted_district)
                ->where('is_active','active')
                ->pluck('block_id');

                $municipalityIds = Municipality::where('district_id',$user->posted_district)
                ->where('is_active','active')
                ->pluck('municipality_id');
            }

            \Log::info('Block Count: '.$blockIds->count());
            \Log::info('Municipality Count: '.$municipalityIds->count());

            $fields = [
                'mbpy_oap_below_80_years' => 1000,
                'mbpy_oap_above_80_years' => 3500,
                'mbpy_wp' => 1000,
                'mbpy_dp' => 1000,
                'mbpy_sdp_below_80_percent' => 1200,
                'mbpy_sdp_above_80_percent' => 3500,
                'mbpy_sdoap' => 3500,
                'mbpy_clp' => 1000,
                'mbpy_wp_aids' => 1000,
                'mbpy_dp_aids' => 1000,
                'mbpy_unmarried_women' => 1000,
                'mbpy_orphan_due_to_covide' => 1000,
                'mbpy_widow_due_to_covid' => 1000,
                'mbpy_divorce_or_destitute' => 1000,
                'mbpy_transgender' => 1000,
                'death_reported' => 0,
            ];

            if ($request->ajax()) {

                \Log::info('AJAX Request Detected');

                if ($approve_status == 3) {

                    \Log::info('Fetching Data Not Provided Districts');

                    $zeroFields = collect($fields)->keys()
                    ->map(fn($f) => "0 as $f")
                    ->implode(',');

                    $query = DB::table('districts as d')
                    ->selectRaw("d.district_name, $zeroFields")
                    ->leftJoin('daily_pension_disbursements as pfr', function($join) use ($month){
                        $join->on('d.district_id','=','pfr.district_id')
                        ->where('pfr.for_the_month',$month);
                    })
                    ->whereNull('pfr.id');

                    $totalsRaw = (object) array_fill_keys(array_keys($fields), 0);

                } else {

                    \Log::info('Fetching Approved/Pending District Data');

                    $sumFields = collect($fields)->keys()
                    ->map(fn($f) => "COALESCE(SUM(pfr.$f),0) as $f")
                    ->implode(',');

                    $query = DB::table('daily_pension_disbursements as pfr')
                    ->selectRaw("d.district_name, $sumFields")
                    ->leftJoin('districts as d','pfr.district_id','=','d.district_id')
                    ->where('pfr.for_the_month',$month)
                    ->where('pfr.approve_status',$approve_status)
                    ->where('pfr.status',1)
                    ->where(function($q) use ($blockIds,$municipalityIds){
                        $q->whereIn('pfr.block_id',$blockIds)
                        ->orWhereIn('pfr.municipality_id',$municipalityIds);
                    })
                    ->groupBy('d.district_name');

                    $totalsRaw = DB::table('daily_pension_disbursements as pfr')
                    ->selectRaw($sumFields)
                    ->where('pfr.for_the_month',$month)
                    ->where('pfr.approve_status',$approve_status)
                    ->where('pfr.status',1)
                    ->where(function($q) use ($blockIds,$municipalityIds){
                        $q->whereIn('pfr.block_id',$blockIds)
                        ->orWhereIn('pfr.municipality_id',$municipalityIds);
                    })
                    ->first();
                }

                \Log::info('Totals Raw', (array)$totalsRaw);

                $totals = [];
                $totalBeneficiaries = 0;
                $totalFund = 0;

                foreach ($fields as $field => $rate) {

                    $value = $totalsRaw->$field ?? 0;

                    $totals[$field] = $value;
                    $totals["funds_$field"] = $value * $rate;

                    $totalBeneficiaries += $value;
                    $totalFund += ($value * $rate);
                }

                $totals['total_beneficiaries'] = $totalBeneficiaries;
                $totals['total_fund'] = $totalFund;

                \Log::info('Calculated Totals', $totals);

                $dt = DataTables::of($query)
                ->addIndexColumn()

                ->filter(function ($query) use ($request) {

                    if ($request->has('search') && $request->search['value']) {

                        $keyword = $request->search['value'];

                        \Log::info('Search Keyword: '.$keyword);

                        $query->where(function ($q) use ($keyword) {

                            $q->where('d.district_name', 'like', "%{$keyword}%");

                        });
                    }
                });

                foreach ($fields as $field => $rate) {

                    $dt->addColumn("funds_$field", fn($row) => ($row->$field ?? 0) * $rate);
                }

                $dt->addColumn('total_beneficiaries', function($row) use ($fields){

                    return collect($fields)->keys()->sum(fn($f) => $row->$f ?? 0);
                });

                $dt->addColumn('total_fund', function($row) use ($fields){

                    return collect($fields)
                    ->map(fn($rate, $field) => ($row->$field ?? 0) * $rate)
                    ->sum();
                });

                \Log::info('Returning DataTables Response');

                return $dt->with(['totals'=>$totals])->make(true);
            }

            return view(
                'dashboard.pension.pension_fund_management.pension_dpr_report_of_district',
                compact('dateConfig','month','approve_status','fields')
            );

        } catch (\Exception $e) {

            \Log::error('District Fund Report Error: '.$e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => $e->getMessage()
            ],500);
        }
    }

    public function pension_dpr_report_of_block_ulb(Request $request)
    {
        try {
            \Log::info('Block/ULB DPR Report Request', $request->all());

            $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
            ->where('is_active', 'active')
            ->orderBy('id', 'desc')
            ->get();

            $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

            \Log::info('Selected Month: '.$month);

            $activeConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
            ->where('for_the_month', $month)
            ->first();

            if (!$activeConfig) {

                \Log::warning('Submission dates not configured for month: '.$month);

                return redirect()->back()->with('error', 'Submission dates are not configured for the selected month.');
            }

            $approve_status = $request->approve_status;

            \Log::info('Approve Status: '.$approve_status);

            $user = auth()->user();
            $userRole = $user->role_id;

            \Log::info('User Role: '.$userRole);

            $blockIds = collect();
            $municipalityIds = collect();

            if (in_array($userRole, [1,2,12,13,14,15,25])) {

                $blockIds = Block::where('is_active','active')->pluck('block_id');
                $municipalityIds = Municipality::where('is_active','active')->pluck('municipality_id');

            } elseif (in_array($userRole, [4,6])) {

                $blockIds = collect([$user->posted_block]);

            } elseif ($userRole == 5) {

                $municipalityIds = collect([$user->posted_municipality]);

            } elseif (in_array($userRole, [8,10])) {

                $blockIds = Block::where('subdivision_id',$user->posted_subdiv)->where('is_active','active')->pluck('block_id');
                $municipalityIds = Municipality::where('subdivision_id',$user->posted_subdiv)->where('is_active','active')->pluck('municipality_id');

            } elseif (in_array($userRole, [9,11])) {

                $blockIds = Block::where('district_id',$user->posted_district)->where('is_active','active')->pluck('block_id');
                $municipalityIds = Municipality::where('district_id',$user->posted_district)->where('is_active','active')->pluck('municipality_id');
            }

            \Log::info('Block Count: '.$blockIds->count());
            \Log::info('Municipality Count: '.$municipalityIds->count());

            $fields = [
                'mbpy_oap_below_80_years' => 1000,
                'mbpy_oap_above_80_years' => 3500,
                'mbpy_wp' => 1000,
                'mbpy_dp' => 1000,
                'mbpy_sdp_below_80_percent' => 1200,
                'mbpy_sdp_above_80_percent' => 3500,
                'mbpy_sdoap' => 3500,
                'mbpy_clp' => 1000,
                'mbpy_wp_aids' => 1000,
                'mbpy_dp_aids' => 1000,
                'mbpy_unmarried_women' => 1000,
                'mbpy_orphan_due_to_covide' => 1000,
                'mbpy_widow_due_to_covid' => 1000,
                'mbpy_divorce_or_destitute' => 1000,
                'mbpy_transgender' => 1000,
                'death_reported' => 0,
            ];

            if ($request->ajax()) {

                \Log::info('AJAX Request Detected');

                if ($approve_status == 3) {

                    \Log::info('Fetching Data Not Provided Block/ULB');

                    $zeroFields = collect($fields)->keys()
                    ->map(fn($f) => "0 as $f")
                    ->implode(',');

                    $blocksQuery = DB::table('blocks as b')
                    ->selectRaw("d.district_name, b.block_name, NULL as municipality_name, $zeroFields")
                    ->leftJoin('districts as d','b.district_id','=','d.district_id')
                    ->leftJoin('daily_pension_disbursements as pfr', function($join) use ($month){
                        $join->on('b.block_id','=','pfr.block_id')
                        ->where('pfr.for_the_month',$month);
                    })
                    ->whereNull('pfr.id')
                    ->whereIn('b.block_id',$blockIds);

                    $ulbQuery = DB::table('municipalities as m')
                    ->selectRaw("d.district_name, NULL as block_name, m.municipality_name, $zeroFields")
                    ->leftJoin('districts as d','m.district_id','=','d.district_id')
                    ->leftJoin('daily_pension_disbursements as pfr', function($join) use ($month){
                        $join->on('m.municipality_id','=','pfr.municipality_id')
                        ->where('pfr.for_the_month',$month);
                    })
                    ->whereNull('pfr.id')
                    ->whereIn('m.municipality_id',$municipalityIds);

                    $query = $blocksQuery->unionAll($ulbQuery);

                    $totalsRaw = (object) array_fill_keys(array_keys($fields), 0);

                } else {

                    \Log::info('Fetching Approved/Pending Block/ULB Data');

                    $sumFields = collect($fields)->keys()
                    ->map(fn($f) => "COALESCE(SUM(pfr.$f),0) as $f")
                    ->implode(',');

                    $query = DB::table('daily_pension_disbursements as pfr')
                    ->selectRaw("d.district_name, b.block_name, m.municipality_name, $sumFields")
                    ->leftJoin('districts as d','pfr.district_id','=','d.district_id')
                    ->leftJoin('blocks as b','pfr.block_id','=','b.block_id')
                    ->leftJoin('municipalities as m','pfr.municipality_id','=','m.municipality_id')
                    ->where('pfr.for_the_month',$month)
                    ->where('pfr.approve_status',$approve_status)
                    ->where('pfr.status',1)
                    ->where(function($q) use ($blockIds,$municipalityIds){
                        $q->whereIn('pfr.block_id',$blockIds)
                        ->orWhereIn('pfr.municipality_id',$municipalityIds);
                    })
                    ->groupBy('d.district_name','b.block_name','m.municipality_name');

                    $totalsRaw = DB::table('daily_pension_disbursements as pfr')
                    ->selectRaw($sumFields)
                    ->where('pfr.for_the_month',$month)
                    ->where('pfr.approve_status',$approve_status)
                    ->where('pfr.status',1)
                    ->where(function($q) use ($blockIds,$municipalityIds){
                        $q->whereIn('pfr.block_id',$blockIds)
                        ->orWhereIn('pfr.municipality_id',$municipalityIds);
                    })
                    ->first();
                }

                \Log::info('Totals Raw', (array)$totalsRaw);

                $totals = [];
                $totalBeneficiaries = 0;
                $totalFund = 0;

                foreach ($fields as $field => $rate) {

                    $value = $totalsRaw->$field ?? 0;

                    $totals[$field] = $value;
                    $totals["funds_$field"] = $value * $rate;

                    $totalBeneficiaries += $value;
                    $totalFund += ($value * $rate);
                }

                $totals['total_beneficiaries'] = $totalBeneficiaries;
                $totals['total_fund'] = $totalFund;

                \Log::info('Calculated Totals', $totals);

                $dt = DataTables::of($query)

                ->addIndexColumn()

                ->addColumn('area_name', fn($row) => $row->block_name ?? $row->municipality_name)

                ->filter(function ($query) use ($request) {

                    if ($request->has('search') && $request->search['value']) {

                        $keyword = $request->search['value'];

                        \Log::info('Search Keyword: '.$keyword);

                        $query->where(function ($q) use ($keyword) {
                            $q->where('d.district_name', 'like', "%{$keyword}%")
                            ->orWhere('b.block_name', 'like', "%{$keyword}%")
                            ->orWhere('m.municipality_name', 'like', "%{$keyword}%");
                        });
                    }
                });

                foreach ($fields as $field => $rate) {
                    $dt->addColumn("funds_$field", fn($row) => ($row->$field ?? 0) * $rate);
                }

                $dt->addColumn('total_beneficiaries', function($row) use ($fields){
                    return collect($fields)->keys()->sum(fn($f) => $row->$f ?? 0);
                });

                $dt->addColumn('total_fund', function($row) use ($fields){
                    return collect($fields)
                    ->map(fn($rate, $field) => ($row->$field ?? 0) * $rate)
                    ->sum();
                });

                \Log::info('Returning DataTables Response');

                return $dt->with(['totals'=>$totals])->make(true);
            }

            return view(
                'dashboard.pension.pension_fund_management.pension_dpr_report_of_block_ulb',
                compact('dateConfig','month','approve_status','fields')
            );

        } catch (\Exception $e) {

            \Log::error('Block/ULB Fund Report Error: '.$e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => $e->getMessage()
            ],500);
        }
    }

    /*************************************************************************************************************************************/

    /**
     * These 2 codes are not using just keeping it for the reference
     */
    public function pension_fund_requirement_report_of_districts(Request $request)
    {
        $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('is_active', 'active')
        ->orderBy('id', 'desc')
        ->get();

        $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

        $activeConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('for_the_month', $month)
        ->first();

        if (!$activeConfig) {
            return redirect()->back()->with('error', 'Submission dates are not configured for the selected month. Please contact admin.');
        }

        $approve_status = $request->approve_status;

        if ($request->ajax() || $request->wantsJson()) {


            $query = District::select('districts.district_id', 'districts.district_name',

                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_below_80_years),0) as mbpy_oap_below_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_above_80_years),0) as mbpy_oap_above_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp),0) as mbpy_wp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp),0) as mbpy_dp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_below_80_percent),0) as mbpy_sdp_below_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_above_80_percent),0) as mbpy_sdp_above_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdoap),0) as mbpy_sdoap'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_clp),0) as mbpy_clp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp_aids),0) as mbpy_wp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp_aids),0) as mbpy_dp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_unmarried_women),0) as mbpy_unmarried_women'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_orphan_due_to_covide),0) as mbpy_orphan_due_to_covide'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_widow_due_to_covid),0) as mbpy_widow_due_to_covid'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_divorce_or_destitute),0) as mbpy_divorce_or_destitute'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_transgender),0) as mbpy_transgender')
            )
            ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month, $approve_status) {
                $join->on('districts.district_id', '=', 'pfr.district_id')
                ->where('pfr.for_the_month', $month)
                ->where('pfr.approve_status', $approve_status)
                ->where('pfr.status', 1);
            })
            ->groupBy('districts.district_id', 'districts.district_name')
            ->orderBy('districts.district_name', 'asc');

            $user = auth()->user();
            $userRole = $user->role_id;
            if (in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {

            } elseif (in_array($userRole, [9, 11])) {
                $query->where('districts.district_id', $user->posted_district);
            }
            return DataTables::of($query)

            ->addIndexColumn()
            ->addColumn('funds_mbpy_oap_below_80_years', fn($row) => $row->mbpy_oap_below_80_years * 1000)
            ->addColumn('funds_mbpy_oap_above_80_years', fn($row) => $row->mbpy_oap_above_80_years * 3500)
            ->addColumn('funds_mbpy_wp', fn($row) => $row->mbpy_wp * 1000)
            ->addColumn('funds_mbpy_dp', fn($row) => $row->mbpy_dp * 1000)
            ->addColumn('funds_mbpy_sdp_below_80_percent', fn($row) => $row->mbpy_sdp_below_80_percent * 1200)
            ->addColumn('funds_mbpy_sdp_above_80_percent', fn($row) => $row->mbpy_sdp_above_80_percent * 3500)
            ->addColumn('funds_mbpy_sdoap', fn($row) => $row->mbpy_sdoap * 3500)
            ->addColumn('funds_mbpy_clp', fn($row) => $row->mbpy_clp * 1000)
            ->addColumn('funds_mbpy_wp_aids', fn($row) => $row->mbpy_wp_aids * 1000)
            ->addColumn('funds_mbpy_dp_aids', fn($row) => $row->mbpy_dp_aids * 1000)
            ->addColumn('funds_mbpy_unmarried_women', fn($row) => $row->mbpy_unmarried_women * 1000)
            ->addColumn('funds_mbpy_orphan_due_to_covide', fn($row) => $row->mbpy_orphan_due_to_covide * 1000)
            ->addColumn('funds_mbpy_widow_due_to_covid', fn($row) => $row->mbpy_widow_due_to_covid * 1000)
            ->addColumn('funds_mbpy_divorce_or_destitute', fn($row) => $row->mbpy_divorce_or_destitute * 1000)
            ->addColumn('funds_mbpy_transgender', fn($row) => $row->mbpy_transgender * 1000)
            ->addColumn('total_beneficiaries', function ($row) {
                return
                ($row->mbpy_oap_below_80_years) +
                ($row->mbpy_oap_above_80_years) +
                ($row->mbpy_wp) +
                ($row->mbpy_dp) +
                ($row->mbpy_sdp_below_80_percent) +
                ($row->mbpy_sdp_above_80_percent) +
                ($row->mbpy_sdoap) +
                ($row->mbpy_clp) +
                ($row->mbpy_wp_aids) +
                ($row->mbpy_dp_aids) +
                ($row->mbpy_unmarried_women) +
                ($row->mbpy_orphan_due_to_covide) +
                ($row->mbpy_widow_due_to_covid) +
                ($row->mbpy_divorce_or_destitute) +
                ($row->mbpy_transgender);
            })
            ->addColumn('total_fund', function ($row) {
                return
                ($row->mbpy_oap_below_80_years * 1000) +
                ($row->mbpy_oap_above_80_years * 3500) +
                ($row->mbpy_wp * 1000) +
                ($row->mbpy_dp * 1000) +
                ($row->mbpy_sdp_below_80_percent * 1200) +
                ($row->mbpy_sdp_above_80_percent * 3500) +
                ($row->mbpy_sdoap * 3500) +
                ($row->mbpy_clp * 1000) +
                ($row->mbpy_wp_aids * 1000) +
                ($row->mbpy_dp_aids * 1000) +
                ($row->mbpy_unmarried_women * 1000) +
                ($row->mbpy_orphan_due_to_covide * 1000) +
                ($row->mbpy_widow_due_to_covid * 1000) +
                ($row->mbpy_divorce_or_destitute * 1000) +
                ($row->mbpy_transgender * 1000);
            })
            ->make(true);
        }

        return view('dashboard.pension.pension_fund_management.pension_fund_requirement_report_of_districts', compact('dateConfig', 'month', 'approve_status'));
    }

    public function pension_fund_requirement_report_of_district_data(Request $request)
    {
        $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('is_active', 'active')
        ->orderBy('id', 'desc')
        ->get();

        $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

        $activeConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('for_the_month', $month)
        ->first();

        if (!$activeConfig) {
            return redirect()->back()->with('error', 'Submission dates are not configured for the selected month. Please contact admin.');
        }

        $approve_status = $request->approve_status;

        if ($request->ajax() || $request->wantsJson()) {

            $baseQuery = District::select(
                'districts.district_id',
                'districts.district_name',

                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_below_80_years),0) as mbpy_oap_below_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_above_80_years),0) as mbpy_oap_above_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp),0) as mbpy_wp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp),0) as mbpy_dp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_below_80_percent),0) as mbpy_sdp_below_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_above_80_percent),0) as mbpy_sdp_above_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdoap),0) as mbpy_sdoap'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_clp),0) as mbpy_clp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp_aids),0) as mbpy_wp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp_aids),0) as mbpy_dp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_unmarried_women),0) as mbpy_unmarried_women'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_orphan_due_to_covide),0) as mbpy_orphan_due_to_covide'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_widow_due_to_covid),0) as mbpy_widow_due_to_covid'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_divorce_or_destitute),0) as mbpy_divorce_or_destitute'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_transgender),0) as mbpy_transgender')
            )
            ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month, $approve_status) {

                $join->on('districts.district_id', '=', 'pfr.district_id')
                ->where('pfr.for_the_month', $month)
                ->where('pfr.status', 1);

                if ($approve_status !== null && $approve_status !== '') {
                    $join->where('pfr.approve_status', $approve_status);
                }
            })
            ->groupBy('districts.district_id', 'districts.district_name');

            $user = auth()->user();
            if (in_array($user->role_id, [9, 11])) {
                $baseQuery->where('districts.district_id', $user->posted_district);
            }

            $totalsRaw = \DB::table('districts')
            ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month, $approve_status) {

                $join->on('districts.district_id', '=', 'pfr.district_id')
                ->where('pfr.for_the_month', $month)
                ->where('pfr.status', 1);

                if ($approve_status !== null && $approve_status !== '') {
                    $join->where('pfr.approve_status', $approve_status);
                }
            })
            ->when(in_array($user->role_id, [9, 11]), function ($q) use ($user) {
                $q->where('districts.district_id', $user->posted_district);
            })
            ->select(
                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_below_80_years),0) as mbpy_oap_below_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_above_80_years),0) as mbpy_oap_above_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp),0) as mbpy_wp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp),0) as mbpy_dp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_below_80_percent),0) as mbpy_sdp_below_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_above_80_percent),0) as mbpy_sdp_above_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdoap),0) as mbpy_sdoap'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_clp),0) as mbpy_clp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp_aids),0) as mbpy_wp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp_aids),0) as mbpy_dp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_unmarried_women),0) as mbpy_unmarried_women'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_orphan_due_to_covide),0) as mbpy_orphan_due_to_covide'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_widow_due_to_covid),0) as mbpy_widow_due_to_covid'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_divorce_or_destitute),0) as mbpy_divorce_or_destitute'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_transgender),0) as mbpy_transgender')
            )
            ->first();

            $totals = [
                'mbpy_oap_below_80_years' => $totalsRaw->mbpy_oap_below_80_years,
                'funds_mbpy_oap_below_80_years' => $totalsRaw->mbpy_oap_below_80_years * 1000,

                'mbpy_oap_above_80_years' => $totalsRaw->mbpy_oap_above_80_years,
                'funds_mbpy_oap_above_80_years' => $totalsRaw->mbpy_oap_above_80_years * 3500,

                'mbpy_wp' => $totalsRaw->mbpy_wp,
                'funds_mbpy_wp' => $totalsRaw->mbpy_wp * 1000,

                'mbpy_dp' => $totalsRaw->mbpy_dp,
                'funds_mbpy_dp' => $totalsRaw->mbpy_dp * 1000,

                'mbpy_sdp_below_80_percent' => $totalsRaw->mbpy_sdp_below_80_percent,
                'funds_mbpy_sdp_below_80_percent' => $totalsRaw->mbpy_sdp_below_80_percent * 1200,

                'mbpy_sdp_above_80_percent' => $totalsRaw->mbpy_sdp_above_80_percent,
                'funds_mbpy_sdp_above_80_percent' => $totalsRaw->mbpy_sdp_above_80_percent * 3500,

                'mbpy_sdoap' => $totalsRaw->mbpy_sdoap,
                'funds_mbpy_sdoap' => $totalsRaw->mbpy_sdoap * 3500,

                'mbpy_clp' => $totalsRaw->mbpy_clp,
                'funds_mbpy_clp' => $totalsRaw->mbpy_clp * 1000,

                'mbpy_wp_aids' => $totalsRaw->mbpy_wp_aids,
                'funds_mbpy_wp_aids' => $totalsRaw->mbpy_wp_aids * 1000,

                'mbpy_dp_aids' => $totalsRaw->mbpy_dp_aids,
                'funds_mbpy_dp_aids' => $totalsRaw->mbpy_dp_aids * 1000,

                'mbpy_unmarried_women' => $totalsRaw->mbpy_unmarried_women,
                'funds_mbpy_unmarried_women' => $totalsRaw->mbpy_unmarried_women * 1000,

                'mbpy_orphan_due_to_covide' => $totalsRaw->mbpy_orphan_due_to_covide,
                'funds_mbpy_orphan_due_to_covide' => $totalsRaw->mbpy_orphan_due_to_covide * 1000,

                'mbpy_widow_due_to_covid' => $totalsRaw->mbpy_widow_due_to_covid,
                'funds_mbpy_widow_due_to_covid' => $totalsRaw->mbpy_widow_due_to_covid * 1000,

                'mbpy_divorce_or_destitute' => $totalsRaw->mbpy_divorce_or_destitute,
                'funds_mbpy_divorce_or_destitute' => $totalsRaw->mbpy_divorce_or_destitute * 1000,

                'mbpy_transgender' => $totalsRaw->mbpy_transgender,
                'funds_mbpy_transgender' => $totalsRaw->mbpy_transgender * 1000,
            ];

            $totals['total_beneficiaries'] = array_sum([
                $totalsRaw->mbpy_oap_below_80_years,
                $totalsRaw->mbpy_oap_above_80_years,
                $totalsRaw->mbpy_wp,
                $totalsRaw->mbpy_dp,
                $totalsRaw->mbpy_sdp_below_80_percent,
                $totalsRaw->mbpy_sdp_above_80_percent,
                $totalsRaw->mbpy_sdoap,
                $totalsRaw->mbpy_clp,
                $totalsRaw->mbpy_wp_aids,
                $totalsRaw->mbpy_dp_aids,
                $totalsRaw->mbpy_unmarried_women,
                $totalsRaw->mbpy_orphan_due_to_covide,
                $totalsRaw->mbpy_widow_due_to_covid,
                $totalsRaw->mbpy_divorce_or_destitute,
                $totalsRaw->mbpy_transgender,
            ]);

            $totals['total_fund'] =
            $totals['funds_mbpy_oap_below_80_years'] +
            $totals['funds_mbpy_oap_above_80_years'] +
            $totals['funds_mbpy_wp'] +
            $totals['funds_mbpy_dp'] +
            $totals['funds_mbpy_sdp_below_80_percent'] +
            $totals['funds_mbpy_sdp_above_80_percent'] +
            $totals['funds_mbpy_sdoap'] +
            $totals['funds_mbpy_clp'] +
            $totals['funds_mbpy_wp_aids'] +
            $totals['funds_mbpy_dp_aids'] +
            $totals['funds_mbpy_unmarried_women'] +
            $totals['funds_mbpy_orphan_due_to_covide'] +
            $totals['funds_mbpy_widow_due_to_covid'] +
            $totals['funds_mbpy_divorce_or_destitute'] +
            $totals['funds_mbpy_transgender'];

            return DataTables::of($baseQuery)
            ->addIndexColumn()

            ->addColumn('funds_mbpy_oap_below_80_years', fn($row) => $row->mbpy_oap_below_80_years * 1000)
            ->addColumn('funds_mbpy_oap_above_80_years', fn($row) => $row->mbpy_oap_above_80_years * 3500)
            ->addColumn('funds_mbpy_wp', fn($row) => $row->mbpy_wp * 1000)
            ->addColumn('funds_mbpy_dp', fn($row) => $row->mbpy_dp * 1000)
            ->addColumn('funds_mbpy_sdp_below_80_percent', fn($row) => $row->mbpy_sdp_below_80_percent * 1200)
            ->addColumn('funds_mbpy_sdp_above_80_percent', fn($row) => $row->mbpy_sdp_above_80_percent * 3500)
            ->addColumn('funds_mbpy_sdoap', fn($row) => $row->mbpy_sdoap * 3500)
            ->addColumn('funds_mbpy_clp', fn($row) => $row->mbpy_clp * 1000)
            ->addColumn('funds_mbpy_wp_aids', fn($row) => $row->mbpy_wp_aids * 1000)
            ->addColumn('funds_mbpy_dp_aids', fn($row) => $row->mbpy_dp_aids * 1000)
            ->addColumn('funds_mbpy_unmarried_women', fn($row) => $row->mbpy_unmarried_women * 1000)
            ->addColumn('funds_mbpy_orphan_due_to_covide', fn($row) => $row->mbpy_orphan_due_to_covide * 1000)
            ->addColumn('funds_mbpy_widow_due_to_covid', fn($row) => $row->mbpy_widow_due_to_covid * 1000)
            ->addColumn('funds_mbpy_divorce_or_destitute', fn($row) => $row->mbpy_divorce_or_destitute * 1000)
            ->addColumn('funds_mbpy_transgender', fn($row) => $row->mbpy_transgender * 1000)

            ->addColumn('total_beneficiaries', function ($row) {
                return
                $row->mbpy_oap_below_80_years +
                $row->mbpy_oap_above_80_years +
                $row->mbpy_wp +
                $row->mbpy_dp +
                $row->mbpy_sdp_below_80_percent +
                $row->mbpy_sdp_above_80_percent +
                $row->mbpy_sdoap +
                $row->mbpy_clp +
                $row->mbpy_wp_aids +
                $row->mbpy_dp_aids +
                $row->mbpy_unmarried_women +
                $row->mbpy_orphan_due_to_covide +
                $row->mbpy_widow_due_to_covid +
                $row->mbpy_divorce_or_destitute +
                $row->mbpy_transgender;
            })

            ->addColumn('total_fund', function ($row) {
                return
                ($row->mbpy_oap_below_80_years * 1000) +
                ($row->mbpy_oap_above_80_years * 3500) +
                ($row->mbpy_wp * 1000) +
                ($row->mbpy_dp * 1000) +
                ($row->mbpy_sdp_below_80_percent * 1200) +
                ($row->mbpy_sdp_above_80_percent * 3500) +
                ($row->mbpy_sdoap * 3500) +
                ($row->mbpy_clp * 1000) +
                ($row->mbpy_wp_aids * 1000) +
                ($row->mbpy_dp_aids * 1000) +
                ($row->mbpy_unmarried_women * 1000) +
                ($row->mbpy_orphan_due_to_covide * 1000) +
                ($row->mbpy_widow_due_to_covid * 1000) +
                ($row->mbpy_divorce_or_destitute * 1000) +
                ($row->mbpy_transgender * 1000);
            })

            ->with(['totals' => $totals])

            ->filter(function ($query) use ($request) {

                if ($request->has('search') && $request->search['value'] != '') {

                    $search = $request->search['value'];

                    $query->havingRaw("
                        districts.district_name LIKE ?
                        ", ["%{$search}%"]);
                }
            })

            ->make(true);
        }

        return view(
            'dashboard.pension.pension_fund_management.pension_fund_requirement_report_of_district_data',
            compact('dateConfig', 'month', 'approve_status')
        );
    }
}
