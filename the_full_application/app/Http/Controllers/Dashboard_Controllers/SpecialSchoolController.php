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
use App\Models\SpecialSchoolMapping;
use App\Models\SpecialSchool;
use App\Models\SpecialSchoolStaff;

class SpecialSchoolController extends Controller
{
/**
* Display a listing of the resource.
*/
/*public function index()
{
    $user = auth()->user();
    $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();
    $specialSchool = SpecialSchool::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality'])->where('user_table_id', $specialSchoolMapping->user_table_id)->get();
    foreach ($specialSchool as $schoolDetails) {
        $addressParts = [
            optional($schoolDetails->village)->village_name,
            optional($schoolDetails->grampanchayat)->gp_name,
            optional($schoolDetails->block)->block_name,
            optional($schoolDetails->municipality)->municipality_name,
            optional($schoolDetails->district)->district_name,
        ];

        $schoolDetails->full_address = implode(', ', array_filter($addressParts));
    }
    return view('dashboard.special_school.index', compact('specialSchool', 'specialSchoolMapping'));
}*/

public function index()
{
    $user = auth()->user();
    $userRole = $user->role_id;

    $specialSchoolMapping = null;
    $specialSchoolQuery = SpecialSchool::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality']);

    /*Role-based filtering logic*/
    if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {
        /*SuperAdmin, Admin, HO, BO, Director, Secretary — see all records (no filter)*/
    } elseif (in_array($userRole, [4, 6])) {
        /*BSSO, BDO — filter by posted_block*/
        $specialSchoolQuery->where('block_id', $user->posted_block);
    } elseif ($userRole == 5) {
        /*MEO — filter by posted_municipality*/
        $specialSchoolQuery->where('municipality_id', $user->posted_municipality);
    } elseif (in_array($userRole, [8, 10])) {
        /*SSSO, SubCollector — filter by posted_subdiv*/

        /*Get all block_ids and municipality_ids under user's subdivision*/
        $blockIds = \App\Models\Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id')->toArray();
        $municipalityIds = \App\Models\Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id')->toArray();

        $specialSchoolQuery->where(function ($query) use ($blockIds, $municipalityIds) {
            $query->whereIn('block_id', $blockIds)
            ->orWhereIn('municipality_id', $municipalityIds);
        });
    } elseif (in_array($userRole, [9, 11])) {
        /*DSSO, Collector — filter by posted_district*/
        $specialSchoolQuery->where('district_id', $user->posted_district);
    } elseif ($userRole == 22) {
        /*SpecialSchool Role — fetch via mapping*/
        $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();
        $specialSchool = SpecialSchool::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality'])
        ->where('user_table_id', $specialSchoolMapping->user_table_id)
        ->get();

        if ($specialSchool->isEmpty()) {
            return redirect()->route('admin.specialschool.create')->with('info', 'Kindly provide the basic information of the school to proceed further.');
        }

        /*Append full address for each school*/
        foreach ($specialSchool as $schoolDetails) {
            $addressParts = [
                optional($schoolDetails->village)->village_name,
                optional($schoolDetails->grampanchayat)->gp_name,
                optional($schoolDetails->block)->block_name,
                optional($schoolDetails->municipality)->municipality_name,
                optional($schoolDetails->district)->district_name,
            ];
            $schoolDetails->full_address = implode(', ', array_filter($addressParts));
        }

        return view('dashboard.special_school.index', compact('specialSchool', 'specialSchoolMapping'));
    }

    /*If not role 22, execute the query normally*/
    $specialSchool = $specialSchoolQuery->get();

    /*Generate full address for each school*/
    foreach ($specialSchool as $schoolDetails) {
        $addressParts = [
            optional($schoolDetails->village)->village_name,
            optional($schoolDetails->grampanchayat)->gp_name,
            optional($schoolDetails->block)->block_name,
            optional($schoolDetails->municipality)->municipality_name,
            optional($schoolDetails->district)->district_name,
        ];
        $schoolDetails->full_address = implode(', ', array_filter($addressParts));
    }

    return view('dashboard.special_school.index', compact('specialSchool', 'specialSchoolMapping'));
}


/**
* Show the form for creating a new resource.
*/
public function create()
{
    $user = auth()->user();
    $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();
    $specialSchool = SpecialSchool::where('user_table_id', $specialSchoolMapping->user_table_id)->first();

    if (!$specialSchool) {
        return view('dashboard.special_school.school_basic_details', compact('specialSchoolMapping'));
    }
    return view('dashboard.special_school.staff_details_entry', compact('specialSchool', 'specialSchoolMapping'));
}

/**
* Store a newly created resource in storage.
*/
public function store_school_basic_details(Request $request)
{
    $validationRules = [
        'special_school_management_name' => 'required',
        'special_school_name' => 'required',
        'school_establishment_date' => 'required|date',
        'school_category' => 'required',
        'school_type' => 'required|in:1,2',
        'act_reg_no' => 'required',
        'file_act_reg' => 'required|file|mimes:pdf|max:2048',
        'school_email_id' => 'required|email',
        'school_mobile_no' => 'required|digits:10',
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
        $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();

        $previousId = SpecialSchool::latest()->value('id') ?? 0;
        $currentDate = now()->format('d/m/Y');
        $randomNumber = mt_rand(1000, 9999);
        $schoolSystemGeneratedRegNo = "SSEPD/SPECIALSCHOOL/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
        $schoolSystemGenRegNo = str_replace('/', '_', $schoolSystemGeneratedRegNo);

        $folderPath = public_path("special_school_files/{$schoolSystemGenRegNo}");
        /*A folder i.e. storage/special_school_files is created inside the root directory ssepd_ngo_working_portal/storage/special_school_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/special_school_files/{$schoolSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('file_act_reg')) {
            $actFile = $request->file('file_act_reg');
            $actExtension = $actFile->getClientOriginalExtension();
            $actRandomName = 'SPECIAL_SCHOOL_ACT_' . Str::random(40) . '.' . $actExtension;

            $actStoredPath = $actFile->storeAs("special_school_files/{$schoolSystemGenRegNo}", $actRandomName, 'public');
            copy(storage_path("app/public/{$actStoredPath}"), "{$folderPath}/{$actRandomName}");
            copy(storage_path("app/public/{$actStoredPath}"), "{$externalPath}/{$actRandomName}");
        }

        $specialSchool = new SpecialSchool();
        $specialSchool->management_id = $specialSchoolMapping->management_id;
        $specialSchool->special_school_management_name = $validatedData['special_school_management_name'];
        $specialSchool->special_school_id = $specialSchoolMapping->special_school_id;
        $specialSchool->special_school_name = $validatedData['special_school_name'];
        $specialSchool->school_establishment_date = $validatedData['school_establishment_date'];
        $specialSchool->school_category = $validatedData['school_category'];
        $specialSchool->school_type = $validatedData['school_type'];
        $specialSchool->act_reg_no = $validatedData['act_reg_no'];
        $specialSchool->file_act_reg = $actStoredPath;
        $specialSchool->school_email_id = $validatedData['school_email_id'];
        $specialSchool->school_mobile_no = $validatedData['school_mobile_no'];
        $specialSchool->school_system_gen_reg_no = $schoolSystemGeneratedRegNo;
        $specialSchool->school_address_type = $validatedData['ngo_address_type'];
        $specialSchool->state_id = $validatedData['state'];
        $specialSchool->district_id = $validatedData['district'];
        $specialSchool->block_id = $request->input('block', null);
        $specialSchool->gp_id = $request->input('grampanchayat', null);
        $specialSchool->village_id = $request->input('village', null);
        $specialSchool->municipality_id = $request->input('municipality', null);
        $specialSchool->pin = $validatedData['pin'];
        $specialSchool->school_postal_address_at = $validatedData['ngo_postal_address_at'];
        $specialSchool->school_postal_address_post = $validatedData['ngo_postal_address_post'];
        $specialSchool->school_postal_address_via = $validatedData['ngo_postal_address_via'];
        $specialSchool->school_postal_address_ps = $validatedData['ngo_postal_address_ps'];
        $specialSchool->school_postal_address_district = $validatedData['ngo_postal_address_district'];
        $specialSchool->school_postal_address_pin = $validatedData['ngo_postal_address_pin'];
        $specialSchool->is_active = 'active';
        $specialSchool->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $specialSchool->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $specialSchool->created_by = Auth::id() ?? null;
        $specialSchool->application_stage_id = 1;
        $specialSchool->no_of_form_completed = 1;
        $specialSchool->user_table_id = $specialSchoolMapping->user_table_id;;
        $specialSchool->status = 1;
        $specialSchool->save();

        $applicationstagehistory = new ApplicationStageHistory();
        /*department_scheme_id Special School = 2*/
        $applicationstagehistory->department_scheme_id = 2;
        $applicationstagehistory->model_name = 'SpecialSchool';
        $applicationstagehistory->model_table_id = $specialSchool->id;
        $applicationstagehistory->initial_model_table_id = $specialSchool->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'Special School Basic Details have been submitted';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        return view('dashboard.special_school.staff_details_entry')->with('success', 'Special 1st step registration successful.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Special School Part One Registration failed: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}

public function store_school_staff_details(Request $request)
{
    $validationRules = [
        'special_school_staff_name' => 'required',
        'staff_engagement_date' => 'required|date',
        'staff_designation' => 'required',
        'staff_employment_type' => 'required',
        'highest_qualification' => 'required',
        'basic_remuneration' => 'required',
        'special_school_staff_aadhar_no' => 'required|string|regex:/^[A-Za-z0-9]{12}$/',
        'special_school_file_staff_aadhar' => 'required|file|mimes:pdf|max:2048',
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
        $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();
        $specialSchool = SpecialSchool::where('user_table_id', $specialSchoolMapping->user_table_id)->first();

        $schoolSystemGenRegNo = str_replace('/', '_', $specialSchool->school_system_gen_reg_no);

        $folderPath = public_path("special_school_files/{$schoolSystemGenRegNo}");
        /*A folder i.e. storage/special_school_files is created inside the root directory ssepd_ngo_working_portal/storage/special_school_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/special_school_files/{$schoolSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('special_school_file_staff_aadhar')) {
            $staffAadharFile = $request->file('special_school_file_staff_aadhar');
            $staffAadharExtension = $staffAadharFile->getClientOriginalExtension();
            $staffAadharRandomName = 'STAFF_AADHAR_' . Str::random(40) . '.' . $staffAadharExtension;

            $staffAadharStoredPath = $staffAadharFile->storeAs("special_school_files/{$schoolSystemGenRegNo}", $staffAadharRandomName, 'public');
            copy(storage_path("app/public/{$staffAadharStoredPath}"), "{$folderPath}/{$staffAadharRandomName}");
            copy(storage_path("app/public/{$staffAadharStoredPath}"), "{$externalPath}/{$staffAadharRandomName}");
        }

        if ($request->hasFile('file_staff_image')) {
            $staffImageFile = $request->file('file_staff_image');
            $staffImageExtension = $staffImageFile->getClientOriginalExtension();
            $staffImageRandomName = 'STAFF_IMAGE_' . Str::random(40) . '.' . $staffImageExtension;

            $staffImageStoredPath = $staffImageFile->storeAs("special_school_files/{$schoolSystemGenRegNo}", $staffImageRandomName, 'public');
            copy(storage_path("app/public/{$staffImageStoredPath}"), "{$folderPath}/{$staffImageRandomName}");
            copy(storage_path("app/public/{$staffImageStoredPath}"), "{$externalPath}/{$staffImageRandomName}");
        }

        if ($request->hasFile('file_udid_certificate')) {
            $staffUdidFile = $request->file('file_udid_certificate');
            $staffUdidExtension = $staffUdidFile->getClientOriginalExtension();
            $staffUdidRandomName = 'STAFF_UDID_' . Str::random(40) . '.' . $staffUdidExtension;

            $staffUdidStoredPath = $staffUdidFile->storeAs("special_school_files/{$schoolSystemGenRegNo}", $staffUdidRandomName, 'public');
            copy(storage_path("app/public/{$staffUdidStoredPath}"), "{$folderPath}/{$staffUdidRandomName}");
            copy(storage_path("app/public/{$staffUdidStoredPath}"), "{$externalPath}/{$staffUdidRandomName}");
        }

        $specialSchoolStaff = new SpecialSchoolStaff();
        $specialSchoolStaff->management_id = $specialSchool->management_id;
        $specialSchoolStaff->special_school_management_name = $specialSchool->special_school_management_name;
        $specialSchoolStaff->special_school_id = $specialSchool->special_school_id;
        $specialSchoolStaff->special_school_name = $specialSchool->special_school_name;
        $specialSchoolStaff->school_system_gen_reg_no = $specialSchool->school_system_gen_reg_no;
        $specialSchoolStaff->special_school_staff_name = $validatedData['special_school_staff_name'];
        $specialSchoolStaff->staff_engagement_date = $validatedData['staff_engagement_date'];
        $specialSchoolStaff->staff_designation = $validatedData['staff_designation'];
        $specialSchoolStaff->highest_qualification = $validatedData['highest_qualification'];
        $specialSchoolStaff->staff_employment_type = $validatedData['staff_employment_type'];
        $specialSchoolStaff->basic_remuneration = $validatedData['basic_remuneration'];
        $specialSchoolStaff->special_school_staff_aadhar_no = $validatedData['special_school_staff_aadhar_no'];
        $specialSchoolStaff->special_school_file_staff_aadhar = $staffAadharStoredPath;
        $specialSchoolStaff->staff_email_id = $validatedData['staff_email_id'];
        $specialSchoolStaff->staff_mob_no = $validatedData['staff_mob_no'];
        $specialSchoolStaff->staff_date_of_birth = $validatedData['staff_date_of_birth'];
        $specialSchoolStaff->file_staff_image = $staffImageStoredPath;
        $specialSchoolStaff->disability_type = $validatedData['disability_type'];
        $specialSchoolStaff->udid_no = $validatedData['udid_no'];
        $specialSchoolStaff->file_udid_certificate = $staffUdidStoredPath ?? null;
        $specialSchoolStaff->staff_address_type = $validatedData['ngo_address_type'];
        $specialSchoolStaff->state_id = $validatedData['state'];
        $specialSchoolStaff->district_id = $validatedData['district'];
        $specialSchoolStaff->block_id = $request->input('block', null);
        $specialSchoolStaff->gp_id = $request->input('grampanchayat', null);
        $specialSchoolStaff->village_id = $request->input('village', null);
        $specialSchoolStaff->municipality_id = $request->input('municipality', null);
        $specialSchoolStaff->pin = $validatedData['pin'];
        $specialSchoolStaff->staff_postal_address_at = $validatedData['ngo_postal_address_at'];
        $specialSchoolStaff->staff_postal_address_post = $validatedData['ngo_postal_address_post'];
        $specialSchoolStaff->staff_postal_address_via = $validatedData['ngo_postal_address_via'];
        $specialSchoolStaff->staff_postal_address_ps = $validatedData['ngo_postal_address_ps'];
        $specialSchoolStaff->staff_postal_address_district = $validatedData['ngo_postal_address_district'];
        $specialSchoolStaff->staff_postal_address_pin = $validatedData['ngo_postal_address_pin'];
        $specialSchoolStaff->is_active = 'active';
        $specialSchoolStaff->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $specialSchoolStaff->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $specialSchoolStaff->created_by = Auth::id() ?? null;
        $specialSchoolStaff->application_stage_id = 2;
        $specialSchoolStaff->no_of_form_completed = 2;
        $specialSchoolStaff->user_table_id = $specialSchool->user_table_id;;
        $specialSchoolStaff->status = 1;
        $specialSchoolStaff->save();

        $applicationstagehistory = new ApplicationStageHistory();
        /*department_scheme_id Special School = 2*/
        $applicationstagehistory->department_scheme_id = 2;
        $applicationstagehistory->model_name = 'SpecialSchoolStaff';
        $applicationstagehistory->model_table_id = $specialSchoolStaff->id;
        $applicationstagehistory->initial_model_table_id = $specialSchoolStaff->id;
        $applicationstagehistory->stage_id = 2;
        $applicationstagehistory->stage_name = 'Application Applied Successfully';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'Special School Staff Details Saved Successfully';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        return redirect()->route('admin.specialschool.view_staff_details')->with('success', 'One staff record added successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Special School Staff Details Submission failed: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}

public function check_staff_aadhar(Request $request): JsonResponse
{
    $special_school_staff_aadhar_no = $request->get('special_school_staff_aadhar_no');
    if (empty($special_school_staff_aadhar_no)) {
        return response()->json(2);
    }
    $aadharExistsInNgo = SpecialSchoolStaff::where('special_school_staff_aadhar_no', $special_school_staff_aadhar_no)->exists();
    return response()->json($aadharExistsInNgo ? 1 : 0);
}

public function check_staff_udidno(Request $request)
{
    $udid = $request->udid_no;

    if (!$udid || strlen($udid) < 18) {
        return response()->json(2);
    }
    $exists = SpecialSchoolStaff::where('udid_no', $udid)->exists();
    return response()->json($exists ? 1 : 0);
}

public function view_staff_details()
{
    $user = auth()->user();
    $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();
    $specialSchool = SpecialSchool::where('user_table_id', $specialSchoolMapping->user_table_id)->first();
    if (!$specialSchool) {
        return redirect()->route('admin.specialschool.create')->with('info', 'Kindly provide the basic information of the school to proceed further.');
    }
    $specialSchoolStaff = SpecialSchoolStaff::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality'])->where('special_school_id', $specialSchool->special_school_id)->get();

    foreach ($specialSchoolStaff as $staff) {
        $addressParts = [];

        if ($staff->village) {
            $addressParts[] = $staff->village->village_name;
        }
        if ($staff->grampanchayat) {
            $addressParts[] = $staff->grampanchayat->gp_name;
        }
        if ($staff->block) {
            $addressParts[] = $staff->block->block_name;
        }
        if ($staff->municipality) {
            $addressParts[] = $staff->municipality->municipality_name;
        }
        if ($staff->district) {
            $addressParts[] = $staff->district->district_name;
        }

        $staff->full_address = implode(', ', array_filter($addressParts));
    }
    return view('dashboard.special_school.view_staff_details', compact('specialSchool', 'specialSchoolMapping', 'specialSchoolStaff'));
}

}
