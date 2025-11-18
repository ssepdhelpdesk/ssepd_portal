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
use App\Models\Subdivision;
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

    if (!in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {
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


/*public function combined_report(Request $request)
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

if (!in_array($userRole, [1,2,12,13,14,15,25])) {
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

$gpQuery = Grampanchayat::where('is_active', 'active')->with('block.district');
$wardQuery = WardMaster::where('is_active', 1)->with('municipality.district');

if (!in_array($userRole, [1,2,12,13,14,15,25])) {
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
}*/

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

    if (!in_array($userRole, [1,2,12,13,14,15,25])) {
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
        return $item->staff_address_type == 1 ? 'gp_'.$item->gp_id : 'ward_'.$item->ward_id;
    })->map(function($group) use($numericColumns){
        $first = $group->first();
        $row = [
            'staff_address_type' => $first->staff_address_type,
            'district_name' => $first->staff_address_type == 1 
            ? ($first->grampanchayat->district->district_name ?? '') 
            : ($first->ward->district->district_name ?? ''),
            'block_ulb_name' => $first->staff_address_type == 1 
            ? ($first->grampanchayat->block->block_name ?? '') 
            : ($first->ward->municipality->municipality_name ?? ''),
            'gp_ward_name' => $first->staff_address_type == 1 
            ? ($first->grampanchayat->gp_name ?? '') 
            : ($first->ward->ward_name ?? ''),
            'forTheMonth' => $first->for_the_month ?? null,
            'disbursement_dates' => $group->pluck('disbursement_start_date')->filter()
            ->sort()
            ->map(fn($d)=>\Carbon\Carbon::parse($d)->format('D, d-M-Y'))
            ->unique()->implode(' | '),
            'status' => '<span class="badge bg-success">Submitted</span>'
        ];

        foreach($numericColumns as $col){
            $row['totals']['total_'.$col] = $group->sum($col);
        }
        return $row;
    })->values();

    $submittedGpIds   = $allRecords->where('staff_address_type',1)->pluck('gp_id')->unique();
    $submittedWardIds = $allRecords->where('staff_address_type',2)->pluck('ward_id')->unique();

    $gpQuery = Grampanchayat::where('is_active', 'active')->with('block.district');
    $wardQuery = WardMaster::where('is_active', 1)->with('municipality.district');

    if (!in_array($userRole, [1,2,12,13,14,15,25])) {
        if (in_array($userRole, [4,6])) {
            $gpQuery->where('block_id', $user->posted_block);
            $wardQuery = collect();
        } elseif ($userRole == 5) {
            $wardQuery->where('municipal_area_code', $user->posted_municipality);
            $gpQuery = collect();
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');
            $gpQuery->whereIn('block_id', $blockIds);
            $wardQuery->whereIn('municipal_area_code', $municipalityIds);
        } elseif (in_array($userRole, [9,11])) {
            $gpQuery->where('district_id', $user->posted_district);
            $wardQuery->where('district_code', $user->posted_district);
        }
    }

    $gps = $gpQuery instanceof \Illuminate\Database\Eloquent\Builder ? $gpQuery->get() : collect();
    $wards = $wardQuery instanceof \Illuminate\Database\Eloquent\Builder ? $wardQuery->get() : collect();

    $missing = collect();

    $submittedGpIds   = $submittedGpIds->map(fn($id) => (string)$id);
    $submittedWardIds = $submittedWardIds->map(fn($id) => (string)$id);

    foreach($gps as $gp){
        if(!$submittedGpIds->contains((string)$gp->gp_id)){
            $missing->push([
                'staff_address_type' => 1,
                'district_name' => $gp->block->district->district_name ?? '',
                'block_ulb_name' => $gp->block->block_name ?? '',
                'gp_ward_name' => $gp->gp_name ?? '',
                'forTheMonth' => $forTheMonth,
                'disbursement_dates' => '<span class="badge bg-danger">Not Submitted</span>',
                'status' => '<span class="badge bg-danger">Not Submitted</span>'
            ]);
        }
    }

    foreach($wards as $ward){
        if(!$submittedWardIds->contains((string)$ward->ward_code)){
            $missing->push([
                'staff_address_type' => 2,
                'district_name' => $ward->municipality->district->district_name ?? '',
                'block_ulb_name' => $ward->municipality->municipality_name ?? '',
                'gp_ward_name' => $ward->ward_name ?? '',
                'forTheMonth' => $forTheMonth,
                'disbursement_dates' => '<span class="badge bg-danger">Not Submitted</span>',
                'status' => '<span class="badge bg-danger">Not Submitted</span>'
            ]);
        }
    }

    $combined = $submitted->merge($missing)->values();

    if ($request->ajax()) {
        return DataTables::of($combined)
        ->addIndexColumn()
        ->rawColumns(['disbursement_dates', 'status'])
        ->make(true);
    }

    return view('dashboard.pension.dailypension.daily_pension_disbursement_combined_listing', compact(
        'forTheMonth', 'numericColumns', 'dateConfig'
    ));
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

    if (in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {
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

public function daily_pension_disbursement_vs_funds_requirements()
{
    $month = 'October-2025';

    $fundsRaw = DB::table('pension_funds_requirements')
    ->selectRaw("
        address_type,
        district_id,
        block_id,
        municipality_id,
        SUM(mbpy_oap_below_80_years) AS oap_below_80,
        SUM(mbpy_oap_above_80_years) AS oap_above_80,
        SUM(mbpy_wp) AS widow_pension,
        SUM(mbpy_dp) AS disabled_pension,
        SUM(mbpy_sdp_below_80_percent) AS sdp_below_80,
        SUM(mbpy_sdp_above_80_percent) AS sdp_above_80,
        SUM(mbpy_sdoap) AS sdoap,
        SUM(mbpy_clp) AS clp,
        SUM(mbpy_wp_aids) AS wp_aids,
        SUM(mbpy_dp_aids) AS dp_aids,
        SUM(mbpy_unmarried_women) AS unmarried_women,
        SUM(mbpy_orphan_due_to_covide) AS orphan_covid,
        SUM(mbpy_widow_due_to_covid) AS widow_covid,
        SUM(mbpy_divorce_or_destitute) AS divorce_destitute,
        SUM(mbpy_transgender) AS transgender,
        SUM(mbpy_total_beneficiaries) AS total_benf
        ")
    ->where('for_the_month', $month)
    ->where('status', 1)
    ->groupBy('address_type', 'district_id', 'block_id', 'municipality_id')
    ->get();

    $funds = $fundsRaw->keyBy(function ($r) {
        $addr = (int) ($r->address_type ?? 0);
        $dist = (int) ($r->district_id ?? 0);
        $blk  = (int) ($r->block_id ?? 0);
        $mun  = (int) ($r->municipality_id ?? 0);
        return "{$addr}_{$dist}_{$blk}_{$mun}";
    });

    $disbRaw = DB::table('daily_pension_disbursements')
    ->selectRaw("
        staff_address_type AS address_type,
        district_id AS district_id,
        block_id,
        municipality_id,
        SUM(mbpy_oap_below_80_years) AS oap_below_80,
        SUM(mbpy_oap_above_80_years) AS oap_above_80,
        SUM(mbpy_wp) AS widow_pension,
        SUM(mbpy_dp) AS disabled_pension,
        SUM(mbpy_sdp_below_80_percent) AS sdp_below_80,
        SUM(mbpy_sdp_above_80_percent) AS sdp_above_80,
        SUM(mbpy_sdoap) AS sdoap,
        SUM(mbpy_clp) AS clp,
        SUM(mbpy_wp_aids) AS wp_aids,
        SUM(mbpy_dp_aids) AS dp_aids,
        SUM(mbpy_unmarried_women) AS unmarried_women,
        SUM(mbpy_orphan_due_to_covide) AS orphan_covid,
        SUM(mbpy_widow_due_to_covid) AS widow_covid,
        SUM(mbpy_divorce_or_destitute) AS divorce_destitute,
        SUM(mbpy_transgender) AS transgender,
        SUM(mbpy_total_beneficiaries) AS total_benf
        ")
    ->where('for_the_month', $month)
    ->where('status', 1)
    ->groupBy('staff_address_type', 'district_id', 'block_id', 'municipality_id')
    ->get();

    $disbursements = $disbRaw->keyBy(function ($r) {
        $addr = (int) ($r->address_type ?? 0);
        $dist = (int) ($r->district_id ?? 0);
        $blk  = (int) ($r->block_id ?? 0);
        $mun  = (int) ($r->municipality_id ?? 0);
        return "{$addr}_{$dist}_{$blk}_{$mun}";
    });

    $blocks = Block::where('is_active', 'active')->get();
    $municipalities = Municipality::where('is_active', 'active')->get();

    $report = collect();

    $getValues = function ($key, $funds, $disbursements) {
        $f = $funds[$key] ?? null;
        $d = $disbursements[$key] ?? null;

        return [
            'oap_below_80_requirement' => $f->oap_below_80 ?? 0,
            'oap_above_80_requirement' => $f->oap_above_80 ?? 0,
            'widow_pension_requirement' => $f->widow_pension ?? 0,
            'disabled_pension_requirement' => $f->disabled_pension ?? 0,
            'sdp_below_80_requirement' => $f->sdp_below_80 ?? 0,
            'sdp_above_80_requirement' => $f->sdp_above_80 ?? 0,
            'sdoap_requirement' => $f->sdoap ?? 0,
            'clp_requirement' => $f->clp ?? 0,
            'wp_aids_requirement' => $f->wp_aids ?? 0,
            'dp_aids_requirement' => $f->dp_aids ?? 0,
            'unmarried_women_requirement' => $f->unmarried_women ?? 0,
            'orphan_covid_requirement' => $f->orphan_covid ?? 0,
            'widow_covid_requirement' => $f->widow_covid ?? 0,
            'divorce_destitute_requirement' => $f->divorce_destitute ?? 0,
            'transgender_requirement' => $f->transgender ?? 0,
            'total_benf_requirement' => $f->total_benf ?? 0,

            'oap_below_80_disbursement' => $d->oap_below_80 ?? 0,
            'oap_above_80_disbursement' => $d->oap_above_80 ?? 0,
            'widow_pension_disbursement' => $d->widow_pension ?? 0,
            'disabled_pension_disbursement' => $d->disabled_pension ?? 0,
            'sdp_below_80_disbursement' => $d->sdp_below_80 ?? 0,
            'sdp_above_80_disbursement' => $d->sdp_above_80 ?? 0,
            'sdoap_disbursement' => $d->sdoap ?? 0,
            'clp_disbursement' => $d->clp ?? 0,
            'wp_aids_disbursement' => $d->wp_aids ?? 0,
            'dp_aids_disbursement' => $d->dp_aids ?? 0,
            'unmarried_women_disbursement' => $d->unmarried_women ?? 0,
            'orphan_covid_disbursement' => $d->orphan_covid ?? 0,
            'widow_covid_disbursement' => $d->widow_covid ?? 0,
            'divorce_destitute_disbursement' => $d->divorce_destitute ?? 0,
            'transgender_disbursement' => $d->transgender ?? 0,
            'total_benf_disbursement' => $d->total_benf ?? 0,
        ];
    };

    foreach ($blocks as $block) {
        $address_type = 1;
        $district_id = (int) $block->district_id;
        $block_id = (int) $block->block_id;
        $municipality_id = 0;

        $key = "{$address_type}_{$district_id}_{$block_id}_{$municipality_id}";

        $vals = $getValues($key, $funds, $disbursements);

        $district_name = District::where('district_id', $district_id)->value('district_name') ?? 'NA';

        $report->push(array_merge([
            'area_type' => 'Block',
            'area_id' => $block_id,
            'district_name' => $district_name,
            'area_name' => $block->block_name ?? 'NA',
        ], $vals));
    }

    foreach ($municipalities as $mun) {
        $address_type = 2;
        $district_id = (int) $mun->district_id;
        $block_id = 0;
        $municipality_id = (int) $mun->municipality_id;

        $key = "{$address_type}_{$district_id}_{$block_id}_{$municipality_id}";

        $vals = $getValues($key, $funds, $disbursements);

        $district_name = District::where('district_id', $district_id)->value('district_name') ?? 'NA';

        $report->push(array_merge([
            'area_type' => 'ULB',
            'area_id' => $municipality_id,
            'district_name' => $district_name,
            'area_name' => $mun->municipality_name ?? 'NA',
        ], $vals));
    }

    $finalReport = $report->sortBy('district_name')
    ->values()
    ->map(function ($item, $index) {
        $item['sl_no'] = $index + 1;
        return $item;
    });

    return view('dashboard.pension.dailypension.daily_pension_disbursement_vs_funds_requirements_beneficiaries', compact('finalReport'));
}

public function daily_pension_disbursement_vs_funds_requirements_beneficiaries(Request $request)
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
    ->where('is_active', 'active')
    ->orderBy('id', 'desc')
    ->get();

    $month = $request->for_the_month 
    ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

    $user = Auth::user();
    $userRole = $user->role_id;

    $fundsQuery = DB::table('pension_funds_requirements')
    ->where('for_the_month', $month)
    ->where('status', 1);

    $disbQuery = DB::table('daily_pension_disbursements')
    ->where('for_the_month', $month)
    ->where('status', 1);

    if (!in_array($userRole, [1,2,12,13,14,15,25])) {
        if (in_array($userRole, [4,6])) {
            $fundsQuery->where('block_id', $user->posted_block);
            $disbQuery->where('block_id', $user->posted_block);
        } elseif ($userRole == 5) {
            $fundsQuery->where('municipality_id', $user->posted_municipality);
            $disbQuery->where('municipality_id', $user->posted_municipality);
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');

            $fundsQuery->where(function($q) use($blockIds, $municipalityIds){
                $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
            });

            $disbQuery->where(function($q) use($blockIds, $municipalityIds){
                $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
            });
        } elseif (in_array($userRole, [9,11])) {
            $fundsQuery->where('district_id', $user->posted_district);
            $disbQuery->where('district_id', $user->posted_district);
        }
    }

    $fundsRaw = $fundsQuery->selectRaw("
        address_type, district_id, block_id, municipality_id,
        SUM(mbpy_oap_below_80_years) AS oap_below_80,
        SUM(mbpy_oap_above_80_years) AS oap_above_80,
        SUM(mbpy_wp) AS widow_pension,
        SUM(mbpy_dp) AS disabled_pension,
        SUM(mbpy_sdp_below_80_percent) AS sdp_below_80,
        SUM(mbpy_sdp_above_80_percent) AS sdp_above_80,
        SUM(mbpy_sdoap) AS sdoap,
        SUM(mbpy_clp) AS clp,
        SUM(mbpy_wp_aids) AS wp_aids,
        SUM(mbpy_dp_aids) AS dp_aids,
        SUM(mbpy_unmarried_women) AS unmarried_women,
        SUM(mbpy_orphan_due_to_covide) AS orphan_covid,
        SUM(mbpy_widow_due_to_covid) AS widow_covid,
        SUM(mbpy_divorce_or_destitute) AS divorce_destitute,
        SUM(mbpy_transgender) AS transgender,
        SUM(mbpy_total_beneficiaries) AS total_benf
        ")->groupBy('address_type','district_id','block_id','municipality_id')->get();

    $funds = $fundsRaw->keyBy(fn($r) => 
        ((int)$r->address_type) . '_' . ((int)$r->district_id) . '_' . ((int)($r->block_id ?? 0)) . '_' . ((int)($r->municipality_id ?? 0))
    );

    $disbRaw = $disbQuery->selectRaw("
        staff_address_type AS address_type, district_id, block_id, municipality_id,
        SUM(mbpy_oap_below_80_years) AS oap_below_80,
        SUM(mbpy_oap_above_80_years) AS oap_above_80,
        SUM(mbpy_wp) AS widow_pension,
        SUM(mbpy_dp) AS disabled_pension,
        SUM(mbpy_sdp_below_80_percent) AS sdp_below_80,
        SUM(mbpy_sdp_above_80_percent) AS sdp_above_80,
        SUM(mbpy_sdoap) AS sdoap,
        SUM(mbpy_clp) AS clp,
        SUM(mbpy_wp_aids) AS wp_aids,
        SUM(mbpy_dp_aids) AS dp_aids,
        SUM(mbpy_unmarried_women) AS unmarried_women,
        SUM(mbpy_orphan_due_to_covide) AS orphan_covid,
        SUM(mbpy_widow_due_to_covid) AS widow_covid,
        SUM(mbpy_divorce_or_destitute) AS divorce_destitute,
        SUM(mbpy_transgender) AS transgender,
        SUM(mbpy_total_beneficiaries) AS total_benf
        ")->groupBy('staff_address_type','district_id','block_id','municipality_id')->get();

    $disbursements = $disbRaw->keyBy(fn($r) => 
        ((int)$r->address_type) . '_' . ((int)$r->district_id) . '_' . ((int)($r->block_id ?? 0)) . '_' . ((int)($r->municipality_id ?? 0))
    );

    $blocksQuery = Block::where('is_active','active');
    $municipalitiesQuery = Municipality::where('is_active','active');

    if (!in_array($userRole, [1,2,12,13,14,15,25])) {
        if (in_array($userRole, [4,6])) {
            $blocksQuery->where('block_id', $user->posted_block);
            $municipalitiesQuery = collect();
        } elseif ($userRole == 5) {
            $municipalitiesQuery->where('municipality_id', $user->posted_municipality);
            $blocksQuery = collect();
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');
            $blocksQuery->whereIn('block_id', $blockIds);
            $municipalitiesQuery->whereIn('municipality_id', $municipalityIds);
        } elseif (in_array($userRole, [9,11])) {
            $blocksQuery->where('district_id', $user->posted_district);
            $municipalitiesQuery->where('district_id', $user->posted_district);
        }
    }

    $blocks = $blocksQuery instanceof \Illuminate\Database\Eloquent\Builder ? $blocksQuery->get() : $blocksQuery;
    $municipalities = $municipalitiesQuery instanceof \Illuminate\Database\Eloquent\Builder ? $municipalitiesQuery->get() : $municipalitiesQuery;

    $report = collect();

    $getValues = function ($key) use ($funds, $disbursements) {
        $f = $funds[$key] ?? null;
        $d = $disbursements[$key] ?? null;

        return [
            'oap_below_80_requirement' => $f->oap_below_80 ?? 0,
            'oap_above_80_requirement' => $f->oap_above_80 ?? 0,
            'widow_pension_requirement' => $f->widow_pension ?? 0,
            'disabled_pension_requirement' => $f->disabled_pension ?? 0,
            'sdp_below_80_requirement' => $f->sdp_below_80 ?? 0,
            'sdp_above_80_requirement' => $f->sdp_above_80 ?? 0,
            'sdoap_requirement' => $f->sdoap ?? 0,
            'clp_requirement' => $f->clp ?? 0,
            'wp_aids_requirement' => $f->wp_aids ?? 0,
            'dp_aids_requirement' => $f->dp_aids ?? 0,
            'unmarried_women_requirement' => $f->unmarried_women ?? 0,
            'orphan_covid_requirement' => $f->orphan_covid ?? 0,
            'widow_covid_requirement' => $f->widow_covid ?? 0,
            'divorce_destitute_requirement' => $f->divorce_destitute ?? 0,
            'transgender_requirement' => $f->transgender ?? 0,
            'total_benf_requirement' => $f->total_benf ?? 0,

            'oap_below_80_disbursement' => $d->oap_below_80 ?? 0,
            'oap_above_80_disbursement' => $d->oap_above_80 ?? 0,
            'widow_pension_disbursement' => $d->widow_pension ?? 0,
            'disabled_pension_disbursement' => $d->disabled_pension ?? 0,
            'sdp_below_80_disbursement' => $d->sdp_below_80 ?? 0,
            'sdp_above_80_disbursement' => $d->sdp_above_80 ?? 0,
            'sdoap_disbursement' => $d->sdoap ?? 0,
            'clp_disbursement' => $d->clp ?? 0,
            'wp_aids_disbursement' => $d->wp_aids ?? 0,
            'dp_aids_disbursement' => $d->dp_aids ?? 0,
            'unmarried_women_disbursement' => $d->unmarried_women ?? 0,
            'orphan_covid_disbursement' => $d->orphan_covid ?? 0,
            'widow_covid_disbursement' => $d->widow_covid ?? 0,
            'divorce_destitute_disbursement' => $d->divorce_destitute ?? 0,
            'transgender_disbursement' => $d->transgender ?? 0,
            'total_benf_disbursement' => $d->total_benf ?? 0,
        ];
    };

    foreach ($blocks as $block) {
        $key = "1_{$block->district_id}_{$block->block_id}_0";
        $vals = $getValues($key);
        $district_name = District::where('district_id', $block->district_id)->value('district_name') ?? 'NA';
        $report->push(array_merge([
            'area_type' => 'Block',
            'area_id' => $block->block_id,
            'district_name' => $district_name,
            'area_name' => $block->block_name ?? 'NA',
        ], $vals));
    }

    foreach ($municipalities as $mun) {
        $key = "2_{$mun->district_id}_0_{$mun->municipality_id}";
        $vals = $getValues($key);
        $district_name = District::where('district_id', $mun->district_id)->value('district_name') ?? 'NA';
        $report->push(array_merge([
            'area_type' => 'ULB',
            'area_id' => $mun->municipality_id,
            'district_name' => $district_name,
            'area_name' => $mun->municipality_name ?? 'NA',
        ], $vals));
    }

    $finalReport = $report->sortBy('district_name')
    ->values()
    ->map(fn($item, $index) => array_merge($item, ['sl_no' => $index + 1]));

    return view('dashboard.pension.dailypension.daily_pension_disbursement_vs_funds_requirements_beneficiaries', compact('finalReport', 'dateConfig', 'month'));
}

public function daily_pension_disbursement_fund_vs_funds_requirements(Request $request)
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
    ->where('is_active', 'active')
    ->orderBy('id', 'desc')
    ->get();

    $month = $request->for_the_month 
    ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

    $user = Auth::user();
    $userRole = $user->role_id;

    $fundsQuery = DB::table('pension_funds_requirements')
    ->where('for_the_month', $month)
    ->where('status', 1);

    $disbQuery = DB::table('daily_pension_disbursements')
    ->where('for_the_month', $month)
    ->where('status', 1);

    if (!in_array($userRole, [1,2,12,13,14,15,25])) {
        if (in_array($userRole, [4,6])) {
            $fundsQuery->where('block_id', $user->posted_block);
            $disbQuery->where('block_id', $user->posted_block);
        } elseif ($userRole == 5) {
            $fundsQuery->where('municipality_id', $user->posted_municipality);
            $disbQuery->where('municipality_id', $user->posted_municipality);
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');

            $fundsQuery->where(function($q) use($blockIds, $municipalityIds){
                $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
            });

            $disbQuery->where(function($q) use($blockIds, $municipalityIds){
                $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
            });
        } elseif (in_array($userRole, [9,11])) {
            $fundsQuery->where('district_id', $user->posted_district);
            $disbQuery->where('district_id', $user->posted_district);
        }
    }

    $fundsRaw = $fundsQuery->selectRaw("
        address_type, district_id, block_id, municipality_id,
        SUM(funds_mbpy_oap_below_80_years) AS oap_below_80,
        SUM(funds_mbpy_oap_above_80_years) AS oap_above_80,
        SUM(funds_mbpy_wp) AS widow_pension,
        SUM(funds_mbpy_dp) AS disabled_pension,
        SUM(funds_mbpy_sdp_below_80_percent) AS sdp_below_80,
        SUM(funds_mbpy_sdp_above_80_percent) AS sdp_above_80,
        SUM(funds_mbpy_sdoap) AS sdoap,
        SUM(funds_mbpy_clp) AS clp,
        SUM(funds_mbpy_wp_aids) AS wp_aids,
        SUM(funds_mbpy_dp_aids) AS dp_aids,
        SUM(funds_mbpy_unmarried_women) AS unmarried_women,
        SUM(funds_mbpy_orphan_due_to_covide) AS orphan_covid,
        SUM(funds_mbpy_widow_due_to_covid) AS widow_covid,
        SUM(funds_mbpy_divorce_or_destitute) AS divorce_destitute,
        SUM(funds_mbpy_transgender) AS transgender,
        SUM(funds_mbpy_total_beneficiaries) AS total_benf
        ")->groupBy('address_type','district_id','block_id','municipality_id')->get();

    $funds = $fundsRaw->keyBy(fn($r) => 
        ((int)$r->address_type) . '_' . ((int)$r->district_id) . '_' . ((int)($r->block_id ?? 0)) . '_' . ((int)($r->municipality_id ?? 0))
    );

    $disbRaw = $disbQuery->selectRaw("
        staff_address_type AS address_type, district_id, block_id, municipality_id,
        SUM(funds_mbpy_oap_below_80_years) AS oap_below_80,
        SUM(funds_mbpy_oap_above_80_years) AS oap_above_80,
        SUM(funds_mbpy_wp) AS widow_pension,
        SUM(funds_mbpy_dp) AS disabled_pension,
        SUM(funds_mbpy_sdp_below_80_percent) AS sdp_below_80,
        SUM(funds_mbpy_sdp_above_80_percent) AS sdp_above_80,
        SUM(funds_mbpy_sdoap) AS sdoap,
        SUM(funds_mbpy_clp) AS clp,
        SUM(funds_mbpy_wp_aids) AS wp_aids,
        SUM(funds_mbpy_dp_aids) AS dp_aids,
        SUM(funds_mbpy_unmarried_women) AS unmarried_women,
        SUM(funds_mbpy_orphan_due_to_covide) AS orphan_covid,
        SUM(funds_mbpy_widow_due_to_covid) AS widow_covid,
        SUM(funds_mbpy_divorce_or_destitute) AS divorce_destitute,
        SUM(funds_mbpy_transgender) AS transgender,
        SUM(funds_mbpy_total_beneficiaries) AS total_benf
        ")->groupBy('staff_address_type','district_id','block_id','municipality_id')->get();

    $disbursements = $disbRaw->keyBy(fn($r) => 
        ((int)$r->address_type) . '_' . ((int)$r->district_id) . '_' . ((int)($r->block_id ?? 0)) . '_' . ((int)($r->municipality_id ?? 0))
    );

    $blocksQuery = Block::where('is_active','active');
    $municipalitiesQuery = Municipality::where('is_active','active');

    if (!in_array($userRole, [1,2,12,13,14,15,25])) {
        if (in_array($userRole, [4,6])) {
            $blocksQuery->where('block_id', $user->posted_block);
            $municipalitiesQuery = collect();
        } elseif ($userRole == 5) {
            $municipalitiesQuery->where('municipality_id', $user->posted_municipality);
            $blocksQuery = collect();
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');
            $blocksQuery->whereIn('block_id', $blockIds);
            $municipalitiesQuery->whereIn('municipality_id', $municipalityIds);
        } elseif (in_array($userRole, [9,11])) {
            $blocksQuery->where('district_id', $user->posted_district);
            $municipalitiesQuery->where('district_id', $user->posted_district);
        }
    }

    $blocks = $blocksQuery instanceof \Illuminate\Database\Eloquent\Builder ? $blocksQuery->get() : $blocksQuery;
    $municipalities = $municipalitiesQuery instanceof \Illuminate\Database\Eloquent\Builder ? $municipalitiesQuery->get() : $municipalitiesQuery;

    $report = collect();

    $getValues = function ($key) use ($funds, $disbursements) {
        $f = $funds[$key] ?? null;
        $d = $disbursements[$key] ?? null;

        return [
            'oap_below_80_requirement' => $f->oap_below_80 ?? 0,
            'oap_above_80_requirement' => $f->oap_above_80 ?? 0,
            'widow_pension_requirement' => $f->widow_pension ?? 0,
            'disabled_pension_requirement' => $f->disabled_pension ?? 0,
            'sdp_below_80_requirement' => $f->sdp_below_80 ?? 0,
            'sdp_above_80_requirement' => $f->sdp_above_80 ?? 0,
            'sdoap_requirement' => $f->sdoap ?? 0,
            'clp_requirement' => $f->clp ?? 0,
            'wp_aids_requirement' => $f->wp_aids ?? 0,
            'dp_aids_requirement' => $f->dp_aids ?? 0,
            'unmarried_women_requirement' => $f->unmarried_women ?? 0,
            'orphan_covid_requirement' => $f->orphan_covid ?? 0,
            'widow_covid_requirement' => $f->widow_covid ?? 0,
            'divorce_destitute_requirement' => $f->divorce_destitute ?? 0,
            'transgender_requirement' => $f->transgender ?? 0,
            'total_benf_requirement' => $f->total_benf ?? 0,

            'oap_below_80_disbursement' => $d->oap_below_80 ?? 0,
            'oap_above_80_disbursement' => $d->oap_above_80 ?? 0,
            'widow_pension_disbursement' => $d->widow_pension ?? 0,
            'disabled_pension_disbursement' => $d->disabled_pension ?? 0,
            'sdp_below_80_disbursement' => $d->sdp_below_80 ?? 0,
            'sdp_above_80_disbursement' => $d->sdp_above_80 ?? 0,
            'sdoap_disbursement' => $d->sdoap ?? 0,
            'clp_disbursement' => $d->clp ?? 0,
            'wp_aids_disbursement' => $d->wp_aids ?? 0,
            'dp_aids_disbursement' => $d->dp_aids ?? 0,
            'unmarried_women_disbursement' => $d->unmarried_women ?? 0,
            'orphan_covid_disbursement' => $d->orphan_covid ?? 0,
            'widow_covid_disbursement' => $d->widow_covid ?? 0,
            'divorce_destitute_disbursement' => $d->divorce_destitute ?? 0,
            'transgender_disbursement' => $d->transgender ?? 0,
            'total_benf_disbursement' => $d->total_benf ?? 0,
        ];
    };

    foreach ($blocks as $block) {
        $key = "1_{$block->district_id}_{$block->block_id}_0";
        $vals = $getValues($key);
        $district_name = District::where('district_id', $block->district_id)->value('district_name') ?? 'NA';
        $report->push(array_merge([
            'area_type' => 'Block',
            'area_id' => $block->block_id,
            'district_name' => $district_name,
            'area_name' => $block->block_name ?? 'NA',
        ], $vals));
    }

    foreach ($municipalities as $mun) {
        $key = "2_{$mun->district_id}_0_{$mun->municipality_id}";
        $vals = $getValues($key);
        $district_name = District::where('district_id', $mun->district_id)->value('district_name') ?? 'NA';
        $report->push(array_merge([
            'area_type' => 'ULB',
            'area_id' => $mun->municipality_id,
            'district_name' => $district_name,
            'area_name' => $mun->municipality_name ?? 'NA',
        ], $vals));
    }

    $finalReport = $report->sortBy('district_name')
    ->values()
    ->map(fn($item, $index) => array_merge($item, ['sl_no' => $index + 1]));

    return view('dashboard.pension.dailypension.daily_pension_disbursement_fund_vs_funds_requirements', compact('finalReport', 'dateConfig', 'month'));
}

public function daily_pension_disbursement_vs_funds_requirements_beneficiaries_and_funds(Request $request)
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
    ->where('is_active', 'active')
    ->orderBy('id', 'desc')
    ->get();

    $month = $request->for_the_month 
    ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

    $user = Auth::user();
    $userRole = $user->role_id;

    $fundRates = [
        'oap_below_80' => 1000,
        'oap_above_80' => 3500,
        'widow_pension' => 1000,
        'disabled_pension' => 1000,
        'sdp_below_80' => 1200,
        'sdp_above_80' => 3500,
        'sdoap' => 3500,
        'clp' => 1000,
        'wp_aids' => 1000,
        'dp_aids' => 1000,
        'unmarried_women' => 1000,
        'orphan_covid' => 1000,
        'widow_covid' => 1000,
        'divorce_destitute' => 1000,
        'transgender' => 1000,
    ];

    $fundsQuery = DB::table('pension_funds_requirements')
    ->where('for_the_month', $month)
    ->where('status', 1);

    $disbQuery = DB::table('daily_pension_disbursements')
    ->where('for_the_month', $month)
    ->where('status', 1);

    if (!in_array($userRole, [1,2,12,13,14,15,25])) {
        if (in_array($userRole, [4,6])) {
            $fundsQuery->where('block_id', $user->posted_block);
            $disbQuery->where('block_id', $user->posted_block);
        } elseif ($userRole == 5) {
            $fundsQuery->where('municipality_id', $user->posted_municipality);
            $disbQuery->where('municipality_id', $user->posted_municipality);
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');

            $fundsQuery->where(function($q) use($blockIds, $municipalityIds){
                $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
            });

            $disbQuery->where(function($q) use($blockIds, $municipalityIds){
                $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
            });
        } elseif (in_array($userRole, [9,11])) {
            $fundsQuery->where('district_id', $user->posted_district);
            $disbQuery->where('district_id', $user->posted_district);
        }
    }

    $fundsRaw = $fundsQuery->selectRaw("
        address_type, district_id, block_id, municipality_id,
        SUM(mbpy_oap_below_80_years) AS oap_below_80,
        SUM(mbpy_oap_above_80_years) AS oap_above_80,
        SUM(mbpy_wp) AS widow_pension,
        SUM(mbpy_dp) AS disabled_pension,
        SUM(mbpy_sdp_below_80_percent) AS sdp_below_80,
        SUM(mbpy_sdp_above_80_percent) AS sdp_above_80,
        SUM(mbpy_sdoap) AS sdoap,
        SUM(mbpy_clp) AS clp,
        SUM(mbpy_wp_aids) AS wp_aids,
        SUM(mbpy_dp_aids) AS dp_aids,
        SUM(mbpy_unmarried_women) AS unmarried_women,
        SUM(mbpy_orphan_due_to_covide) AS orphan_covid,
        SUM(mbpy_widow_due_to_covid) AS widow_covid,
        SUM(mbpy_divorce_or_destitute) AS divorce_destitute,
        SUM(mbpy_transgender) AS transgender,
        SUM(mbpy_total_beneficiaries) AS total_benf
        ")->groupBy('address_type','district_id','block_id','municipality_id')->get();

    $funds = $fundsRaw->keyBy(fn($r) => 
        ((int)$r->address_type) . '_' . ((int)$r->district_id) . '_' . ((int)($r->block_id ?? 0)) . '_' . ((int)($r->municipality_id ?? 0))
    );

    $disbRaw = $disbQuery->selectRaw("
        staff_address_type AS address_type, district_id, block_id, municipality_id,
        SUM(mbpy_oap_below_80_years) AS oap_below_80,
        SUM(mbpy_oap_above_80_years) AS oap_above_80,
        SUM(mbpy_wp) AS widow_pension,
        SUM(mbpy_dp) AS disabled_pension,
        SUM(mbpy_sdp_below_80_percent) AS sdp_below_80,
        SUM(mbpy_sdp_above_80_percent) AS sdp_above_80,
        SUM(mbpy_sdoap) AS sdoap,
        SUM(mbpy_clp) AS clp,
        SUM(mbpy_wp_aids) AS wp_aids,
        SUM(mbpy_dp_aids) AS dp_aids,
        SUM(mbpy_unmarried_women) AS unmarried_women,
        SUM(mbpy_orphan_due_to_covide) AS orphan_covid,
        SUM(mbpy_widow_due_to_covid) AS widow_covid,
        SUM(mbpy_divorce_or_destitute) AS divorce_destitute,
        SUM(mbpy_transgender) AS transgender,
        SUM(mbpy_total_beneficiaries) AS total_benf
        ")->groupBy('staff_address_type','district_id','block_id','municipality_id')->get();

    $disbursements = $disbRaw->keyBy(fn($r) => 
        ((int)$r->address_type) . '_' . ((int)$r->district_id) . '_' . ((int)($r->block_id ?? 0)) . '_' . ((int)($r->municipality_id ?? 0))
    );

    $blocksQuery = Block::where('is_active','active');
    $municipalitiesQuery = Municipality::where('is_active','active');

    if (!in_array($userRole, [1,2,12,13,14,15,25])) {
        if (in_array($userRole, [4,6])) {
            $blocksQuery->where('block_id', $user->posted_block);
            $municipalitiesQuery = collect();
        } elseif ($userRole == 5) {
            $municipalitiesQuery->where('municipality_id', $user->posted_municipality);
            $blocksQuery = collect();
        } elseif (in_array($userRole, [8,10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');
            $blocksQuery->whereIn('block_id', $blockIds);
            $municipalitiesQuery->whereIn('municipality_id', $municipalityIds);
        } elseif (in_array($userRole, [9,11])) {
            $blocksQuery->where('district_id', $user->posted_district);
            $municipalitiesQuery->where('district_id', $user->posted_district);
        }
    }

    $blocks = $blocksQuery instanceof \Illuminate\Database\Eloquent\Builder ? $blocksQuery->get() : $blocksQuery;
    $municipalities = $municipalitiesQuery instanceof \Illuminate\Database\Eloquent\Builder ? $municipalitiesQuery->get() : $municipalitiesQuery;

    $report = collect();

    $getValues = function ($key) use ($funds, $disbursements) {
        $f = $funds[$key] ?? null;
        $d = $disbursements[$key] ?? null;
        $vals = [];

        foreach ([
            'oap_below_80','oap_above_80','widow_pension','disabled_pension',
            'sdp_below_80','sdp_above_80','sdoap','clp','wp_aids','dp_aids',
            'unmarried_women','orphan_covid','widow_covid','divorce_destitute','transgender','total_benf'
        ] as $field) {
            $vals["{$field}_requirement"] = $f->$field ?? 0;
            $vals["{$field}_disbursement"] = $d->$field ?? 0;
        }
        return $vals;
    };

    foreach ($blocks as $block) {
        $key = "1_{$block->district_id}_{$block->block_id}_0";
        $vals = $getValues($key);
        $district_name = District::where('district_id', $block->district_id)->value('district_name') ?? 'NA';
        $report->push(array_merge([
            'area_type' => 'Block',
            'area_id' => $block->block_id,
            'district_name' => $district_name,
            'area_name' => $block->block_name ?? 'NA',
        ], $vals));
    }

    foreach ($municipalities as $mun) {
        $key = "2_{$mun->district_id}_0_{$mun->municipality_id}";
        $vals = $getValues($key);
        $district_name = District::where('district_id', $mun->district_id)->value('district_name') ?? 'NA';
        $report->push(array_merge([
            'area_type' => 'ULB',
            'area_id' => $mun->municipality_id,
            'district_name' => $district_name,
            'area_name' => $mun->municipality_name ?? 'NA',
        ], $vals));
    }

    $finalReport = $report->sortBy('district_name')
    ->values()
    ->map(fn($item, $index) => array_merge($item, ['sl_no' => $index + 1]))
    ->map(function ($row) use ($fundRates) {
        foreach ($fundRates as $scheme => $rate) {
            $reqKey = "{$scheme}_requirement";
            $disbKey = "{$scheme}_disbursement";
            $row["funds_{$reqKey}"] = ($row[$reqKey] ?? 0) * $rate;
            $row["funds_{$disbKey}"] = ($row[$disbKey] ?? 0) * $rate;
            $row["funds_{$scheme}_diff"] = $row["funds_{$reqKey}"] - $row["funds_{$disbKey}"];
        }
        return $row;
    });

    return view('dashboard.pension.dailypension.daily_pension_disbursement_vs_funds_requirements_beneficiaries_and_funds', compact('finalReport', 'dateConfig', 'month'));
}

public function month_wise_fund_requirement_comparison_for_district(Request $request)
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
    ->where('is_active', 'active')
    ->orderBy('id', 'desc')
    ->get();

    $from_the_month = $request->from_the_month 
    ?? ($dateConfig->skip(1)->first()->for_the_month 
        ?? $dateConfig->first()->for_the_month 
        ?? now()->format('F-Y'));

    $to_the_month = $request->to_the_month 
    ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

    $user = Auth::user();
    $userRole = $user->role_id;

    $districtFilter = null;
    if (!in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {
        if (in_array($userRole, [4, 6])) {
            $districtFilter = Block::where('block_id', $user->posted_block)->pluck('district_id')->toArray();
        } elseif ($userRole == 5) {
            $districtFilter = Municipality::where('municipality_id', $user->posted_municipality)->pluck('district_id')->toArray();
        } elseif (in_array($userRole, [8, 10])) {
            $districtFilter = Subdivision::where('subdivision_id', $user->posted_subdiv)->pluck('district_id')->toArray();
        } elseif (in_array($userRole, [9, 11])) {
            $districtFilter = [$user->posted_district];
        }
    }

    $schemeRates = [
        'oap_below_80' => 1000,
        'oap_above_80' => 3500,
        'widow_pension' => 1000,
        'disabled_pension' => 1000,
        'sdp_below_80' => 1200,
        'sdp_above_80' => 3500,
        'sdoap' => 3500,
        'clp' => 1000,
        'wp_aids' => 1000,
        'dp_aids' => 1000,
        'unmarried_women' => 1000,
        'orphan_covid' => 1000,
        'widow_covid' => 1000,
        'divorce_destitute' => 1000,
        'transgender' => 1000,
    ];

    $sumCols = [
        'mbpy_oap_below_80_years', 'mbpy_oap_above_80_years', 'mbpy_wp', 'mbpy_dp',
        'mbpy_sdp_below_80_percent', 'mbpy_sdp_above_80_percent', 'mbpy_sdoap',
        'mbpy_clp', 'mbpy_wp_aids', 'mbpy_dp_aids', 'mbpy_unmarried_women',
        'mbpy_orphan_due_to_covide', 'mbpy_widow_due_to_covid',
        'mbpy_divorce_or_destitute', 'mbpy_transgender',
        'mbpy_total_beneficiaries', 'funds_mbpy_total_beneficiaries'
    ];

    $selectRaw = implode(', ', array_map(fn($c) => "SUM($c) as $c", $sumCols));

    $query = DB::table('pension_funds_requirements')
    ->selectRaw("district_id, for_the_month, $selectRaw")
    ->where('status', 1)
    ->whereIn('for_the_month', [$from_the_month, $to_the_month])
    ->groupBy('district_id', 'for_the_month');

    if ($districtFilter !== null) {
        $query->whereIn('district_id', $districtFilter);
    }

    $data = $query->get();

    $grouped = $data->groupBy('district_id');

    $districtNames = District::pluck('district_name', 'district_id')->toArray();

    $comparisonData = [];

    foreach ($grouped as $districtId => $rows) {
        $fundFrom = $rows->where('for_the_month', $from_the_month)->first();
        $fundTo = $rows->where('for_the_month', $to_the_month)->first();

        $fFrom = (object) collect($sumCols)->mapWithKeys(fn($c) => [$c => $fundFrom->$c ?? 0])->toArray();
        $fTo   = (object) collect($sumCols)->mapWithKeys(fn($c) => [$c => $fundTo->$c ?? 0])->toArray();

        $normal_from = $fFrom->mbpy_oap_below_80_years + $fFrom->mbpy_wp + $fFrom->mbpy_dp +
        $fFrom->mbpy_sdp_below_80_percent + $fFrom->mbpy_clp + $fFrom->mbpy_wp_aids +
        $fFrom->mbpy_dp_aids + $fFrom->mbpy_unmarried_women + $fFrom->mbpy_orphan_due_to_covide +
        $fFrom->mbpy_widow_due_to_covid + $fFrom->mbpy_divorce_or_destitute + $fFrom->mbpy_transgender;

        $ep_from = $fFrom->mbpy_oap_above_80_years + $fFrom->mbpy_sdp_above_80_percent + $fFrom->mbpy_sdoap;

        $normal_to = $fTo->mbpy_oap_below_80_years + $fTo->mbpy_wp + $fTo->mbpy_dp +
        $fTo->mbpy_sdp_below_80_percent + $fTo->mbpy_clp + $fTo->mbpy_wp_aids +
        $fTo->mbpy_dp_aids + $fTo->mbpy_unmarried_women + $fTo->mbpy_orphan_due_to_covide +
        $fTo->mbpy_widow_due_to_covid + $fTo->mbpy_divorce_or_destitute + $fTo->mbpy_transgender;

        $ep_to = $fTo->mbpy_oap_above_80_years + $fTo->mbpy_sdp_above_80_percent + $fTo->mbpy_sdoap;

        $normal_fund_from =
        ($fFrom->mbpy_oap_below_80_years * $schemeRates['oap_below_80']) +
        ($fFrom->mbpy_wp * $schemeRates['widow_pension']) +
        ($fFrom->mbpy_dp * $schemeRates['disabled_pension']) +
        ($fFrom->mbpy_sdp_below_80_percent * $schemeRates['sdp_below_80']) +
        ($fFrom->mbpy_clp * $schemeRates['clp']) +
        ($fFrom->mbpy_wp_aids * $schemeRates['wp_aids']) +
        ($fFrom->mbpy_dp_aids * $schemeRates['dp_aids']) +
        ($fFrom->mbpy_unmarried_women * $schemeRates['unmarried_women']) +
        ($fFrom->mbpy_orphan_due_to_covide * $schemeRates['orphan_covid']) +
        ($fFrom->mbpy_widow_due_to_covid * $schemeRates['widow_covid']) +
        ($fFrom->mbpy_divorce_or_destitute * $schemeRates['divorce_destitute']) +
        ($fFrom->mbpy_transgender * $schemeRates['transgender']);

        $ep_fund_from =
        ($fFrom->mbpy_oap_above_80_years * $schemeRates['oap_above_80']) +
        ($fFrom->mbpy_sdp_above_80_percent * $schemeRates['sdp_above_80']) +
        ($fFrom->mbpy_sdoap * $schemeRates['sdoap']);

        $normal_fund_to =
        ($fTo->mbpy_oap_below_80_years * $schemeRates['oap_below_80']) +
        ($fTo->mbpy_wp * $schemeRates['widow_pension']) +
        ($fTo->mbpy_dp * $schemeRates['disabled_pension']) +
        ($fTo->mbpy_sdp_below_80_percent * $schemeRates['sdp_below_80']) +
        ($fTo->mbpy_clp * $schemeRates['clp']) +
        ($fTo->mbpy_wp_aids * $schemeRates['wp_aids']) +
        ($fTo->mbpy_dp_aids * $schemeRates['dp_aids']) +
        ($fTo->mbpy_unmarried_women * $schemeRates['unmarried_women']) +
        ($fTo->mbpy_orphan_due_to_covide * $schemeRates['orphan_covid']) +
        ($fTo->mbpy_widow_due_to_covid * $schemeRates['widow_covid']) +
        ($fTo->mbpy_divorce_or_destitute * $schemeRates['divorce_destitute']) +
        ($fTo->mbpy_transgender * $schemeRates['transgender']);

        $ep_fund_to =
        ($fTo->mbpy_oap_above_80_years * $schemeRates['oap_above_80']) +
        ($fTo->mbpy_sdp_above_80_percent * $schemeRates['sdp_above_80']) +
        ($fTo->mbpy_sdoap * $schemeRates['sdoap']);

        $comparisonData[] = (object)[
            'district_name' => $districtNames[$districtId] ?? 'Unknown',
            'normal_pensioners_from' => $normal_from,
            'ep_pensioners_from' => $ep_from,
            'beneficiaries_from_month' => $fFrom->mbpy_total_beneficiaries,
            'normal_pensioners_to' => $normal_to,
            'ep_pensioners_to' => $ep_to,
            'beneficiaries_to_month' => $fTo->mbpy_total_beneficiaries,
            'difference_of_beneficiaries' => $fTo->mbpy_total_beneficiaries - $fFrom->mbpy_total_beneficiaries,
            'normal_fund_from' => $normal_fund_from,
            'ep_fund_from' => $ep_fund_from,
            'funds_from_month' => $fFrom->funds_mbpy_total_beneficiaries,
            'normal_fund_to' => $normal_fund_to,
            'ep_fund_to' => $ep_fund_to,
            'funds_to_month' => $fTo->funds_mbpy_total_beneficiaries,
            'difference_of_funds' => $fTo->funds_mbpy_total_beneficiaries - $fFrom->funds_mbpy_total_beneficiaries,
        ];
    }

    $comparisonData = collect($comparisonData)->sortBy('district_name', SORT_NATURAL | SORT_FLAG_CASE)->values();

    return view('dashboard.pension.dailypension.month_wise_fund_requirement_comparison_for_district', compact(
        'dateConfig', 'from_the_month', 'to_the_month', 'comparisonData'
    ));
}

public function month_wise_fund_requirement_comparison_for_block_ulb(Request $request)
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
    ->where('is_active', 'active')
    ->orderBy('id', 'desc')
    ->get();

    $from_the_month = $request->from_the_month 
    ?? ($dateConfig->skip(1)->first()->for_the_month 
        ?? $dateConfig->first()->for_the_month 
        ?? now()->format('F-Y'));

    $to_the_month = $request->to_the_month 
    ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

    $user = Auth::user();
    $userRole = $user->role_id;

    $schemeRates = [
        'oap_below_80' => 1000,
        'oap_above_80' => 3500,
        'widow_pension' => 1000,
        'disabled_pension' => 1000,
        'sdp_below_80' => 1200,
        'sdp_above_80' => 3500,
        'sdoap' => 3500,
        'clp' => 1000,
        'wp_aids' => 1000,
        'dp_aids' => 1000,
        'unmarried_women' => 1000,
        'orphan_covid' => 1000,
        'widow_covid' => 1000,
        'divorce_destitute' => 1000,
        'transgender' => 1000,
    ];

    if (in_array($userRole, [1,2,12,13,14,15,25])) {
        $allBlocks = Block::with('district')->where('is_active', 'active')->get();
        $allMunicipalities = Municipality::with('district')->where('is_active', 'active')->get();
    } elseif (in_array($userRole, [4,6])) {
        $allBlocks = Block::with('district')
        ->where('block_id', $user->posted_block)
        ->where('is_active', 'active')
        ->get();
        $allMunicipalities = collect();
    } elseif ($userRole == 5) {
        $allBlocks = collect();
        $allMunicipalities = Municipality::with('district')
        ->where('municipality_id', $user->posted_municipality)
        ->where('is_active', 'active')
        ->get();
    } elseif (in_array($userRole, [8,10])) {
        $blockIds = Block::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')
        ->pluck('block_id');
        $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')
        ->pluck('municipality_id');
        $allBlocks = Block::with('district')->whereIn('block_id', $blockIds)->get();
        $allMunicipalities = Municipality::with('district')->whereIn('municipality_id', $municipalityIds)->get();
    } elseif (in_array($userRole, [9,11])) {
        $allBlocks = Block::with('district')
        ->where('district_id', $user->posted_district)
        ->where('is_active', 'active')
        ->get();
        $allMunicipalities = Municipality::with('district')
        ->where('district_id', $user->posted_district)
        ->where('is_active', 'active')
        ->get();
    } else {
        $allBlocks = collect();
        $allMunicipalities = collect();
    }


    $comparisonData = [];

    foreach ($allBlocks as $block) {
        $fundFrom = DB::table('pension_funds_requirements')
        ->where('for_the_month', $from_the_month)
        ->where('block_id', $block->block_id)
        ->first();

        $fundTo = DB::table('pension_funds_requirements')
        ->where('for_the_month', $to_the_month)
        ->where('block_id', $block->block_id)
        ->first();

        if ($fundFrom || $fundTo) {
            $normal_from = 
            ($fundFrom->mbpy_oap_below_80_years ?? 0) +
            ($fundFrom->mbpy_wp ?? 0) +
            ($fundFrom->mbpy_dp ?? 0) +
            ($fundFrom->mbpy_sdp_below_80_percent ?? 0) +
            ($fundFrom->mbpy_clp ?? 0) +
            ($fundFrom->mbpy_wp_aids ?? 0) +
            ($fundFrom->mbpy_dp_aids ?? 0) +
            ($fundFrom->mbpy_unmarried_women ?? 0) +
            ($fundFrom->mbpy_orphan_due_to_covide ?? 0) +
            ($fundFrom->mbpy_widow_due_to_covid ?? 0) +
            ($fundFrom->mbpy_divorce_or_destitute ?? 0) +
            ($fundFrom->mbpy_transgender ?? 0);

            $normal_to = 
            ($fundTo->mbpy_oap_below_80_years ?? 0) +
            ($fundTo->mbpy_wp ?? 0) +
            ($fundTo->mbpy_dp ?? 0) +
            ($fundTo->mbpy_sdp_below_80_percent ?? 0) +
            ($fundTo->mbpy_clp ?? 0) +
            ($fundTo->mbpy_wp_aids ?? 0) +
            ($fundTo->mbpy_dp_aids ?? 0) +
            ($fundTo->mbpy_unmarried_women ?? 0) +
            ($fundTo->mbpy_orphan_due_to_covide ?? 0) +
            ($fundTo->mbpy_widow_due_to_covid ?? 0) +
            ($fundTo->mbpy_divorce_or_destitute ?? 0) +
            ($fundTo->mbpy_transgender ?? 0);

            $ep_from = 
            ($fundFrom->mbpy_oap_above_80_years ?? 0) +
            ($fundFrom->mbpy_sdp_above_80_percent ?? 0) +
            ($fundFrom->mbpy_sdoap ?? 0);
            $ep_to = 
            ($fundTo->mbpy_oap_above_80_years ?? 0) +
            ($fundTo->mbpy_sdp_above_80_percent ?? 0) +
            ($fundTo->mbpy_sdoap ?? 0);

            $normal_fund_from =
            ($fundFrom->mbpy_oap_below_80_years ?? 0) * $schemeRates['oap_below_80'] +
            ($fundFrom->mbpy_wp ?? 0) * $schemeRates['widow_pension'] +
            ($fundFrom->mbpy_dp ?? 0) * $schemeRates['disabled_pension'] +
            ($fundFrom->mbpy_sdp_below_80_percent ?? 0) * $schemeRates['sdp_below_80'];

            $normal_fund_to =
            ($fundTo->mbpy_oap_below_80_years ?? 0) * $schemeRates['oap_below_80'] +
            ($fundTo->mbpy_wp ?? 0) * $schemeRates['widow_pension'] +
            ($fundTo->mbpy_dp ?? 0) * $schemeRates['disabled_pension'] +
            ($fundTo->mbpy_sdp_below_80_percent ?? 0) * $schemeRates['sdp_below_80'];

            $ep_fund_from =
            ($fundFrom->mbpy_oap_above_80_years ?? 0) * $schemeRates['oap_above_80'] +
            ($fundFrom->mbpy_sdp_above_80_percent ?? 0) * $schemeRates['sdp_above_80'] +
            ($fundFrom->mbpy_sdoap ?? 0) * $schemeRates['sdoap'];

            $ep_fund_to =
            ($fundTo->mbpy_oap_above_80_years ?? 0) * $schemeRates['oap_above_80'] +
            ($fundTo->mbpy_sdp_above_80_percent ?? 0) * $schemeRates['sdp_above_80'] +
            ($fundTo->mbpy_sdoap ?? 0) * $schemeRates['sdoap'];

            $comparisonData[] = (object)[
                'address_type' => 'Block',
                'district_name' => $block->district->district_name ?? '-',
                'block_or_municipality_name' => $block->block_name,
                'beneficiaries_from_month' => $fundFrom->mbpy_total_beneficiaries ?? 0,
                'beneficiaries_to_month' => $fundTo->mbpy_total_beneficiaries ?? 0,
                'difference_of_beneficiaries' => (($fundTo->mbpy_total_beneficiaries ?? 0) - ($fundFrom->mbpy_total_beneficiaries ?? 0)),
                'funds_from_month' => $fundFrom->funds_mbpy_total_beneficiaries ?? 0,
                'funds_to_month' => $fundTo->funds_mbpy_total_beneficiaries ?? 0,
                'difference_of_funds' => (($fundTo->funds_mbpy_total_beneficiaries ?? 0) - ($fundFrom->funds_mbpy_total_beneficiaries ?? 0)),
                'normal_pensioners_from' => $normal_from,
                'normal_pensioners_to' => $normal_to,
                'ep_pensioners_from' => $ep_from,
                'ep_pensioners_to' => $ep_to,
                'normal_fund_from' => $normal_fund_from,
                'normal_fund_to' => $normal_fund_to,
                'ep_fund_from' => $ep_fund_from,
                'ep_fund_to' => $ep_fund_to,
            ];
        }
    }

    foreach ($allMunicipalities as $municipality) {
        $fundFrom = DB::table('pension_funds_requirements')
        ->where('for_the_month', $from_the_month)
        ->where('municipality_id', $municipality->municipality_id)
        ->first();

        $fundTo = DB::table('pension_funds_requirements')
        ->where('for_the_month', $to_the_month)
        ->where('municipality_id', $municipality->municipality_id)
        ->first();

        if ($fundFrom || $fundTo) {
            $normal_from = 
            ($fundFrom->mbpy_oap_below_80_years ?? 0) +
            ($fundFrom->mbpy_wp ?? 0) +
            ($fundFrom->mbpy_dp ?? 0) +
            ($fundFrom->mbpy_sdp_below_80_percent ?? 0) +
            ($fundFrom->mbpy_clp ?? 0) +
            ($fundFrom->mbpy_wp_aids ?? 0) +
            ($fundFrom->mbpy_dp_aids ?? 0) +
            ($fundFrom->mbpy_unmarried_women ?? 0) +
            ($fundFrom->mbpy_orphan_due_to_covide ?? 0) +
            ($fundFrom->mbpy_widow_due_to_covid ?? 0) +
            ($fundFrom->mbpy_divorce_or_destitute ?? 0) +
            ($fundFrom->mbpy_transgender ?? 0);

            $normal_to = 
            ($fundTo->mbpy_oap_below_80_years ?? 0) +
            ($fundTo->mbpy_wp ?? 0) +
            ($fundTo->mbpy_dp ?? 0) +
            ($fundTo->mbpy_sdp_below_80_percent ?? 0) +
            ($fundTo->mbpy_clp ?? 0) +
            ($fundTo->mbpy_wp_aids ?? 0) +
            ($fundTo->mbpy_dp_aids ?? 0) +
            ($fundTo->mbpy_unmarried_women ?? 0) +
            ($fundTo->mbpy_orphan_due_to_covide ?? 0) +
            ($fundTo->mbpy_widow_due_to_covid ?? 0) +
            ($fundTo->mbpy_divorce_or_destitute ?? 0) +
            ($fundTo->mbpy_transgender ?? 0);

            $ep_from = 
            ($fundFrom->mbpy_oap_above_80_years ?? 0) +
            ($fundFrom->mbpy_sdp_above_80_percent ?? 0) +
            ($fundFrom->mbpy_sdoap ?? 0);
            $ep_to = 
            ($fundTo->mbpy_oap_above_80_years ?? 0) +
            ($fundTo->mbpy_sdp_above_80_percent ?? 0) +
            ($fundTo->mbpy_sdoap ?? 0);

            $normal_fund_from =
            ($fundFrom->mbpy_oap_below_80_years ?? 0) * $schemeRates['oap_below_80'] +
            ($fundFrom->mbpy_wp ?? 0) * $schemeRates['widow_pension'] +
            ($fundFrom->mbpy_dp ?? 0) * $schemeRates['disabled_pension'] +
            ($fundFrom->mbpy_sdp_below_80_percent ?? 0) * $schemeRates['sdp_below_80'];

            $normal_fund_to =
            ($fundTo->mbpy_oap_below_80_years ?? 0) * $schemeRates['oap_below_80'] +
            ($fundTo->mbpy_wp ?? 0) * $schemeRates['widow_pension'] +
            ($fundTo->mbpy_dp ?? 0) * $schemeRates['disabled_pension'] +
            ($fundTo->mbpy_sdp_below_80_percent ?? 0) * $schemeRates['sdp_below_80'];

            $ep_fund_from =
            ($fundFrom->mbpy_oap_above_80_years ?? 0) * $schemeRates['oap_above_80'] +
            ($fundFrom->mbpy_sdp_above_80_percent ?? 0) * $schemeRates['sdp_above_80'] +
            ($fundFrom->mbpy_sdoap ?? 0) * $schemeRates['sdoap'];

            $ep_fund_to =
            ($fundTo->mbpy_oap_above_80_years ?? 0) * $schemeRates['oap_above_80'] +
            ($fundTo->mbpy_sdp_above_80_percent ?? 0) * $schemeRates['sdp_above_80'] +
            ($fundTo->mbpy_sdoap ?? 0) * $schemeRates['sdoap'];

            $comparisonData[] = (object)[
                'address_type' => 'ULB',
                'district_name' => $municipality->district->district_name ?? '-',
                'block_or_municipality_name' => $municipality->municipality_name,
                'beneficiaries_from_month' => $fundFrom->mbpy_total_beneficiaries ?? 0,
                'beneficiaries_to_month' => $fundTo->mbpy_total_beneficiaries ?? 0,
                'difference_of_beneficiaries' => (($fundTo->mbpy_total_beneficiaries ?? 0) - ($fundFrom->mbpy_total_beneficiaries ?? 0)),
                'funds_from_month' => $fundFrom->funds_mbpy_total_beneficiaries ?? 0,
                'funds_to_month' => $fundTo->funds_mbpy_total_beneficiaries ?? 0,
                'difference_of_funds' => (($fundTo->funds_mbpy_total_beneficiaries ?? 0) - ($fundFrom->funds_mbpy_total_beneficiaries ?? 0)),
                'normal_pensioners_from' => $normal_from,
                'normal_pensioners_to' => $normal_to,
                'ep_pensioners_from' => $ep_from,
                'ep_pensioners_to' => $ep_to,
                'normal_fund_from' => $normal_fund_from,
                'normal_fund_to' => $normal_fund_to,
                'ep_fund_from' => $ep_fund_from,
                'ep_fund_to' => $ep_fund_to,
            ];
        }
    }

    $comparisonData = collect($comparisonData)->sortBy('district_name')->values();

    return view('dashboard.pension.dailypension.month_wise_fund_requirement_comparison_for_block_ulb', compact(
        'dateConfig', 'from_the_month', 'to_the_month', 'comparisonData'
    ));
}

public function block_ulb_wise_daily_pension_disbursement_report(Request $request)
{
    $selectedDate = $request->date ?? date('Y-m-d');

    $blocks = Block::where('is_active', 'active')->get();
    $municipalities = Municipality::where('is_active', 'active')->get();

    $daily = DailyPensionDisbursement::where('disbursement_start_date', $selectedDate)
        ->where('is_active', 'active')
        ->get();

    $fundRates = [
        'oap_below_80' => 1000,
        'oap_above_80' => 3500,
        'widow_pension' => 1000,
        'disabled_pension' => 1000,
        'sdp_below_80' => 1200,
        'sdp_above_80' => 3500,
        'sdoap' => 3500,
        'clp' => 1000,
        'wp_aids' => 1000,
        'dp_aids' => 1000,
        'unmarried_women' => 1000,
        'orphan_covid' => 1000,
        'widow_covid' => 1000,
        'divorce_destitute' => 1000,
        'transgender' => 1000,
    ];

    $fundsRaw = DailyPensionDisbursement::where('disbursement_start_date', $selectedDate)
        ->where('is_active', 'active')
        ->selectRaw("
            staff_address_type, district_id, block_id, municipality_id,
            SUM(mbpy_oap_below_80_years) AS oap_below_80,
            SUM(mbpy_oap_above_80_years) AS oap_above_80,
            SUM(mbpy_wp) AS widow_pension,
            SUM(mbpy_dp) AS disabled_pension,
            SUM(mbpy_sdp_below_80_percent) AS sdp_below_80,
            SUM(mbpy_sdp_above_80_percent) AS sdp_above_80,
            SUM(mbpy_sdoap) AS sdoap,
            SUM(mbpy_clp) AS clp,
            SUM(mbpy_wp_aids) AS wp_aids,
            SUM(mbpy_dp_aids) AS dp_aids,
            SUM(mbpy_unmarried_women) AS unmarried_women,
            SUM(mbpy_orphan_due_to_covide) AS orphan_covid,
            SUM(mbpy_widow_due_to_covid) AS widow_covid,
            SUM(mbpy_divorce_or_destitute) AS divorce_destitute,
            SUM(mbpy_transgender) AS transgender
        ")
        ->groupBy('staff_address_type', 'district_id', 'block_id', 'municipality_id')
        ->get()
        ->keyBy(function ($item) {
            return $item->block_id ?: ('ULB_' . $item->municipality_id);
        });

    $final = [];

    foreach ($blocks as $b) {

        $entries = $daily->where('block_id', $b->block_id);

        $fundSource = $fundsRaw[$b->block_id] ?? null;

        $fund_normal = 0;
        $fund_ep = 0;

        if ($fundSource) {
            foreach ($fundRates as $key => $rate) {
                $count = $fundSource->$key ?? 0;

                if ($rate == 3500) {
                    $fund_ep += $count * 3500;
                } else {
                    $fund_normal += $count * $rate;
                }
            }
        }

        $final[] = [
            'district_name' => District::where('district_id', $b->district_id)->value('district_name'),
            'staff_address_type' => 'Block',
            'block_ulb_name' => $b->block_name,
            'status' => $entries->count() > 0 ? 'Available' : 'Not Available',
            'no_of_normal_pensioners' => $entries->sum('no_of_normal_pensioners'),
            'no_of_ep_pensioners' => $entries->sum('no_of_ep_pensioners'),
            'funds_no_of_normal_pensioners' => $fund_normal,
            'funds_no_of_ep_pensioners' => $fund_ep,
        ];
    }

    foreach ($municipalities as $m) {

        $entries = $daily->where('municipality_id', $m->municipality_id);

        $key = 'ULB_' . $m->municipality_id;
        $fundSource = $fundsRaw[$key] ?? null;

        $fund_normal = 0;
        $fund_ep = 0;

        if ($fundSource) {
            foreach ($fundRates as $key => $rate) {
                $count = $fundSource->$key ?? 0;

                if ($rate == 3500) {
                    $fund_ep += $count * 3500;
                } else {
                    $fund_normal += $count * $rate;
                }
            }
        }

        $final[] = [
            'district_name' => District::where('district_id', $m->district_id)->value('district_name'),
            'staff_address_type' => 'ULB',
            'block_ulb_name' => $m->municipality_name,
            'status' => $entries->count() > 0 ? 'Available' : 'Not Available',
            'no_of_normal_pensioners' => $entries->sum('no_of_normal_pensioners'),
            'no_of_ep_pensioners' => $entries->sum('no_of_ep_pensioners'),
            'funds_no_of_normal_pensioners' => $fund_normal,
            'funds_no_of_ep_pensioners' => $fund_ep,
        ];
    }

    return view('dashboard.pension.dailypension.block_ulb_wise_daily_pension_disbursement_report', [
        'data' => $final,
        'selectedDate' => $selectedDate
    ]);
}


}