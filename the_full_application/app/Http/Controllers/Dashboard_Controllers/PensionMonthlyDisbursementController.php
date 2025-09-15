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
    public function index()
    {
        $user = auth()->user();
        $userRole = $user->role_id;

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

        foreach ($request->gp_ward_id as $index => $gpWardId) {
            $startDate = $request->disbursement_start_date[$index] ?? null;
            $no_of_normal_pensioners = $request->no_of_normal_pensioners[$index] ?? 0;
            $no_of_ep_pensioners = $request->no_of_ep_pensioners[$index] ?? 0;

            \DB::table('monthly_pension_disbursemenets')->insert([
                'for_the_month'            => $forTheMonth,
                'disbursement_start_date'  => $startDate,
                'no_of_normal_pensioners'      => $no_of_normal_pensioners,
                'no_of_ep_pensioners'      => $no_of_ep_pensioners,                
                'disbursement_started'     => $startDate ? 1 : 0,
                'staff_address_type'       => $staff_address_type,
                'state_id'                 => 228,
                'district_id'              => $district_id,
                'municipality_id'          => $municipality_id,
                'block_id'                 => $block_id,
                'gp_id'                    => $user->role_name == 'BSSO' ? $gpWardId : null,
                'ward_id'                  => $user->role_name == 'MEO' ? $gpWardId : null,
                'is_active'                => 'active',
                'created_date'             => now()->setTimezone('Asia/Kolkata')->toDateString(),
                'created_time'             => now()->setTimezone('Asia/Kolkata')->toTimeString(),
                'created_by'               => $user->id,
                'status'                   => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Pension disbursement dates saved successfully.');
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
