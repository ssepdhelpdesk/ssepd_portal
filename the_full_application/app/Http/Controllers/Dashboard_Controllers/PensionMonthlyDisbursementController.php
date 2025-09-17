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
use Yajra\DataTables\Facades\DataTables;

class PensionMonthlyDisbursementController extends Controller
{
/**
* Display a listing of the resource.
*/
/*public function index()
{
$user = auth()->user();
$userRole = $user->role_id;
$today_date = Carbon::today('Asia/Kolkata')->format('Y-m-d');

$dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')->where('status', 1)->first();

if (!$dateConfig) {
return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
}

$startDate = $dateConfig->start_date;
$endDate   = $dateConfig->end_date;
$forTheMonth = $dateConfig->for_the_month;

if ($user->role_name == 'BSSO')
{
$gp_ward_id = Grampanchayat::where('block_id', $user->posted_block)->where('is_active', 'active')->get();
} elseif ($user->role_name == 'MEO')
{
$gp_ward_id = WardMaster::where('municipal_area_code', $user->posted_municipality)->where('is_active', '1')->get();
} else {
return redirect()->back()->with('error', 'You have no specific permission for this page. Please contact admin.');
}

$gpIds   = $gp_ward_id->pluck('gp_id')->toArray();
$wardIds = $gp_ward_id->pluck('ward_id')->toArray();

$alreadySubmitted = MonthlyPensionDisbursemenet::where(function ($q) use ($gpIds, $wardIds) {
$q->whereIn('gp_id', $gpIds)
->orWhereIn('ward_id', $wardIds);
})
->where('for_the_month', $forTheMonth)
->where('created_date', $today_date)
->where('is_active', 'active')
->where('status', 1)
->exists();

if ($alreadySubmitted) {
return redirect()->back()->with('error', 'You have already submitted the details for ' . $forTheMonth . '. If any changes are required, then go to Daily Pension Disbursement under the Report section and Edit.');
}

return view('dashboard.pension.monthly_pension_disbursement', compact('gp_ward_id', 'user', 'startDate', 'endDate', 'forTheMonth'));
}*/

public function index()
{
    $user = auth()->user();
    $userRole = $user->role_id;
    $today_date = Carbon::today('Asia/Kolkata')->format('Y-m-d');

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
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
        $wardIds = $gp_ward_id->pluck('ward_id')->toArray();
    } else {
        return redirect()->back()->with('error', 'You have no specific permission for this page. Please contact admin.');
    }

/*$submittedGpIds = MonthlyPensionDisbursemenet::whereIn('gp_id', $gpIds)
->where('for_the_month', $forTheMonth)
->where('disbursement_start_date', $today_date)
->where('is_active', 'active')
->where('status', 1)
->pluck('gp_id')
->toArray();

$submittedWardIds = MonthlyPensionDisbursemenet::whereIn('ward_id', $wardIds)
->where('for_the_month', $forTheMonth)
->where('disbursement_start_date', $today_date)
->where('is_active', 'active')
->where('status', 1)
->pluck('ward_id')
->toArray();

if ($user->role_name == 'BSSO') {
$gp_ward_id = $gp_ward_id->whereNotIn('gp_id', $submittedGpIds);
} elseif ($user->role_name == 'MEO') {
$gp_ward_id = $gp_ward_id->whereNotIn('ward_id', $submittedWardIds);
}

if ($gp_ward_id->isEmpty()) {
return redirect()->back()->with('error', 'You have already submitted the details for ' . $forTheMonth . ' (for today). If any changes are required, please go to Daily Pension Disbursement under the Report section and Edit.');
}*/

return view('dashboard.pension.monthly_pension_disbursement', compact('gp_ward_id', 'user', 'startDate', 'endDate', 'forTheMonth'));
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
public function store(Request $request)
{
    $validationRules = [
        'gp_ward_id'              => 'required|array',
        'gp_ward_id.*'            => 'required|string',
        'gp_ward_name'            => 'required|array',
        'gp_ward_name.*'          => 'required|string',
        'no_of_normal_pensioners'   => 'required|array',
        'no_of_normal_pensioners.*' => 'required|integer|min:0',
        'no_of_ep_pensioners'       => 'required|array',
        'no_of_ep_pensioners.*'     => 'required|integer|min:0',
    ];

    $validatedData = $request->validate($validationRules);

    $user = auth()->user();

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
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
        $gp_id = null;
        $municipality_id = $user->posted_municipality;
        $district_id = Municipality::where('municipality_id', $municipality_id)->value('district_id');
    } else {
        return redirect()->back()->with('error', 'You have no specific permission for this page. Please contact admin.');
    }
    DB::beginTransaction();
    try {

        foreach ($request->gp_ward_id as $index => $gpWardId) {
            $startDate = $request->disbursement_start_date[$index] ?? null;
            $no_of_normal_pensioners = (int) ($request->no_of_normal_pensioners[$index] ?? 0);
            $no_of_ep_pensioners     = (int) ($request->no_of_ep_pensioners[$index] ?? 0);

            if (!empty($startDate) && ($no_of_normal_pensioners > 0 || $no_of_ep_pensioners > 0)) {

                $gpId   = $user->role_name == 'BSSO' ? $gpWardId : null;
                $wardId = $user->role_name == 'MEO'  ? $gpWardId : null;

                $existing = MonthlyPensionDisbursemenet::where('for_the_month', $forTheMonth)
                ->where('disbursement_start_date', $startDate)
                ->when($gpId, fn($q) => $q->where('gp_id', $gpId))
                ->when($wardId, fn($q) => $q->where('ward_id', $wardId))
                ->first();

                if ($existing) {
                    $existing->update([
                        'no_of_normal_pensioners' => $no_of_normal_pensioners,
                        'no_of_ep_pensioners'     => $no_of_ep_pensioners,
                        'disbursement_started'    => 1,
                        'is_active'               => 'active',
                        'status'                  => 1,
                        'updated_date'            => now()->setTimezone('Asia/Kolkata')->toDateString(),
                        'updated_time'            => now()->setTimezone('Asia/Kolkata')->toTimeString(),
                        'updated_by'              => $user->user_table_id,
                    ]);
                } else {
                    MonthlyPensionDisbursemenet::create([
                        'for_the_month'           => $forTheMonth,
                        'disbursement_start_date' => $startDate,
                        'no_of_normal_pensioners' => $no_of_normal_pensioners,
                        'no_of_ep_pensioners'     => $no_of_ep_pensioners,
                        'disbursement_started'    => 1,
                        'staff_address_type'      => $staff_address_type,
                        'state_id'                => 228,
                        'district_id'             => $district_id,
                        'municipality_id'         => $municipality_id,
                        'block_id'                => $block_id,
                        'gp_id'                   => $gpId,
                        'ward_id'                 => $wardId,
                        'is_active'               => 'active',
                        'created_date'            => now()->setTimezone('Asia/Kolkata')->toDateString(),
                        'created_time'            => now()->setTimezone('Asia/Kolkata')->toTimeString(),
                        'created_by'              => $user->user_table_id,
                        'status'                  => 1,
                    ]);
                }
            }
        }
        DB::commit();

        return redirect()->back()->with('success', 'Pension disbursement dates saved successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🛑 Pension Disbursement Authority form update failed", [
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

public function monthly_pension_disbursement_report()
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')->where('status', 1)->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $startDate = $dateConfig->start_date;
    $endDate   = $dateConfig->end_date;
    $forTheMonth = $dateConfig->for_the_month;

    ini_set('memory_limit', '512M');
    $user = auth()->user();
    $userRole = $user->role_id;

    $monthlyPensionDisbursemenetQuery = MonthlyPensionDisbursemenet::where('disbursement_started', 1)
    ->where('for_the_month', $forTheMonth)
    ->where('is_active', 'active')
    ->where('status', 1)
    ->with(['state', 'district', 'block', 'grampanchayat', 'municipality', 'ward']);

    $allGps = collect();
    $allWards = collect();

    if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('is_active', 'active')->get();
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('is_active', '1')->get();
    } elseif (in_array($userRole, [4, 6])) {
        $monthlyPensionDisbursemenetQuery->where('block_id', $user->posted_block);
        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('block_id', $user->posted_block)->where('is_active', 'active')->get();
    } elseif ($userRole == 5) {
        $monthlyPensionDisbursemenetQuery->where('municipality_id', $user->posted_municipality);
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('municipal_area_code', $user->posted_municipality)->where('is_active', '1')->get();
    } elseif (in_array($userRole, [8, 10])) {
        $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->where('is_active', 'active')->pluck('block_id');
        $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->where('is_active', 'active')->pluck('municipality_id');

        $monthlyPensionDisbursemenetQuery->where(function ($query) use ($blockIds, $municipalityIds) {
            $query->whereIn('block_id', $blockIds)
            ->orWhereIn('municipality_id', $municipalityIds);
        });

        $allGps = Grampanchayat::with(['district', 'block'])
        ->whereIn('block_id', $blockIds)->where('is_active', 'active')->get();
        $allWards = WardMaster::with(['district', 'municipality'])
        ->whereIn('municipal_area_code', $municipalityIds)->where('is_active', '1')->get();
    } elseif (in_array($userRole, [9, 11])) {
        $monthlyPensionDisbursemenetQuery->where('district_id', $user->posted_district);

        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('district_id', $user->posted_district)->where('is_active', 'active')->get();
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('district_code', $user->posted_district)->where('is_active', '1')->get();
    }

    $monthlyPension = $monthlyPensionDisbursemenetQuery->where('is_active', 'active')->where('status', 1)->where('disbursement_started', 1)->get();

    $submittedGpIds = $monthlyPension->pluck('gp_id')->filter()->unique();
    $submittedWardIds = $monthlyPension->pluck('ward_id')->filter()->unique();

    $pendingGps = $allGps->whereNotIn('gp_id', $submittedGpIds)->map(function ($gp) {
        return (object)[
            'id' => null,
            'district' => $gp->district,
            'block' => $gp->block,
            'grampanchayat' => $gp,
            'municipality' => null,
            'ward' => null,
            'staff_address_type' => 1,
            'for_the_month' => null,
            'disbursement_start_date' => null,
            'no_of_normal_pensioners' => null,
            'no_of_ep_pensioners' => null,
            'disbursement_started'     => 0,
        ];
    });

    $pendingWards = $allWards->whereNotIn('ward_code', $submittedWardIds)->map(function ($ward) {
        return (object)[
            'id' => null,
            'district' => $ward->district,
            'block' => null,
            'grampanchayat' => null,
            'municipality' => $ward->municipality,
            'ward' => $ward,
            'staff_address_type' => 2,
            'for_the_month' => null,
            'disbursement_start_date' => null,
            'no_of_normal_pensioners' => null,
            'no_of_ep_pensioners' => null,
            'disbursement_started' => null,
            'disbursement_started'     => 0,
        ];
    });

    $combined = $monthlyPension
    ->concat($pendingGps)
    ->concat($pendingWards);

    $monthlyPensionDisbursemenet = $combined->sortBy(function ($item) {
        return $item->district->district_name ?? '';
    })->values();

    return view('dashboard.pension.monthly_pension_disbursement_report', compact('monthlyPensionDisbursemenet', 'startDate', 'endDate', 'forTheMonth'));
}

/*public function monthly_pension_disbursement_report_abstract()
{
$dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
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

$monthlyPensionQuery = MonthlyPensionDisbursemenet::where('disbursement_started', 1)
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
$monthlyPensionQuery->where('block_id', $user->posted_block);
$allGps = Grampanchayat::with(['district', 'block'])
->where('block_id', $user->posted_block)
->where('is_active', 'active')->get();
} elseif ($userRole == 5) {
$monthlyPensionQuery->where('municipality_id', $user->posted_municipality);
$allWards = WardMaster::with(['district', 'municipality'])
->where('municipal_area_code', $user->posted_municipality)
->where('is_active', '1')->get();
} elseif (in_array($userRole, [8, 10])) {
$blockIds = Block::where('subdivision_id', $user->posted_subdiv)
->where('is_active', 'active')
->pluck('block_id');
$municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)
->where('is_active', 'active')
->pluck('municipality_id');

$monthlyPensionQuery->where(function ($query) use ($blockIds, $municipalityIds) {
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
$monthlyPensionQuery->where('district_id', $user->posted_district);

$allGps = Grampanchayat::with(['district', 'block'])
->where('district_id', $user->posted_district)
->where('is_active', 'active')->get();
$allWards = WardMaster::with(['district', 'municipality'])
->where('district_code', $user->posted_district)
->where('is_active', '1')->get();
}

$monthlyPension = $monthlyPensionQuery->where('is_active', 'active')
->where('status', 1)->where('disbursement_started', 1)->get();

$submittedGpIds   = $monthlyPension->pluck('gp_id')->filter()->unique();
$submittedWardIds = $monthlyPension->pluck('ward_id')->filter()->unique();

$summary = collect();

foreach ($allGps->groupBy(fn($gp) => $gp->district->district_name . '-' . $gp->block->block_name) as $key => $gpsGroup) {
[$districtName, $blockOrUlb] = explode('-', $key);

$submittedCount = $gpsGroup->whereIn('gp_id', $submittedGpIds)->count();
$pendingCount   = $gpsGroup->whereNotIn('gp_id', $submittedGpIds)->count();

$summary[$key] = [
'district'                  => $districtName,
'type'                      => 'Block',
'block_or_ulb'              => $blockOrUlb,
'Data_provided_by_GP'       => $submittedCount,
'Data_provided_by_ward'     => 0,
'Data_not_provided_by_GP'   => $pendingCount,
'Data_not_provided_by_ward' => 0,
];
}

foreach ($allWards->groupBy(fn($ward) => $ward->district->district_name . '-' . $ward->municipality->municipality_name) as $key => $wardsGroup) {
[$districtName, $blockOrUlb] = explode('-', $key);

$submittedCount = $wardsGroup->whereIn('ward_code', $submittedWardIds)->count();
$pendingCount   = $wardsGroup->whereNotIn('ward_code', $submittedWardIds)->count();

if (!$summary->has($key)) {
$summary[$key] = [
'district'                  => $districtName,
'type'                      => 'ULB',
'block_or_ulb'              => $blockOrUlb,
'Data_provided_by_GP'       => 0,
'Data_provided_by_ward'     => $submittedCount,
'Data_not_provided_by_GP'   => 0,
'Data_not_provided_by_ward' => $pendingCount,
];
} else {
$summary[$key]['type']                      = 'ULB';
$summary[$key]['Data_provided_by_ward']     = $submittedCount;
$summary[$key]['Data_not_provided_by_ward'] = $pendingCount;
}
}

$summaryReport = $summary->values();

return view('dashboard.pension.monthly_pension_disbursement_report_abstract', compact(
'summaryReport',
'startDate',
'endDate',
'forTheMonth',
'monthlyPension'
));
}*/

public function monthly_pension_disbursement_report_abstract()
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
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

    $monthlyPensionQuery = MonthlyPensionDisbursemenet::where('disbursement_started', 1)
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
        $monthlyPensionQuery->where('block_id', $user->posted_block);
        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('block_id', $user->posted_block)
        ->where('is_active', 'active')->get();
    } elseif ($userRole == 5) {
        $monthlyPensionQuery->where('municipality_id', $user->posted_municipality);
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('municipal_area_code', $user->posted_municipality)
        ->where('is_active', '1')->get();
    } elseif (in_array($userRole, [8, 10])) {
        $blockIds = Block::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')
        ->pluck('block_id');
        $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')
        ->pluck('municipality_id');

        $monthlyPensionQuery->where(function ($query) use ($blockIds, $municipalityIds) {
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
        $monthlyPensionQuery->where('district_id', $user->posted_district);

        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('district_id', $user->posted_district)
        ->where('is_active', 'active')->get();
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('district_code', $user->posted_district)
        ->where('is_active', '1')->get();
    }

    $monthlyPension = $monthlyPensionQuery->get();

    $submittedGpIds   = $monthlyPension->pluck('gp_id')->filter()->unique();
    $submittedWardIds = $monthlyPension->pluck('ward_id')->filter()->unique();

    $summary = collect();

    foreach ($allGps->groupBy(fn($gp) => $gp->district->district_name . '-' . $gp->block->block_name) as $key => $gpsGroup) {
        [$districtName, $blockOrUlb] = explode('-', $key);

        $submittedGps = $gpsGroup->whereIn('gp_id', $submittedGpIds);
        $pendingGps   = $gpsGroup->whereNotIn('gp_id', $submittedGpIds);

        $submittedPensionData = $monthlyPension->whereIn('gp_id', $submittedGps->pluck('gp_id'));
        $normalPensioners = $submittedPensionData->sum('no_of_normal_pensioners');
        $epPensioners     = $submittedPensionData->sum('no_of_ep_pensioners');

        $summary[$key] = [
            'district'                  => $districtName,
            'type'                      => 'Block',
            'block_or_ulb'              => $blockOrUlb,
            'Data_provided_by_GP'       => $submittedGps->count(),
            'Data_provided_by_ward'     => 0,
            'Data_not_provided_by_GP'   => $pendingGps->count(),
            'Data_not_provided_by_ward' => 0,
            'no_of_normal_pensioners'   => $normalPensioners,
            'no_of_ep_pensioners'       => $epPensioners,
        ];
    }

    foreach ($allWards->groupBy(fn($ward) => $ward->district->district_name . '-' . $ward->municipality->municipality_name) as $key => $wardsGroup) {
        [$districtName, $blockOrUlb] = explode('-', $key);

        $submittedWards = $wardsGroup->whereIn('ward_code', $submittedWardIds);
        $pendingWards   = $wardsGroup->whereNotIn('ward_code', $submittedWardIds);

        $submittedPensionData = $monthlyPension->whereIn('ward_id', $submittedWards->pluck('ward_code'));
        $normalPensioners = $submittedPensionData->sum('no_of_normal_pensioners');
        $epPensioners     = $submittedPensionData->sum('no_of_ep_pensioners');

        if (!$summary->has($key)) {
            $summary[$key] = [
                'district'                  => $districtName,
                'type'                      => 'ULB',
                'block_or_ulb'              => $blockOrUlb,
                'Data_provided_by_GP'       => 0,
                'Data_provided_by_ward'     => $submittedWards->count(),
                'Data_not_provided_by_GP'   => 0,
                'Data_not_provided_by_ward' => $pendingWards->count(),
                'no_of_normal_pensioners'   => $normalPensioners,
                'no_of_ep_pensioners'       => $epPensioners,
            ];
        } else {
            $summary[$key]['type']                      = 'ULB';
            $summary[$key]['Data_provided_by_ward']     = $submittedWards->count();
            $summary[$key]['Data_not_provided_by_ward'] = $pendingWards->count();
            $summary[$key]['no_of_normal_pensioners']   = $normalPensioners;
            $summary[$key]['no_of_ep_pensioners']       = $epPensioners;
        }
    }

    $summaryReport = $summary->values();

    $grandTotals = [
        'district'                  => 'Grand Total',
        'type'                      => '',
        'block_or_ulb'              => '',
        'Data_provided_by_GP'       => $summaryReport->sum('Data_provided_by_GP'),
        'Data_provided_by_ward'     => $summaryReport->sum('Data_provided_by_ward'),
        'Data_not_provided_by_GP'   => $summaryReport->sum('Data_not_provided_by_GP'),
        'Data_not_provided_by_ward' => $summaryReport->sum('Data_not_provided_by_ward'),
        'no_of_normal_pensioners'   => $summaryReport->sum('no_of_normal_pensioners'),
        'no_of_ep_pensioners'       => $summaryReport->sum('no_of_ep_pensioners'),
    ];

    $summaryReport->push($grandTotals);

    return view('dashboard.pension.monthly_pension_disbursement_report_abstract', compact(
        'summaryReport',
        'startDate',
        'endDate',
        'forTheMonth',
        'monthlyPension'
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
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
    ->where('status', 1)
    ->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $startDate   = $dateConfig->start_date;
    $endDate     = $dateConfig->end_date;
    $forTheMonth = $dateConfig->for_the_month;
    $monthly_pension_disbursemenet = MonthlyPensionDisbursemenet::findOrFail($id);

    return view('dashboard.pension.monthly_pension_disbursement_edit', compact('monthly_pension_disbursemenet', 'startDate', 'endDate', 'forTheMonth',));
}

/**
* Update the specified resource in storage.
*/
public function update(Request $request, string $id)
{
    $validationRules = [
        'no_of_normal_pensioners' => 'required|integer|min:0',
        'no_of_ep_pensioners'     => 'required|integer|min:0',
    ];

    $validatedData = $request->validate($validationRules);

    $user = auth()->user();

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
    ->where('status', 1)
    ->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $startDate   = $dateConfig->start_date;
    $endDate     = $dateConfig->end_date;
    $forTheMonth = $dateConfig->for_the_month;
    DB::beginTransaction();
    try {
        $monthly_pension_disbursemenet = MonthlyPensionDisbursemenet::findOrFail($id);
        $monthly_pension_disbursemenet->no_of_normal_pensioners = $validatedData['no_of_normal_pensioners'];
        $monthly_pension_disbursemenet->no_of_ep_pensioners = $validatedData['no_of_ep_pensioners'];
        $monthly_pension_disbursemenet->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $monthly_pension_disbursemenet->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $monthly_pension_disbursemenet->updated_by = $user->user_table_id;
        $monthly_pension_disbursemenet->save();

        DB::commit();
        return redirect()->route('admin.monthlypensiondisbursement.monthly_pension_disbursement_report')->with('message', 'Pension Data Updated Successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🛑 Pension Disbursement form update failed.", [
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
public function delete(string $id)
{
    $user = auth()->user();

    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
    ->where('status', 1)
    ->first();

    if (!$dateConfig) {
        return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
    }

    $startDate   = $dateConfig->start_date;
    $endDate     = $dateConfig->end_date;
    $forTheMonth = $dateConfig->for_the_month;
    DB::beginTransaction();
    try {
        $monthly_pension_disbursemenet = MonthlyPensionDisbursemenet::findOrFail($id);
        $monthly_pension_disbursemenet->disbursement_start_date = NULL;
        $monthly_pension_disbursemenet->no_of_normal_pensioners = 0;
        $monthly_pension_disbursemenet->no_of_ep_pensioners = 0;
        $monthly_pension_disbursemenet->is_active = 'Inactive';
        $monthly_pension_disbursemenet->status = '0';
        $monthly_pension_disbursemenet->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $monthly_pension_disbursemenet->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $monthly_pension_disbursemenet->updated_by = $user->user_table_id;
        $monthly_pension_disbursemenet->save();

        DB::commit();
        return redirect()->route('admin.monthlypensiondisbursement.monthly_pension_disbursement_report')->with('message', 'Pension Data Deleted Successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🛑 Pension Disbursement form update failed.", [
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

public function pension_disbursement_daily_submission()
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
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

    $monthlyPensionDisbursemenetQuery = MonthlyPensionDisbursemenet::where('disbursement_started', 1)
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
        $monthlyPensionDisbursemenetQuery->where('block_id', $user->posted_block);

        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('block_id', $user->posted_block)
        ->where('is_active', 'active')->get();

    } elseif ($userRole == 5) {
        $monthlyPensionDisbursemenetQuery->where('municipality_id', $user->posted_municipality);

        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('municipal_area_code', $user->posted_municipality)
        ->where('is_active', '1')->get();

    } elseif (in_array($userRole, [8, 10])) {
        $blockIds        = Block::where('subdivision_id', $user->posted_subdiv)->where('is_active', 'active')->pluck('block_id');
        $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->where('is_active', 'active')->pluck('municipality_id');

        $monthlyPensionDisbursemenetQuery->where(function ($query) use ($blockIds, $municipalityIds) {
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
        $monthlyPensionDisbursemenetQuery->where('district_id', $user->posted_district);

        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('district_id', $user->posted_district)
        ->where('is_active', 'active')->get();

        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('district_code', $user->posted_district)
        ->where('is_active', '1')->get();
    }

    $monthlyPensionDisbursemenet = $monthlyPensionDisbursemenetQuery
    ->orderBy('district_id')
    ->get();

    return view('dashboard.pension.monthly_pension_disbursement_report', compact(
        'monthlyPensionDisbursemenet',
        'startDate',
        'endDate',
        'forTheMonth',
        'allGps',
        'allWards'
    ));
}

public function pension_disbursement_daily_not_submission()
{
    $dateConfig = PensionFundRequirementDates::where('for_which_page', 'monthly_pension_disbursemenets')
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

    $monthlyPensionDisbursemenetQuery = MonthlyPensionDisbursemenet::where('disbursement_started', 1)
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
        $monthlyPensionDisbursemenetQuery->where('block_id', $user->posted_block);
        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('block_id', $user->posted_block)
        ->where('is_active', 'active')->get();
    } elseif ($userRole == 5) {
        $monthlyPensionDisbursemenetQuery->where('municipality_id', $user->posted_municipality);
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('municipal_area_code', $user->posted_municipality)
        ->where('is_active', '1')->get();
    } elseif (in_array($userRole, [8, 10])) {
        $blockIds        = Block::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')->pluck('block_id');
        $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')->pluck('municipality_id');

        $monthlyPensionDisbursemenetQuery->where(function ($query) use ($blockIds, $municipalityIds) {
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
        $monthlyPensionDisbursemenetQuery->where('district_id', $user->posted_district);

        $allGps = Grampanchayat::with(['district', 'block'])
        ->where('district_id', $user->posted_district)
        ->where('is_active', 'active')->get();
        $allWards = WardMaster::with(['district', 'municipality'])
        ->where('district_code', $user->posted_district)
        ->where('is_active', '1')->get();
    }

    $monthlyPension = $monthlyPensionDisbursemenetQuery
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

    return view('dashboard.pension.monthly_pension_disbursement_report', compact(
        'monthlyPensionDisbursemenet',
        'startDate',
        'endDate',
        'forTheMonth'
    ));
}

}
