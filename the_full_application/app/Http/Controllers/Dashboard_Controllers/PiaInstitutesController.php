<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AadhaarVerifier;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DB;

use App\Models\{
    Gender,
    BankMaster,
    District,
    Block,
    Subdivision,
    Municipality,
    Grampanchayat,
    WardMaster,
    Village,
    VisitorCount,
    User,
    ApplicationStage,
    PiaInstituteMaster,
    ApplicationStageHistory,
};

class PiaInstitutesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pia_institute_basic_details()
    {
        $userId = Auth::user();
        $piainstitute = PiaInstituteMaster::where('user_table_id', $userId->user_table_id)->firstOrFail();
        if($piainstitute->basic_details_completed != 1) {
            return view('dashboard.pia_institutes.pia_institute_basic_details', compact('piainstitute'));
        }
        return view('dashboard.pia_institutes.index', compact('piainstitute'));
    }

    public function pia_institute_basic_details_update(Request $request, string $id)
    {
        $validationRules = [
            'institute_name' => 'required|string|max:255',
            'institute_type_id' => 'required|in:1,2,3,4,5,6',
            'institute_email_id' => 'required|email',
            'date_of_registration' => 'required|date|before_or_equal:today',
            'registration_no' => 'required|string|max:100',
            'registration_certificate' => 'required|file|mimes:pdf|max:2048',
            'which_govt' => 'required|in:1,2',
            'grantee_code' => 'required|string|max:100',
            'nodal_officer_name' => 'required|string|max:255',
            'nodal_officer_contact_number' => 'required|digits:10',
            'pia_name' => 'required|string|max:255',
            'pia_nodal_officer_name' => 'required|string|max:255',
            'pia_nodal_officer_contact_no' => 'required|digits:10',
            'ngo_address_type' => 'required|in:1,2',
        ];

        $messages = [
            'registration_certificate.required' => 'Please upload Registration Certificate.',
            'registration_certificate.mimes' => 'Only PDF files are allowed.',
            'registration_certificate.max' => 'File size must be less than 2MB.',
            'nodal_officer_contact_number.digits' => 'Nodal Officer Mobile must be 10 digits.',
            'pia_nodal_officer_contact_no.digits' => 'PIA/NGO Mobile must be 10 digits.',
            'ngo_address_type.required' => 'Please select Address Type.',
            'date_of_registration.before_or_equal' => 'Date of Registration cannot be greater than today.',
        ];
        $addressMessage = '';

        if ($request->ngo_address_type === "1") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'block' => 'required',
                'grampanchayat' => 'required',
                'village' => 'required',
                'pin' => 'required',
                'ngo_postal_address_at' => 'required|string',
                'ngo_postal_address_post' => 'required|string',
                'ngo_postal_address_via' => 'required|string',
                'ngo_postal_address_ps' => 'required|string',
                'ngo_postal_address_district' => 'required|string',
                'ngo_postal_address_pin' => 'required|digits:6',
            ]);
        } elseif ($request->ngo_address_type === "2") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'municipality' => 'required',
                'ward' => 'required',
                'pin' => 'required',
                'ngo_postal_address_at' => 'required|string',
                'ngo_postal_address_post' => 'required|string',
                'ngo_postal_address_via' => 'required|string',
                'ngo_postal_address_ps' => 'required|string',
                'ngo_postal_address_district' => 'required|string',
                'ngo_postal_address_pin' => 'required|digits:6',
            ]);
        }
        $validatedData = $request->validate($validationRules, $messages);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $piainstitutemaster = PiaInstituteMaster::where('user_table_id', $user->user_table_id)->firstOrFail();
            $previousId = PiaInstituteMaster::latest()->value('id') ?? 0;
            $currentDate = now()->format('d/m/Y');
            $randomNumber = mt_rand(1000, 9999);
            $piaSystemGeneratedRegNo = "SSEPD/PIA/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
            $piaSystemGenRegNo = str_replace('/', '_', $piaSystemGeneratedRegNo);
            $instituteSystemGeneratedRegNo = "SSEPD/INSTITUTE/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
            $instituteSystemGenRegNo = str_replace('/', '_', $instituteSystemGeneratedRegNo);

            $folderPath = public_path("pia_institute_files/{$instituteSystemGenRegNo}");
            /*A folder i.e. storage/pia_institute_files is created inside the root directory ssepd_ngo_working_portal/storage/pia_institute_files*/
            $externalBasePath = dirname(base_path());
            $externalPath = $externalBasePath . "/storage/pia_institute_files/{$instituteSystemGenRegNo}";

            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }
            if (!file_exists($externalPath)) {
                mkdir($externalPath, 0755, true);
            }

            if ($request->hasFile('registration_certificate')) {
                $regCertFile = $request->file('registration_certificate');
                $regCertExtension = $regCertFile->getClientOriginalExtension();
                $regCertRandomName = 'PIA_INST_REG_CERT_' . Str::random(40) . '.' . $regCertExtension;

                $regCertStoredPath = $regCertFile->storeAs("pia_institute_files/{$instituteSystemGenRegNo}", $regCertRandomName, 'public');
                copy(storage_path("app/public/{$regCertStoredPath}"), "{$folderPath}/{$regCertRandomName}");
                copy(storage_path("app/public/{$regCertStoredPath}"), "{$externalPath}/{$regCertRandomName}");
            }

            $piainstitutemaster->institute_name = $validatedData['institute_name'];
            $piainstitutemaster->institute_id = $piainstitutemaster->institute_id;
            $piainstitutemaster->excel_institute_master_id = $piainstitutemaster->excel_institute_master_id;
            $piainstitutemaster->institute_type = $piainstitutemaster->institute_type;
            $piainstitutemaster->institute_user_id = $user->user_table_id;
            $piainstitutemaster->institute_system_email_id = $user->email;
            $piainstitutemaster->institute_type = $piainstitutemaster->institute_type;
            $piainstitutemaster->institute_type_id = $validatedData['institute_type_id'];
            $piainstitutemaster->institute_email_id = $validatedData['institute_email_id'];
            $piainstitutemaster->date_of_registration = $validatedData['date_of_registration'];
            $piainstitutemaster->registration_no = $validatedData['registration_no'];
            $piainstitutemaster->registration_certificate = $regCertStoredPath;
            $piainstitutemaster->which_govt = $validatedData['which_govt'];
            $piainstitutemaster->grantee_code = $validatedData['grantee_code'];
            $piainstitutemaster->nodal_officer_name = $validatedData['nodal_officer_name'];
            $piainstitutemaster->nodal_officer_contact_number = $validatedData['nodal_officer_contact_number'];
            $piainstitutemaster->pia_name = $validatedData['pia_name'];
            $piainstitutemaster->pia_id = $piainstitutemaster->pia_id;
            $piainstitutemaster->pia_nodal_officer_name = $validatedData['pia_nodal_officer_name'];
            $piainstitutemaster->pia_nodal_officer_contact_no = $validatedData['pia_nodal_officer_contact_no'];
            $piainstitutemaster->pia_system_gen_reg_no = $piaSystemGeneratedRegNo;
            $piainstitutemaster->institute_system_gen_reg_no = $instituteSystemGeneratedRegNo;
            $piainstitutemaster->address_type = $validatedData['ngo_address_type'];
            $piainstitutemaster->state_id = $validatedData['state'];
            $piainstitutemaster->district_id = $validatedData['district'];
            $piainstitutemaster->block_id = $request->input('block', null);
            $piainstitutemaster->gp_id = $request->input('grampanchayat', null);
            $piainstitutemaster->village_id = $request->input('village', null);
            $piainstitutemaster->municipality_id = $request->input('municipality', null);
            $piainstitutemaster->ward_id = $request->input('ward', null);
            $piainstitutemaster->pin = $validatedData['pin'];
            $piainstitutemaster->pia_postal_address_at = $validatedData['ngo_postal_address_at'];
            $piainstitutemaster->pia_postal_address_post = $validatedData['ngo_postal_address_post'];
            $piainstitutemaster->pia_postal_address_via = $validatedData['ngo_postal_address_via'];
            $piainstitutemaster->pia_postal_address_ps = $validatedData['ngo_postal_address_ps'];
            $piainstitutemaster->pia_postal_address_district = $validatedData['ngo_postal_address_district'];
            $piainstitutemaster->pia_postal_address_pin = $validatedData['ngo_postal_address_pin'];
            $piainstitutemaster->is_active = 'active';
            $piainstitutemaster->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $piainstitutemaster->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $piainstitutemaster->created_by = Auth::id() ?? null;
            $piainstitutemaster->created_on = now()->setTimezone('Asia/Kolkata')->toDateString();
            $piainstitutemaster->created_by_user_table_id = $piainstitutemaster->user_table_id;
            $piainstitutemaster->status = 1;
            $piainstitutemaster->basic_details_completed = 1;
            $piainstitutemaster->save();

            $applicationstagehistory = new ApplicationStageHistory();
            $applicationstagehistory->department_scheme_id = 4;
            $applicationstagehistory->model_name = 'PiaInstituteMaster';
            $applicationstagehistory->model_table_id = $piainstitutemaster->id;
            $applicationstagehistory->initial_model_table_id = $piainstitutemaster->id;
            $applicationstagehistory->stage_id = 37;
            $applicationstagehistory->stage_name = 'Application updated by User';
            $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $applicationstagehistory->created_by = Auth::id();
            $applicationstagehistory->created_by_remarks = 'PIA Institute Basic Details have been submitted';
            $ipAddress = request()->ip();
            $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
            $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
            $applicationstagehistory->save();
            DB::commit();
            return redirect()->route('admin.piainstitutes.index')->with('success', 'PIA Institute Basic Details have been updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🏫 PIA Institute Basic Details have been updated.", [
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
    public function check_registration_no(Request $request)
    {
        $registration_no = trim($request->registration_no);
        if (!$registration_no) {
            return response()->json(1);
        }

        $exists = PiaInstituteMaster::where('registration_no', $registration_no)->exists();
        if ($exists) {
            return response()->json(1);
        } else {
            return response()->json(0);
        }
    }

    public function check_grantee_code(Request $request)
    {
        $grantee_code = trim($request->grantee_code);
        if (!$grantee_code) {
            return response()->json(1);
        }

        $exists = PiaInstituteMaster::where('grantee_code', $grantee_code)->exists();
        if ($exists) {
            return response()->json(1);
        } else {
            return response()->json(0);
        }
    }

    public function index()
    {
        $userId = Auth::user();
        $piainstitute = PiaInstituteMaster::where('user_table_id', $userId->user_table_id)->firstOrFail();
        if($piainstitute->basic_details_completed != 1) {
            return view('dashboard.pia_institutes.pia_institute_basic_details', compact('piainstitute'));
        }
        return view('dashboard.pia_institutes.index', compact('piainstitute'));
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
