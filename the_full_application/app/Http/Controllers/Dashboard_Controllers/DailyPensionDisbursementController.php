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
            $wardIds = $gp_ward_id->pluck('ward_id')->toArray();
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
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
