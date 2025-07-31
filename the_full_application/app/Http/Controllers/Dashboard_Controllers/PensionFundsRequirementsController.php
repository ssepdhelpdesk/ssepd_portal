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

        if (empty($user->posted_municipality)) {
            $address_type = '1';
            $state_id = '228';
            $block_id = $user->posted_block;
            $district_id = Block::where('block_id', $block_id)->value('district_id');
            $municipality_id = NULL;
        } elseif (empty($user->posted_block)) {
            $address_type = '2';
            $state_id = '228';
            $municipality_id = $user->posted_municipality;
            $district_id = Municipality::where('municipality_id', $municipality_id)->value('district_id');
            $block_id = NULL;
        } else {
            return redirect()->back()->with('info', 'You are not authorised to fill up this form.');
        }

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
        return redirect()->route('admin.dashboard')->with('success', 'Submitted Successfully.');
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
