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

/*Controller Requirements*/
use App\Models\PensionFundsRequirement;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;

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
    public function create()
    {
        return view('dashboard.pension.pension_funds_requirements');
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
            'mbpy_bank_account_number' => 'required',
            'mbpy_bank_ifsc_code' => 'required',
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
            'mbpy_bank_ifsc_code.required' => 'Bank IFSC Code is required.',
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

        DB::beginTransaction();
        try {
            $pensionFundsRequirement = new PensionFundsRequirement();
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

    public function report()
    {
        $user = auth()->user();
        $userRole = $user->role_id;

        $pensionFundsRequirementQuery = PensionFundsRequirement::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality']);

        /*Role-based filtering logic*/
        if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
            /*SuperAdmin, Admin, HO, BO, Director, Secretary — see all records*/
        } elseif (in_array($userRole, [4, 6])) {
            /*BSSO, BDO — filter by posted_block*/
            $pensionFundsRequirementQuery->where('block_id', $user->posted_block);
        } elseif ($userRole == 5) {
            /*MEO — filter by posted_municipality*/
            $pensionFundsRequirementQuery->where('municipality_id', $user->posted_municipality);
        } elseif (in_array($userRole, [8, 10])) {
            /*SSSO, SubCollector — filter by posted_subdiv*/
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id')->toArray();
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id')->toArray();

            $pensionFundsRequirementQuery->where(function ($query) use ($blockIds, $municipalityIds) {
                $query->whereIn('block_id', $blockIds)
                ->orWhereIn('municipality_id', $municipalityIds);
            });
        } elseif (in_array($userRole, [9, 11])) {
            /*DSSO, Collector — filter by posted_district*/
            $pensionFundsRequirementQuery->where('district_id', $user->posted_district);
        }

        $pensionFundsRequirements = $pensionFundsRequirementQuery->get();

        return view('dashboard.pension.pension_funds_requirements_report', compact('pensionFundsRequirements'));
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
            \Log::error("Pension Funds Requirement data deleted by DSSO Failed: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
        }
    }
}
