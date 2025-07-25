<?php

namespace App\Http\Controllers\Dashboard_Controllers;

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
use App\Models\NgoRegistration;
use App\Models\NgoPartTwoOfficeBearer;
use App\Models\NgoPartThreeActRegistration;
use App\Models\NgoPartFourOtherRecognition;
use App\Models\NgoPartFourTrainedStaff;
use App\Models\NgoPartFiveListOfBeneficiary;
use App\Models\NgoPartSixAssetOrganization;
use App\Models\NgoPartSixFinancialStatus;
use App\Models\BankMaster;
use App\Models\NgoCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Mail\NgoRegistrationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AadhaarVerifier;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NgoPartFiveListOfBeneficiaryImport;
use Illuminate\Support\Facades\Auth;
use DB;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;
use Illuminate\Validation\ValidationException as LaravelValidationException;

class NgoRegdController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ngo-access|ngo-list|ngo-create|ngo-edit|ngo-delete|ngo-show', ['only' => ['index','store']]);
        $this->middleware('permission:ngo-create', ['only' => ['create','store', 'continue_application', 'part_one_store', 'part_one_after_initial_store', 'part_two_store', 'part_three_store', 'part_four_store', 'part_five_store', 'part_six_store']]);
        $this->middleware('permission:ngo-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:ngo-delete', ['only' => ['destroy']]);
        $this->middleware('permission:ngo-show', ['only' => ['view_ngo_application']]);
    }
/**
* Display a listing of the resource.
*/
public function index(Request $request):View
{
    $user = auth()->user();

    if ($user->hasRole('Ngo')) { 
        $data = NgoRegistration::where('user_table_id', $user->id)->orWhere('created_by', $user->id)->latest()->get();
    } elseif ($user->hasRole('DSSO')) {
        $data = NgoRegistration::where(function ($query) use ($user) {
            $query->where('district_id', $user->posted_district)->where('no_of_form_completed', 6);
        })->get();
    } elseif ($user->hasRole('Collector')) {
        $data = NgoRegistration::where(function ($query) use ($user) {
            $query->where('district_id', $user->posted_district)->where('no_of_form_completed', 6);
        })->get();
    } else {
        $data = NgoRegistration::latest()->get();
    }
    return view('dashboard.ngo.index',compact('data'));
}

/**
* Show the form for creating a new resource.
*/
public function create()
{
    $categories = NgoCategory::where('status', 1)->get();
    return view('dashboard.ngo.create', compact('categories'));
}

/**
* Store a newly created resource in storage.
*/
public function part_one_store(Request $request)
{
    $validationRules = [
        'ngo_registration_type' => 'required',
        'ngo_category' => 'required',
        'ngo_org_name' => 'required',
        'ngo_org_pan' => 'required|alpha_num|size:10',
        'ngo_org_pan_file' => 'required|file|mimes:pdf|max:2048',
        'ngo_org_email' => 'required|email',
        'ngo_org_phone' => 'required|digits:10',
        'ngo_org_website' => 'required|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|max:255',        
        'ngo_registered_with' => 'required',
        'ngo_other_reg_act' => 'nullable|string',
        'ngo_type_of_vo_or_ngo' => 'required',
        'ngo_reg_no' => 'required|max:50',
        'ngo_file_rc' => 'required|file|mimes:pdf|max:2048',
        'ngo_date_of_registration' => 'required|date|before_or_equal:today',
        'ngo_date_of_registration_validity' => 'required|date|after:ngo_date_of_registration|before_or_equal:today',
        'nature_of_organisation' => 'required|array',
        'nature_of_organisation_other' => 'nullable|string',
        'ngo_organisation_type' => 'required',
        'ngo_file_byelaws' => 'required|file|mimes:pdf|max:2048',
        'ngo_parent_organisation' => 'nullable|string',
        'ngo_reg_velidity_available' => 'required|in:1,0',
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

        $previousId = NgoRegistration::latest()->value('id') ?? 0;
        $currentDate = now()->format('d/m/Y');
        $randomNumber = mt_rand(1000, 9999);
        $ngoSystemGeneratedRegNo = "SSEPD/NGO/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
        $ngoSystemGenRegNo = str_replace('/', '_', $ngoSystemGeneratedRegNo);

        $latestUserId = User::latest('id')->value('id');
        $nextUserId = $latestUserId + 1;
        $parts = explode('_', $ngoSystemGenRegNo);
        $lastPart = end($parts);
        $lastNumber = preg_replace('/[^0-9]/', '', $lastPart);
        $finalGeneratedCode = "ngo_" . $nextUserId . $lastNumber;

        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('ngo_org_pan_file')) {
            $panFile = $request->file('ngo_org_pan_file');
            $panExtension = $panFile->getClientOriginalExtension();
            $panRandomName = 'NGO_PAN_' . Str::random(40) . '.' . $panExtension;

            $panStoredPath = $panFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $panRandomName, 'public');
            copy(storage_path("app/public/{$panStoredPath}"), "{$folderPath}/{$panRandomName}");
            copy(storage_path("app/public/{$panStoredPath}"), "{$externalPath}/{$panRandomName}");
        }

        if ($request->hasFile('ngo_file_rc')) {
            $rcFile = $request->file('ngo_file_rc');
            $rcExtension = $rcFile->getClientOriginalExtension();
            $rcRandomName = 'NGO_RC_' . Str::random(40) . '.' . $rcExtension;

            $rcStoredPath = $rcFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $rcRandomName, 'public');
            copy(storage_path("app/public/{$rcStoredPath}"), "{$folderPath}/{$rcRandomName}");
            copy(storage_path("app/public/{$rcStoredPath}"), "{$externalPath}/{$rcRandomName}");
        }

        if ($request->hasFile('ngo_file_byelaws')) {
            $byelawsFile = $request->file('ngo_file_byelaws');
            $rcExtension = $byelawsFile->getClientOriginalExtension();
            $byelawsRandomName = 'NGO_BYELAWS_' . Str::random(40) . '.' . $rcExtension;

            $byelawsStoredPath = $byelawsFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $byelawsRandomName, 'public');
            copy(storage_path("app/public/{$byelawsStoredPath}"), "{$folderPath}/{$byelawsRandomName}");
            copy(storage_path("app/public/{$byelawsStoredPath}"), "{$externalPath}/{$byelawsRandomName}");
        }

        $previousNgoOrgId = NgoRegistration::max('ngo_org_id');
        $currentNgoOrgId = $previousNgoOrgId ? $previousNgoOrgId + 1 : 1;
        $natureOfOrganisation = implode(',', $request->input('nature_of_organisation'));

        $NgoRegistration = new NgoRegistration();
        $NgoRegistration->ngo_org_id = $currentNgoOrgId;
        $NgoRegistration->ngo_registration_type = $validatedData['ngo_registration_type'];
        $NgoRegistration->ngo_category = $validatedData['ngo_category'];
        $NgoRegistration->ngo_org_name = $validatedData['ngo_org_name'];
        $NgoRegistration->ngo_org_pan = $validatedData['ngo_org_pan'];
        $NgoRegistration->ngo_org_pan_file = $panStoredPath;
        $NgoRegistration->ngo_org_email = $validatedData['ngo_org_email'];
        $NgoRegistration->ngo_org_phone = $validatedData['ngo_org_phone'];
        $NgoRegistration->ngo_org_website = $validatedData['ngo_org_website'];
        $NgoRegistration->ngo_registered_with = $validatedData['ngo_registered_with'];
        $NgoRegistration->ngo_other_reg_act = $validatedData['ngo_other_reg_act'];
        $NgoRegistration->ngo_type_of_vo_or_ngo = $validatedData['ngo_type_of_vo_or_ngo'];
        $NgoRegistration->ngo_reg_no = $validatedData['ngo_reg_no'];
        $NgoRegistration->ngo_system_gen_reg_no = $ngoSystemGeneratedRegNo;
        $NgoRegistration->ngo_file_rc = $rcStoredPath;
        $NgoRegistration->ngo_date_of_registration = $validatedData['ngo_date_of_registration'];
        $NgoRegistration->ngo_date_of_registration_validity = $validatedData['ngo_date_of_registration_validity'];
        $NgoRegistration->nature_of_organisation = $natureOfOrganisation;
        $NgoRegistration->nature_of_organisation_other = $request->input('nature_of_organisation_other');
        $NgoRegistration->ngo_organisation_type = $validatedData['ngo_organisation_type'];
        $NgoRegistration->ngo_file_byelaws = $byelawsStoredPath;
        $NgoRegistration->ngo_parent_organisation = $request->input('ngo_parent_organisation');
        $NgoRegistration->ngo_reg_velidity_available = $validatedData['ngo_reg_velidity_available'];
        $NgoRegistration->ngo_address_type = $validatedData['ngo_address_type'];
        $NgoRegistration->state_id = $validatedData['state'];
        $NgoRegistration->district_id = $validatedData['district'];
        $NgoRegistration->block_id = $request->input('block', null);
        $NgoRegistration->gp_id = $request->input('grampanchayat', null);
        $NgoRegistration->village_id = $request->input('village', null);
        $NgoRegistration->municipality_id = $request->input('municipality', null);
        $NgoRegistration->pin = $validatedData['pin'];
        $NgoRegistration->ngo_postal_address_at = $validatedData['ngo_postal_address_at'];
        $NgoRegistration->ngo_postal_address_post = $validatedData['ngo_postal_address_post'];
        $NgoRegistration->ngo_postal_address_via = $validatedData['ngo_postal_address_via'];
        $NgoRegistration->ngo_postal_address_ps = $validatedData['ngo_postal_address_ps'];
        $NgoRegistration->ngo_postal_address_district = $validatedData['ngo_postal_address_district'];
        $NgoRegistration->ngo_postal_address_pin = $validatedData['ngo_postal_address_pin'];
        $NgoRegistration->is_active = 'active';
        $NgoRegistration->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $NgoRegistration->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $NgoRegistration->created_by = Auth::id() ?? null;
        $NgoRegistration->application_stage_id = 1;
        $NgoRegistration->no_of_form_completed = 1;
        $NgoRegistration->user_table_id = 0;
        $NgoRegistration->status = 1;
        $NgoRegistration->save();

        $UserRegistration = new User();
        $UserRegistration->name = $validatedData['ngo_org_name'];
        $UserRegistration->email = $validatedData['ngo_org_email'];
        $UserRegistration->user_id = $finalGeneratedCode;
        $UserRegistration->mobile_no = $validatedData['ngo_org_phone'];
        $UserRegistration->email_verified_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
        $UserRegistration->password = Hash::make('123456');
        $UserRegistration->created_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
        $UserRegistration->updated_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
        $UserRegistration->save();

        $NgoRegistration->user_table_id = $UserRegistration->id;
        $NgoRegistration->save();

        $role = Role::find(15);
        if ($role) {
            $UserRegistration->assignRole($role->name);
        } else {
            throw new Exception("Role with ID 15 does not exist.");
        }

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoRegistration';
        $applicationstagehistory->model_table_id = $NgoRegistration->id;
        $applicationstagehistory->initial_model_table_id = $NgoRegistration->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 1st Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();
        DB::commit();

        $ngoData = [
            'ngoRegdPhase' => 'first',
            'registrationNumber' => $ngoSystemGeneratedRegNo,
            'ngoOrgName' => $validatedData['ngo_org_name'],
            'ngoOrgEmail' => $validatedData['ngo_org_email'],
            'ngoOrgPhone' => $validatedData['ngo_org_phone'],
            'ngoOrgWebsite' => $validatedData['ngo_org_website'],
            'ngoRegNo' => $validatedData['ngo_reg_no'],
            'ngoDateOfRegistration' => $validatedData['ngo_date_of_registration'],
            'ngoUserId' => $finalGeneratedCode,
            'ngoUserPassword' => '123456',
        ];
        Mail::to($validatedData['ngo_org_email'])->send(new NgoRegistrationMail($ngoData));
        return redirect()->route('admin.ngo.index')->with('success', 'NGO 1st step registration successful.')->with('addressMessage', $addressMessage);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("NGO Part Two Registration failed: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}

public function continue_application($id, $no_of_form_completed)
{
    $ngoRegistration = NgoRegistration::find($id);
    $categories = NgoCategory::where('status', 1)->get();

    if (!$ngoRegistration) {
        return redirect()->route('admin.ngo.index')->with('error', 'NGO registration not found.');
    }

    /*Validation before proceeding to Step 6*/
    if ($no_of_form_completed == 5) {
        /*Check if required records exist before showing 'create_six'*/
        if (
            !$ngoRegistration ||
            !NgoPartTwoOfficeBearer::where('ngo_tbl_id', $id)->exists() ||
            !NgoPartThreeActRegistration::where('ngo_tbl_id', $id)->exists() ||
            !NgoPartFourTrainedStaff::where('ngo_tbl_id', $id)->exists() ||
            !NgoPartFourOtherRecognition::where('ngo_tbl_id', $id)->exists() ||
            !NgoPartFiveListOfBeneficiary::where('ngo_tbl_id', $id)->exists()
        ) {
            /*Redirect user to appropriate incomplete step*/
            if (NgoPartTwoOfficeBearer::where('ngo_tbl_id', $id)->count() < 3) {
                return redirect()->route('admin.ngo.continue_application', [$id, 1])
                ->with('error', 'Please complete Office Bearer details first.');
            }

            if (!NgoPartThreeActRegistration::where('ngo_tbl_id', $id)->exists()) {
                return redirect()->route('admin.ngo.continue_application', [$id, 2])
                ->with('error', 'Please complete Act Registration details first.');
            }

            if (!NgoPartFourTrainedStaff::where('ngo_tbl_id', $id)->exists() ||
                !NgoPartFourOtherRecognition::where('ngo_tbl_id', $id)->exists()) {
                return redirect()->route('admin.ngo.continue_application', [$id, 3])
            ->with('error', 'Please complete Trained Staff & Recognition details first.');
        }

        if (!NgoPartFiveListOfBeneficiary::where('ngo_tbl_id', $id)->exists()) {
            return redirect()->route('admin.ngo.continue_application', [$id, 4])
            ->with('error', 'Please complete Beneficiary details first.');
        }
    }
}

$views = [
    0 => 'dashboard.ngo.create_one',
    1 => 'dashboard.ngo.create_two',
    2 => 'dashboard.ngo.create_three',
    3 => 'dashboard.ngo.create_four',
    4 => 'dashboard.ngo.create_five',
    5 => 'dashboard.ngo.create_six',
];

if (array_key_exists($no_of_form_completed, $views)) {
    $data = [
        'id' => $id,
        'ngoRegistration' => $ngoRegistration,
        'categories' => $categories,
    ];

    if ($no_of_form_completed == 5) {
        $data['ifsc_codes'] = BankMaster::where('is_active', 1)->get();
    }

    return view($views[$no_of_form_completed], $data);
}

if ($no_of_form_completed == 6) {
    return 'You have already completed your NGO Registration.';
}

return 'Invalid step number.';
}

/*continue_application OLD 19-06-2025*/
/*public function continue_application($id, $no_of_form_completed)
{
    $ngoRegistration = NgoRegistration::find($id);
    $categories = NgoCategory::where('status', 1)->get();

    if (!$ngoRegistration) {
        return redirect()->route('admin.ngo.index')->with('error', 'NGO registration not found.');
    }

    $views = [
        0 => 'dashboard.ngo.create_one',
        1 => 'dashboard.ngo.create_two',
        2 => 'dashboard.ngo.create_three',
        3 => 'dashboard.ngo.create_four',
        4 => 'dashboard.ngo.create_five',
        5 => 'dashboard.ngo.create_six',
    ];

    if (array_key_exists($no_of_form_completed, $views)) {
        $data = ['id' => $id, 'ngoRegistration' => $ngoRegistration, 'categories' => $categories,];

        if ($no_of_form_completed == 5) {
            $data['ifsc_codes'] = BankMaster::where('is_active', 1)->get();
        }

        return view($views[$no_of_form_completed], $data);
    }

    if ($no_of_form_completed == 6) {
        return 'You have already completed your NGO Registration.';
    }

    return 'Invalid step number.';
}*/

public function part_one_after_initial_store(Request $request)
{
    $validationRules = [
        'ngo_registration_type' => 'required',
        'ngo_category' => 'required',
        'ngo_org_name' => 'required',
        'ngo_org_pan' => 'required|alpha_num|size:10',
        'ngo_org_pan_file' => 'required|file|mimes:pdf|max:2048',
        'ngo_org_email' => 'required|email',
        'ngo_org_phone' => 'required|digits:10',
        'ngo_org_website' => 'required|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|max:255',        
        'ngo_registered_with' => 'required',
        'ngo_other_reg_act' => 'nullable|string',
        'ngo_type_of_vo_or_ngo' => 'required',
        'ngo_reg_no' => 'required|max:50',
        'ngo_file_rc' => 'required|file|mimes:pdf|max:2048',
        'ngo_date_of_registration' => 'required|date|before_or_equal:today',
        'ngo_date_of_registration_validity' => 'required|date|after:ngo_date_of_registration|before_or_equal:today',
        'nature_of_organisation' => 'required|array',
        'nature_of_organisation_other' => 'nullable|string',
        'ngo_organisation_type' => 'required',
        'ngo_file_byelaws' => 'required|file|mimes:pdf|max:2048',
        'ngo_parent_organisation' => 'nullable|string',
        'ngo_reg_velidity_available' => 'required|in:1,0',
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
        $ngo_tbl_records = NgoRegistration::findOrFail($request->id);

        $finalGeneratedCode = User::findOrFail($ngo_tbl_records->user_table_id)->value('user_id');

        $previousId = NgoRegistration::latest()->value('id') ?? 0;
        $currentDate = now()->format('d/m/Y');
        $randomNumber = mt_rand(1000, 9999);
        $ngoSystemGeneratedRegNo = "SSEPD/NGO/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
        $ngoSystemGenRegNo = str_replace('/', '_', $ngoSystemGeneratedRegNo);

        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('ngo_org_pan_file')) {
            $panFile = $request->file('ngo_org_pan_file');
            $panExtension = $panFile->getClientOriginalExtension();
            $panRandomName = 'NGO_PAN_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $panExtension;

            $panStoredPath = $panFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $panRandomName, 'public');
            copy(storage_path("app/public/{$panStoredPath}"), "{$folderPath}/{$panRandomName}");
            copy(storage_path("app/public/{$panStoredPath}"), "{$externalPath}/{$panRandomName}");
        }

        if ($request->hasFile('ngo_file_rc')) {
            $rcFile = $request->file('ngo_file_rc');
            $rcExtension = $rcFile->getClientOriginalExtension();
            $rcRandomName = 'NGO_RC_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $rcExtension;

            $rcStoredPath = $rcFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $rcRandomName, 'public');
            copy(storage_path("app/public/{$rcStoredPath}"), "{$folderPath}/{$rcRandomName}");
            copy(storage_path("app/public/{$rcStoredPath}"), "{$externalPath}/{$rcRandomName}");
        }

        if ($request->hasFile('ngo_file_byelaws')) {
            $byelawsFile = $request->file('ngo_file_byelaws');
            $rcExtension = $byelawsFile->getClientOriginalExtension();
            $byelawsRandomName = 'NGO_BYELAWS_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $rcExtension;

            $byelawsStoredPath = $byelawsFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $byelawsRandomName, 'public');
            copy(storage_path("app/public/{$byelawsStoredPath}"), "{$folderPath}/{$byelawsRandomName}");
            copy(storage_path("app/public/{$byelawsStoredPath}"), "{$externalPath}/{$byelawsRandomName}");
        }

        $natureOfOrganisation = implode(',', $request->input('nature_of_organisation'));

        $NgoRegistration = NgoRegistration::find($ngo_tbl_records->id);
        if (!$NgoRegistration) {
            return redirect()->back()->with('error', 'NGO record not found.');
        }
        $NgoRegistration->ngo_org_id = $ngo_tbl_records->ngo_org_id;
        $NgoRegistration->ngo_registration_type = $validatedData['ngo_registration_type'];
        $NgoRegistration->ngo_category = $validatedData['ngo_category'];
        $NgoRegistration->ngo_org_name = $validatedData['ngo_org_name'];
        $NgoRegistration->ngo_org_pan = $validatedData['ngo_org_pan'];
        $NgoRegistration->ngo_org_pan_file = $panStoredPath;
        $NgoRegistration->ngo_org_email = $validatedData['ngo_org_email'];
        $NgoRegistration->ngo_org_phone = $validatedData['ngo_org_phone'];
        $NgoRegistration->ngo_org_website = $validatedData['ngo_org_website'];
        $NgoRegistration->ngo_registered_with = $validatedData['ngo_registered_with'];
        $NgoRegistration->ngo_other_reg_act = $validatedData['ngo_other_reg_act'];
        $NgoRegistration->ngo_type_of_vo_or_ngo = $validatedData['ngo_type_of_vo_or_ngo'];
        $NgoRegistration->ngo_reg_no = $validatedData['ngo_reg_no'];
        $NgoRegistration->ngo_system_gen_reg_no = $ngoSystemGeneratedRegNo;
        $NgoRegistration->ngo_file_rc = $rcStoredPath;
        $NgoRegistration->ngo_date_of_registration = $validatedData['ngo_date_of_registration'];
        $NgoRegistration->ngo_date_of_registration_validity = $validatedData['ngo_date_of_registration_validity'];
        $NgoRegistration->nature_of_organisation = $natureOfOrganisation;
        $NgoRegistration->nature_of_organisation_other = $request->input('nature_of_organisation_other');
        $NgoRegistration->ngo_organisation_type = $validatedData['ngo_organisation_type'];
        $NgoRegistration->ngo_file_byelaws = $byelawsStoredPath;
        $NgoRegistration->ngo_parent_organisation = $request->input('ngo_parent_organisation');
        $NgoRegistration->ngo_reg_velidity_available = $validatedData['ngo_reg_velidity_available'];
        $NgoRegistration->ngo_address_type = $validatedData['ngo_address_type'];
        $NgoRegistration->state_id = $validatedData['state'];
        $NgoRegistration->district_id = $validatedData['district'];
        $NgoRegistration->block_id = $request->input('block', null);
        $NgoRegistration->gp_id = $request->input('grampanchayat', null);
        $NgoRegistration->village_id = $request->input('village', null);
        $NgoRegistration->municipality_id = $request->input('municipality', null);
        $NgoRegistration->pin = $validatedData['pin'];
        $NgoRegistration->ngo_postal_address_at = $validatedData['ngo_postal_address_at'];
        $NgoRegistration->ngo_postal_address_post = $validatedData['ngo_postal_address_post'];
        $NgoRegistration->ngo_postal_address_via = $validatedData['ngo_postal_address_via'];
        $NgoRegistration->ngo_postal_address_ps = $validatedData['ngo_postal_address_ps'];
        $NgoRegistration->ngo_postal_address_district = $validatedData['ngo_postal_address_district'];
        $NgoRegistration->ngo_postal_address_pin = $validatedData['ngo_postal_address_pin'];
        $NgoRegistration->is_active = 'active';
        $NgoRegistration->created_date = $ngo_tbl_records->created_date;
        $NgoRegistration->created_time = $ngo_tbl_records->created_time;
        $NgoRegistration->created_by = Auth::id() ?? null;
        $NgoRegistration->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $NgoRegistration->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $NgoRegistration->updated_by = Auth::id() ?? null;
        $NgoRegistration->application_stage_id = 1;
        $NgoRegistration->no_of_form_completed = $ngo_tbl_records->no_of_form_completed < 1 ? 1 : $ngo_tbl_records->no_of_form_completed;
        $NgoRegistration->user_table_id = $ngo_tbl_records->user_table_id;
        $NgoRegistration->status = 1;
        $NgoRegistration->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoRegistration';
        $applicationstagehistory->model_table_id = $NgoRegistration->id;
        $applicationstagehistory->initial_model_table_id = $NgoRegistration->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 1st Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();

        $ngoData = [
            'ngoRegdPhase' => 'first',
            'registrationNumber' => $ngoSystemGeneratedRegNo,
            'ngoOrgName' => $validatedData['ngo_org_name'],
            'ngoOrgEmail' => $validatedData['ngo_org_email'],
            'ngoOrgPhone' => $validatedData['ngo_org_phone'],
            'ngoOrgWebsite' => $validatedData['ngo_org_website'],
            'ngoRegNo' => $validatedData['ngo_reg_no'],
            'ngoDateOfRegistration' => $validatedData['ngo_date_of_registration'],
            'ngoUserId' => $finalGeneratedCode,
            'ngoUserPassword' => '123456',
        ];
        Mail::to($validatedData['ngo_org_email'])->send(new NgoRegistrationMail($ngoData));
        return redirect()->route('admin.ngo.index')->with('success', 'NGO 1st step registration successful.')->with('addressMessage', $addressMessage);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("NGO Initial Part One Registration failed: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}

public function part_two_store(Request $request)
{
    $validationRules = [
        'office_bearer_name' => 'required|string|max:255',
        'office_bearer_gender' => 'required|in:1,2,3',
        'office_bearer_email' => 'required|email|max:255',
        'office_bearer_phone' => 'required|digits:10',
        'office_bearer_designation' => 'required|string|max:255',
        'office_bearer_key_designation' => 'required|string|max:255',
        'office_bearer_date_of_association' => 'required|date',
        'office_bearer_pan' => 'required|alpha_num|size:10',
        'office_bearer_pan_file' => 'required|file|mimes:pdf|max:2048',
        'office_bearer_name_as_aadhar' => 'required|string|max:255',
        'office_bearer_dob' => 'required|date',
        'office_bearer_aadhar' => 'required|numeric|min:12',
        'office_bearer_aadhar_file' => 'required|file|mimes:pdf|max:2048',
        'want_to_add_another_bearer' => 'required|in:1,2',
    ];
    $validatedData = $request->validate($validationRules);
    DB::beginTransaction();
    try {

        $addressMessage = '';
        $ngo_tbl_records = NgoRegistration::findOrFail($request->id);
        $ngoSystemGenRegNo = str_replace('/', '_', $ngo_tbl_records->ngo_system_gen_reg_no);
        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('office_bearer_pan_file')) {
            $panFile = $request->file('office_bearer_pan_file');
            $panExtension = $panFile->getClientOriginalExtension();
            $panRandomName = 'OFFICE_BEARER_PAN_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $panExtension;

            $ngoBearerPanFilePath = $panFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $panRandomName, 'public');
            copy(storage_path("app/public/{$ngoBearerPanFilePath}"), "{$folderPath}/{$panRandomName}");
            copy(storage_path("app/public/{$ngoBearerPanFilePath}"), "{$externalPath}/{$panRandomName}");
        }

        if ($request->hasFile('office_bearer_aadhar_file')) {
            $rcFile = $request->file('office_bearer_aadhar_file');
            $rcExtension = $rcFile->getClientOriginalExtension();
            $rcRandomName = 'OFFICE_BEARER_AADHAR_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $rcExtension;

            $ngoBearerFileAadharPath = $rcFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $rcRandomName, 'public');
            copy(storage_path("app/public/{$ngoBearerFileAadharPath}"), "{$folderPath}/{$rcRandomName}");
            copy(storage_path("app/public/{$ngoBearerFileAadharPath}"), "{$externalPath}/{$rcRandomName}");
        }

        $NgoPartTwoOfficeBearer = new NgoPartTwoOfficeBearer();
        $NgoPartTwoOfficeBearer->ngo_org_id = $ngo_tbl_records->ngo_org_id;
        $NgoPartTwoOfficeBearer->ngo_tbl_id = $ngo_tbl_records->id;
        $NgoPartTwoOfficeBearer->ngo_system_gen_reg_no = $ngo_tbl_records->ngo_system_gen_reg_no;
        $NgoPartTwoOfficeBearer->office_bearer_name = $validatedData['office_bearer_name'];
        $NgoPartTwoOfficeBearer->office_bearer_gender = $validatedData['office_bearer_gender'];
        $NgoPartTwoOfficeBearer->office_bearer_email = $validatedData['office_bearer_email'];
        $NgoPartTwoOfficeBearer->office_bearer_phone = $validatedData['office_bearer_phone'];
        $NgoPartTwoOfficeBearer->office_bearer_designation = $validatedData['office_bearer_designation'];
        $NgoPartTwoOfficeBearer->office_bearer_key_designation = $validatedData['office_bearer_key_designation'];
        $NgoPartTwoOfficeBearer->office_bearer_date_of_association = $validatedData['office_bearer_date_of_association'];
        $NgoPartTwoOfficeBearer->office_bearer_pan = $validatedData['office_bearer_pan'];
        $NgoPartTwoOfficeBearer->office_bearer_pan_file = $ngoBearerPanFilePath;
        $NgoPartTwoOfficeBearer->office_bearer_name_as_aadhar = $validatedData['office_bearer_name_as_aadhar'];
        $NgoPartTwoOfficeBearer->office_bearer_dob = $validatedData['office_bearer_dob'];
        $NgoPartTwoOfficeBearer->office_bearer_aadhar = $validatedData['office_bearer_aadhar'];
        $NgoPartTwoOfficeBearer->office_bearer_aadhar_file = $ngoBearerFileAadharPath;
        $NgoPartTwoOfficeBearer->want_to_add_another_bearer = $validatedData['want_to_add_another_bearer'];
        $NgoPartTwoOfficeBearer->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $NgoPartTwoOfficeBearer->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $NgoPartTwoOfficeBearer->created_by = Auth::id();
        $NgoPartTwoOfficeBearer->status = 1;
        $NgoPartTwoOfficeBearer->save();

        $row_counts = NgoPartTwoOfficeBearer::where('ngo_tbl_id', $ngo_tbl_records->id)->count();
        $formCompleted = $ngo_tbl_records->no_of_form_completed;

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartTwoOfficeBearer';
        $applicationstagehistory->model_table_id = $NgoPartTwoOfficeBearer->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 2nd Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();

        if ($row_counts < 3) {
            return redirect()->route('admin.ngo.continue_application', [
                'id' => $ngo_tbl_records->id, 
                'no_of_form_completed' => 1
            ])->with('warning', 'At least 3 Designatory required.');
        } elseif ($row_counts >= 3 && $NgoPartTwoOfficeBearer->want_to_add_another_bearer == 1) {
            return redirect()->route('admin.ngo.continue_application', [
                'id' => $ngo_tbl_records->id, 
                'no_of_form_completed' => 1
            ])->with('info', 'Please add remaining office bearer(s).');
        } elseif ($row_counts >= 3 && $NgoPartTwoOfficeBearer->want_to_add_another_bearer == 2) {
            $ngo_tbl_records->no_of_form_completed = $ngo_tbl_records->no_of_form_completed < 2 ? 2 : $ngo_tbl_records->no_of_form_completed;
            /*$ngo_tbl_records->no_of_form_completed = 2;*/
            $ngo_tbl_records->save();

            $ngoUserId = User::whereId($ngo_tbl_records->user_table_id)->value('user_id');
            $ngoData = [
                'ngoRegdPhase' => 'second',
                'registrationNumber' => $ngo_tbl_records->ngo_system_gen_reg_no,
                'ngoOrgName' => $ngo_tbl_records->ngo_org_name,
                'ngoOrgEmail' => $ngo_tbl_records->ngo_org_email,
                'ngoOrgPhone' => $ngo_tbl_records->ngo_org_phone,
                'ngoOrgWebsite' => $ngo_tbl_records->ngo_org_website,
                'ngoRegNo' => $ngo_tbl_records->ngo_reg_no,
                'ngoDateOfRegistration' => $ngo_tbl_records->ngo_date_of_registration,
                'ngoUserId' => $ngoUserId,
                'ngoUserPassword' => '123456',
            ];
            Mail::to($ngo_tbl_records->ngo_org_email)->send(new NgoRegistrationMail($ngoData));
            return redirect()->route('admin.ngo.index')->with('success', 'NGO 2nd step registration successful.')->with('addressMessage', $addressMessage);
        } else {
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
        }
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("NGO Part Two Registration failed: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}

public function part_three_store(Request $request)
{
    $validated = $request->validate([
        ...collect(range(1, 9))->mapWithKeys(function ($i) {
            $word = [
                1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
                6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine'
            ][$i];
            return [
                "authority_{$word}" => 'nullable|string',
                "regd_no_{$word}" => 'nullable|string',
                "regd_date_{$word}" => 'nullable|date',
                "validity_date_{$word}" => "nullable|date|after:regd_date_{$word}",
                "regd_certificate_file_{$word}" => 'nullable|file|mimes:pdf|max:1024',
            ];
        })->toArray(),

        'authority_other_act' => 'nullable|string',
        'authority_other' => 'nullable|string',
        'regd_no_other' => 'nullable|string',
        'regd_date_other' => 'nullable|date',
        'validity_date_other' => 'nullable|date|after:regd_date_other',
        'regd_certificate_file_other' => 'nullable|file|mimes:pdf|max:1024',
    ]);

    $groups = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'other'];
    $atLeastOneFilled = collect($groups)->contains(function ($group) use ($request) {
        return $request->filled("authority_{$group}") ||
        $request->filled("regd_no_{$group}") ||
        $request->filled("regd_date_{$group}") ||
        $request->filled("validity_date_{$group}") ||
        $request->hasFile("regd_certificate_file_{$group}");
    });

    if (!$atLeastOneFilled) {
        return redirect()->back()
        ->withErrors(['at_least_one' => 'Please fill at least one ACT before submitting!'])
        ->withInput();
    }

    DB::beginTransaction();

    try {

        $addressMessage = '';

        $ngo_tbl_records = NgoRegistration::findOrFail($request->id);
        $ngoSystemGenRegNo = str_replace('/', '_', $ngo_tbl_records->ngo_system_gen_reg_no);
        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        $externalPath = dirname(base_path()) . "/storage/ngo_files/{$ngoSystemGenRegNo}";

        if (!file_exists($folderPath)) mkdir($folderPath, 0755, true);
        if (!file_exists($externalPath)) mkdir($externalPath, 0755, true);

        $numberWords = ['one','two','three','four','five','six','seven','eight','nine'];
        $fileNames = [];
        $filePaths = [];

        foreach ($numberWords as $word) {
            $fieldKey = "regd_certificate_file_{$word}";
            if ($request->hasFile($fieldKey)) {
                $file = $request->file($fieldKey);
                $extension = $file->getClientOriginalExtension();
                $fileName = "ngo_regd_certificate_file_{$word}_" . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $extension;

                $storagePath = $file->storeAs("ngo_files/{$ngoSystemGenRegNo}", $fileName, 'public');

                copy(storage_path("app/public/{$storagePath}"), "{$folderPath}/{$fileName}");
                copy(storage_path("app/public/{$storagePath}"), "{$externalPath}/{$fileName}");

                $fileNames[$word] = $fileName;
                $filePaths[$word] = "ngo_files/{$ngoSystemGenRegNo}/{$fileName}";
            }
        }

        $fileOtherName = null;
        $fileOtherPath = null;
        if ($request->hasFile('regd_certificate_file_other')) {
            $file = $request->file('regd_certificate_file_other');
            $extension = $file->getClientOriginalExtension();
            $fileOtherName = "ngo_regd_certificate_file_other_" . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $extension;

            $storagePath = $file->storeAs("ngo_files/{$ngoSystemGenRegNo}", $fileOtherName, 'public');

            copy(storage_path("app/public/{$storagePath}"), "{$folderPath}/{$fileOtherName}");
            copy(storage_path("app/public/{$storagePath}"), "{$externalPath}/{$fileOtherName}");

            $fileOtherPath = "ngo_files/{$ngoSystemGenRegNo}/{$fileOtherName}";
        }

        $NgoPartThreeActRegistration = new NgoPartThreeActRegistration();
        $NgoPartThreeActRegistration->ngo_org_id = $ngo_tbl_records->ngo_org_id;
        $NgoPartThreeActRegistration->ngo_tbl_id = $ngo_tbl_records->id;
        $NgoPartThreeActRegistration->ngo_system_gen_reg_no = $ngo_tbl_records->ngo_system_gen_reg_no;

        foreach ($numberWords as $word) {
            $NgoPartThreeActRegistration->{"authority_{$word}"} = $request->input("authority_{$word}");
            $NgoPartThreeActRegistration->{"regd_no_{$word}"} = $request->input("regd_no_{$word}");
            $NgoPartThreeActRegistration->{"regd_date_{$word}"} = $request->input("regd_date_{$word}");
            $NgoPartThreeActRegistration->{"validity_date_{$word}"} = $request->input("validity_date_{$word}");
            $NgoPartThreeActRegistration->{"regd_certificate_file_{$word}"} = $fileNames[$word] ?? null;
            $NgoPartThreeActRegistration->{"regd_certificate_file_{$word}_path"} = $filePaths[$word] ?? null;
        }

        $NgoPartThreeActRegistration->authority_other_act = $request->authority_other_act;
        $NgoPartThreeActRegistration->authority_other = $request->authority_other;
        $NgoPartThreeActRegistration->regd_no_other = $request->regd_no_other;
        $NgoPartThreeActRegistration->regd_date_other = $request->regd_date_other;
        $NgoPartThreeActRegistration->validity_date_other = $request->validity_date_other;
        $NgoPartThreeActRegistration->regd_certificate_file_other = $fileOtherName;
        $NgoPartThreeActRegistration->regd_certificate_file_other_path = $fileOtherPath;

        $NgoPartThreeActRegistration->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $NgoPartThreeActRegistration->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $NgoPartThreeActRegistration->created_by = Auth::id();
        $NgoPartThreeActRegistration->status = 1;

        $NgoPartThreeActRegistration->save();

        /*$ngo_tbl_records->no_of_form_completed = 3;*/
        $ngo_tbl_records->no_of_form_completed = $ngo_tbl_records->no_of_form_completed < 3 ? 3 : $ngo_tbl_records->no_of_form_completed;
        $ngo_tbl_records->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartThreeActRegistration';
        $applicationstagehistory->model_table_id = $NgoPartThreeActRegistration->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 3rd Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();

        $ngoUserId = User::whereId($ngo_tbl_records->user_table_id)->value('user_id');
        $ngoData = [
            'ngoRegdPhase' => 'third',
            'registrationNumber' => $NgoPartThreeActRegistration->ngo_system_gen_reg_no,
            'ngoOrgName' => $ngo_tbl_records->ngo_org_name,
            'ngoOrgEmail' => $ngo_tbl_records->ngo_org_email,
            'ngoOrgPhone' => $ngo_tbl_records->ngo_org_phone,
            'ngoOrgWebsite' => $ngo_tbl_records->ngo_org_website,
            'ngoRegNo' => $ngo_tbl_records->ngo_reg_no,
            'ngoDateOfRegistration' => $ngo_tbl_records->ngo_date_of_registration,
            'ngoUserId' => $ngoUserId,
            'ngoUserPassword' => '123456',
        ];
        Mail::to($ngo_tbl_records->ngo_org_email)->send(new NgoRegistrationMail($ngoData));
        return redirect()->route('admin.ngo.index')->with('success', 'NGO 3rd step registration successful.')->with('addressMessage', $addressMessage);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong. Please try again.')->withInput();
    }
}

public function part_four_store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'project_title.*'        => 'required|string|max:255',
        'approving_authority.*' => 'required|string|max:255',
        'date_of_approval.*'    => 'required|date|before_or_equal:today',
        'project_location.*'    => 'required|string|max:500',
        'no_of_beneficiaries.*' => 'required|integer|min:1',
        'project_cost.*'        => 'required|numeric|min:1',
        'current_status.*'      => 'required|string|max:255',

        'staff_name.*'             => 'required|string|max:255',
        'staff_designation.*'      => 'required|string|max:255',
        'staff_role.*'             => 'required|string|max:255',
        'staff_category.*'         => 'required|string|max:255',
        'staff_category_type.*'    => 'required|string|max:255',
        'staff_qualification.*'    => 'required|string|max:255',
        'staff_date_of_joining.*'  => 'required|date|before_or_equal:today',
        'staff_mob_no.*'           => ['required', 'regex:/^[6-9]\d{9}$/'],
        'staff_aadhar_number.*' => ['required', 'digits:12', 'regex:/^\d{12}$/', 'distinct',
        function ($attribute, $value, $fail) {
            $verifier = new AadhaarVerifier();
            if (!$verifier->validateVerhoeff($value)) {
                $fail("The Aadhaar number {$value} is invalid.");
            }

            if (NgoPartFourTrainedStaff::where('staff_aadhar_number', $value)->exists()) {
                $fail('The Aadhaar number ' . $value . ' already exists.');
            }
        },],
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $addressMessage = '';

    DB::beginTransaction();
    try {
        $ngo_tbl_records = NgoRegistration::findOrFail($request->id);
        $count = count($request->project_title);
        $lastNgoPartFourOtherRecognition = null;
        for ($i = 0; $i < $count; $i++) {
            $lastNgoPartFourOtherRecognition = NgoPartFourOtherRecognition::create([
                'ngo_org_id'          => $ngo_tbl_records->ngo_org_id,
                'ngo_tbl_id'          => $ngo_tbl_records->id,
                'ngo_system_gen_reg_no' => $ngo_tbl_records->ngo_system_gen_reg_no,
                'project_title'       => $request->project_title[$i],
                'approving_authority' => $request->approving_authority[$i],
                'date_of_approval'    => $request->date_of_approval[$i],
                'project_location'    => $request->project_location[$i],
                'no_of_beneficiaries' => $request->no_of_beneficiaries[$i],
                'project_cost'        => $request->project_cost[$i],
                'current_status'      => $request->current_status[$i],
                'created_date'        => now()->setTimezone('Asia/Kolkata')->toDateString(),
                'created_time'        => now()->setTimezone('Asia/Kolkata')->toTimeString(),
                'created_by'          => Auth::id(),
            ]);
        }

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartFourOtherRecognition';
        $applicationstagehistory->model_table_id = $lastNgoPartFourOtherRecognition->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 4th Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        $staffCount = count($request->staff_name);
        $lastNgoPartFourTrainedStaff = null;
        for ($i = 0; $i < $staffCount; $i++) {
            $lastNgoPartFourTrainedStaff = NgoPartFourTrainedStaff::create([
                'ngo_org_id'          => $ngo_tbl_records->ngo_org_id,
                'ngo_tbl_id'          => $ngo_tbl_records->id,
                'ngo_system_gen_reg_no' => $ngo_tbl_records->ngo_system_gen_reg_no,
                'staff_name'          => $request->staff_name[$i],
                'staff_designation'   => $request->staff_designation[$i],
                'staff_role'          => $request->staff_role[$i],
                'staff_category'      => $request->staff_category[$i],
                'staff_category_type' => $request->staff_category_type[$i],
                'staff_qualification' => $request->staff_qualification[$i],
                'staff_date_of_joining' => $request->staff_date_of_joining[$i],
                'staff_aadhar_number' => $request->staff_aadhar_number[$i],
                'staff_mob_no'        => $request->staff_mob_no[$i],
                'created_date'        => now()->setTimezone('Asia/Kolkata')->toDateString(),
                'created_time'        => now()->setTimezone('Asia/Kolkata')->toTimeString(),
                'created_by'          => Auth::id(),
            ]);
        }

        /*$ngo_tbl_records->no_of_form_completed = 4;*/
        $ngo_tbl_records->no_of_form_completed = $ngo_tbl_records->no_of_form_completed < 4 ? 4 : $ngo_tbl_records->no_of_form_completed;
        $ngo_tbl_records->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartFourTrainedStaff';
        $applicationstagehistory->model_table_id = $lastNgoPartFourTrainedStaff->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 4th Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();
        DB::commit();

        $ngoUserId = User::whereId($ngo_tbl_records->user_table_id)->value('user_id');
        $ngoData = [
            'ngoRegdPhase' => 'fourth',
            'registrationNumber' => $ngo_tbl_records->ngo_system_gen_reg_no,
            'ngoOrgName' => $ngo_tbl_records->ngo_org_name,
            'ngoOrgEmail' => $ngo_tbl_records->ngo_org_email,
            'ngoOrgPhone' => $ngo_tbl_records->ngo_org_phone,
            'ngoOrgWebsite' => $ngo_tbl_records->ngo_org_website,
            'ngoRegNo' => $ngo_tbl_records->ngo_reg_no,
            'ngoDateOfRegistration' => $ngo_tbl_records->ngo_date_of_registration,
            'ngoUserId' => $ngoUserId,
            'ngoUserPassword' => '123456',
        ];
        Mail::to($ngo_tbl_records->ngo_org_email)->send(new NgoRegistrationMail($ngoData));
        return redirect()->route('admin.ngo.index')->with('success', 'NGO 4th step registration successful.')->with('addressMessage', $addressMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong. Please try again.')->withInput();
    }
}

public function part_five_store(Request $request)
{
    $request->validate([
        'ngo_org_beneficiaries_excel_file' => 'required|file|mimes:xlsx,xls,csv|max:38000',
    ], [
        'ngo_org_beneficiaries_excel_file.required' => 'Please upload the updated CSV beneficiaries file.',
        'ngo_org_beneficiaries_excel_file.file' => 'The uploaded file must be a valid file.',
        'ngo_org_beneficiaries_excel_file.mimes' => 'Only Excel or CSV files are allowed (xlsx, xls, csv).',
        'ngo_org_beneficiaries_excel_file.max' => 'The file size must not exceed 5MB.',
    ]);

    DB::beginTransaction();
    try {
        $ngo_tbl_records = NgoRegistration::findOrFail($request->id);

        Excel::import(
            new NgoPartFiveListOfBeneficiaryImport($ngo_tbl_records),
            $request->file('ngo_org_beneficiaries_excel_file')
        );

        /*$ngo_tbl_records->no_of_form_completed = 5;*/
        $ngo_tbl_records->no_of_form_completed = $ngo_tbl_records->no_of_form_completed < 5 ? 5 : $ngo_tbl_records->no_of_form_completed;
        $ngo_tbl_records->save();

        $lastNgoPartFiveListOfBeneficiaryImport = NgoPartFiveListOfBeneficiary::where('ngo_tbl_id', $ngo_tbl_records->id)->latest('id')->first();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartFiveListOfBeneficiaryImport';
        $applicationstagehistory->model_table_id = $lastNgoPartFiveListOfBeneficiaryImport?->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 5th Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();

        $ngoSystemGenRegNo = str_replace('/', '_', $ngo_tbl_records->ngo_system_gen_reg_no);
        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('ngo_org_beneficiaries_excel_file')) {
            $benfFile = $request->file('ngo_org_beneficiaries_excel_file');
            $ngoBenfExtension = $benfFile->getClientOriginalExtension();
            $ngoBenfRandomName = 'NGO_BENEFICIARIES_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $ngoBenfExtension;

            $ngoBearerPanFilePath = $benfFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $ngoBenfRandomName, 'public');
            copy(storage_path("app/public/{$ngoBearerPanFilePath}"), "{$folderPath}/{$ngoBenfRandomName}");
            copy(storage_path("app/public/{$ngoBearerPanFilePath}"), "{$externalPath}/{$ngoBenfRandomName}");
        }

        $ngo_tbl_records->ngo_file_beneficiary = $ngoBearerPanFilePath;
        $ngo_tbl_records->save();

        $ngoUserId = User::whereId($ngo_tbl_records->user_table_id)->value('user_id');
        $ngoData = [
            'ngoRegdPhase' => 'fifth',
            'registrationNumber' => $ngo_tbl_records->ngo_system_gen_reg_no,
            'ngoOrgName' => $ngo_tbl_records->ngo_org_name,
            'ngoOrgEmail' => $ngo_tbl_records->ngo_org_email,
            'ngoOrgPhone' => $ngo_tbl_records->ngo_org_phone,
            'ngoOrgWebsite' => $ngo_tbl_records->ngo_org_website,
            'ngoRegNo' => $ngo_tbl_records->ngo_reg_no,
            'ngoDateOfRegistration' => $ngo_tbl_records->ngo_date_of_registration,
            'ngoUserId' => $ngoUserId,
            'ngoUserPassword' => '123456',
        ];

        Mail::to($ngo_tbl_records->ngo_org_email)->send(new NgoRegistrationMail($ngoData));
        return redirect()->route('admin.ngo.index')->with('success', 'NGO 5th step registration successful.');
    } catch (ExcelValidationException $e) {
        DB::rollBack();
        $failures = $e->failures();
        $messages = [];
        foreach ($failures as $failure) {
            $row = $failure->row();
            $errors = implode(', ', $failure->errors());
            $messages[] = "Row {$row}: {$errors}";
        }
        return back()->withErrors($messages)->withInput();
    } catch (LaravelValidationException $e) {
        DB::rollBack();
        throw $e;
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Something went wrong. Please try again.')->withInput();
    }
}

public function part_six_store(Request $request)
{
    $rules = [
        'land_no_of_unit' => 'required|string|max:255',
        'land_permanent_or_rental' => 'required|in:1,2',
        'land_no_of_unit_file' => 'required|file|mimes:pdf|max:2048',
        'building_no_of_unit' => 'required|string|max:255',
        'building_permanent_or_rental' => 'required|in:1,2',
        'building_no_of_unit_file' => 'required|file|mimes:pdf|max:2048',
        'vehicles_no_of_unit' => 'required|string|max:255',
        'vehicles_permanent_or_rental' => 'required|in:1,2',
        'vehicles_no_of_unit_file' => 'required|file|mimes:pdf|max:2048',
        'equipment_no_of_unit' => 'required|string|max:255',
        'equipment_permanent_or_rental' => 'required|in:1,2',
        'equipment_no_of_unit_file' => 'required|file|mimes:pdf|max:2048',
        'others_no_of_unit' => 'nullable|string|max:255',
        'others_permanent_or_rental' => 'nullable|in:1,2',
        'others_no_of_unit_file' => 'nullable|file|mimes:pdf|max:2048',
        'financial_status_financial_year_1' => 'required|string|max:255',
        'financial_status_receipt_price_1' => 'required|numeric',
        'financial_status_payment_1' => 'required|numeric',
        'financial_status_surplus_1' => 'required|numeric',
        'financial_status_audit_file_1' => 'required|file|mimes:pdf|max:2048',
        'financial_status_it_return_file_1' => 'required|file|mimes:pdf|max:2048',
        'financial_status_financial_year_2' => 'required|string|max:255',
        'financial_status_receipt_price_2' => 'required|numeric',
        'financial_status_payment_2' => 'required|numeric',
        'financial_status_surplus_2' => 'required|numeric',
        'financial_status_audit_file_2' => 'required|file|mimes:pdf|max:2048',
        'financial_status_it_return_file_2' => 'required|file|mimes:pdf|max:2048',
        'financial_status_financial_year_3' => 'required|string|max:255',
        'financial_status_receipt_price_3' => 'required|numeric',
        'financial_status_payment_3' => 'required|numeric',
        'financial_status_surplus_3' => 'required|numeric',
        'financial_status_audit_file_3' => 'required|file|mimes:pdf|max:2048',
        'financial_status_it_return_file_3' => 'required|file|mimes:pdf|max:2048',
        'bank_account_type_1' => 'required|in:1,2',
        'bank_account_holder_name_1' => 'required|string|max:255',
        'bank_account_holder_name_2' => 'required|string|max:255',
        'bank_account_number' => 'required|numeric',
        'ifsc_code' => 'required',
        'bank_account_file' => 'required|file|mimes:pdf|max:2048',
        'ngo_additional_docs_file' => 'nullable|file|mimes:pdf|max:5120',
    ];

    $messages = [
        '*.required' => 'The :attribute field is required.',
        '*.string' => 'The :attribute must be a valid text.',
        '*.numeric' => 'The :attribute must be a valid number.',
        '*.in' => 'Please select a valid option for :attribute.',
        '*.file' => 'The :attribute must be a file.',
        '*.mimes' => 'Only PDF files are allowed for :attribute.',
        '*.max' => 'The :attribute may not be greater than 2MB.',
    ];

    $attributes = [
        'land_no_of_unit' => 'number of land units',
        'land_permanent_or_rental' => 'land type',
        'land_no_of_unit_file' => 'land document',
        'building_no_of_unit' => 'number of building units',
        'building_permanent_or_rental' => 'building type',
        'building_no_of_unit_file' => 'building document',
        'vehicles_no_of_unit' => 'number of vehicles',
        'vehicles_permanent_or_rental' => 'vehicle type',
        'vehicles_no_of_unit_file' => 'vehicle document',
        'equipment_no_of_unit' => 'number of equipment items',
        'equipment_permanent_or_rental' => 'equipment type',
        'equipment_no_of_unit_file' => 'equipment document',
        'others_no_of_unit' => 'other units',
        'others_permanent_or_rental' => 'other type',
        'others_no_of_unit_file' => 'other document',
        'financial_status_financial_year_1' => 'financial year 1',
        'financial_status_receipt_price_1' => 'receipt amount (year 1)',
        'financial_status_payment_1' => 'payment amount (year 1)',
        'financial_status_surplus_1' => 'surplus (year 1)',
        'financial_status_audit_file_1' => 'audit file (year 1)',
        'financial_status_it_return_file_1' => 'IT return file (year 1)',
        'financial_status_financial_year_2' => 'financial year 2',
        'financial_status_receipt_price_2' => 'receipt amount (year 2)',
        'financial_status_payment_2' => 'payment amount (year 2)',
        'financial_status_surplus_2' => 'surplus (year 2)',
        'financial_status_audit_file_2' => 'audit file (year 2)',
        'financial_status_it_return_file_2' => 'IT return file (year 2)',
        'financial_status_financial_year_3' => 'financial year 3',
        'financial_status_receipt_price_3' => 'receipt amount (year 3)',
        'financial_status_payment_3' => 'payment amount (year 3)',
        'financial_status_surplus_3' => 'surplus (year 3)',
        'financial_status_audit_file_3' => 'audit file (year 3)',
        'financial_status_it_return_file_3' => 'IT return file (year 3)',
        'bank_account_type_1' => 'bank account type',
        'bank_account_holder_name_1' => 'first account holder name',
        'bank_account_holder_name_2' => 'second account holder name',
        'bank_account_number' => 'bank account number',
        'ifsc_code' => 'ifsc code',
        'bank_account_file' => 'bank account soft copy pdf',
    ];

    $validatedData = $request->validate($rules, $messages, $attributes);
    DB::beginTransaction();
    try{
        $addressMessage = '';
        $ngo_tbl_records = NgoRegistration::findOrFail($request->id);
        $ngoSystemGenRegNo = str_replace('/', '_', $ngo_tbl_records->ngo_system_gen_reg_no);
        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";
        if (!file_exists($folderPath)) { mkdir($folderPath, 0755, true); }
        if (!file_exists($externalPath)) { mkdir($externalPath, 0755, true); }

        if ($request->hasFile('land_no_of_unit_file')) {
            $landFile = $request->file('land_no_of_unit_file');
            $landExtension = $landFile->getClientOriginalExtension();
            $landRandomName = 'land_no_of_unit_file_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $landExtension;

            $landFilePath = $landFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $landRandomName, 'public');
            copy(storage_path("app/public/{$landFilePath}"), "{$folderPath}/{$landRandomName}");
            copy(storage_path("app/public/{$landFilePath}"), "{$externalPath}/{$landRandomName}");
        }

        if ($request->hasFile('building_no_of_unit_file')) {
            $buildingFile = $request->file('building_no_of_unit_file');
            $buildingExtension = $buildingFile->getClientOriginalExtension();
            $buildingRandomName = 'building_no_of_unit_file_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $buildingExtension;

            $buildingFilePath = $buildingFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $buildingRandomName, 'public');
            copy(storage_path("app/public/{$buildingFilePath}"), "{$folderPath}/{$buildingRandomName}");
            copy(storage_path("app/public/{$buildingFilePath}"), "{$externalPath}/{$buildingRandomName}");
        }

        if ($request->hasFile('vehicles_no_of_unit_file')) {
            $vehiclesFile = $request->file('vehicles_no_of_unit_file');
            $vehiclesExtension = $vehiclesFile->getClientOriginalExtension();
            $vehiclesRandomName = 'vehicles_no_of_unit_file_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $vehiclesExtension;

            $vehiclesFilePath = $vehiclesFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $vehiclesRandomName, 'public');
            copy(storage_path("app/public/{$vehiclesFilePath}"), "{$folderPath}/{$vehiclesRandomName}");
            copy(storage_path("app/public/{$vehiclesFilePath}"), "{$externalPath}/{$vehiclesRandomName}");
        }

        if ($request->hasFile('equipment_no_of_unit_file')) {
            $equipmentFile = $request->file('equipment_no_of_unit_file');
            $equipmentExtension = $equipmentFile->getClientOriginalExtension();
            $equipmentRandomName = 'equipment_no_of_unit_file_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $equipmentExtension;

            $equipmentFilePath = $equipmentFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $equipmentRandomName, 'public');
            copy(storage_path("app/public/{$equipmentFilePath}"), "{$folderPath}/{$equipmentRandomName}");
            copy(storage_path("app/public/{$equipmentFilePath}"), "{$externalPath}/{$equipmentRandomName}");
        }

        if ($request->hasFile('others_no_of_unit_file')) {
            $othersFile = $request->file('others_no_of_unit_file');
            $othersExtension = $othersFile->getClientOriginalExtension();
            $othersRandomName = 'others_no_of_unit_file_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $othersExtension;

            $othersFilePath = $othersFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $othersRandomName, 'public');
            copy(storage_path("app/public/{$othersFilePath}"), "{$folderPath}/{$othersRandomName}");
            copy(storage_path("app/public/{$othersFilePath}"), "{$externalPath}/{$othersRandomName}");
        }

        if ($request->hasFile('financial_status_audit_file_1')) {
            $auditFileOneFile = $request->file('financial_status_audit_file_1');
            $auditFileOneExtension = $auditFileOneFile->getClientOriginalExtension();
            $auditFileOneRandomName = 'financial_status_audit_file_1_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $auditFileOneExtension;

            $auditFileOneFilePath = $auditFileOneFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $auditFileOneRandomName, 'public');
            copy(storage_path("app/public/{$auditFileOneFilePath}"), "{$folderPath}/{$auditFileOneRandomName}");
            copy(storage_path("app/public/{$auditFileOneFilePath}"), "{$externalPath}/{$auditFileOneRandomName}");
        }

        if ($request->hasFile('financial_status_it_return_file_1')) {
            $itReturnOneFile = $request->file('financial_status_it_return_file_1');
            $itReturnOneExtension = $itReturnOneFile->getClientOriginalExtension();
            $itReturnOneRandomName = 'financial_status_it_return_file_1_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $itReturnOneExtension;

            $itReturnOneFilePath = $itReturnOneFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $itReturnOneRandomName, 'public');
            copy(storage_path("app/public/{$itReturnOneFilePath}"), "{$folderPath}/{$itReturnOneRandomName}");
            copy(storage_path("app/public/{$itReturnOneFilePath}"), "{$externalPath}/{$itReturnOneRandomName}");
        }

        if ($request->hasFile('financial_status_audit_file_2')) {
            $auditFileTwoFile = $request->file('financial_status_audit_file_2');
            $auditFileTwoExtension = $auditFileTwoFile->getClientOriginalExtension();
            $auditFileTwoRandomName = 'financial_status_audit_file_2_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $auditFileTwoExtension;

            $auditFileTwoFilePath = $auditFileTwoFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $auditFileTwoRandomName, 'public');
            copy(storage_path("app/public/{$auditFileTwoFilePath}"), "{$folderPath}/{$auditFileTwoRandomName}");
            copy(storage_path("app/public/{$auditFileTwoFilePath}"), "{$externalPath}/{$auditFileTwoRandomName}");
        }

        if ($request->hasFile('financial_status_it_return_file_2')) {
            $itReturnTwoFile = $request->file('financial_status_it_return_file_2');
            $itReturnTwoExtension = $itReturnTwoFile->getClientOriginalExtension();
            $itReturnTwoRandomName = 'financial_status_it_return_file_2_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $itReturnTwoExtension;

            $itReturnTwoFilePath = $itReturnTwoFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $itReturnTwoRandomName, 'public');
            copy(storage_path("app/public/{$itReturnTwoFilePath}"), "{$folderPath}/{$itReturnTwoRandomName}");
            copy(storage_path("app/public/{$itReturnTwoFilePath}"), "{$externalPath}/{$itReturnTwoRandomName}");
        }

        if ($request->hasFile('financial_status_audit_file_3')) {
            $auditFileThreeFile = $request->file('financial_status_audit_file_3');
            $auditFileThreeExtension = $auditFileThreeFile->getClientOriginalExtension();
            $auditFileThreeRandomName = 'financial_status_audit_file_3_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $auditFileThreeExtension;

            $auditFileThreeFilePath = $auditFileThreeFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $auditFileThreeRandomName, 'public');
            copy(storage_path("app/public/{$auditFileThreeFilePath}"), "{$folderPath}/{$auditFileThreeRandomName}");
            copy(storage_path("app/public/{$auditFileThreeFilePath}"), "{$externalPath}/{$auditFileThreeRandomName}");
        }

        if ($request->hasFile('financial_status_it_return_file_3')) {
            $itReturnThreeFile = $request->file('financial_status_it_return_file_3');
            $itReturnThreeExtension = $itReturnThreeFile->getClientOriginalExtension();
            $itReturnThreeRandomName = 'financial_status_it_return_file_3_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $itReturnThreeExtension;

            $itReturnThreeFilePath = $itReturnThreeFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $itReturnThreeRandomName, 'public');
            copy(storage_path("app/public/{$itReturnThreeFilePath}"), "{$folderPath}/{$itReturnThreeRandomName}");
            copy(storage_path("app/public/{$itReturnThreeFilePath}"), "{$externalPath}/{$itReturnThreeRandomName}");
        }

        if ($request->hasFile('bank_account_file')) {
            $ngoBankAccountFile = $request->file('bank_account_file');
            $ngoBankAccountFileExtension = $ngoBankAccountFile->getClientOriginalExtension();
            $ngoBankAccountFileRandomName = 'ngo_bank_account_file_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $ngoBankAccountFileExtension;

            $ngoBankAccountFilePath = $ngoBankAccountFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $ngoBankAccountFileRandomName, 'public');
            copy(storage_path("app/public/{$ngoBankAccountFilePath}"), "{$folderPath}/{$ngoBankAccountFileRandomName}");
            copy(storage_path("app/public/{$ngoBankAccountFilePath}"), "{$externalPath}/{$ngoBankAccountFileRandomName}");
        }

        if ($request->hasFile('ngo_additional_docs_file')) {
            $ngoAddlDocsFile = $request->file('ngo_additional_docs_file');
            $ngoAddlDocsFileExtension = $ngoAddlDocsFile->getClientOriginalExtension();
            $ngoAddlDocsFileRandomName = 'ngo_additional_docs_file_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $ngoAddlDocsFileExtension;

            $ngoAddlDocsFilePath = $ngoAddlDocsFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $ngoAddlDocsFileRandomName, 'public');
            copy(storage_path("app/public/{$ngoAddlDocsFilePath}"), "{$folderPath}/{$ngoAddlDocsFileRandomName}");
            copy(storage_path("app/public/{$ngoAddlDocsFilePath}"), "{$externalPath}/{$ngoAddlDocsFileRandomName}");
        }

        $NgoPartSixAssetOrganization = new NgoPartSixAssetOrganization();
        $NgoPartSixAssetOrganization->ngo_org_id = $ngo_tbl_records->ngo_org_id;
        $NgoPartSixAssetOrganization->ngo_tbl_id = $ngo_tbl_records->id;
        $NgoPartSixAssetOrganization->ngo_system_gen_reg_no = $ngo_tbl_records->ngo_system_gen_reg_no;
        $NgoPartSixAssetOrganization->land_no_of_unit = $validatedData['land_no_of_unit'];
        $NgoPartSixAssetOrganization->land_permanent_or_rental = $validatedData['land_permanent_or_rental'];
        $NgoPartSixAssetOrganization->land_no_of_unit_file = $landFilePath;
        $NgoPartSixAssetOrganization->building_no_of_unit = $validatedData['building_no_of_unit'];
        $NgoPartSixAssetOrganization->building_permanent_or_rental = $validatedData['building_permanent_or_rental'];
        $NgoPartSixAssetOrganization->building_no_of_unit_file = $buildingFilePath;
        $NgoPartSixAssetOrganization->vehicles_no_of_unit = $validatedData['vehicles_no_of_unit'];
        $NgoPartSixAssetOrganization->vehicles_permanent_or_rental = $validatedData['vehicles_permanent_or_rental'];
        $NgoPartSixAssetOrganization->vehicles_no_of_unit_file = $vehiclesFilePath;
        $NgoPartSixAssetOrganization->equipment_no_of_unit = $validatedData['equipment_no_of_unit'];
        $NgoPartSixAssetOrganization->equipment_permanent_or_rental = $validatedData['equipment_permanent_or_rental'];
        $NgoPartSixAssetOrganization->equipment_no_of_unit_file = $equipmentFilePath;
        $NgoPartSixAssetOrganization->others_no_of_unit = $validatedData['others_no_of_unit'];
        $NgoPartSixAssetOrganization->others_permanent_or_rental = $validatedData['others_permanent_or_rental'];
        $NgoPartSixAssetOrganization->others_no_of_unit_file = $othersFilePath;
        $NgoPartSixAssetOrganization->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $NgoPartSixAssetOrganization->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $NgoPartSixAssetOrganization->created_by = Auth::id();
        $NgoPartSixAssetOrganization->status = 1;
        $NgoPartSixAssetOrganization->save();

        $NgoPartSixFinancialStatus = new NgoPartSixFinancialStatus();
        $NgoPartSixFinancialStatus->ngo_org_id = $ngo_tbl_records->ngo_org_id;
        $NgoPartSixFinancialStatus->ngo_tbl_id = $ngo_tbl_records->id;
        $NgoPartSixFinancialStatus->ngo_system_gen_reg_no = $ngo_tbl_records->ngo_system_gen_reg_no;
        $NgoPartSixFinancialStatus->financial_status_financial_year_1 = $validatedData['financial_status_financial_year_1'];
        $NgoPartSixFinancialStatus->financial_status_receipt_price_1 = $validatedData['financial_status_receipt_price_1'];
        $NgoPartSixFinancialStatus->financial_status_payment_1 = $validatedData['financial_status_payment_1'];
        $NgoPartSixFinancialStatus->financial_status_surplus_1 = $validatedData['financial_status_surplus_1'];
        $NgoPartSixFinancialStatus->financial_status_audit_file_1 = $auditFileOneFilePath;
        $NgoPartSixFinancialStatus->financial_status_it_return_file_1 = $itReturnOneFilePath;
        $NgoPartSixFinancialStatus->financial_status_financial_year_2 = $validatedData['financial_status_financial_year_2'];
        $NgoPartSixFinancialStatus->financial_status_receipt_price_2 = $validatedData['financial_status_receipt_price_2'];
        $NgoPartSixFinancialStatus->financial_status_payment_2 = $validatedData['financial_status_payment_2'];
        $NgoPartSixFinancialStatus->financial_status_surplus_2 = $validatedData['financial_status_surplus_2'];
        $NgoPartSixFinancialStatus->financial_status_audit_file_2 = $auditFileTwoFilePath;
        $NgoPartSixFinancialStatus->financial_status_it_return_file_2 = $itReturnTwoFilePath;
        $NgoPartSixFinancialStatus->financial_status_financial_year_3 = $validatedData['financial_status_financial_year_3'];
        $NgoPartSixFinancialStatus->financial_status_receipt_price_3 = $validatedData['financial_status_receipt_price_3'];
        $NgoPartSixFinancialStatus->financial_status_payment_3 = $validatedData['financial_status_payment_3'];
        $NgoPartSixFinancialStatus->financial_status_surplus_3 = $validatedData['financial_status_surplus_3'];
        $NgoPartSixFinancialStatus->financial_status_audit_file_3 = $auditFileThreeFilePath;
        $NgoPartSixFinancialStatus->financial_status_it_return_file_3 = $itReturnThreeFilePath;
        $NgoPartSixFinancialStatus->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $NgoPartSixFinancialStatus->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $NgoPartSixFinancialStatus->created_by = Auth::id();
        $NgoPartSixFinancialStatus->status = 1;
        $NgoPartSixFinancialStatus->save();

        $ngo_tbl_records->bank_account_type_1 = $validatedData['bank_account_type_1'];
        $ngo_tbl_records->bank_account_holder_name_1 = $validatedData['bank_account_holder_name_1'];
        $ngo_tbl_records->bank_account_holder_name_2 = $validatedData['bank_account_holder_name_2'];
        $ngo_tbl_records->bank_account_number = $validatedData['bank_account_number'];
        $ngo_tbl_records->ifsc_code = $validatedData['ifsc_code'];
        $ngo_tbl_records->bank_account_file = $ngoBankAccountFilePath;
        $ngo_tbl_records->ngo_additional_docs_file = $ngoAddlDocsFilePath;
        $ngo_tbl_records->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $ngo_tbl_records->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $ngo_tbl_records->updated_by = Auth::id();
        $ngo_tbl_records->dsso_assigned = User::where('role_id', '9')->where('posted_district', $ngo_tbl_records->district_id)->value('id');
        /*$ngo_tbl_records->no_of_form_completed = 6;*/
        $ngo_tbl_records->application_stage_id = 2;
        $ngo_tbl_records->no_of_form_completed = $ngo_tbl_records->no_of_form_completed < 6 ? 6 : $ngo_tbl_records->no_of_form_completed;
        $ngo_tbl_records->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartSixAssetOrganization';
        $applicationstagehistory->model_table_id = $NgoPartSixAssetOrganization->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 1;
        $applicationstagehistory->stage_name = 'Pending for final submit';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 6th Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartSixFinancialStatus';
        $applicationstagehistory->model_table_id = $NgoPartSixFinancialStatus->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 2;
        $applicationstagehistory->stage_name = 'Application Applied Successfully';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'NGO 6th Phase Registration Completed';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        $ngoUserId = User::whereId($ngo_tbl_records->user_table_id)->value('user_id');
        $ngoData = [
            'ngoRegdPhase' => 'sixth',
            'registrationNumber' => $ngo_tbl_records->ngo_system_gen_reg_no,
            'ngoOrgName' => $ngo_tbl_records->ngo_org_name,
            'ngoOrgEmail' => $ngo_tbl_records->ngo_org_email,
            'ngoOrgPhone' => $ngo_tbl_records->ngo_org_phone,
            'ngoOrgWebsite' => $ngo_tbl_records->ngo_org_website,
            'ngoRegNo' => $ngo_tbl_records->ngo_reg_no,
            'ngoDateOfRegistration' => $ngo_tbl_records->ngo_date_of_registration,
            'ngoUserId' => $ngoUserId,
            'ngoUserPassword' => '123456',
        ];
        Mail::to($ngo_tbl_records->ngo_org_email)->send(new NgoRegistrationMail($ngoData));
        return redirect()->route('admin.ngo.index')->with('success', "Your application for NGO registration has been submitted successfully. Please check your '{$ngo_tbl_records->ngo_org_email}' email for the details.")->with('addressMessage', $addressMessage);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong. Please try again.')->withInput();
    }
}

public function check_pan_no(Request $request): JsonResponse
{
    $ngo_org_pan = $request->get('ngo_org_pan');
    if (empty($ngo_org_pan)) {
        return response()->json(2);
    }
    $exists = NgoRegistration::where('ngo_org_pan', $ngo_org_pan)->exists();
    return response()->json($exists ? 1 : 0);
}

public function check_ngo_org_email(Request $request): JsonResponse
{
    $ngo_org_email = $request->get('ngo_org_email');
    if (empty($ngo_org_email)) {
        return response()->json(2);
    }
    $emailExistsInNgo = NgoRegistration::where('ngo_org_email', $ngo_org_email)->exists();
    $emailExistsInUser = User::where('email', $ngo_org_email)->exists();
    $exists = $emailExistsInNgo || $emailExistsInUser;
    return response()->json($exists ? 1 : 0);
}

public function check_trained_staff_aadhar_no(Request $request): JsonResponse
{
    $staff_aadhar_number = $request->get('staff_aadhar_number');
    if (empty($staff_aadhar_number)) {
        return response()->json(2);
    }
    $exists = NgoPartFourTrainedStaff::where('staff_aadhar_number', $staff_aadhar_number)->exists();
    return response()->json($exists ? 1 : 0);
}
/**
* Display the specified resource.
*/
public function view_ngo_application(string $id)
{
    $ngo_id = $id;
    $NgoRegistration = NgoRegistration::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality'])->findOrFail($id);
    $NgoPartTwoOfficeBearer = NgoPartTwoOfficeBearer::where('ngo_tbl_id', $id)->get();
    $NgoPartThreeActRegistration = NgoPartThreeActRegistration::where('ngo_tbl_id', $id)->first();
    $NgoPartFourOtherRecognition = NgoPartFourOtherRecognition::where('ngo_tbl_id', $id)->get();
    $NgoPartFourTrainedStaff = NgoPartFourTrainedStaff::where('ngo_tbl_id', $id)->get();
    $NgoPartFiveListOfBeneficiary = NgoPartFiveListOfBeneficiary::where('ngo_tbl_id', $id)->get();
    $NgoPartSixAssetOrganization = NgoPartSixAssetOrganization::where('ngo_tbl_id', $id)->first();
    $NgoPartSixFinancialStatus = NgoPartSixFinancialStatus::where('ngo_tbl_id', $id)->first();
    $user = User::find($NgoRegistration->user_table_id);
    $bankMaster = BankMaster::where('bank_id', $NgoRegistration->ifsc_code)->first();
    $applicationStageHistory = ApplicationStageHistory::with('creator')->where('department_scheme_id', 1)->where('initial_model_table_id', $id)->where('stage_id', '>=', 2)->get();

    return view('dashboard.ngo.view_ngo', compact(
        'NgoRegistration',
        'NgoPartTwoOfficeBearer',
        'NgoPartThreeActRegistration',
        'NgoPartFourOtherRecognition',
        'NgoPartFourTrainedStaff',
        'NgoPartFiveListOfBeneficiary',
        'NgoPartSixAssetOrganization',
        'NgoPartSixFinancialStatus',
        'applicationStageHistory',
        'user',
        'bankMaster',
        'ngo_id'
    ));
}

public function check_pan_no_of_office_bearer(Request $request): JsonResponse
{
    $office_bearer_pan = $request->get('office_bearer_pan');
    $ngo_id = $request->get('ngo_id');
    if (empty($office_bearer_pan)) {
        return response()->json(2);
    }
    $exists = NgoPartTwoOfficeBearer::where('ngo_tbl_id', $ngo_id)->where('office_bearer_pan', $office_bearer_pan)->exists();
    return response()->json($exists ? 1 : 0);
}

public function check_aadhar_no_of_office_bearer(Request $request): JsonResponse
{
    $office_bearer_aadhar = $request->get('office_bearer_aadhar');
    $ngo_id = $request->get('ngo_id');
    if (empty($office_bearer_aadhar)) {
        return response()->json(2);
    }
    $exists = NgoPartTwoOfficeBearer::where('ngo_tbl_id', $ngo_id)->where('office_bearer_aadhar', $office_bearer_aadhar)->exists();
    return response()->json($exists ? 1 : 0);
}

/**
* Show the form for editing the specified resource.
*/
public function executive_remarks(Request $request, string $id)
{
    $validationRules = [
        'remarks_type' => 'required|in:1,2,3,4,5,6,7',
        'inspection_report_file' => ['required_if:remarks_type,1,2,3,4,5', 'file', 'mimes:pdf', 'max:2048',],
        'remark_data' => 'required',
    ];
    
    $validatedData = $request->validate($validationRules);
    DB::beginTransaction();
    try{
        $addressMessage = '';
        $loggedin_user = Auth::user();
        $role = $loggedin_user->getRoleNames()->first();
        $ngo_tbl_records = NgoRegistration::findOrFail($request->id);
        $ngoSystemGenRegNo = str_replace('/', '_', $ngo_tbl_records->ngo_system_gen_reg_no);
        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";
        if (!file_exists($folderPath)) { mkdir($folderPath, 0755, true); }
        if (!file_exists($externalPath)) { mkdir($externalPath, 0755, true); }
        if ($request->hasFile('inspection_report_file')) {
            $inspectionFile = $request->file('inspection_report_file');
            $inspectionExtension = $inspectionFile->getClientOriginalExtension();
            $inspectionRandomName = $role . '_inspection_report_file_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $inspectionExtension;

            $inspectionFilePath = $inspectionFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $inspectionRandomName, 'public');
            copy(storage_path("app/public/{$inspectionFilePath}"), "{$folderPath}/{$inspectionRandomName}");
            copy(storage_path("app/public/{$inspectionFilePath}"), "{$externalPath}/{$inspectionRandomName}");
        }

        $ipAddress = request()->ip();
        $ip_v4 = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $ip_v6 = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $currentDate = now()->setTimezone('Asia/Kolkata')->toDateString();
        $currentTime = now()->setTimezone('Asia/Kolkata')->toTimeString();

        if ($role == 'DSSO') {
            $ngo_tbl_records->dsso_remarks_type = $validatedData['remarks_type'];
            $ngo_tbl_records->dsso_inspection_report_file = $inspectionFilePath;
            $ngo_tbl_records->dsso_remark = $validatedData['remark_data'];
            $ngo_tbl_records->dsso_created_by = Auth::id();
            $ngo_tbl_records->dsso_created_date = $currentDate;
            $ngo_tbl_records->dsso_created_time = $currentTime;

            if ($validatedData['remarks_type'] == 1) {
                $ngo_tbl_records->application_stage_id = 9;
                $ngo_tbl_records->collector_assigned = User::role('Collector')->where('posted_district', $ngo_tbl_records->district_id)->value('id');
                $stageId = 9;
                $stageName = 'Forwarded to Collector';
            } elseif ($validatedData['remarks_type'] == 6) {
                $ngo_tbl_records->application_stage_id = 18;
                $stageId = 18;
                $stageName = 'Rejected by DSSO';
            } elseif ($validatedData['remarks_type'] == 7) {
                $ngo_tbl_records->application_stage_id = 30;
                $stageId = 30;
                $stageName = 'Reverted by DSSO';
            } else {
                return redirect()->back()->with('warning', "Please select the appropriate option!");
            }

            $ngo_tbl_records->save();

            ApplicationStageHistory::create([
                'department_scheme_id' => 1,
                'model_name' => 'NgoRegistration',
                'model_table_id' => $ngo_tbl_records->id,
                'initial_model_table_id' => $ngo_tbl_records->id,
                'stage_id' => $stageId,
                'stage_name' => $stageName,
                'created_date' => $currentDate,
                'created_time' => $currentTime,
                'created_by' => Auth::id(),
                'created_by_remarks' => $validatedData['remark_data'],
                'created_by_inspection_report_file' => $ngo_tbl_records->dsso_inspection_report_file,
                'created_by_ip_v_four' => $ip_v4,
                'created_by_ip_v_six' => $ip_v6,
            ]);

            return redirect()->route('admin.ngo.index')->with('success', "Application Updated.");

        } elseif ($role == 'Collector') {
            $ngo_tbl_records->collector_remarks_type = $validatedData['remarks_type'];
            $ngo_tbl_records->collector_inspection_report_file = $inspectionFilePath;
            $ngo_tbl_records->collector_remark = $validatedData['remark_data'];
            $ngo_tbl_records->collector_created_by = Auth::id();
            $ngo_tbl_records->collector_created_date = $currentDate;
            $ngo_tbl_records->collector_created_time = $currentTime;

            if ($validatedData['remarks_type'] == 2) {
                $ngo_tbl_records->application_stage_id = 10;
                $ngo_tbl_records->ho_assigned = User::role('HO')->where('department_section_id', 6)->value('id');
                $stageId = 10;
                $stageName = 'Forwarded to HO';
            } elseif ($validatedData['remarks_type'] == 6) {
                $ngo_tbl_records->application_stage_id = 20;
                $stageId = 20;
                $stageName = 'Rejected by Collector';
            } elseif ($validatedData['remarks_type'] == 7) {
                $ngo_tbl_records->application_stage_id = 32;
                $stageId = 32;
                $stageName = 'Reverted by Collector';
            } else {
                return redirect()->back()->with('warning', "Please select the appropriate option!");
            }

            $ngo_tbl_records->save();

            ApplicationStageHistory::create([
                'department_scheme_id' => 1,
                'model_name' => 'NgoRegistration',
                'model_table_id' => $ngo_tbl_records->id,
                'initial_model_table_id' => $ngo_tbl_records->id,
                'stage_id' => $stageId,
                'stage_name' => $stageName,
                'created_date' => $currentDate,
                'created_time' => $currentTime,
                'created_by' => Auth::id(),
                'created_by_remarks' => $validatedData['remark_data'],
                'created_by_inspection_report_file' => $ngo_tbl_records->collector_inspection_report_file,
                'created_by_ip_v_four' => $ip_v4,
                'created_by_ip_v_six' => $ip_v6,
            ]);

            return redirect()->route('admin.ngo.index')->with('success', "Application Updated.");
        } elseif ($role == 'HO') {
            $ngo_tbl_records->ho_remarks_type = $validatedData['remarks_type'];
            $ngo_tbl_records->ho_inspection_report_file = $inspectionFilePath;
            $ngo_tbl_records->ho_remark = $validatedData['remark_data'];
            $ngo_tbl_records->ho_created_by = Auth::id();
            $ngo_tbl_records->ho_created_date = $currentDate;
            $ngo_tbl_records->ho_created_time = $currentTime;

            if ($validatedData['remarks_type'] == 3) {
                $ngo_tbl_records->application_stage_id = 11;
                $ngo_tbl_records->bo_assigned = User::role('BO')->where('department_section_id', 6)->value('id');
                $stageId = 11;
                $stageName = 'Forwarded to BO';
            } elseif ($validatedData['remarks_type'] == 6) {
                $ngo_tbl_records->application_stage_id = 22;
                $stageId = 22;
                $stageName = 'Rejected by HO';
            } elseif ($validatedData['remarks_type'] == 7) {
                $ngo_tbl_records->application_stage_id = 33;
                $stageId = 33;
                $stageName = 'Reverted by HO';
            } else {
                return redirect()->back()->with('warning', "Please select the appropriate option!");
            }

            $ngo_tbl_records->save();

            ApplicationStageHistory::create([
                'department_scheme_id' => 1,
                'model_name' => 'NgoRegistration',
                'model_table_id' => $ngo_tbl_records->id,
                'initial_model_table_id' => $ngo_tbl_records->id,
                'stage_id' => $stageId,
                'stage_name' => $stageName,
                'created_date' => $currentDate,
                'created_time' => $currentTime,
                'created_by' => Auth::id(),
                'created_by_remarks' => $validatedData['remark_data'],
                'created_by_inspection_report_file' => $ngo_tbl_records->ho_inspection_report_file,
                'created_by_ip_v_four' => $ip_v4,
                'created_by_ip_v_six' => $ip_v6,
            ]);

            DB::commit();
            return redirect()->route('admin.ngo.index')->with('success', "Application Updated.");
        } else {
            return redirect()->back()->with('warning', "You have no access!");
        }
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong. Please try again.')->withInput();
    }
}

public function edit_ngo_application(Request $request, string $id)
{
    $categories = NgoCategory::where('status', 1)->get();
    $NgoRegistration = NgoRegistration::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality'])->findOrFail($id);
    $NgoPartTwoOfficeBearer = NgoPartTwoOfficeBearer::where('ngo_tbl_id', $id)->orderBy('office_bearer_designation', 'ASC')->get();
    $NgoPartThreeActRegistration = NgoPartThreeActRegistration::where('ngo_tbl_id', $id)->first();
    $NgoPartFourOtherRecognition = NgoPartFourOtherRecognition::where('ngo_tbl_id', $id)->get();
    $NgoPartFourTrainedStaff = NgoPartFourTrainedStaff::where('ngo_tbl_id', $id)->get();
    $NgoPartFiveListOfBeneficiary = NgoPartFiveListOfBeneficiary::where('ngo_tbl_id', $id)->get();
    $NgoPartSixAssetOrganization = NgoPartSixAssetOrganization::where('ngo_tbl_id', $id)->first();
    $NgoPartSixFinancialStatus = NgoPartSixFinancialStatus::where('ngo_tbl_id', $id)->first();
    $user = User::find($NgoRegistration->user_table_id);
    $bankMaster = BankMaster::where('bank_id', $NgoRegistration->ifsc_code)->first();
    return view('dashboard.ngo.ngo_edit.edit_ngo_sections', compact(
        'categories',
        'NgoRegistration',
        'NgoPartTwoOfficeBearer',
        'NgoPartThreeActRegistration',
        'NgoPartFourOtherRecognition',
        'NgoPartFourTrainedStaff',
        'NgoPartFiveListOfBeneficiary',
        'NgoPartSixAssetOrganization',
        'NgoPartSixFinancialStatus',
        'user',
        'bankMaster',
    ));
}

public function update_ngo_application_part_one(Request $request, string $id)
{
    $validationRules = [
        'ngo_registration_type' => 'required|in:1,2',
        'ngo_category' => 'required|exists:ngo_categories,id',
        'ngo_org_name' => 'required|string|max:255',
        'ngo_org_pan' => 'required|string|max:20',
        'ngo_org_email' => 'required|email|max:255',
        'ngo_org_phone' => 'required|string|max:15',
        'ngo_org_website' => 'required|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|max:255',
        'ngo_registered_with' => 'required|in:1,2,3,4,5,6,7,8,9,10',
        'ngo_other_reg_act' => 'nullable|required_if:ngo_registered_with,10|string|max:255',
        'ngo_type_of_vo_or_ngo' => 'required|in:1,2,3,4,5,6',
        'ngo_reg_no' => 'required|string|max:100',
        'ngo_date_of_registration' => 'required|date',
        'ngo_date_of_registration_validity' => 'required|date|after:ngo_date_of_registration',
        'nature_of_organisation' => 'required|array|min:1',
        'nature_of_organisation.*' => 'in:1,2,3,4,5,6',
        'nature_of_organisation_other' => 'nullable|required_if:nature_of_organisation.5,6|string|max:255',
        'ngo_organisation_type' => 'required|in:1,2',
        'ngo_parent_organisation' => 'nullable|string|max:255',
        'ngo_reg_velidity_available' => 'required|in:0,1',
        'ngo_org_pan_file' => 'nullable|file|mimes:pdf|max:2048',
        'ngo_file_rc' => 'nullable|file|mimes:pdf|max:2048',
        'ngo_file_byelaws' => 'nullable|file|mimes:pdf|max:2048',
    ];

    $validatedData = $request->validate($validationRules);

    DB::beginTransaction();
    try{
        $ngo_tbl_records = NgoRegistration::findOrFail($id);

        if (!$ngo_tbl_records) {
            return redirect()->back()->with('error', 'NGO record not found.');
        }

        $ngoSystemGeneratedRegNo = $ngo_tbl_records->ngo_system_gen_reg_no;
        $ngoSystemGenRegNo = str_replace('/', '_', $ngoSystemGeneratedRegNo);

        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('ngo_org_pan_file')) {
            $panFile = $request->file('ngo_org_pan_file');
            $panExtension = $panFile->getClientOriginalExtension();
            $panRandomName = 'NGO_PAN_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $panExtension;

            $panStoredPath = $panFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $panRandomName, 'public');
            copy(storage_path("app/public/{$panStoredPath}"), "{$folderPath}/{$panRandomName}");
            copy(storage_path("app/public/{$panStoredPath}"), "{$externalPath}/{$panRandomName}");
        }

        if ($request->hasFile('ngo_file_rc')) {
            $rcFile = $request->file('ngo_file_rc');
            $rcExtension = $rcFile->getClientOriginalExtension();
            $rcRandomName = 'NGO_RC_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $rcExtension;

            $rcStoredPath = $rcFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $rcRandomName, 'public');
            copy(storage_path("app/public/{$rcStoredPath}"), "{$folderPath}/{$rcRandomName}");
            copy(storage_path("app/public/{$rcStoredPath}"), "{$externalPath}/{$rcRandomName}");
        }

        if ($request->hasFile('ngo_file_byelaws')) {
            $byelawsFile = $request->file('ngo_file_byelaws');
            $rcExtension = $byelawsFile->getClientOriginalExtension();
            $byelawsRandomName = 'NGO_BYELAWS_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $rcExtension;

            $byelawsStoredPath = $byelawsFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $byelawsRandomName, 'public');
            copy(storage_path("app/public/{$byelawsStoredPath}"), "{$folderPath}/{$byelawsRandomName}");
            copy(storage_path("app/public/{$byelawsStoredPath}"), "{$externalPath}/{$byelawsRandomName}");
        }

        $natureOfOrganisation = implode(',', $request->input('nature_of_organisation'));

        $NgoRegistration = NgoRegistration::find($ngo_tbl_records->id);
        if (!$NgoRegistration) {
            return redirect()->back()->with('error', 'NGO record not found.');
        }

        $NgoRegistration->ngo_registration_type = $validatedData['ngo_registration_type'];
        $NgoRegistration->ngo_category = $validatedData['ngo_category'];
        $NgoRegistration->ngo_org_name = $validatedData['ngo_org_name'];
        $NgoRegistration->ngo_org_pan = $validatedData['ngo_org_pan'];
        $NgoRegistration->ngo_org_pan_file = !empty($panStoredPath) ? $panStoredPath : $ngo_tbl_records->ngo_org_pan_file;
        $NgoRegistration->ngo_org_email = $validatedData['ngo_org_email'];
        $NgoRegistration->ngo_org_phone = $validatedData['ngo_org_phone'];
        $NgoRegistration->ngo_org_website = $validatedData['ngo_org_website'];
        $NgoRegistration->ngo_registered_with = $validatedData['ngo_registered_with'];
        $NgoRegistration->ngo_other_reg_act = $validatedData['ngo_registered_with'] == 10 ? $validatedData['ngo_other_reg_act'] : null;
        $NgoRegistration->ngo_type_of_vo_or_ngo = $validatedData['ngo_type_of_vo_or_ngo'];
        $NgoRegistration->ngo_reg_no = $validatedData['ngo_reg_no'];
        $NgoRegistration->ngo_system_gen_reg_no = $ngoSystemGeneratedRegNo;
        $NgoRegistration->ngo_file_rc = !empty($rcStoredPath) ? $rcStoredPath : $ngo_tbl_records->ngo_file_rc;
        $NgoRegistration->ngo_date_of_registration = $validatedData['ngo_date_of_registration'];
        $NgoRegistration->ngo_date_of_registration_validity = $validatedData['ngo_date_of_registration_validity'];
        $NgoRegistration->nature_of_organisation = $natureOfOrganisation;
        $natureOfOrganisationArray = $request->input('nature_of_organisation', []);
        $NgoRegistration->nature_of_organisation_other = in_array('6', $natureOfOrganisationArray) ? $request->input('nature_of_organisation_other') : null;
        $NgoRegistration->ngo_organisation_type = $validatedData['ngo_organisation_type'];
        $NgoRegistration->ngo_file_byelaws = !empty($byelawsStoredPath) ? $byelawsStoredPath : $ngo_tbl_records->ngo_file_byelaws;
        $NgoRegistration->ngo_parent_organisation = $request->input('ngo_parent_organisation');
        $NgoRegistration->ngo_reg_velidity_available = $validatedData['ngo_reg_velidity_available'];
        $NgoRegistration->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $NgoRegistration->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $NgoRegistration->updated_by = Auth::id() ?? null;
        $action = $request->input('register');
        if (in_array($action, ['submit', 'draft'])) {
            $NgoRegistration->application_stage_id = $action === 'submit' ? 2 : 1;
        }
        $resetFields = array_fill_keys([
            'dsso_remarks_type', 'dsso_created_by', 'dsso_created_date', 'dsso_created_time',
            'dsso_updated_date', 'dsso_updated_time', 'dsso_remark', 'dsso_inspection_report_file',

            'collector_assigned', 'collector_remarks_type', 'collector_created_by', 'collector_created_date',
            'collector_created_time', 'collector_updated_date', 'collector_updated_time',
            'collector_remark', 'collector_inspection_report_file',

            'ho_assigned', 'ho_remarks_type', 'ho_created_by', 'ho_created_date', 'ho_created_time',
            'ho_updated_date', 'ho_updated_time', 'ho_remark', 'ho_inspection_report_file',

            'bo_assigned', 'bo_remarks_type', 'bo_created_by', 'bo_created_date', 'bo_created_time',
            'bo_updated_date', 'bo_updated_time', 'bo_remark', 'bo_inspection_report_file',

            'director_assigned', 'director_remarks_type', 'director_created_by', 'director_created_date',
            'director_created_time', 'director_updated_date', 'director_updated_time',
            'director_remark', 'director_inspection_report_file',

            'admin_assigned', 'admin_remarks_type', 'admin_created_by', 'admin_created_date',
            'admin_created_time', 'admin_updated_date', 'admin_updated_time',
            'admin_remark', 'admin_inspection_report_file',

            'ngo_approved_from_date', 'ngo_approved_validity_date',
        ], null);

        $NgoRegistration->status = 1;
        $NgoRegistration->fill($resetFields)->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoRegistration';
        $applicationstagehistory->model_table_id = $NgoRegistration->id;
        $applicationstagehistory->initial_model_table_id = $NgoRegistration->id;
        $applicationstagehistory->stage_id = 37;
        $applicationstagehistory->stage_name = 'Application updated by User';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'User has updated the first step of the NGO application.';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        return redirect()->back()->with('info', 'NGO application updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong. Please try again.')->withInput();
    }
}

public function update_ngo_application_part_two_get_office_bearer(Request $request)
{
    $bearer = NgoPartTwoOfficeBearer::findOrFail($request->id);

    return response()->json([
        'id' => $bearer->id,
        'office_bearer_name' => $bearer->office_bearer_name,
        'office_bearer_gender' => $bearer->office_bearer_gender,
        'office_bearer_email' => $bearer->office_bearer_email,
        'office_bearer_phone' => $bearer->office_bearer_phone,
        'office_bearer_designation' => $bearer->office_bearer_designation,
        'office_bearer_key_designation' => $bearer->office_bearer_key_designation,
        'office_bearer_date_of_association' => $bearer->office_bearer_date_of_association,
        'office_bearer_pan' => $bearer->office_bearer_pan,
        'office_bearer_name_as_aadhar' => $bearer->office_bearer_name_as_aadhar,
        'office_bearer_dob' => $bearer->office_bearer_dob,
        'office_bearer_aadhar' => $bearer->office_bearer_aadhar,
        'office_bearer_pan_file' => $bearer->office_bearer_pan_file ? asset('storage/' . $bearer->office_bearer_pan_file) : null,
        'office_bearer_aadhar_file' => $bearer->office_bearer_aadhar_file ? asset('storage/' . $bearer->office_bearer_aadhar_file) : null,
    ]);
}

public function update_ngo_application_part_two_update_office_bearer(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id' => 'required|exists:ngo_part_two_office_bearers,id',
        'office_bearer_name' => 'required|string|max:255',
        'office_bearer_gender' => 'required|in:1,2,3',
        'office_bearer_email' => 'required|email|max:255',
        'office_bearer_phone' => 'required|digits:10',
        'office_bearer_designation' => 'required|string|max:255',
        'office_bearer_key_designation' => 'required|string|max:255',
        'office_bearer_date_of_association' => 'required|date',
        'office_bearer_pan' => 'required|alpha_num|size:10',
        'office_bearer_pan_file' => 'nullable|file|mimes:pdf|max:2048',
        'office_bearer_name_as_aadhar' => 'required|string|max:255',
        'office_bearer_dob' => 'required|date',
        'office_bearer_aadhar' => 'required|numeric|min:12',
        'office_bearer_aadhar_file' => 'nullable|file|mimes:pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();
    try {
        $bearer = NgoPartTwoOfficeBearer::findOrFail($request->id);

        $ngo_tbl_records = NgoRegistration::findOrFail($bearer->ngo_tbl_id);
        $ngoSystemGenRegNo = str_replace('/', '_', $ngo_tbl_records->ngo_system_gen_reg_no);
        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";
        if (!file_exists($folderPath)) { mkdir($folderPath, 0755, true); }
        if (!file_exists($externalPath)) { mkdir($externalPath, 0755, true); }

        if ($request->hasFile('office_bearer_pan_file')) {
            $panFile = $request->file('office_bearer_pan_file');
            $panExtension = $panFile->getClientOriginalExtension();
            $panRandomName = 'OFFICE_BEARER_PAN_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $panExtension;

            $ngoBearerPanFilePath = $panFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $panRandomName, 'public');
            copy(storage_path("app/public/{$ngoBearerPanFilePath}"), "{$folderPath}/{$panRandomName}");
            copy(storage_path("app/public/{$ngoBearerPanFilePath}"), "{$externalPath}/{$panRandomName}");
        }

        if ($request->hasFile('office_bearer_aadhar_file')) {
            $rcFile = $request->file('office_bearer_aadhar_file');
            $rcExtension = $rcFile->getClientOriginalExtension();
            $rcRandomName = 'OFFICE_BEARER_AADHAR_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $rcExtension;

            $ngoBearerFileAadharPath = $rcFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $rcRandomName, 'public');
            copy(storage_path("app/public/{$ngoBearerFileAadharPath}"), "{$folderPath}/{$rcRandomName}");
            copy(storage_path("app/public/{$ngoBearerFileAadharPath}"), "{$externalPath}/{$rcRandomName}");
        }

        $bearer->office_bearer_name = $request->office_bearer_name;
        $bearer->office_bearer_gender = $request->office_bearer_gender;
        $bearer->office_bearer_email = $request->office_bearer_email;
        $bearer->office_bearer_phone = $request->office_bearer_phone;
        $bearer->office_bearer_designation = $request->office_bearer_designation;
        $bearer->office_bearer_key_designation = $request->office_bearer_key_designation;
        $bearer->office_bearer_date_of_association = $request->office_bearer_date_of_association;
        $bearer->office_bearer_pan = $request->office_bearer_pan;
        $bearer->office_bearer_name_as_aadhar = $request->office_bearer_name_as_aadhar;
        $bearer->office_bearer_dob = $request->office_bearer_dob;
        $bearer->office_bearer_aadhar = $request->office_bearer_aadhar;
        $bearer->office_bearer_pan_file = !empty($ngoBearerPanFilePath) ? $ngoBearerPanFilePath : $bearer->office_bearer_pan_file;
        $bearer->office_bearer_aadhar_file = !empty($ngoBearerFileAadharPath) ? $ngoBearerFileAadharPath : $bearer->office_bearer_aadhar_file;
        $bearer->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartTwoOfficeBearer';
        $applicationstagehistory->model_table_id = $bearer->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 37;
        $applicationstagehistory->stage_name = 'Application updated by User';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'User has updated the record of an Office Bearer.';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("NGO Part Two Registration failed: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}

public function update_ngo_application_part_two_add_another_office_bearer(Request $request, string $id)
{
    $NgoRegistration = NgoRegistration::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality'])->findOrFail($id);
    return view('dashboard.ngo.ngo_edit.add_another_office_bearer', compact('id', 'NgoRegistration'));
}

public function update_ngo_application_part_two_store_another_office_bearer(Request $request, string $id)
{
    $validationRules = [
        'office_bearer_name' => 'required|string|max:255',
        'office_bearer_gender' => 'required|in:1,2,3',
        'office_bearer_email' => 'required|email|max:255',
        'office_bearer_phone' => 'required|digits:10',
        'office_bearer_designation' => 'required|string|max:255',
        'office_bearer_key_designation' => 'required|string|max:255',
        'office_bearer_date_of_association' => 'required|date',
        'office_bearer_pan' => 'required|alpha_num|size:10',
        'office_bearer_pan_file' => 'required|file|mimes:pdf|max:2048',
        'office_bearer_name_as_aadhar' => 'required|string|max:255',
        'office_bearer_dob' => 'required|date',
        'office_bearer_aadhar' => 'required|numeric|min:12',
        'office_bearer_aadhar_file' => 'required|file|mimes:pdf|max:2048',
        'want_to_add_another_bearer' => 'required|in:1,2',
    ];
    $validatedData = $request->validate($validationRules);
    DB::beginTransaction();
    try {

        $addressMessage = '';
        $ngo_tbl_records = NgoRegistration::findOrFail($request->id);
        $ngoSystemGenRegNo = str_replace('/', '_', $ngo_tbl_records->ngo_system_gen_reg_no);
        $folderPath = public_path("ngo_files/{$ngoSystemGenRegNo}");
        /*A folder i.e. storage/ngo_files is created inside the root directory ssepd_ngo_working_portal/storage/ngo_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ngo_files/{$ngoSystemGenRegNo}";
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('office_bearer_pan_file')) {
            $panFile = $request->file('office_bearer_pan_file');
            $panExtension = $panFile->getClientOriginalExtension();
            $panRandomName = 'OFFICE_BEARER_PAN_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $panExtension;

            $ngoBearerPanFilePath = $panFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $panRandomName, 'public');
            copy(storage_path("app/public/{$ngoBearerPanFilePath}"), "{$folderPath}/{$panRandomName}");
            copy(storage_path("app/public/{$ngoBearerPanFilePath}"), "{$externalPath}/{$panRandomName}");
        }

        if ($request->hasFile('office_bearer_aadhar_file')) {
            $rcFile = $request->file('office_bearer_aadhar_file');
            $rcExtension = $rcFile->getClientOriginalExtension();
            $rcRandomName = 'OFFICE_BEARER_AADHAR_' . Str::random(10) . '_' . now()->format('Ymd_His') . '_' . '.' . $rcExtension;

            $ngoBearerFileAadharPath = $rcFile->storeAs("ngo_files/{$ngoSystemGenRegNo}", $rcRandomName, 'public');
            copy(storage_path("app/public/{$ngoBearerFileAadharPath}"), "{$folderPath}/{$rcRandomName}");
            copy(storage_path("app/public/{$ngoBearerFileAadharPath}"), "{$externalPath}/{$rcRandomName}");
        }

        $NgoPartTwoOfficeBearer = new NgoPartTwoOfficeBearer();
        $NgoPartTwoOfficeBearer->ngo_org_id = $ngo_tbl_records->ngo_org_id;
        $NgoPartTwoOfficeBearer->ngo_tbl_id = $ngo_tbl_records->id;
        $NgoPartTwoOfficeBearer->ngo_system_gen_reg_no = $ngo_tbl_records->ngo_system_gen_reg_no;
        $NgoPartTwoOfficeBearer->office_bearer_name = $validatedData['office_bearer_name'];
        $NgoPartTwoOfficeBearer->office_bearer_gender = $validatedData['office_bearer_gender'];
        $NgoPartTwoOfficeBearer->office_bearer_email = $validatedData['office_bearer_email'];
        $NgoPartTwoOfficeBearer->office_bearer_phone = $validatedData['office_bearer_phone'];
        $NgoPartTwoOfficeBearer->office_bearer_designation = $validatedData['office_bearer_designation'];
        $NgoPartTwoOfficeBearer->office_bearer_key_designation = $validatedData['office_bearer_key_designation'];
        $NgoPartTwoOfficeBearer->office_bearer_date_of_association = $validatedData['office_bearer_date_of_association'];
        $NgoPartTwoOfficeBearer->office_bearer_pan = $validatedData['office_bearer_pan'];
        $NgoPartTwoOfficeBearer->office_bearer_pan_file = $ngoBearerPanFilePath;
        $NgoPartTwoOfficeBearer->office_bearer_name_as_aadhar = $validatedData['office_bearer_name_as_aadhar'];
        $NgoPartTwoOfficeBearer->office_bearer_dob = $validatedData['office_bearer_dob'];
        $NgoPartTwoOfficeBearer->office_bearer_aadhar = $validatedData['office_bearer_aadhar'];
        $NgoPartTwoOfficeBearer->office_bearer_aadhar_file = $ngoBearerFileAadharPath;
        $NgoPartTwoOfficeBearer->want_to_add_another_bearer = $validatedData['want_to_add_another_bearer'];
        $NgoPartTwoOfficeBearer->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $NgoPartTwoOfficeBearer->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $NgoPartTwoOfficeBearer->created_by = Auth::id();
        $NgoPartTwoOfficeBearer->status = 1;
        $NgoPartTwoOfficeBearer->save();

        $action = $request->input('register');
        $ngo_tbl_records->application_stage_id = empty($action) ? 2 : 1;
        $ngo_tbl_records->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 1;
        $applicationstagehistory->model_name = 'NgoPartTwoOfficeBearer';
        $applicationstagehistory->model_table_id = $NgoPartTwoOfficeBearer->id;
        $applicationstagehistory->initial_model_table_id = $ngo_tbl_records->id;
        $applicationstagehistory->stage_id = 37;
        $applicationstagehistory->stage_name = 'Application updated by User';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'User has created a new record of an Office Bearer.';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();

        if($NgoPartTwoOfficeBearer->want_to_add_another_bearer == 1) {
            return redirect()->back()->with('info', 'One Office Bearer Inserted Successfully.');
        } else {
            return redirect()->route('admin.ngo.edit_ngo_application', compact('id'));
        }

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("NGO Part Two Registration failed: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
    }
}
}