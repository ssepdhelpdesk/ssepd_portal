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
use Yajra\DataTables\Facades\DataTables;

class PensionFundsRequirementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    /*public function create()
    {
        $userId = auth()->id();
        $currentMonth = now()->format('Y-m');

        $existingEntry = PensionFundsRequirement::where('created_by', $userId)
        ->whereRaw("DATE_FORMAT(created_date, '%Y-%m') = ?", [$currentMonth])
        ->first();

        if ($existingEntry) {
            return redirect()->back()->with('warning', 'You have already submitted this form for the current month on ' . \Carbon\Carbon::parse($existingEntry->created_date)->format('d M, Y') . '. If any modification is required, please contact your concerned district.');
        }

        return view('dashboard.pension.pension_funds_requirements');
    }*/

    /*public function create()
    {
        $userId = auth()->id();

        $startDate = '2025-08-22';
        $endDate = '2025-09-03';

        $existingEntry = PensionFundsRequirement::where('created_by', $userId)
        ->whereBetween('created_date', [$startDate, $endDate])
        ->first();

        if ($existingEntry) {
            return redirect()->back()->with('warning', 'You have already submitted this form for the current month on ' . \Carbon\Carbon::parse($existingEntry->created_date)->format('d M, Y') . '. If any modification is required, please contact your concerned district.');
        }

        return view('dashboard.pension.pension_funds_requirements', compact('startDate', 'endDate'));
    }*/

    public function create()
    {
        $userId = auth()->id();

        $dateConfig = PensionFundRequirementDates::where('status', 1)->first();

        if (!$dateConfig) {
            return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
        }

        $startDate = $dateConfig->start_date;
        $endDate   = $dateConfig->end_date;
        $forTheMonth = $dateConfig->for_the_month;

        $existingEntry = PensionFundsRequirement::where('created_by', $userId)
        ->whereBetween('created_date', [$startDate, $endDate])
        ->first();

        if ($existingEntry) {
            return redirect()->back()->with(
                'warning',
                'You have already submitted this form for ' . $forTheMonth . ' on ' .
                \Carbon\Carbon::parse($existingEntry->created_date)->format('d M, Y') .
                '. If any modification is required, please contact your concerned district.'
            );
        }

        return view('dashboard.pension.pension_funds_requirements', compact('startDate', 'endDate', 'forTheMonth'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'mbpy_oap_below_80_years' => 'required|integer|min:0',
            'mbpy_oap_above_80_years' => 'required|integer|min:0',
            'mbpy_wp' => 'required|integer|min:0',
            'mbpy_dp' => 'required|integer|min:0',
            'mbpy_sdp_below_80_percent' => 'required|integer|min:0',
            'mbpy_sdp_above_80_percent' => 'required|integer|min:0',
            'mbpy_sdoap' => 'required|integer|min:0',
            'mbpy_clp' => 'required|integer|min:0',
            'mbpy_wp_aids' => 'required|integer|min:0',
            'mbpy_dp_aids' => 'required|integer|min:0',
            'mbpy_unmarried_women' => 'required|integer|min:0',
            'mbpy_orphan_due_to_covide' => 'required|integer|min:0',
            'mbpy_widow_due_to_covid' => 'required|integer|min:0',
            'mbpy_divorce_or_destitute' => 'required|integer|min:0',
            'mbpy_transgender' => 'required|integer|min:0',
            'mbpy_bank_account_number' => 'required|string|regex:/^[0-9]{9,18}$/',
            'mbpy_bank_ifsc_code' => 'required|string|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i',
        ];

        $customMessages = [
            'required' => 'This field is required.',
            'integer' => 'This field must be a valid number.',
            'min' => 'This field must be at least 0.',

            'mbpy_oap_below_80_years.required' => 'MBPOAP (Below 80 Years) is required.',
            'mbpy_oap_above_80_years.required' => 'MBPOAP (Above 80 Years) is required.',
            'mbpy_wp.required' => 'MBPWP (Widow Pension) is required.',
            'mbpy_dp.required' => 'MBPDP (Disabled Pension) is required.',
            'mbpy_sdp_below_80_percent.required' => 'MBPSDP (Below 80%) is required.',
            'mbpy_sdp_above_80_percent.required' => 'MBPSDP (Above 80%) is required.',
            'mbpy_sdoap.required' => 'MBPSDOAP (Disabled + Old Age) is required.',
            'mbpy_clp.required' => 'MBPCLP (Leprosy) is required.',
            'mbpy_wp_aids.required' => 'MBPWP AIDS Affected is required.',
            'mbpy_dp_aids.required' => 'MBPDP AIDS Affected is required.',
            'mbpy_unmarried_women.required' => 'MBP for Unmarried Women is required.',
            'mbpy_orphan_due_to_covide.required' => 'Orphan due to COVID is required.',
            'mbpy_widow_due_to_covid.required' => 'Widow due to COVID is required.',
            'mbpy_divorce_or_destitute.required' => 'Divorced/Destitute Women is required.',
            'mbpy_transgender.required' => 'Transgender pension count is required.',
            'mbpy_bank_account_number.required' => 'Bank Account is Required.',
            'mbpy_bank_account_number.regex' => 'Enter a valid Bank Account Number (9 to 18 digits).',
            'mbpy_bank_ifsc_code.required' => 'Bank IFSC Code is required.',
            'mbpy_bank_ifsc_code.regex' => 'Enter a valid IFSC code (e.g., SBIN0001234).',
        ];

        $validatedData = $request->validate($validationRules, $customMessages);

        $user = auth()->user();
        $userRole = $user->role_id;
        $state_id = 228;

        if ($userRole == 4 && $user->posted_block) {
            $address_type = 1;
            $block_id = $user->posted_block;
            $municipality_id = null;

            $district_id = Block::where('block_id', $block_id)->value('district_id');
            if (!$district_id) {
                return redirect()->back()->with('info', 'Invalid Block mapping. Contact admin.');
            }
        } elseif ($userRole == 5 && $user->posted_municipality) {
            $address_type = 2;
            $municipality_id = $user->posted_municipality;
            $block_id = null;

            $district_id = Municipality::where('municipality_id', $municipality_id)->value('district_id');
            if (!$district_id) {
                return redirect()->back()->with('info', 'Invalid Municipality mapping. Contact admin.');
            }
        } else {
            return redirect()->back()->with('warning', 'You are not authorised to fill up this form.');
        }

        $dateConfig = PensionFundRequirementDates::where('status', 1)->first();

        if (!$dateConfig) {
            return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
        }

        DB::beginTransaction();
        try {
            $pensionFundsRequirement = new PensionFundsRequirement();
            $pensionFundsRequirement->for_the_month = $dateConfig->for_the_month;
            $pensionFundsRequirement->mbpy_oap_below_80_years = $validatedData['mbpy_oap_below_80_years'];
            $pensionFundsRequirement->mbpy_oap_above_80_years = $validatedData['mbpy_oap_above_80_years'];
            $pensionFundsRequirement->mbpy_wp = $validatedData['mbpy_wp'];
            $pensionFundsRequirement->mbpy_dp = $validatedData['mbpy_dp'];
            $pensionFundsRequirement->mbpy_sdp_below_80_percent = $validatedData['mbpy_sdp_below_80_percent'];
            $pensionFundsRequirement->mbpy_sdp_above_80_percent = $validatedData['mbpy_sdp_above_80_percent'];
            $pensionFundsRequirement->mbpy_sdoap = $validatedData['mbpy_sdoap'];
            $pensionFundsRequirement->mbpy_clp = $validatedData['mbpy_clp'];
            $pensionFundsRequirement->mbpy_wp_aids = $validatedData['mbpy_wp_aids'];
            $pensionFundsRequirement->mbpy_dp_aids = $validatedData['mbpy_dp_aids'];
            $pensionFundsRequirement->mbpy_unmarried_women = $validatedData['mbpy_unmarried_women'];
            $pensionFundsRequirement->mbpy_orphan_due_to_covide = $validatedData['mbpy_orphan_due_to_covide'];
            $pensionFundsRequirement->mbpy_widow_due_to_covid = $validatedData['mbpy_widow_due_to_covid'];
            $pensionFundsRequirement->mbpy_divorce_or_destitute = $validatedData['mbpy_divorce_or_destitute'];
            $pensionFundsRequirement->mbpy_transgender = $validatedData['mbpy_transgender'];
            $pensionFundsRequirement->mbpy_bank_account_number = $validatedData['mbpy_bank_account_number'];
            $pensionFundsRequirement->mbpy_bank_ifsc_code = $validatedData['mbpy_bank_ifsc_code'];
            $pensionFundsRequirement->address_type = $address_type;
            $pensionFundsRequirement->state_id = $state_id;
            $pensionFundsRequirement->district_id = $district_id;
            $pensionFundsRequirement->municipality_id = $municipality_id;
            $pensionFundsRequirement->block_id = $block_id;
            $pensionFundsRequirement->gp_id = NULL;
            $pensionFundsRequirement->village_id = NULL;
            $pensionFundsRequirement->pin = NULL;
            $pensionFundsRequirement->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $pensionFundsRequirement->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $pensionFundsRequirement->created_by = Auth::id() ?? null;
            $pensionFundsRequirement->status = 1;
            $pensionFundsRequirement->save();

            $applicationstagehistory = new ApplicationStageHistory();
            /*department_scheme_id Special School = 2*/
            $applicationstagehistory->department_scheme_id = 3;
            $applicationstagehistory->model_name = 'PensionFundsRequirement';
            $applicationstagehistory->model_table_id = $pensionFundsRequirement->id;
            $applicationstagehistory->initial_model_table_id = $pensionFundsRequirement->id;
            $applicationstagehistory->stage_id = 38;
            $applicationstagehistory->stage_name = 'Pension Funds Requirement form Submitted';
            $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $applicationstagehistory->created_by = Auth::id();
            $applicationstagehistory->created_by_remarks = 'Pension Funds Requirement form Submitted Successfully';
            $ipAddress = request()->ip();
            $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
            $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
            $applicationstagehistory->save();

            DB::commit();
            return redirect()->route('admin.dashboard')->with('success', 'Submitted Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🛑 Pension Funds Requirements form submission failed", [
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

        /*public function report()
    {
        $user = auth()->user();
        $userRole = $user->role_id;

        $pensionFundsRequirementQuery = PensionFundsRequirement::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality']);
        
        if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
            
        } elseif (in_array($userRole, [4, 6])) {
            
            $pensionFundsRequirementQuery->where('block_id', $user->posted_block);
        } elseif ($userRole == 5) {
            
            $pensionFundsRequirementQuery->where('municipality_id', $user->posted_municipality);
        } elseif (in_array($userRole, [8, 10])) {
            
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id')->toArray();
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id')->toArray();

            $pensionFundsRequirementQuery->where(function ($query) use ($blockIds, $municipalityIds) {
                $query->whereIn('block_id', $blockIds)
                ->orWhereIn('municipality_id', $municipalityIds);
            });
        } elseif (in_array($userRole, [9, 11])) {
            
            $pensionFundsRequirementQuery->where('district_id', $user->posted_district);
        }
        
        $pensionFundsRequirements = $pensionFundsRequirementQuery->get()->sortBy(function ($item) {
            return $item->district->district_name ?? '';
        })->values();

        $submittedBlockIds = PensionFundsRequirement::whereNotNull('block_id')->pluck('block_id')->toArray();
        $submittedMunicipalityIds = PensionFundsRequirement::whereNotNull('municipality_id')->pluck('municipality_id')->toArray();
        
        if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
            
            $pendingBlocks = Block::whereNotIn('block_id', $submittedBlockIds)->where('is_active', 'active')->get();
            $municipalities = Municipality::whereNotIn('municipality_id', $submittedMunicipalityIds)->where('is_active', 'active')->get();
        } elseif (in_array($userRole, [4, 6])) {
            
            $pensionFundsRequirementQuery->where('block_id', $user->posted_block);
            if()
        } elseif ($userRole == 5) {
            
            $pensionFundsRequirementQuery->where('municipality_id', $user->posted_municipality);
        } elseif (in_array($userRole, [8, 10])) {
            
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id')->toArray();
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id')->toArray();

            $pensionFundsRequirementQuery->where(function ($query) use ($blockIds, $municipalityIds) {
                $query->whereIn('block_id', $blockIds)
                ->orWhereIn('municipality_id', $municipalityIds);
            });
        } elseif (in_array($userRole, [9, 11])) {
            
            $pensionFundsRequirementQuery->where('district_id', $user->posted_district);
        }
        return view('dashboard.pension.pension_funds_requirements_report', compact('pensionFundsRequirements'));
    }*/


    /*Created on  06-08-2025 to fetch all Blocks & ULBs*/
    public function report()
    {
        $user = auth()->user();
        $userRole = $user->role_id;

        $dateConfig = PensionFundRequirementDates::where('status', 1)->first();

        if (!$dateConfig) {
            return redirect()->back()->with('error', 'Submission dates are not configured. Please contact admin.');
        }

        $startDate = $dateConfig->start_date;
        $endDate   = $dateConfig->end_date;
        $forTheMonth1 = $dateConfig->for_the_month;

        $pensionFundsRequirementQuery = PensionFundsRequirement::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality'])->whereBetween('created_date', [$startDate, $endDate]);
        $allBlocks = collect();
        $allMunicipalities = collect();

        if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
            $allBlocks = Block::where('is_active', 'active')->get();
            $allMunicipalities = Municipality::where('is_active', 'active')->get();
        } elseif (in_array($userRole, [4, 6])) {
            $pensionFundsRequirementQuery->where('block_id', $user->posted_block);
            $allBlocks = Block::where('block_id', $user->posted_block)->where('is_active', 'active')->get();
        } elseif ($userRole == 5) {
            $pensionFundsRequirementQuery->where('municipality_id', $user->posted_municipality);
            $allMunicipalities = Municipality::where('municipality_id', $user->posted_municipality)->where('is_active', 'active')->get();
        } elseif (in_array($userRole, [8, 10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');

            $pensionFundsRequirementQuery->where(function ($query) use ($blockIds, $municipalityIds) {
                $query->whereIn('block_id', $blockIds)
                ->orWhereIn('municipality_id', $municipalityIds);
            });

            $allBlocks = Block::whereIn('block_id', $blockIds)->where('is_active', 'active')->get();
            $allMunicipalities = Municipality::whereIn('municipality_id', $municipalityIds)->where('is_active', 'active')->get();
        } elseif (in_array($userRole, [9, 11])) {
            $pensionFundsRequirementQuery->where('district_id', $user->posted_district);

            $allBlocks = Block::where('district_id', $user->posted_district)->where('is_active', 'active')->get();
            $allMunicipalities = Municipality::where('district_id', $user->posted_district)->where('is_active', 'active')->get();
        }

        $filledRequirements = $pensionFundsRequirementQuery->get();

        $submittedBlockIds = $filledRequirements->pluck('block_id')->filter()->unique();
        $submittedMunicipalityIds = $filledRequirements->pluck('municipality_id')->filter()->unique();

        $pendingBlocks = $allBlocks->whereNotIn('block_id', $submittedBlockIds)->map(function ($block) {
            return (object)[
                'id' => 'block-'.$block->block_id,
                'district' => $block->district,
                'block' => $block,
                'municipality' => null,
                'address_type' => 1,
            ];
        });

        $pendingUlbs = $allMunicipalities->whereNotIn('municipality_id', $submittedMunicipalityIds)->map(function ($ulb) {
            return (object)[
                'id' => 'ulb-'.$ulb->municipality_id,
                'district' => $ulb->district,
                'block' => null,
                'municipality' => $ulb,
                'address_type' => 2,
            ];
        });

        $combined = $filledRequirements->concat($pendingBlocks)->concat($pendingUlbs);
        $pensionFundsRequirements = $combined->sortBy(function ($item) {
            return $item->district->district_name ?? '';
        })->values();

        return view('dashboard.pension.pension_funds_requirements_report', compact('pensionFundsRequirements', 'startDate', 'endDate', 'forTheMonth1'));
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
        $pensionFundsRequirement = PensionFundsRequirement::whereId($id)->firstOrFail();
        return view('dashboard.pension.edit', compact('pensionFundsRequirement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validationRules = [
            'mbpy_oap_below_80_years' => 'required|integer|min:0',
            'mbpy_oap_above_80_years' => 'required|integer|min:0',
            'mbpy_wp' => 'required|integer|min:0',
            'mbpy_dp' => 'required|integer|min:0',
            'mbpy_sdp_below_80_percent' => 'required|integer|min:0',
            'mbpy_sdp_above_80_percent' => 'required|integer|min:0',
            'mbpy_sdoap' => 'required|integer|min:0',
            'mbpy_clp' => 'required|integer|min:0',
            'mbpy_wp_aids' => 'required|integer|min:0',
            'mbpy_dp_aids' => 'required|integer|min:0',
            'mbpy_unmarried_women' => 'required|integer|min:0',
            'mbpy_orphan_due_to_covide' => 'required|integer|min:0',
            'mbpy_widow_due_to_covid' => 'required|integer|min:0',
            'mbpy_divorce_or_destitute' => 'required|integer|min:0',
            'mbpy_transgender' => 'required|integer|min:0',
            'mbpy_bank_account_number' => 'required|string|regex:/^[0-9]{9,18}$/',
            'mbpy_bank_ifsc_code' => 'required|string|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i',
        ];

        $customMessages = [
            'required' => 'This field is required.',
            'integer' => 'This field must be a valid number.',
            'min' => 'This field must be at least 0.',

            'mbpy_oap_below_80_years.required' => 'MBPOAP (Below 80 Years) is required.',
            'mbpy_oap_above_80_years.required' => 'MBPOAP (Above 80 Years) is required.',
            'mbpy_wp.required' => 'MBPWP (Widow Pension) is required.',
            'mbpy_dp.required' => 'MBPDP (Disabled Pension) is required.',
            'mbpy_sdp_below_80_percent.required' => 'MBPSDP (Below 80%) is required.',
            'mbpy_sdp_above_80_percent.required' => 'MBPSDP (Above 80%) is required.',
            'mbpy_sdoap.required' => 'MBPSDOAP (Disabled + Old Age) is required.',
            'mbpy_clp.required' => 'MBPCLP (Leprosy) is required.',
            'mbpy_wp_aids.required' => 'MBPWP AIDS Affected is required.',
            'mbpy_dp_aids.required' => 'MBPDP AIDS Affected is required.',
            'mbpy_unmarried_women.required' => 'MBP for Unmarried Women is required.',
            'mbpy_orphan_due_to_covide.required' => 'Orphan due to COVID is required.',
            'mbpy_widow_due_to_covid.required' => 'Widow due to COVID is required.',
            'mbpy_divorce_or_destitute.required' => 'Divorced/Destitute Women is required.',
            'mbpy_transgender.required' => 'Transgender pension count is required.',
            'mbpy_bank_account_number.required' => 'Bank Account is Required.',
            'mbpy_bank_account_number.regex' => 'Enter a valid Bank Account Number (9 to 18 digits).',
            'mbpy_bank_ifsc_code.required' => 'Bank IFSC Code is required.',
            'mbpy_bank_ifsc_code.regex' => 'Enter a valid IFSC code (e.g., SBIN0001234).',
        ];

        $validatedData = $request->validate($validationRules, $customMessages);

        $user = auth()->user();

        DB::beginTransaction();
        try {
            $pensionFundsRequirement = PensionFundsRequirement::find($id);

            if (!$pensionFundsRequirement) {
                return redirect()->back()->with('error', 'Record not found.');
            }
            $pensionFundsRequirement->mbpy_oap_below_80_years = $validatedData['mbpy_oap_below_80_years'];
            $pensionFundsRequirement->mbpy_oap_above_80_years = $validatedData['mbpy_oap_above_80_years'];
            $pensionFundsRequirement->mbpy_wp = $validatedData['mbpy_wp'];
            $pensionFundsRequirement->mbpy_dp = $validatedData['mbpy_dp'];
            $pensionFundsRequirement->mbpy_sdp_below_80_percent = $validatedData['mbpy_sdp_below_80_percent'];
            $pensionFundsRequirement->mbpy_sdp_above_80_percent = $validatedData['mbpy_sdp_above_80_percent'];
            $pensionFundsRequirement->mbpy_sdoap = $validatedData['mbpy_sdoap'];
            $pensionFundsRequirement->mbpy_clp = $validatedData['mbpy_clp'];
            $pensionFundsRequirement->mbpy_wp_aids = $validatedData['mbpy_wp_aids'];
            $pensionFundsRequirement->mbpy_dp_aids = $validatedData['mbpy_dp_aids'];
            $pensionFundsRequirement->mbpy_unmarried_women = $validatedData['mbpy_unmarried_women'];
            $pensionFundsRequirement->mbpy_orphan_due_to_covide = $validatedData['mbpy_orphan_due_to_covide'];
            $pensionFundsRequirement->mbpy_widow_due_to_covid = $validatedData['mbpy_widow_due_to_covid'];
            $pensionFundsRequirement->mbpy_divorce_or_destitute = $validatedData['mbpy_divorce_or_destitute'];
            $pensionFundsRequirement->mbpy_transgender = $validatedData['mbpy_transgender'];
            $pensionFundsRequirement->mbpy_bank_account_number = $validatedData['mbpy_bank_account_number'];
            $pensionFundsRequirement->mbpy_bank_ifsc_code = $validatedData['mbpy_bank_ifsc_code'];
            $pensionFundsRequirement->address_type = $pensionFundsRequirement->address_type;
            $pensionFundsRequirement->state_id = $pensionFundsRequirement->state_id;
            $pensionFundsRequirement->district_id = $pensionFundsRequirement->district_id;
            $pensionFundsRequirement->municipality_id = $pensionFundsRequirement->municipality_id;
            $pensionFundsRequirement->block_id = $pensionFundsRequirement->block_id;
            $pensionFundsRequirement->gp_id = $pensionFundsRequirement->gp_id;
            $pensionFundsRequirement->village_id = $pensionFundsRequirement->village_id;
            $pensionFundsRequirement->pin = $pensionFundsRequirement->pin;
            $pensionFundsRequirement->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $pensionFundsRequirement->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $pensionFundsRequirement->updated_by = Auth::id() ?? null;
            $pensionFundsRequirement->status = 1;
            $pensionFundsRequirement->save();

            $applicationstagehistory = new ApplicationStageHistory();
            /*department_scheme_id Special School = 2*/
            $applicationstagehistory->department_scheme_id = 3;
            $applicationstagehistory->model_name = 'PensionFundsRequirement';
            $applicationstagehistory->model_table_id = $pensionFundsRequirement->id;
            $applicationstagehistory->initial_model_table_id = $pensionFundsRequirement->id;
            $applicationstagehistory->stage_id = 40;
            $applicationstagehistory->stage_name = 'Pension Funds Requirement form Updated by DSSO';
            $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $applicationstagehistory->created_by = Auth::id();
            $applicationstagehistory->created_by_remarks = 'Pension Funds Requirement form Updated by DSSO Successfully';
            $ipAddress = request()->ip();
            $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
            $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
            $applicationstagehistory->save();

            DB::commit();
            return redirect()->route('admin.pension.report')->with('info', 'Updated Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🛑 Pension Funds Requirements form update failed", [
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
        DB::beginTransaction();
        try {
            $pensionFundsRequirement = PensionFundsRequirement::find($id);
            if (!$pensionFundsRequirement) {
                return redirect()->back()->with('error', 'Record not found.');
            }
            $pensionFundsRequirement->delete();

            $applicationstagehistory = new ApplicationStageHistory();
            /*department_scheme_id Special School = 2*/
            $applicationstagehistory->department_scheme_id = 3;
            $applicationstagehistory->model_name = 'PensionFundsRequirement';
            $applicationstagehistory->model_table_id = $pensionFundsRequirement->id;
            $applicationstagehistory->initial_model_table_id = $pensionFundsRequirement->id;
            $applicationstagehistory->stage_id = 42;
            $applicationstagehistory->stage_name = 'Pension Funds Requirement data deleted by DSSO';
            $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $applicationstagehistory->created_by = Auth::id();
            $applicationstagehistory->created_by_remarks = 'Pension Funds Requirement data deleted by DSSO Successfully';
            $ipAddress = request()->ip();
            $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
            $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
            $applicationstagehistory->save();

            DB::commit();
            return redirect()->route('admin.pension.report')->with('warning', 'Deleted Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("💸 Pension Funds Requirement deletion by DSSO failed", [
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

    public function pension_authority_index()
    {
        $pension = PensionDisbursementAuthority::get();
        $user = auth()->user();
        $grampanchayats = collect();
        $wards = collect();

        if ($user->role_id == 4) {
            $grampanchayats = Grampanchayat::where('block_id', $user->posted_block)->get();
        } elseif ($user->role_id == 5) {
            $wards = WardMaster::where('municipal_area_code', $user->posted_municipality)->get();
        }

        return view('dashboard.pension.pension_authority', compact('user', 'grampanchayats', 'wards'));
    }

    public function pension_authority_store(Request $request)
    {
        $user = auth()->user();

        $validationRules = [
            'authority_name'       => 'required|string|max:255',
            'authority_mobile_no'  => 'required|digits:10',
            'authority_designation'=> 'required|in:1,2,3,4,5,6,7,8,9 ',
        ];

        if ($user->role_id == 4) {
            $validationRules['grampanchayat'] = 'required|array|min:1';
            $validationRules['grampanchayat.*'] = 'exists:grampanchayats,gp_id';
        } elseif ($user->role_id == 5) {
            $validationRules['ward'] = 'required|array|min:1';
            $validationRules['ward.*'] = 'exists:ward_masters,ward_code';
        }

        $validatedData = $request->validate($validationRules);
        DB::beginTransaction();
        try {

            if ($user->role_id == 4 && isset($validatedData['grampanchayat'])) {
                foreach ($validatedData['grampanchayat'] as $gpId) {
                    $gp = Grampanchayat::where('gp_id', $gpId)->first();

                    if ($gp) {
                        $pension_authority = new PensionDisbursementAuthority();
                        $pension_authority->authority_name        = $validatedData['authority_name'];
                        $pension_authority->authority_mobile_no   = $validatedData['authority_mobile_no'];
                        $pension_authority->authority_email_id    = $validatedData['authority_mobile_no']."@gmail.com";
                        $pension_authority->authority_designation = $validatedData['authority_designation'];
                        $pension_authority->disbursement_month    = 'September-2025';
                        $pension_authority->staff_address_type    = 1;
                        $pension_authority->state_id              = 228;
                        $pension_authority->district_id           = $gp->district_id;
                        $pension_authority->block_id              = $gp->block_id;
                        $pension_authority->gp_id                 = $gp->gp_id;
                        $pension_authority->is_active             = 'active';
                        $pension_authority->created_date          = now()->setTimezone('Asia/Kolkata')->toDateString();
                        $pension_authority->created_time          = now()->setTimezone('Asia/Kolkata')->toTimeString();
                        $pension_authority->created_by            = Auth::id();
                        $pension_authority->user_table_id         = $user->user_table_id;
                        $pension_authority->status                = 1;
                        $pension_authority->save();
                    }
                }
            }

            if ($user->role_id == 5 && isset($validatedData['ward'])) {
                foreach ($validatedData['ward'] as $wardCode) {
                    $ward = WardMaster::where('ward_code', $wardCode)->first();

                    if ($ward) {
                        $pension_authority = new PensionDisbursementAuthority();
                        $pension_authority->for_the_month         = 'Sepetember-2025'; 
                        $pension_authority->authority_name        = $validatedData['authority_name'];
                        $pension_authority->authority_mobile_no   = $validatedData['authority_mobile_no'];
                        $pension_authority->authority_email_id    = $validatedData['authority_mobile_no']."@gmail.com";
                        $pension_authority->authority_designation = $validatedData['authority_designation'];
                        $pension_authority->disbursement_month    = 'September-2025';
                        $pension_authority->staff_address_type    = 2;
                        $pension_authority->state_id              = 228;
                        $pension_authority->district_id           = $ward->district_code;
                        $pension_authority->municipality_id       = $ward->municipal_area_code;
                        $pension_authority->ward_id               = $ward->ward_code;
                        $pension_authority->is_active             = 'active';
                        $pension_authority->created_date          = now()->setTimezone('Asia/Kolkata')->toDateString();
                        $pension_authority->created_time          = now()->setTimezone('Asia/Kolkata')->toTimeString();
                        $pension_authority->created_by            = Auth::id();
                        $pension_authority->user_table_id         = $user->user_table_id;
                        $pension_authority->status                = 1;
                        $pension_authority->save();
                    }
                }
            }
            DB::commit();

            return redirect()->route('admin.pension.pension_authority_index')->with('message', "Pension Disbursement Authority Details Provided Successfully.");
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

    public function pension_authority_report()
    {
        ini_set('memory_limit', '512M');
        $user = auth()->user();
        $userRole = $user->role_id;

        $pensionDisbursementAuthorityQuery = PensionDisbursementAuthority::with([
            'state', 'district', 'block', 'grampanchayat', 'municipality', 'ward'
        ]);

        $allGps = collect();
        $allWards = collect();

        if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
            $allGps = Grampanchayat::with(['district', 'block'])
            ->where('is_active', 'active')->get();
            $allWards = WardMaster::with(['district', 'municipality'])
            ->where('is_active', '1')->get();
        } elseif (in_array($userRole, [4, 6])) {
            $pensionDisbursementAuthorityQuery->where('block_id', $user->posted_block);
            $allGps = Grampanchayat::with(['district', 'block'])
            ->where('block_id', $user->posted_block)->where('is_active', 'active')->get();
        } elseif ($userRole == 5) {
            $pensionDisbursementAuthorityQuery->where('municipality_id', $user->posted_municipality);
            $allWards = WardMaster::with(['district', 'municipality'])
            ->where('municipal_area_code', $user->posted_municipality)->where('is_active', '1')->get();
        } elseif (in_array($userRole, [8, 10])) {
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->where('is_active', 'active')->pluck('block_id');
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->where('is_active', 'active')->pluck('municipality_id');

            $pensionDisbursementAuthorityQuery->where(function ($query) use ($blockIds, $municipalityIds) {
                $query->whereIn('block_id', $blockIds)
                ->orWhereIn('municipality_id', $municipalityIds);
            });

            $allGps = Grampanchayat::with(['district', 'block'])
            ->whereIn('block_id', $blockIds)->where('is_active', 'active')->get();
            $allWards = WardMaster::with(['district', 'municipality'])
            ->whereIn('municipal_area_code', $municipalityIds)->where('is_active', '1')->get();
        } elseif (in_array($userRole, [9, 11])) {
            $pensionDisbursementAuthorityQuery->where('district_id', $user->posted_district);

            $allGps = Grampanchayat::with(['district', 'block'])
            ->where('district_id', $user->posted_district)->where('is_active', 'active')->get();
            $allWards = WardMaster::with(['district', 'municipality'])
            ->where('district_code', $user->posted_district)->where('is_active', '1')->get();
        }

        $disbursementAuthority = $pensionDisbursementAuthorityQuery->get();

        $submittedGpIds = $disbursementAuthority->pluck('gp_id')->filter()->unique();
        $submittedWardIds = $disbursementAuthority->pluck('ward_id')->filter()->unique();

        $pendingGps = $allGps->whereNotIn('gp_id', $submittedGpIds)->map(function ($gp) {
            return (object)[
                'id' => null,
                'district' => $gp->district,
                'block' => $gp->block,
                'grampanchayat' => $gp,
                'municipality' => null,
                'ward' => null,
                'staff_address_type' => 1,
                'authority_name' => null,
                'authority_mobile_no' => null,
                'authority_designation' => null,
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
                'authority_name' => null,
                'authority_mobile_no' => null,
                'authority_designation' => null,
            ];
        });

        $combined = $disbursementAuthority
        ->concat($pendingGps)
        ->concat($pendingWards);

        $pensiondisbursementAuthority = $combined->sortBy(function ($item) {
            return $item->district->district_name ?? '';
        })->values();

        return view('dashboard.pension.pension_authority_report', compact('pensiondisbursementAuthority'));
    }

    public function pension_authority_delete(string $id)
    {
        $pensionDisbursementAuthority = PensionDisbursementAuthority::find($id);

        if (!$pensionDisbursementAuthority) {
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.']);
        }

        $pensionDisbursementAuthority->delete();

        return redirect()->back()->with('info', 'Deleted Successfully');
    }
}
