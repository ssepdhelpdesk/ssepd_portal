<?php

namespace App\Http\Controllers\Dashboard_Controllers;

/*Basic Requirements*/
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use App\Models\ApplicationStageHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Mail\NgoRegistrationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AadhaarVerifier;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Collection;

/*Controller Requirements*/
use App\Models\PensionFundsRequirement;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\Grampanchayat;
use App\Models\WardMaster;
use App\Models\PensionDisbursementAuthority;
use App\Models\PensionFundRequirementDates;
use App\Models\MonthlyPensionDisbursemenet;
use App\Models\DailyPensionDisbursement;
use Yajra\DataTables\Facades\DataTables;

class DailyPensionDisbursementController extends Controller
{
/**
* Display a listing of the resource.
*/
public function index()
{
    $user = auth()->user();
    $userRole = $user->role_id;
    $today_date = Carbon::today('Asia/Kolkata')->format('Y-m-d');

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
    ->where('status', 1)
    ->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $startDate   = $dateConfig->start_date;
    $endDate     = $dateConfig->end_date;
    $forTheMonth = $dateConfig->for_the_month;

    if ($user->role_name == 'BSSO') {
        $gp_ward_id = Grampanchayat::where('block_id', $user->posted_block)
        ->where('is_active', 'active')
        ->get();
        $gpIds   = $gp_ward_id->pluck('gp_id')->toArray();
        $wardIds = [];
    } elseif ($user->role_name == 'MEO') {
        $gp_ward_id = WardMaster::where('municipal_area_code', $user->posted_municipality)
        ->where('is_active', '1')
        ->get();
        $gpIds   = [];
        $wardIds = $gp_ward_id->pluck('ward_code')->toArray();
    } else {
        return redirect()->back()->with('error', 'You have no specific permission for this page. Please contact admin.');
    }
    return view('dashboard.pension.dailypension.daily_pension_disbursement', compact('gp_ward_id', 'user', 'startDate', 'endDate', 'forTheMonth'));
}

/**
* Show the form for creating a new resource.
*/
public function create()
{
//
}

/**
* Store a newly created resource in storage.
*/

/*public function store(Request $request)
{
$rows = count($request->gp_ward_id ?? []);
$user = auth()->user();
$atLeastOneRowCompleted = false;
$errors = [];

$dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
->where('status', 1)
->first();

if (!$dateConfig) {
return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
}

$forTheMonth = $dateConfig->for_the_month;

if ($user->role_name == 'BSSO') {
$staff_address_type = 1;
$block_id = $user->posted_block;
$district_id = Block::where('block_id', $block_id)->value('district_id');
$municipality_id = null;
} elseif ($user->role_name == 'MEO') {
$staff_address_type = 2;
$block_id = null;
$municipality_id = $user->posted_municipality;
$district_id = Municipality::where('municipality_id', $municipality_id)->value('district_id');
} else {
return redirect()->back()->with('error', 'You have no specific permission for this page. Please contact admin.');
}

for ($i = 0; $i < $rows; $i++) {
$rowFields = [
'disbursement_start_date'     => $request->disbursement_start_date[$i] ?? null,
'mbpy_oap_below_80_years'    => $request->mbpy_oap_below_80_years[$i] ?? null,
'mbpy_oap_above_80_years'    => $request->mbpy_oap_above_80_years[$i] ?? null,
'mbpy_wp'                     => $request->mbpy_wp[$i] ?? null,
'mbpy_dp'                     => $request->mbpy_dp[$i] ?? null,
'mbpy_sdp_below_80_percent'  => $request->mbpy_sdp_below_80_percent[$i] ?? null,
'mbpy_sdp_above_80_percent'  => $request->mbpy_sdp_above_80_percent[$i] ?? null,
'mbpy_sdoap'                  => $request->mbpy_sdoap[$i] ?? null,
'mbpy_clp'                    => $request->mbpy_clp[$i] ?? null,
'mbpy_wp_aids'                => $request->mbpy_wp_aids[$i] ?? null,
'mbpy_dp_aids'                => $request->mbpy_dp_aids[$i] ?? null,
'mbpy_unmarried_women'        => $request->mbpy_unmarried_women[$i] ?? null,
'mbpy_orphan_due_to_covide'   => $request->mbpy_orphan_due_to_covide[$i] ?? null,
'mbpy_widow_due_to_covid'     => $request->mbpy_widow_due_to_covid[$i] ?? null,
'mbpy_divorce_or_destitute'   => $request->mbpy_divorce_or_destitute[$i] ?? null,
'mbpy_transgender'            => $request->mbpy_transgender[$i] ?? null,
'no_of_normal_pensioners'     => $request->no_of_normal_pensioners[$i] ?? null,
'no_of_ep_pensioners'         => $request->no_of_ep_pensioners[$i] ?? null,
];

$hasAnyValue = collect($rowFields)->filter(fn($v) => $v !== null && $v !== '')->isNotEmpty();

if ($hasAnyValue) {
$missingFields = collect($rowFields)->filter(fn($v) => $v === null || $v === '')->keys();

if ($missingFields->isNotEmpty()) {
foreach ($missingFields as $field) {
$errors["{$field}.{$i}"] = "Row ".($i+1).": The field ".str_replace('_',' ',$field)." is required when filling this row.";
}
} else {
$atLeastOneRowCompleted = true;

$gp_id = $user->role_name == 'BSSO' ? $request->gp_ward_id[$i] : null;
$ward_id = $user->role_name == 'MEO' ? $request->gp_ward_id[$i] : null;

$rowFields = array_merge($rowFields, [
'for_the_month'     => $forTheMonth,
'gp_ward_id'        => $request->gp_ward_id[$i],
'gp_ward_name'      => $request->gp_ward_name[$i],
'staff_address_type'=> $staff_address_type,
'state_id'          => 228,
'district_id'       => $district_id,
'municipality_id'   => $municipality_id,
'block_id'          => $block_id,
'gp_id'             => $gp_id,
'ward_id'           => $ward_id,
'disbursement_started'=> 1,
'is_active'         => 'active',
'created_date'      => now()->setTimezone('Asia/Kolkata')->toDateString(),
'created_time'      => now()->setTimezone('Asia/Kolkata')->toTimeString(),
'created_by'        => $user->user_table_id,
'status'            => 1,
]);

$checkColumn = $user->role_name == 'BSSO' ? 'gp_id' : 'ward_id';
$checkValue  = $request->gp_ward_id[$i];
$existingRecord = DailyPensionDisbursement::where($checkColumn, $checkValue)
->where('disbursement_start_date', $request->disbursement_start_date[$i])
->first();

if ($existingRecord) {
$existingRecord->update($rowFields);
} else {
DailyPensionDisbursement::create($rowFields);
}
}
}
}

if (!empty($errors)) {
return back()->withErrors($errors)->withInput();
}

if (!$atLeastOneRowCompleted) {
return back()->withErrors([
'at_least_one_row' => 'Please fill at least one complete GP/Ward Beneficiary Count before submitting the form.'
])->withInput();
}

return redirect()->route('admin.dailypensiondisbursement.index')->with('success', 'Pension disbursement records saved successfully.');
}*/

public function store(Request $request)
{
    $rows = count($request->gp_ward_id ?? []);
    $user = auth()->user();
    $atLeastOneRowCompleted = false;
    $errors = [];

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
    ->where('status', 1)
    ->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $forTheMonth = $dateConfig->for_the_month;

    if ($user->role_name == 'BSSO') {
        $staff_address_type = 1;
        $block_id = $user->posted_block;
        $district_id = Block::where('block_id', $block_id)->value('district_id');
        $municipality_id = null;
    } elseif ($user->role_name == 'MEO') {
        $staff_address_type = 2;
        $municipality_id = $user->posted_municipality;
        $district_id = Municipality::where('municipality_id', $municipality_id)->value('district_id');
        $block_id = null;
    } else {
        return redirect()->back()->with('error', 'You have no specific permission for this page. Please contact admin.');
    }

    DB::beginTransaction();
    try {

        for ($i = 0; $i < $rows; $i++) {
            $rowFields = [
                'disbursement_start_date'      => $request->disbursement_start_date[$i] ?? null,
                'mbpy_oap_below_80_years'      => $request->mbpy_oap_below_80_years[$i] ?? null,
                'mbpy_oap_above_80_years'      => $request->mbpy_oap_above_80_years[$i] ?? null,
                'mbpy_wp'                      => $request->mbpy_wp[$i] ?? null,
                'mbpy_dp'                      => $request->mbpy_dp[$i] ?? null,
                'mbpy_sdp_below_80_percent'    => $request->mbpy_sdp_below_80_percent[$i] ?? null,
                'mbpy_sdp_above_80_percent'    => $request->mbpy_sdp_above_80_percent[$i] ?? null,
                'mbpy_sdoap'                   => $request->mbpy_sdoap[$i] ?? null,
                'mbpy_clp'                     => $request->mbpy_clp[$i] ?? null,
                'mbpy_wp_aids'                 => $request->mbpy_wp_aids[$i] ?? null,
                'mbpy_dp_aids'                 => $request->mbpy_dp_aids[$i] ?? null,
                'mbpy_unmarried_women'         => $request->mbpy_unmarried_women[$i] ?? null,
                'mbpy_orphan_due_to_covide'    => $request->mbpy_orphan_due_to_covide[$i] ?? null,
                'mbpy_widow_due_to_covid'      => $request->mbpy_widow_due_to_covid[$i] ?? null,
                'mbpy_divorce_or_destitute'    => $request->mbpy_divorce_or_destitute[$i] ?? null,
                'mbpy_transgender'             => $request->mbpy_transgender[$i] ?? null,
                'no_of_normal_pensioners'      => $request->no_of_normal_pensioners[$i] ?? null,
                'no_of_ep_pensioners'          => $request->no_of_ep_pensioners[$i] ?? null,
            ];

            $hasAnyValue = collect($rowFields)->filter(fn($v) => $v !== null && $v !== '')->isNotEmpty();

            if ($hasAnyValue) {
                $missingFields = collect($rowFields)->filter(fn($v) => $v === null || $v === '')->keys();
                if ($missingFields->isNotEmpty()) {
                    foreach ($missingFields as $field) {
                        $errors["{$field}.{$i}"] = "Row " . ($i + 1) . ": The field " . str_replace('_', ' ', $field) . " is required.";
                    }
                    continue;
                }

                $atLeastOneRowCompleted = true;

                $fundMultipliers = [
                    'mbpy_oap_below_80_years'       => 1000,
                    'mbpy_oap_above_80_years'       => 3500,
                    'mbpy_wp'                        => 1000,
                    'mbpy_dp'                        => 1000,
                    'mbpy_sdp_below_80_percent'      => 1200,
                    'mbpy_sdp_above_80_percent'      => 3500,
                    'mbpy_sdoap'                     => 3500,
                    'mbpy_clp'                       => 1000,
                    'mbpy_wp_aids'                   => 1000,
                    'mbpy_dp_aids'                   => 1000,
                    'mbpy_unmarried_women'           => 1000,
                    'mbpy_orphan_due_to_covide'      => 1000,
                    'mbpy_widow_due_to_covid'        => 1000,
                    'mbpy_divorce_or_destitute'      => 1000,
                    'mbpy_transgender'               => 1000,
                ];

                $totalBeneficiaries = 0;
                $totalFunds = 0;

                foreach ($fundMultipliers as $field => $rate) {
                    $count = (int)($rowFields[$field] ?? 0);
                    $rowFields["funds_{$field}"] = $count * $rate;
                    $totalBeneficiaries += $count;
                    $totalFunds += ($count * $rate);
                }

                $rowFields['mbpy_total_beneficiaries'] = $totalBeneficiaries;
                $rowFields['funds_mbpy_total_beneficiaries'] = $totalFunds;

                $gp_id = $user->role_name == 'BSSO' ? $request->gp_ward_id[$i] : null;
                $ward_id = $user->role_name == 'MEO' ? $request->gp_ward_id[$i] : null;

                $rowFields = array_merge($rowFields, [
                    'for_the_month'          => $forTheMonth,
                    'gp_ward_id'             => $request->gp_ward_id[$i],
                    'gp_ward_name'           => $request->gp_ward_name[$i],
                    'staff_address_type'     => $staff_address_type,
                    'state_id'               => 228,
                    'district_id'            => $district_id,
                    'municipality_id'        => $municipality_id,
                    'block_id'               => $block_id,
                    'gp_id'                  => $gp_id,
                    'ward_id'                => $ward_id,
                    'disbursement_started'   => 1,
                    'is_active'              => 'active',
                    'created_date'           => now('Asia/Kolkata')->toDateString(),
                    'created_time'           => now('Asia/Kolkata')->toTimeString(),
                    'created_by'             => $user->user_table_id,
                    'status'                 => 1,
                ]);

                $checkColumn = $user->role_name == 'BSSO' ? 'gp_id' : 'ward_id';
                $checkValue  = $request->gp_ward_id[$i];

                $existingRecord = DailyPensionDisbursement::where($checkColumn, $checkValue)
                ->where('disbursement_start_date', $request->disbursement_start_date[$i])
                ->first();

                if ($existingRecord) {
                    $existingRecord->update($rowFields);
                } else {
                    DailyPensionDisbursement::create($rowFields);
                }
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        if (!$atLeastOneRowCompleted) {
            return back()->withErrors([
                'at_least_one_row' => 'Please fill at least one complete GP/Ward Beneficiary Count before submitting the form.'
            ])->withInput();
        }
        DB::commit();

        return redirect()->route('admin.dailypensiondisbursement.index')->with('success', 'Daily Pension disbursement records saved successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Daily Pension disbursement form submission failed", [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
            'time'    => now()->toDateTimeString(),
            'user_id' => auth()->id(),
        ]);
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}

public function listing_report(Request $request)
{
    $user = Auth::user();
    $userRole = $user->role_id;

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
    ->where('is_active', 'active')
    ->orderBy('id', 'desc')
    ->get();

    $forTheMonth = $request->for_the_month 
    ?? ($dateConfig->sortByDesc('id')->first()->for_the_month ?? null);

    $numericColumns = [
        'mbpy_oap_below_80_years',
        'mbpy_oap_above_80_years',
        'mbpy_wp',
        'mbpy_dp',
        'mbpy_sdp_below_80_percent',
        'mbpy_sdp_above_80_percent',
        'mbpy_sdoap',
        'mbpy_clp',
        'mbpy_wp_aids',
        'mbpy_dp_aids',
        'mbpy_unmarried_women',
        'mbpy_orphan_due_to_covide',
        'mbpy_widow_due_to_covid',
        'mbpy_divorce_or_destitute',
        'mbpy_transgender',
        'no_of_normal_pensioners',
        'no_of_ep_pensioners',
    ];

    $bssosQuery = DailyPensionDisbursement::where('status', 1)
    ->where('for_the_month', $forTheMonth)
    ->where('staff_address_type', 1)
    ->with(['grampanchayat.block', 'grampanchayat.district']);

    $meosQuery = DailyPensionDisbursement::where('status', 1)
    ->where('for_the_month', $forTheMonth)
    ->where('staff_address_type', 2)
    ->with(['ward.municipality', 'ward.district']);

    if (!in_array($userRole, [1, 2, 12, 13, 14, 15])) {
        if (in_array($userRole, [4, 6])) {
            $bssosQuery->where('block_id', $user->posted_block);
            $meosQuery = collect();
        } elseif ($userRole == 5) {
            $meosQuery->where('municipality_id', $user->posted_municipality);
            $bssosQuery = collect();
        } elseif (in_array($userRole, [8, 10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');
            $bssosQuery->whereIn('block_id', $blockIds);
            $meosQuery->whereIn('municipality_id', $municipalityIds);
        } elseif (in_array($userRole, [9, 11])) {
            $bssosQuery->where('district_id', $user->posted_district);
            $meosQuery->where('district_id', $user->posted_district);
        }
    }

    $bssos = $bssosQuery instanceof \Illuminate\Database\Eloquent\Builder ? $bssosQuery->get() : $bssosQuery;
    $meos  = $meosQuery instanceof \Illuminate\Database\Eloquent\Builder ? $meosQuery->get() : $meosQuery;
    $allRecords = collect($bssos)->merge($meos);

    $grouped = $allRecords->groupBy(function ($item) {
        return $item->staff_address_type == 1
        ? 'gp_' . $item->gp_id
        : 'ward_' . $item->ward_id;
    })->map(function ($group) use ($numericColumns) {
        $first = $group->first();

        $dateIdMap = $group->mapWithKeys(function($item) {
            $date = \Carbon\Carbon::parse($item->disbursement_start_date)->format('Y-m-d');
            return [$date => $item->id];
        });

        $row = [
            'staff_address_type' => $first->staff_address_type,
            'district_name'      => $first->staff_address_type == 1
            ? ($first->grampanchayat->district->district_name ?? '')
            : ($first->ward->district->district_name ?? ''),
            'block_ulb_name'     => $first->staff_address_type == 1
            ? ($first->grampanchayat->block->block_name ?? '')
            : ($first->ward->municipality->municipality_name ?? ''),
            'gp_ward_name'       => $first->staff_address_type == 1
            ? ($first->grampanchayat->gp_name ?? '')
            : ($first->ward->ward_name ?? ''),
            'forTheMonth'        => $first->for_the_month,
            'disbursement_dates' => $dateIdMap,
        ];

        foreach ($numericColumns as $col) {
            $row['totals']['total_' . $col] = $group->sum($col);
        }

        return $row;
    })->values();

    if ($request->ajax()) {
        return DataTables::of($grouped)
        ->addIndexColumn()
        ->rawColumns(['disbursement_dates'])
        ->make(true);
    }

    return view(
        'dashboard.pension.dailypension.daily_pension_disbursement_listing',
        compact('forTheMonth', 'numericColumns', 'dateConfig')
    );
}


public function combined_report(Request $request)
{
    $user = Auth::user();
    $userRole = $user->role_id;

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
    ->where('is_active', 'active')
    ->orderBy('id', 'desc')
    ->get();

    $forTheMonth = $request->for_the_month
    ?? ($dateConfig->sortByDesc('id')->first()->for_the_month ?? null);

    $numericColumns = [
        'mbpy_oap_below_80_years','mbpy_oap_above_80_years','mbpy_wp','mbpy_dp',
        'mbpy_sdp_below_80_percent','mbpy_sdp_above_80_percent','mbpy_sdoap','mbpy_clp',
        'mbpy_wp_aids','mbpy_dp_aids','mbpy_unmarried_women','mbpy_orphan_due_to_covide',
        'mbpy_widow_due_to_covid','mbpy_divorce_or_destitute','mbpy_transgender',
        'no_of_normal_pensioners','no_of_ep_pensioners',
    ];

    $bssosQuery = DailyPensionDisbursement::where('status', 1)
    ->where('for_the_month', $forTheMonth)
    ->where('staff_address_type', 1)
    ->with(['grampanchayat.block', 'grampanchayat.district']);

    $meosQuery = DailyPensionDisbursement::where('status', 1)
    ->where('for_the_month', $forTheMonth)
    ->where('staff_address_type', 2)
    ->with(['ward.municipality', 'ward.district']);

    if (!in_array($userRole, [1,2,12,13,14,15])) {
        if (in_array($userRole, [4,6])) {
            $bssosQuery->where('block_id', $user->posted_block);
            $meosQuery = collect();
        } elseif ($userRole == 5) {
            $meosQuery->where('municipality_id', $user->posted_municipality);
            $bssosQuery = collect();
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');
            $bssosQuery->whereIn('block_id', $blockIds);
            $meosQuery->whereIn('municipality_id', $municipalityIds);
        } elseif (in_array($userRole, [9,11])) {
            $bssosQuery->where('district_id', $user->posted_district);
            $meosQuery->where('district_id', $user->posted_district);
        }
    }

    $bssos = $bssosQuery instanceof \Illuminate\Database\Eloquent\Builder ? $bssosQuery->get() : $bssosQuery;
    $meos  = $meosQuery instanceof \Illuminate\Database\Eloquent\Builder ? $meosQuery->get() : $meosQuery;

    $allRecords = collect($bssos)->merge($meos);

    $submitted = $allRecords->groupBy(function($item){
        return $item->staff_address_type==1 ? 'gp_'.$item->gp_id : 'ward_'.$item->ward_id;
    })->map(function($group) use($numericColumns){
        $first = $group->first();
        $row = [
            'staff_address_type'=>$first->staff_address_type,
            'district_name'=>$first->staff_address_type==1 ? ($first->grampanchayat->district->district_name ?? '') : ($first->ward->district->district_name ?? ''),
            'block_ulb_name'=>$first->staff_address_type==1 ? ($first->grampanchayat->block->block_name ?? '') : ($first->ward->municipality->municipality_name ?? ''),
            'gp_ward_name'=>$first->staff_address_type==1 ? ($first->grampanchayat->gp_name ?? '') : ($first->ward->ward_name ?? ''),
            'forTheMonth' => $first->for_the_month ?? $forTheMonth,
            'disbursement_dates'=>$group->pluck('disbursement_start_date')->sort()
            ->map(fn($d)=>\Carbon\Carbon::parse($d)->format('D, d-M-Y'))
            ->unique()->implode(' | '),
            'status' => 'Submitted'
        ];
        foreach($numericColumns as $col){
            $row['totals']['total_'.$col] = $group->sum($col);
        }
        return $row;
    })->values();

    $submittedGpIds   = $allRecords->where('staff_address_type',1)->pluck('gp_id')->unique();
    $submittedWardIds = $allRecords->where('staff_address_type',2)->pluck('ward_id')->unique();

    $gpQuery = Grampanchayat::with('block.district');
    $wardQuery = WardMaster::with('municipality.district');

    if (!in_array($userRole, [1,2,12,13,14,15])) {
        if (in_array($userRole, [4,6])) {
            $gpQuery->where('block_id', $user->posted_block);
            $wardQuery = collect();
        } elseif ($userRole == 5) {
            $wardQuery->where('municipality_id', $user->posted_municipality);
            $gpQuery = collect();
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');
            $gpQuery->whereIn('block_id', $blockIds);
            $wardQuery->whereIn('municipality_id', $municipalityIds);
        } elseif (in_array($userRole, [9,11])) {
            $gpQuery->where('district_id', $user->posted_district);
            $wardQuery->where('district_code', $user->posted_district);
        }
    }

    $gps = $gpQuery instanceof \Illuminate\Database\Eloquent\Builder ? $gpQuery->get() : collect();
    $wards = $wardQuery instanceof \Illuminate\Database\Eloquent\Builder ? $wardQuery->get() : collect();

    $missing = collect();

    foreach($gps as $gp){
        if(!$submittedGpIds->contains($gp->gp_id)){
            $missing->push([
                'staff_address_type'=>1,
                'district_name'=>$gp->block->district->district_name ?? '',
                'block_ulb_name'=>$gp->block->block_name ?? '',
                'gp_ward_name'=>$gp->gp_name ?? '',
                'forTheMonth' => $forTheMonth,
                'disbursement_dates'=>'<span class="badge bg-danger">Not Submitted</span>',
                'status'=>'Not Submitted'
            ]);
        }
    }

    foreach($wards as $ward){
        if(!$submittedWardIds->contains($ward->ward_id)){
            $missing->push([
                'staff_address_type'=>2,
                'district_name'=>$ward->municipality->district->district_name ?? '',
                'block_ulb_name'=>$ward->municipality->municipality_name ?? '',
                'gp_ward_name'=>$ward->ward_name ?? '',
                'forTheMonth' => $forTheMonth,
                'disbursement_dates'=>'<span class="badge bg-danger">Not Submitted</span>',
                'status'=>'Not Submitted'
            ]);
        }
    }

    $combined = $submitted->merge($missing)->values();

    if($request->ajax()){
        return DataTables::of($combined)
        ->addIndexColumn()
        ->rawColumns(['disbursement_dates'])
        ->make(true);
    }

    return view('dashboard.pension.dailypension.daily_pension_disbursement_combined_listing', compact('forTheMonth','numericColumns', 'dateConfig'));
}


public function pension_disbursement_daily_not_submission()
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
    ->where('status', 1)
    ->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $startDate   = $dateConfig->start_date;
    $endDate     = $dateConfig->end_date;
    $forTheMonth = $dateConfig->for_the_month;

    ini_set('memory_limit', '512M');
    $user     = auth()->user();
    $userRole = $user->role_id;

    $dailyPensionDisbursemenetQuery = DailyPensionDisbursement::where('disbursement_started', 1)
    ->where('for_the_month', $forTheMonth)
    ->where('is_active', 'active')
    ->where('status', 1)
    ->with(['state', 'district', 'block', 'grampanchayat', 'municipality', 'ward']);

    $allGps   = collect();
    $allWards = collect();

    if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('is_active', 'active')->get();
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('is_active', '1')->get();
    } elseif (in_array($userRole, [4, 6])) {
        $dailyPensionDisbursemenetQuery->where('block_id', $user->posted_block);
        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('block_id', $user->posted_block)
        ->where('is_active', 'active')->get();
    } elseif ($userRole == 5) {
        $dailyPensionDisbursemenetQuery->where('municipality_id', $user->posted_municipality);
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('municipal_area_code', $user->posted_municipality)
        ->where('is_active', '1')->get();
    } elseif (in_array($userRole, [8, 10])) {
        $blockIds        = Block::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')->pluck('block_id');
        $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')->pluck('municipality_id');

        $dailyPensionDisbursemenetQuery->where(function ($query) use ($blockIds, $municipalityIds) {
            $query->whereIn('block_id', $blockIds)
            ->orWhereIn('municipality_id', $municipalityIds);
        });

        $allGps = Grampanchayat::with(['district', 'block'])
        ->whereIn('block_id', $blockIds)
        ->where('is_active', 'active')->get();
        $allWards = WardMaster::with(['district', 'municipality'])
        ->whereIn('municipal_area_code', $municipalityIds)
        ->where('is_active', '1')->get();
    } elseif (in_array($userRole, [9, 11])) {
        $dailyPensionDisbursemenetQuery->where('district_id', $user->posted_district);

        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('district_id', $user->posted_district)
        ->where('is_active', 'active')->get();
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('district_code', $user->posted_district)
        ->where('is_active', '1')->get();
    }

    $monthlyPension = $dailyPensionDisbursemenetQuery
    ->where('is_active', 'active')
    ->where('status', 1)
    ->where('disbursement_started', 1)
    ->get();

    $submittedGpIds   = $monthlyPension->pluck('gp_id')->filter()->unique();
    $submittedWardIds = $monthlyPension->pluck('ward_id')->filter()->unique();

    $pendingGps = $allGps->whereNotIn('gp_id', $submittedGpIds)->map(function ($gp) {
        return (object)[
            'id'                       => null,
            'district'                 => $gp->district,
            'block'                    => $gp->block,
            'grampanchayat'            => $gp,
            'municipality'             => null,
            'ward'                     => null,
            'staff_address_type'       => 1,
            'for_the_month'            => null,
            'disbursement_start_date'  => null,
            'no_of_normal_pensioners'  => null,
            'no_of_ep_pensioners'      => null,
            'disbursement_started'     => 0,
        ];
    });

    $pendingWards = $allWards->whereNotIn('ward_code', $submittedWardIds)->map(function ($ward) {
        return (object)[
            'id'                       => null,
            'district'                 => $ward->district,
            'block'                    => null,
            'grampanchayat'            => null,
            'municipality'             => $ward->municipality,
            'ward'                     => $ward,
            'staff_address_type'       => 2,
            'for_the_month'            => null,
            'disbursement_start_date'  => null,
            'no_of_normal_pensioners'  => null,
            'no_of_ep_pensioners'      => null,
            'disbursement_started'     => 0,
        ];
    });

    $monthlyPensionDisbursemenet = $pendingGps
    ->concat($pendingWards)
    ->sortBy(function ($item) {
        return $item->district->district_name ?? '';
    })
    ->values();

    return view('dashboard.pension.dailypension.daily_pension_disbursement_pending_listing', compact(
        'monthlyPensionDisbursemenet',
        'startDate',
        'endDate',
        'forTheMonth'
    ));
}

/**
* Display the specified resource.
*/
public function show(string $id)
{
//
}

/**
* Show the form for editing the specified resource.
*/
public function edit(string $id)
{
    $user = auth()->user();
    $userRole = $user->role_id;
    $today_date = Carbon::today('Asia/Kolkata')->format('Y-m-d');

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
    ->where('status', 1)
    ->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $startDate   = $dateConfig->start_date;
    $endDate     = $dateConfig->end_date;
    $forTheMonth = $dateConfig->for_the_month;

    if ($user->role_name == 'BSSO') {
        $gp_ward_id = Grampanchayat::where('block_id', $user->posted_block)
        ->where('is_active', 'active')
        ->get();
        $gpIds   = $gp_ward_id->pluck('gp_id')->toArray();
        $wardIds = [];
    } elseif ($user->role_name == 'MEO') {
        $gp_ward_id = WardMaster::where('municipal_area_code', $user->posted_municipality)
        ->where('is_active', '1')
        ->get();
        $gpIds   = [];
        $wardIds = $gp_ward_id->pluck('ward_code')->toArray();
    } else {
        return redirect()->back()->with('error', 'You have no specific permission for this page. Please contact admin.');
    }
    $dailypensiondisbursementdata =  DailyPensionDisbursement::whereId($id)->firstOrFail();
    return view('dashboard.pension.dailypension.daily_pension_disbursement_edit', compact('gp_ward_id', 'user', 'startDate', 'endDate', 'forTheMonth', 'dailypensiondisbursementdata'));
}

/**
* Update the specified resource in storage.
*/
public function update(Request $request, string $id)
{
    $user = auth()->user();

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'daily_pension_disbursemenets')
    ->where('status', 1)
    ->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $forTheMonth = $dateConfig->for_the_month;

    if ($user->role_name == 'BSSO') {
        $staff_address_type = 1;
        $block_id = $user->posted_block;
        $district_id = Block::where('block_id', $block_id)->value('district_id');
        $municipality_id = null;
    } elseif ($user->role_name == 'MEO') {
        $staff_address_type = 2;
        $municipality_id = $user->posted_municipality;
        $district_id = Municipality::where('municipality_id', $municipality_id)->value('district_id');
        $block_id = null;
    } else {
        return redirect()->back()->with('error', 'You have no specific permission for this page. Please contact admin.');
    }

    DB::beginTransaction();
    try {

        $dailypensiondisbursementdata = DailyPensionDisbursement::findOrFail($id);

        $rowFields = [
            'mbpy_oap_below_80_years'      => $request->mbpy_oap_below_80_years[0] ?? null,
            'mbpy_oap_above_80_years'      => $request->mbpy_oap_above_80_years[0] ?? null,
            'mbpy_wp'                      => $request->mbpy_wp[0] ?? null,
            'mbpy_dp'                      => $request->mbpy_dp[0] ?? null,
            'mbpy_sdp_below_80_percent'    => $request->mbpy_sdp_below_80_percent[0] ?? null,
            'mbpy_sdp_above_80_percent'    => $request->mbpy_sdp_above_80_percent[0] ?? null,
            'mbpy_sdoap'                   => $request->mbpy_sdoap[0] ?? null,
            'mbpy_clp'                     => $request->mbpy_clp[0] ?? null,
            'mbpy_wp_aids'                 => $request->mbpy_wp_aids[0] ?? null,
            'mbpy_dp_aids'                 => $request->mbpy_dp_aids[0] ?? null,
            'mbpy_unmarried_women'         => $request->mbpy_unmarried_women[0] ?? null,
            'mbpy_orphan_due_to_covide'    => $request->mbpy_orphan_due_to_covide[0] ?? null,
            'mbpy_widow_due_to_covid'      => $request->mbpy_widow_due_to_covid[0] ?? null,
            'mbpy_divorce_or_destitute'    => $request->mbpy_divorce_or_destitute[0] ?? null,
            'mbpy_transgender'             => $request->mbpy_transgender[0] ?? null,
            'no_of_normal_pensioners'      => $request->no_of_normal_pensioners[0] ?? null,
            'no_of_ep_pensioners'          => $request->no_of_ep_pensioners[0] ?? null,
        ];

        $hasAnyValue = collect($rowFields)->filter(fn($v) => $v !== null && $v !== '')->isNotEmpty();

        if (!$hasAnyValue) {
            return back()->withErrors([
                'at_least_one_row' => 'Please fill at least one complete GP/Ward Beneficiary Count before submitting the form.'
            ])->withInput();
        }

        $fundMultipliers = [
            'mbpy_oap_below_80_years'       => 1000,
            'mbpy_oap_above_80_years'       => 3500,
            'mbpy_wp'                        => 1000,
            'mbpy_dp'                        => 1000,
            'mbpy_sdp_below_80_percent'      => 1200,
            'mbpy_sdp_above_80_percent'      => 3500,
            'mbpy_sdoap'                     => 3500,
            'mbpy_clp'                       => 1000,
            'mbpy_wp_aids'                   => 1000,
            'mbpy_dp_aids'                   => 1000,
            'mbpy_unmarried_women'           => 1000,
            'mbpy_orphan_due_to_covide'      => 1000,
            'mbpy_widow_due_to_covid'        => 1000,
            'mbpy_divorce_or_destitute'      => 1000,
            'mbpy_transgender'               => 1000,
        ];

        $totalBeneficiaries = 0;
        $totalFunds = 0;

        foreach ($fundMultipliers as $field => $rate) {
            $count = (int)($rowFields[$field] ?? 0);
            $rowFields["funds_{$field}"] = $count * $rate;
            $totalBeneficiaries += $count;
            $totalFunds += ($count * $rate);
        }

        $rowFields['mbpy_total_beneficiaries'] = $totalBeneficiaries;
        $rowFields['funds_mbpy_total_beneficiaries'] = $totalFunds;

        $rowFields = array_merge($rowFields, [
            'updated_date'           => now('Asia/Kolkata')->toDateString(),
            'updated_time'           => now('Asia/Kolkata')->toTimeString(),
            'updated_by'             => $user->user_table_id,
        ]);

        $dailypensiondisbursementdata->update($rowFields);
        DB::commit();

        return redirect()->route('admin.dailypensiondisbursement.listing_report')->with('success', 'Pension disbursement record updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Daily Pension disbursement update form submission failed", [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
            'time'    => now()->toDateTimeString(),
            'user_id' => auth()->id(),
        ]);
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}


/**
* Remove the specified resource from storage.
*/
public function destroy(string $id)
{
//
}
}
