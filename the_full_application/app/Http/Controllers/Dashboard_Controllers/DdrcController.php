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
use App\Models\DdrcDetails;
use App\Models\DdrcStaffDetails;

class DdrcController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:DDRC-access|DDRC-list|DDRC-create|DDRC-edit|DDRC-delete|DDRC-show', ['only' => ['index','store']]);
        $this->middleware('permission:DDRC-create', ['only' => ['create','store', 'staff_store', 'ddrc_check_staff_aadhar', 'ddrc_check_staff_udidno']]);
        $this->middleware('permission:DDRC-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:DDRC-delete', ['only' => ['destroy']]);
        $this->middleware('permission:DDRC-show', ['only' => ['show']]);
    }
/**
* Display a listing of the resource.
*/

public function index()
{
    $user = auth()->user();
    $userRole = $user->role_id;    
    $all_ddrc = User::where('role_id', 23)->where('status', 1)->withCount('ddrcStaff')->get();
    
    if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
        
    } elseif (in_array($userRole, [4, 6])) {
        $district_id = DB::table('blocks')->where('block_id', $user->posted_block)->value('district_id');
        $all_ddrc = $all_ddrc->where('district_id', $district_id);
    } elseif ($userRole == 5) {
        $district_id = DB::table('municipalities')->where('municipality_id', $user->posted_municipality)->value('district_id');
        $all_ddrc = $all_ddrc->where('district_id', $district_id);
    } elseif (in_array($userRole, [8, 10])) {
        $district_id = DB::table('subdivisions')->where('subdivision_id', $user->posted_subdiv)->value('district_id');
        $all_ddrc = $all_ddrc->where('district_id', $district_id);
    } elseif (in_array($userRole, [9, 11])) {
        $all_ddrc = $all_ddrc->where('district_id', $user->posted_district);
    }
    
    if ($userRole == 23) {
        $ddrc = User::where('id', $user->id)->withCount('ddrcStaff')->first();
        $ddrcDetails = DdrcDetails::where('user_table_id', $ddrc->id)->first();

        if (!$ddrcDetails) {
            return redirect()->route('admin.ddrc.create')->with('info', 'Kindly provide the basic information of the DDRC to proceed further.');
        }

        $all_ddrc = collect([$ddrc]);
    }

    return view('dashboard.ddrc.index', compact('all_ddrc'));
}


public function create()
{
    $user = auth()->user();    
    $ddrcDetails = DdrcDetails::where('user_table_id', $user->id)->exists();
    if (!$ddrcDetails) {
        return view('dashboard.ddrc.ddrc_details', compact('user'))->with('info', 'Kindly provide the basic information of the DDRC to proceed further.');
    }
    return view('dashboard.ddrc.ddrc_staff_details_entry', compact('user', 'ddrcDetails'));
}

/**
* Store a newly created resource in storage.
*/
public function store(Request $request)
{
    $validationRules = [
        'ddrc_name' => 'required',
        'file_geo_tagged_image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        'ddrc_latitude' => 'required|numeric|between:-90,90',
        'ddrc_longitude' => 'required|numeric|between:-180,180',
        'ngo_address_type' => 'required|in:1,2',
    ];

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
            'pin' => 'required',
            'ngo_postal_address_at' => 'required|string',
            'ngo_postal_address_post' => 'required|string',
            'ngo_postal_address_via' => 'required|string',
            'ngo_postal_address_ps' => 'required|string',
            'ngo_postal_address_district' => 'required|string',
            'ngo_postal_address_pin' => 'required|digits:6',
        ]);
    }
    $validatedData = $request->validate($validationRules);
    DB::beginTransaction();
    try {
        $user = auth()->user();
        $alreadyExists = DdrcDetails::where('user_table_id', $user->id)->exists();
        if ($alreadyExists) {
            return back()->with('error', 'You have already provided the details.');
        }

        $previousId = DdrcDetails::latest()->value('id') ?? 0;
        $currentDate = now()->format('d/m/Y');
        $randomNumber = mt_rand(1000, 9999);
        $ddrcSystemGeneratedRegNo = "SSEPD/DDRC/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
        $ddrcSystemGenRegNo = str_replace('/', '_', $ddrcSystemGeneratedRegNo);

        $folderPath = public_path("ddrc_files/{$ddrcSystemGenRegNo}");
        /*A folder i.e. storage/ddrc_files is created inside the root directory ssepd_ngo_working_portal/storage/ddrc_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ddrc_files/{$ddrcSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('file_geo_tagged_image')) {
            $imageFile = $request->file('file_geo_tagged_image');
            $imageExtension = $imageFile->getClientOriginalExtension();
            $imageRandomName = 'DDRC_' . Str::random(40) . '.' . $imageExtension;

            $imageStoredPath = $imageFile->storeAs("ddrc_files/{$ddrcSystemGenRegNo}", $imageRandomName, 'public');
            copy(storage_path("app/public/{$imageStoredPath}"), "{$folderPath}/{$imageRandomName}");
            copy(storage_path("app/public/{$imageStoredPath}"), "{$externalPath}/{$imageRandomName}");
        }

        $ddrcDetails = new DdrcDetails();
        $ddrcDetails->user_table_id = $user->id;
        $ddrcDetails->ddrc_name = $validatedData['ddrc_name'];
        $ddrcDetails->ddrc_system_gen_reg_no = $ddrcSystemGeneratedRegNo;
        $ddrcDetails->file_geo_tagged_image = $imageStoredPath;
        $ddrcDetails->ddrc_latitude = $validatedData['ddrc_latitude'];
        $ddrcDetails->ddrc_longitude = $validatedData['ddrc_longitude'];
        $ddrcDetails->system_stored_latitude = $request->system_stored_latitude;
        $ddrcDetails->system_stored_longitude = $request->system_stored_longitude;
        $ddrcDetails->ddrc_address_type = $validatedData['ngo_address_type'];
        $ddrcDetails->state_id = $validatedData['state'];
        $ddrcDetails->district_id = $validatedData['district'];
        $ddrcDetails->block_id = $request->input('block', null);
        $ddrcDetails->gp_id = $request->input('grampanchayat', null);
        $ddrcDetails->village_id = $request->input('village', null);
        $ddrcDetails->municipality_id = $request->input('municipality', null);
        $ddrcDetails->pin = $validatedData['pin'];
        $ddrcDetails->ddrc_postal_address_at = $validatedData['ngo_postal_address_at'];
        $ddrcDetails->ddrc_postal_address_post = $validatedData['ngo_postal_address_post'];
        $ddrcDetails->ddrc_postal_address_via = $validatedData['ngo_postal_address_via'];
        $ddrcDetails->ddrc_postal_address_ps = $validatedData['ngo_postal_address_ps'];
        $ddrcDetails->ddrc_postal_address_district = $validatedData['ngo_postal_address_district'];
        $ddrcDetails->ddrc_postal_address_pin = $validatedData['ngo_postal_address_pin'];
        $ddrcDetails->is_active = 'active';
        $ddrcDetails->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $ddrcDetails->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $ddrcDetails->created_by = Auth::id() ?? null;
        $ddrcDetails->status = 1;
        $ddrcDetails->save();
        DB::commit();
        return back()->with('success', 'You have successfully provided the details.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("NGO Part Two Registration failed: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}

public function staff_store(Request $request)
{
    $validationRules = [
        'ddrc_staff_name' => 'required',
        'staff_engagement_date' => 'required|date',
        'staff_designation' => 'required',
        'staff_employment_type' => 'required',
        'highest_qualification' => 'required',
        'basic_remuneration' => 'required',
        'ddrc_staff_aadhar_no' => 'required|string|regex:/^[A-Za-z0-9]{12}$/',
        'ddrc_file_staff_aadhar' => 'required|file|mimes:pdf|max:2048',
        'staff_email_id' => 'nullable',
        'staff_mob_no' => 'required|digits:10',
        'staff_date_of_birth' => 'required|date',
        'disability_type' => 'nullable',
        'udid_no' => 'nullable',
        'file_udid_certificate' => 'nullable|file|mimes:pdf|max:2048',
        'file_staff_image' => 'required|image|mimes:jpg,jpeg,png|max:3072',
        'ngo_address_type' => 'required|in:1,2',
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
            'pin' => 'required',
            'ngo_postal_address_at' => 'required|string',
            'ngo_postal_address_post' => 'required|string',
            'ngo_postal_address_via' => 'required|string',
            'ngo_postal_address_ps' => 'required|string',
            'ngo_postal_address_district' => 'required|string',
            'ngo_postal_address_pin' => 'required|digits:6',
        ]);
    }
    $validatedData = $request->validate($validationRules);

    DB::beginTransaction();
    try {
        $user = auth()->user();
        $ddrcDetails = DdrcDetails::where('user_table_id', $user->id)->firstOrFail();

        if (!$ddrcDetails) {
            return view('dashboard.ddrc.ddrc_details', compact('user'))->with('info', 'Please provide the basic details first.');
        }

        $ddrcSystemGenRegNo = str_replace('/', '_', $ddrcDetails->ddrc_system_gen_reg_no);

        $folderPath = public_path("ddrc_files/{$ddrcSystemGenRegNo}");
        /*A folder i.e. storage/ddrc_files is created inside the root directory ssepd_ngo_working_portal/storage/ddrc_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ddrc_files/{$ddrcSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('ddrc_file_staff_aadhar')) {
            $staffAadharFile = $request->file('ddrc_file_staff_aadhar');
            $staffAadharExtension = $staffAadharFile->getClientOriginalExtension();
            $staffAadharRandomName = 'STAFF_AADHAR_' . Str::random(40) . '.' . $staffAadharExtension;

            $staffAadharStoredPath = $staffAadharFile->storeAs("ddrc_files/{$ddrcSystemGenRegNo}", $staffAadharRandomName, 'public');
            copy(storage_path("app/public/{$staffAadharStoredPath}"), "{$folderPath}/{$staffAadharRandomName}");
            copy(storage_path("app/public/{$staffAadharStoredPath}"), "{$externalPath}/{$staffAadharRandomName}");
        }

        if ($request->hasFile('file_staff_image')) {
            $staffImageFile = $request->file('file_staff_image');
            $staffImageExtension = $staffImageFile->getClientOriginalExtension();
            $staffImageRandomName = 'STAFF_IMAGE_' . Str::random(40) . '.' . $staffImageExtension;

            $staffImageStoredPath = $staffImageFile->storeAs("ddrc_files/{$ddrcSystemGenRegNo}", $staffImageRandomName, 'public');
            copy(storage_path("app/public/{$staffImageStoredPath}"), "{$folderPath}/{$staffImageRandomName}");
            copy(storage_path("app/public/{$staffImageStoredPath}"), "{$externalPath}/{$staffImageRandomName}");
        }

        if ($request->hasFile('file_udid_certificate')) {
            $staffUdidFile = $request->file('file_udid_certificate');
            $staffUdidExtension = $staffUdidFile->getClientOriginalExtension();
            $staffUdidRandomName = 'STAFF_UDID_' . Str::random(40) . '.' . $staffUdidExtension;

            $staffUdidStoredPath = $staffUdidFile->storeAs("ddrc_files/{$ddrcSystemGenRegNo}", $staffUdidRandomName, 'public');
            copy(storage_path("app/public/{$staffUdidStoredPath}"), "{$folderPath}/{$staffUdidRandomName}");
            copy(storage_path("app/public/{$staffUdidStoredPath}"), "{$externalPath}/{$staffUdidRandomName}");
        }

        $ddrcStaffDetails = new DdrcStaffDetails();
        $ddrcStaffDetails->ddrc_id = $ddrcDetails->id;
        $ddrcStaffDetails->ddrc_name = $ddrcDetails->ddrc_name;
        $ddrcStaffDetails->ddrc_system_gen_reg_no = $ddrcDetails->ddrc_system_gen_reg_no;
        $ddrcStaffDetails->ddrc_staff_name = $validatedData['ddrc_staff_name'];
        $ddrcStaffDetails->staff_engagement_date = $validatedData['staff_engagement_date'];
        $ddrcStaffDetails->staff_designation = $validatedData['staff_designation'];
        $ddrcStaffDetails->highest_qualification = $validatedData['highest_qualification'];
        $ddrcStaffDetails->staff_employment_type = $validatedData['staff_employment_type'];
        $ddrcStaffDetails->basic_remuneration = $validatedData['basic_remuneration'];
        $ddrcStaffDetails->ddrc_staff_aadhar_no = $validatedData['ddrc_staff_aadhar_no'];
        $ddrcStaffDetails->ddrc_file_staff_aadhar = $staffAadharStoredPath;
        $ddrcStaffDetails->staff_email_id = $validatedData['staff_email_id'];
        $ddrcStaffDetails->staff_mob_no = $validatedData['staff_mob_no'];
        $ddrcStaffDetails->staff_date_of_birth = $validatedData['staff_date_of_birth'];
        $ddrcStaffDetails->file_staff_image = $staffImageStoredPath;
        $ddrcStaffDetails->disability_type = $validatedData['disability_type'];
        $ddrcStaffDetails->udid_no = $validatedData['udid_no'];
        $ddrcStaffDetails->file_udid_certificate = $staffUdidStoredPath ?? null;
        $ddrcStaffDetails->staff_address_type = $validatedData['ngo_address_type'];
        $ddrcStaffDetails->state_id = $validatedData['state'];
        $ddrcStaffDetails->district_id = $validatedData['district'];
        $ddrcStaffDetails->block_id = $request->input('block', null);
        $ddrcStaffDetails->gp_id = $request->input('grampanchayat', null);
        $ddrcStaffDetails->village_id = $request->input('village', null);
        $ddrcStaffDetails->municipality_id = $request->input('municipality', null);
        $ddrcStaffDetails->pin = $validatedData['pin'];
        $ddrcStaffDetails->staff_postal_address_at = $validatedData['ngo_postal_address_at'];
        $ddrcStaffDetails->staff_postal_address_post = $validatedData['ngo_postal_address_post'];
        $ddrcStaffDetails->staff_postal_address_via = $validatedData['ngo_postal_address_via'];
        $ddrcStaffDetails->staff_postal_address_ps = $validatedData['ngo_postal_address_ps'];
        $ddrcStaffDetails->staff_postal_address_district = $validatedData['ngo_postal_address_district'];
        $ddrcStaffDetails->staff_postal_address_pin = $validatedData['ngo_postal_address_pin'];
        $ddrcStaffDetails->is_active = 'active';
        $ddrcStaffDetails->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $ddrcStaffDetails->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $ddrcStaffDetails->created_by = Auth::id() ?? null;
        $ddrcStaffDetails->application_stage_id = 2;
        $ddrcStaffDetails->no_of_form_completed = 2;
        $ddrcStaffDetails->user_table_id = $ddrcDetails->user_table_id;;
        $ddrcStaffDetails->status = 1;
        $ddrcStaffDetails->save();

        $applicationstagehistory = new ApplicationStageHistory();
        /*department_scheme_id Special School = 2*/
        $applicationstagehistory->department_scheme_id = 2;
        $applicationstagehistory->model_name = 'DdrcStaffDetails';
        $applicationstagehistory->model_table_id = $ddrcStaffDetails->id;
        $applicationstagehistory->initial_model_table_id = $ddrcStaffDetails->id;
        $applicationstagehistory->stage_id = 2;
        $applicationstagehistory->stage_name = 'Application Applied Successfully';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'DDRC Staff Details Saved Successfully';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        return redirect()->route('admin.ddrc.index')->with('success', 'One staff record added successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("👨‍🏫 DDRC Staff Details Submission failed", [
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

public function ddrc_check_staff_aadhar(Request $request): JsonResponse
{
    $ddrc_staff_aadhar_no = $request->get('ddrc_staff_aadhar_no');
    if (empty($ddrc_staff_aadhar_no)) {
        return response()->json(2);
    }
    $aadharExistsInNgo = DdrcStaffDetails::where('ddrc_staff_aadhar_no', $ddrc_staff_aadhar_no)->exists();
    return response()->json($aadharExistsInNgo ? 1 : 0);
}

public function ddrc_check_staff_udidno(Request $request)
{
    $udid = $request->udid_no;

    if (!$udid || strlen($udid) < 18) {
        return response()->json(2);
    }
    $exists = DdrcStaffDetails::where('udid_no', $udid)->exists();
    return response()->json($exists ? 1 : 0);
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