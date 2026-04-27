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
use Illuminate\Support\Facades\Log;

/*Controller Requirements*/
use App\Models\SpecialSchoolMapping;
use App\Models\SpecialSchool;
use App\Models\SpecialSchoolStaff;
use App\Models\SpecialSchoolConstruction;

class SpecialSchoolConstructionController extends Controller
{

    function __construct()
    {
       $this->middleware('permission:special-school-access|special-school-list|special-school-show|special-school-create|special-school-delete|special-school-approve-form', ['only' => ['index','construction_timeline']]);
       $this->middleware('permission:special-school-create', ['only' => ['create','construction_timeline_store']]);
       $this->middleware('permission:special-school-edit', ['only' => ['edit','update']]);
       $this->middleware('permission:special-school-delete', ['only' => ['destroy']]);
   }

/**
* Display a listing of the resource.
*/
/*public function index($id)
{
    $id = $id;
    $specialSchool = SpecialSchool::where('special_school_id', $id)->firstOrFail();
    if (!$specialSchool) {
        return redirect()->route('admin.specialschool.index')->with('danger', 'Something went wrong. Please reach out to your system administrator.');
    }
    $specialSchoolConstruction = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)->where('status', 1)->first();
    $phaseNumbers = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)->where('status', 1)->pluck('phase_no')->unique()->values();

    return view('dashboard.special_school.construction_timeline_for_state', compact(
        'specialSchool',
        'specialSchoolConstruction',
        'phaseNumbers',
        'id'
    ));
}*/

public function index($id)
{
    $specialSchool = SpecialSchool::where('special_school_id', $id)->first();

    if (!$specialSchool) {
        return redirect()
        ->route('admin.specialschool.index')
        ->with('danger', 'Something went wrong. Please reach out to your system administrator.');
    }

    $specialSchoolConstruction = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)
    ->where('status', 1)
    ->first();

    $phaseNumbers = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)
    ->where('status', 1)
    ->pluck('phase_no')
    ->unique()
    ->values();

    $approve_status = null;
    if ($phaseNumbers->isNotEmpty()) {
        $latestPhase = $phaseNumbers->last(); // use last or change to first() if you prefer
        $approve_status = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)
        ->where('phase_no', $latestPhase)
        ->value('approve_status');
    }

    return view('dashboard.special_school.construction_timeline_for_state', compact(
        'specialSchool',
        'specialSchoolConstruction',
        'phaseNumbers',
        'approve_status',
        'id'
    ));
}


public function construction_timeline()
{
    $user = auth()->user();
    $userRole = $user->role_id;

    $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();
    $specialSchool = SpecialSchool::where('user_table_id', $specialSchoolMapping->user_table_id)->first();

    if (!$specialSchool) {
        return redirect()->route('admin.specialschool.create')->with('info', 'Kindly provide the basic information of the school to proceed further.');
    }

    $id = $specialSchool->special_school_id;

    $allConstructionRecords = SpecialSchoolConstruction::where('special_school_id', $id)->where('status', 1)->get();

    $phaseNumbers = $allConstructionRecords->pluck('phase_no')->unique()->sort()->values();

    $constructionByPhase = $allConstructionRecords->keyBy('phase_no');

    $latestImage = $allConstructionRecords->sortByDesc('created_date')->first();

    return view('dashboard.special_school.construction_timeline', compact(
        'latestImage',
        'specialSchoolMapping',
        'specialSchool',
        'phaseNumbers',
        'constructionByPhase',
        'id'
    ));
}


/**
* Show the form for creating a new resource.
*/
public function create()
{

}

/**
* Store a newly created resource in storage.
*/
public function construction_timeline_store(Request $request)
{
    $validationRules = [
        'new_or_existing' => 'required',
        'file_construction_image_1' => 'required|mimes:jpg,jpeg,png|max:3072',
        'latitude_1' => 'required|numeric|between:-90,90',
        'longitude_1' => 'required|numeric|between:-180,180',
        'file_construction_image_2' => 'required|mimes:jpg,jpeg,png|max:3072',
        'latitude_2' => 'required|numeric|between:-90,90',
        'longitude_2' => 'required|numeric|between:-180,180',
        'file_construction_image_3' => 'required|mimes:jpg,jpeg,png|max:3072',
        'latitude_3' => 'required|numeric|between:-90,90',
        'longitude_3' => 'required|numeric|between:-180,180',
        'file_construction_image_4' => 'required|mimes:jpg,jpeg,png|max:3072',
        'latitude_4' => 'required|numeric|between:-90,90',
        'longitude_4' => 'required|numeric|between:-180,180',
        'file_construction_image_5' => 'required|mimes:jpg,jpeg,png|max:3072',
        'latitude_5' => 'required|numeric|between:-90,90',
        'longitude_5' => 'required|numeric|between:-180,180',
        'any_remarks' => 'nullable|string|max:1000',
    ];
    $customMessages = [
        'new_or_existing.required' => 'Please select any option.',
        'file_construction_image_1.required' => 'Geo tagged image 1 is required.',
        'file_construction_image_1.mimes' => 'Image 1 must be a JPG, JPEG, or PNG file.',
        'file_construction_image_1.max' => 'Image 1 size must not exceed 3MB.',

        'latitude_1.required' => 'Latitude 1 is required.',
        'latitude_1.numeric' => 'Latitude 1 must be a number.',
        'latitude_1.between' => 'Latitude 1 must be between -90 and 90.',

        'longitude_1.required' => 'Longitude 1 is required.',
        'longitude_1.numeric' => 'Longitude 1 must be a number.',
        'longitude_1.between' => 'Longitude 1 must be between -180 and 180.',

        'file_construction_image_2.required' => 'Geo tagged image 2 is required.',
        'file_construction_image_2.mimes' => 'Image 2 must be a JPG, JPEG, or PNG file.',
        'file_construction_image_2.max' => 'Image 2 size must not exceed 3MB.',

        'latitude_2.required' => 'Latitude 2 is required.',
        'latitude_2.numeric' => 'Latitude 2 must be a number.',
        'latitude_2.between' => 'Latitude 2 must be between -90 and 90.',

        'longitude_2.required' => 'Longitude 2 is required.',
        'longitude_2.numeric' => 'Longitude 2 must be a number.',
        'longitude_2.between' => 'Longitude 2 must be between -180 and 180.',

        'file_construction_image_3.required' => 'Geo tagged image 3 is required.',
        'file_construction_image_3.mimes' => 'Image 3 must be a JPG, JPEG, or PNG file.',
        'file_construction_image_3.max' => 'Image 3 size must not exceed 3MB.',

        'latitude_3.required' => 'Latitude 3 is required.',
        'latitude_3.numeric' => 'Latitude 3 must be a number.',
        'latitude_3.between' => 'Latitude 3 must be between -90 and 90.',

        'longitude_3.required' => 'Longitude 3 is required.',
        'longitude_3.numeric' => 'Longitude 3 must be a number.',
        'longitude_3.between' => 'Longitude 3 must be between -180 and 180.',

        'file_construction_image_4.required' => 'Geo tagged image 4 is required.',
        'file_construction_image_4.mimes' => 'Image 4 must be a JPG, JPEG, or PNG file.',
        'file_construction_image_4.max' => 'Image 4 size must not exceed 3MB.',

        'latitude_4.required' => 'Latitude 4 is required.',
        'latitude_4.numeric' => 'Latitude 4 must be a number.',
        'latitude_4.between' => 'Latitude 4 must be between -90 and 90.',

        'longitude_4.required' => 'Longitude 4 is required.',
        'longitude_4.numeric' => 'Longitude 4 must be a number.',
        'longitude_4.between' => 'Longitude 4 must be between -180 and 180.',

        'file_construction_image_5.required' => 'Geo tagged image 5 is required.',
        'file_construction_image_5.mimes' => 'Image 5 must be a JPG, JPEG, or PNG file.',
        'file_construction_image_5.max' => 'Image 5 size must not exceed 3MB.',

        'latitude_5.required' => 'Latitude 5 is required.',
        'latitude_5.numeric' => 'Latitude 5 must be a number.',
        'latitude_5.between' => 'Latitude 5 must be between -90 and 90.',

        'longitude_5.required' => 'Longitude 5 is required.',
        'longitude_5.numeric' => 'Longitude 5 must be a number.',
        'longitude_5.between' => 'Longitude 5 must be between -180 and 180.',

        'any_remarks.string' => 'Remarks must be text.',
        'any_remarks.max' => 'Remarks must not exceed 1000 characters.',
    ];

    $validatedData = $request->validate($validationRules, $customMessages);
    //abort(415, 'The uploaded image is not in proper format.');
    DB::beginTransaction();
    try {

        $user = auth()->user();
        $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();
        $specialSchool = SpecialSchool::where('user_table_id', $specialSchoolMapping->user_table_id)->first();

        $phase_no_latest = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)->latest('phase_no')->value('phase_no');
        $phase_no = ($phase_no_latest ?? 0) + 1;
        $schoolSystemGenRegNo = str_replace('/', '_', $specialSchool->school_system_gen_reg_no);

        $folderPath = public_path("special_school_files/{$schoolSystemGenRegNo}");
        /*A folder i.e. storage/special_school_files is created inside the root directory ssepd_ngo_working_portal/storage/special_school_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/special_school_files/{$schoolSystemGenRegNo}";

        $constructionImagePath = $folderPath . '/construction_images';
        $externalConstructionPath = $externalPath . '/construction_images';

        if (!file_exists($constructionImagePath)) {
            mkdir($constructionImagePath, 0755, true);
        }
        if (!file_exists($externalConstructionPath)) {
            mkdir($externalConstructionPath, 0755, true);
        }

        if ($request->hasFile('file_construction_image_1')) {
            $constructionFile_1 = $request->file('file_construction_image_1');
            $constructionExtension_1 = $constructionFile_1->getClientOriginalExtension();
            $constructionRandomName_1 = 'CONSTRUCTION_IMAGE_1_' . Str::random(40) . '.' . $constructionExtension_1;

            $constructionStoredPath_1 = $constructionFile_1->storeAs("special_school_files/{$schoolSystemGenRegNo}/construction_images", $constructionRandomName_1, 'public');
            copy(storage_path("app/public/{$constructionStoredPath_1}"), "{$constructionImagePath}/{$constructionRandomName_1}");
            copy(storage_path("app/public/{$constructionStoredPath_1}"), "{$externalConstructionPath}/{$constructionRandomName_1}");
        }

        if ($request->hasFile('file_construction_image_2')) {
            $constructionFile_2 = $request->file('file_construction_image_2');
            $constructionExtension_2 = $constructionFile_2->getClientOriginalExtension();
            $constructionRandomName_2 = 'CONSTRUCTION_IMAGE_2_' . Str::random(40) . '.' . $constructionExtension_2;

            $constructionStoredPath_2 = $constructionFile_2->storeAs("special_school_files/{$schoolSystemGenRegNo}/construction_images", $constructionRandomName_2, 'public');
            copy(storage_path("app/public/{$constructionStoredPath_2}"), "{$constructionImagePath}/{$constructionRandomName_2}");
            copy(storage_path("app/public/{$constructionStoredPath_2}"), "{$externalConstructionPath}/{$constructionRandomName_2}");
        }

        if ($request->hasFile('file_construction_image_3')) {
            $constructionFile_3 = $request->file('file_construction_image_3');
            $constructionExtension_3 = $constructionFile_3->getClientOriginalExtension();
            $constructionRandomName_3 = 'CONSTRUCTION_IMAGE_3_' . Str::random(40) . '.' . $constructionExtension_3;

            $constructionStoredPath_3 = $constructionFile_3->storeAs("special_school_files/{$schoolSystemGenRegNo}/construction_images", $constructionRandomName_3, 'public');
            copy(storage_path("app/public/{$constructionStoredPath_3}"), "{$constructionImagePath}/{$constructionRandomName_3}");
            copy(storage_path("app/public/{$constructionStoredPath_3}"), "{$externalConstructionPath}/{$constructionRandomName_3}");
        }

        if ($request->hasFile('file_construction_image_4')) {
            $constructionFile_4 = $request->file('file_construction_image_4');
            $constructionExtension_4 = $constructionFile_4->getClientOriginalExtension();
            $constructionRandomName_4 = 'CONSTRUCTION_IMAGE_4_' . Str::random(40) . '.' . $constructionExtension_4;

            $constructionStoredPath_4 = $constructionFile_4->storeAs("special_school_files/{$schoolSystemGenRegNo}/construction_images", $constructionRandomName_4, 'public');
            copy(storage_path("app/public/{$constructionStoredPath_4}"), "{$constructionImagePath}/{$constructionRandomName_4}");
            copy(storage_path("app/public/{$constructionStoredPath_4}"), "{$externalConstructionPath}/{$constructionRandomName_4}");
        }

        if ($request->hasFile('file_construction_image_5')) {
            $constructionFile_5 = $request->file('file_construction_image_5');
            $constructionExtension_5 = $constructionFile_5->getClientOriginalExtension();
            $constructionRandomName_5 = 'CONSTRUCTION_IMAGE_5_' . Str::random(40) . '.' . $constructionExtension_5;

            $constructionStoredPath_5 = $constructionFile_5->storeAs("special_school_files/{$schoolSystemGenRegNo}/construction_images", $constructionRandomName_5, 'public');
            copy(storage_path("app/public/{$constructionStoredPath_5}"), "{$constructionImagePath}/{$constructionRandomName_5}");
            copy(storage_path("app/public/{$constructionStoredPath_5}"), "{$externalConstructionPath}/{$constructionRandomName_5}");
        }

        $school_construction = new SpecialSchoolConstruction();
        $school_construction->management_id = $specialSchool->management_id;
        $school_construction->special_school_management_name = $specialSchool->special_school_management_name;
        $school_construction->special_school_id = $specialSchool->special_school_id;
        $school_construction->special_school_name = $specialSchool->special_school_name;
        $school_construction->school_system_gen_reg_no = $specialSchool->school_system_gen_reg_no;
        $school_construction->new_or_existing = $validatedData['new_or_existing'];
        $school_construction->file_construction_image_1 = $constructionStoredPath_1;
        $school_construction->latitude_1 = $validatedData['latitude_1'];
        $school_construction->longitude_1 = $validatedData['longitude_1'];
        $school_construction->file_construction_image_2 = $constructionStoredPath_2;
        $school_construction->latitude_2 = $validatedData['latitude_2'];
        $school_construction->longitude_2 = $validatedData['longitude_2'];
        $school_construction->file_construction_image_3 = $constructionStoredPath_3;
        $school_construction->latitude_3 = $validatedData['latitude_3'];
        $school_construction->longitude_3 = $validatedData['longitude_3'];
        $school_construction->file_construction_image_4 = $constructionStoredPath_4;
        $school_construction->latitude_4 = $validatedData['latitude_4'];
        $school_construction->longitude_4 = $validatedData['longitude_4'];
        $school_construction->file_construction_image_5 = $constructionStoredPath_5;
        $school_construction->latitude_5 = $validatedData['latitude_5'];
        $school_construction->longitude_5 = $validatedData['longitude_5'];
        $school_construction->any_remarks = $validatedData['any_remarks'];
        $school_construction->phase_no = $phase_no;
        $school_construction->school_address_type = $specialSchool->school_address_type;
        $school_construction->state_id = $specialSchool->state_id;
        $school_construction->district_id = $specialSchool->district_id;
        $school_construction->municipality_id = $specialSchool->municipality_id;
        $school_construction->ward_id = $specialSchool->ward_id;
        $school_construction->block_id = $specialSchool->block_id;
        $school_construction->gp_id = $specialSchool->gp_id;
        $school_construction->village_id = $specialSchool->village_id;
        $school_construction->pin = $specialSchool->pin;
        $school_construction->school_postal_address_at = $specialSchool->school_postal_address_at;
        $school_construction->school_postal_address_post = $specialSchool->school_postal_address_post;
        $school_construction->school_postal_address_via = $specialSchool->school_postal_address_via;
        $school_construction->school_postal_address_ps = $specialSchool->school_postal_address_ps;
        $school_construction->school_postal_address_district = $specialSchool->school_postal_address_district;
        $school_construction->school_postal_address_pin = $specialSchool->school_postal_address_pin;
        $school_construction->system_stored_latitude = $validatedData['latitude_1'];
        $school_construction->system_stored_longitude = $validatedData['longitude_1'];
        $school_construction->is_active = 'active';
        $school_construction->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $school_construction->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $school_construction->created_by = Auth::id() ?? null;
        $school_construction->no_of_image_uploaded = 5;
        $school_construction->which_govt = $specialSchool->which_govt;
        $school_construction->status = 1;
        $school_construction->save();

        DB::commit();
        return redirect()->route('admin.specialschoolconstructions.construction_timeline')->with('success', 'Image added successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🏫 Special School Construction form submission failed", [
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

public function approve_construction_status_store(Request $request, $id)
{
    $validatedData = $request->validate([
        'approve_status'    => 'required',
        'approver_remarks'  => 'nullable|string|max:1000',
    ]);

    DB::beginTransaction();
    try {
        $special_school = SpecialSchoolConstruction::where('special_school_id', $id)
        ->orderByDesc('phase_no')
        ->first();

        if (!$special_school) {
            return redirect()->back()->with('info', 'Something went wrong, kindly contact your Administrator.');
        }

        $special_school->update([
            'approve_status'    => $validatedData['approve_status'],
            'approver_remarks'  => $validatedData['approver_remarks'],
            'approved_date'     => now()->setTimezone('Asia/Kolkata')->toDateString(),
            'no_of_phase_approved' => $special_school->phase_no,
        ]);

        $applicationstagehistory = new ApplicationStageHistory();
        /*department_scheme_id Special School = 2*/
        $applicationstagehistory->department_scheme_id = 2;
        $applicationstagehistory->model_name = 'SpecialSchoolConstruction';
        $applicationstagehistory->model_table_id = $special_school->id;
        $applicationstagehistory->initial_model_table_id = $special_school->id;
        if ($special_school->approve_status == 1) {
            $applicationstagehistory->stage_id = 25;
            $applicationstagehistory->stage_name = 'Approved';
        } elseif ($special_school->approve_status == 2) {
            $applicationstagehistory->stage_id = 21;
            $applicationstagehistory->stage_name = 'Rejected by HO';
        } elseif ($special_school->approve_status == 3) {
            $applicationstagehistory->stage_id = 21;
            $applicationstagehistory->stage_name = 'Waiting for Varification';
        }      
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'Special School Construction Approver form submission Successfully.';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        return redirect()->back()->with('success', 'Construction status updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🏫 Special School Construction Approver form submission failed", [
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

public function all_in_one_approval()
{
    $special_school_construction = SpecialSchoolConstruction::where('status', 1)->orderBy('management_id', 'asc')->orderBy('special_school_id', 'asc')->get();
    return view('dashboard.special_school.construction_timeline_all_in_one_approval', compact('special_school_construction'));
}

public function school_wise_toilet_construction_report()
{
    $user = auth()->user();
    $userRole = $user->role_id;

    $specialSchoolMappingQuery = SpecialSchoolMapping::where('status', 1)
    ->with(['district', 'construction'])
    ->withMax('construction as latest_phase_no', 'phase_no');

    if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {

    } elseif (in_array($userRole, [4, 6])) {
        $district_id = DB::table('blocks')->where('block_id', $user->posted_block)->value('district_id');
        $specialSchoolMappingQuery->where('district_id', $district_id);
    } elseif ($userRole == 5) {
        $district_id = DB::table('municipalities')->where('municipality_id', $user->posted_municipality)->value('district_id');
        $specialSchoolMappingQuery->where('district_id', $district_id);
    } elseif (in_array($userRole, [8, 10])) {
        $district_id = DB::table('subdivisions')->where('subdivision_id', $user->posted_subdiv)->value('district_id');
        $specialSchoolMappingQuery->where('district_id', $district_id);
    } elseif (in_array($userRole, [9, 11])) {
        $specialSchoolMappingQuery->where('district_id', $user->posted_district);
    }

    if ($userRole == 22) {
        $specialSchoolMapping = SpecialSchoolMapping::where('status', 1)->with(['district', 'construction'])
        ->withMax('construction as latest_phase_no', 'phase_no')
        ->where('user_table_id', $user->user_table_id)
        ->first();

        if (!$specialSchoolMapping) {
            return redirect()->route('admin.specialschool.create')
            ->with('info', 'Kindly provide the basic information of the school to proceed further.');
        }

        $specialSchoolMapping = collect([$specialSchoolMapping]);
    } else {
        $specialSchoolMapping = $specialSchoolMappingQuery->get();
    }

    $specialSchoolMapping->transform(function ($school, $index) {
        if ($school->latest_phase_no) {
            $school->construction_status = "Phase {$school->latest_phase_no} Photo Updated";

            $latestConstruction = $school->construction()->latest('phase_no')->first();

            if (!$latestConstruction || $latestConstruction->approve_status == 0) {
                $school->approve_status_text = 'Pending at HO';
            } elseif ($latestConstruction->approve_status == 1) {
                $school->approve_status_text = 'Phase ' . $latestConstruction->no_of_phase_approved
                . ' Completed on ' . Carbon::parse($latestConstruction->approved_date)->format('d-m-Y');
            } elseif ($latestConstruction->approve_status == 2) {
                $school->approve_status_text = 'Phase ' . $latestConstruction->no_of_phase_approved
                . ' Rejected on ' . Carbon::parse($latestConstruction->approved_date)->format('d-m-Y');
            } elseif ($latestConstruction->approve_status == 3) {
                $school->approve_status_text = 'Phase ' . $latestConstruction->phase_no . ' is waiting for verification';
            } else {
                $school->approve_status_text = 'Not Yet Uploaded';
            }

            if ($latestConstruction) {
                $school->verifier_status = $latestConstruction->verifier_status;
                $school->latest_construction_id = $latestConstruction->id;
                $school->new_or_existing_text = $latestConstruction->new_or_existing == 1 ? 'New' :
                ($latestConstruction->new_or_existing == 2 ? 'Existing' : 'Not Yet Uploaded');
                $school->latest_construction_school_id = $latestConstruction->special_school_id;
            } else {
                $school->verifier_status = null;
                $school->latest_construction_id = null;
                $school->new_or_existing_text = 'Not Yet Uploaded';
                $school->latest_construction_school_id = null;
            }
        } else {
            $school->construction_status = "Not Yet Uploaded";
            $school->approve_status_text = "Not Yet Uploaded";
            $school->new_or_existing_text = 'Not Yet Uploaded';
            $school->latest_construction_id = null;
        }

        $school->sl_no = $index + 1;

        return $school;
    });

    return view('dashboard.special_school.report.school_wise_toilet_construction_report', compact('specialSchoolMapping'));
}

public function approve_construction_status_by_dsso_store(Request $request, $id)
{
    $validatedData = $request->validate([
        'verifier_status' => 'required|in:1,2',
        'dsso_verification_date' => 'required|date|before_or_equal:today',
        'dsso_verification_report' => 'required|file|mimes:pdf|max:2048',
        'dsso_verification_remark'  => 'nullable|string|max:1000',
    ]);

    DB::beginTransaction();
    try {

        $special_school = SpecialSchoolConstruction::where('special_school_id', $id)->orderByDesc('phase_no')->first();

        if (!$special_school) {
            Log::warning('DSSO Verification Failed: Special school not found', [
                'user_id' => auth()->id(),
                'school_id' => $id,
                'ip' => request()->ip(),
            ]);
            return redirect()->back()->with('info', 'Something went wrong, kindly contact your Administrator.');
        }

        if ($special_school->verifier_status == 1) {
            Log::info('DSSO Verification Skipped: Already verified', [
                'user_id' => auth()->id(),
                'school_id' => $id,
                'ip' => request()->ip(),
            ]);
            return redirect()->back()->with('info', 'You have already Verified this Toilet Construction.');
        }

        $schoolSystemGenRegNo = str_replace('/', '_', $special_school->school_system_gen_reg_no);

        $folderPath = public_path("special_school_files/{$schoolSystemGenRegNo}");
        /*A folder i.e. storage/special_school_files is created inside the root directory ssepd_ngo_working_portal/storage/special_school_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/special_school_files/{$schoolSystemGenRegNo}";

        $constructionImagePath = $folderPath . '/construction_images';
        $externalConstructionPath = $externalPath . '/construction_images';

        if (!file_exists($constructionImagePath)) {
            mkdir($constructionImagePath, 0755, true);
        }
        if (!file_exists($externalConstructionPath)) {
            mkdir($externalConstructionPath, 0755, true);
        }

        if ($request->hasFile('dsso_verification_report')) {
            $constructionFile_1 = $request->file('dsso_verification_report');
            $constructionExtension_1 = $constructionFile_1->getClientOriginalExtension();
            $constructionRandomName_1 = 'DSSO_VERIFICATION_REPORT_' . Str::random(40) . '.' . $constructionExtension_1;

            $constructionStoredPath_1 = $constructionFile_1->storeAs("special_school_files/{$schoolSystemGenRegNo}/construction_images", $constructionRandomName_1, 'public');
            copy(storage_path("app/public/{$constructionStoredPath_1}"), "{$constructionImagePath}/{$constructionRandomName_1}");
            copy(storage_path("app/public/{$constructionStoredPath_1}"), "{$externalConstructionPath}/{$constructionRandomName_1}");
        }

        $special_school->update([
            'verifier_status'    => '1',
            'dsso_verification_date'  => $validatedData['dsso_verification_date'],
            'dsso_verification_report'  => $constructionStoredPath_1,
            'verifier_user_id'  => Auth::id(),
            'dsso_verification_remark'  => $validatedData['dsso_verification_remark'],
        ]);

        $applicationstagehistory = new ApplicationStageHistory();
        /*department_scheme_id Special School = 2*/
        $applicationstagehistory->department_scheme_id = 2;
        $applicationstagehistory->model_name = 'SpecialSchoolConstruction';
        $applicationstagehistory->model_table_id = $special_school->id;
        $applicationstagehistory->initial_model_table_id = $special_school->id;
        if ($special_school->verifier_status == 1) {
            $applicationstagehistory->stage_id = 25;
            $applicationstagehistory->stage_name = 'Approved';
        } elseif ($special_school->verifier_status == 2) {
            $applicationstagehistory->stage_id = 21;
            $applicationstagehistory->stage_name = 'Rejected';
        }      
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'Special School Construction DSSO Verification form submission Successfully.';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        return redirect()->back()->with('info', 'DSSO Verification Successful.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🏫 Special School Construction DSSO Verification form submission failed", [
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

/*Admin Level report Developed on 20-04-2026*/
public function special_school_list()
{
    $user = auth()->user();
    $userRole = $user->role_id;

    $specialSchoolMapping = null;

    $query = SpecialSchoolMapping::where('special_school_mappings.status', 1)
    ->leftJoin('special_schools', function ($join) {
        $join->on('special_school_mappings.special_school_id', '=', 'special_schools.special_school_id')
        ->where('special_schools.status', 1);
    })
    ->leftJoin('districts', 'special_school_mappings.district_id', '=', 'districts.district_id')
    ->leftJoin('villages', 'special_schools.village_id', '=', 'villages.village_id')
    ->leftJoin('grampanchayats', 'special_schools.gp_id', '=', 'grampanchayats.gp_id')
    ->leftJoin('blocks', 'special_schools.block_id', '=', 'blocks.block_id')
    ->leftJoin('municipalities', 'special_schools.municipality_id', '=', 'municipalities.municipality_id')
    ->select(
        'special_school_mappings.id',
        'special_school_mappings.management_name',
        'special_school_mappings.management_id',
        'special_school_mappings.special_school_id',
        'special_school_mappings.special_school_name',
        'special_school_mappings.which_govt',

        'special_schools.school_establishment_date',
        'special_schools.school_category',
        'special_schools.school_type',
        'special_schools.act_reg_no',
        'special_schools.file_act_reg',

        'districts.district_name',
        \DB::raw("
            (
            SELECT COUNT(*) 
            FROM special_school_constructions ssc 
            WHERE ssc.special_school_id = special_school_mappings.special_school_id 
            AND ssc.status = 1
            ) as construction_count
            "),
        \DB::raw("
            (
            SELECT GROUP_CONCAT(
            CONCAT('Phase', ssc.no_of_phase_approved, ': ', DATE_FORMAT(ssc.approved_date, '%d %b %Y'))
            ORDER BY ssc.no_of_phase_approved ASC
            SEPARATOR ' || '
            )
            FROM special_school_constructions ssc
            WHERE ssc.special_school_id = special_school_mappings.special_school_id
            AND ssc.status = 1
            ) as phase_approval_details
            "),
        \DB::raw("
            (special_school_mappings.teaching_approved_staff_strength 
            + special_school_mappings.non_teaching_approved_staff_strength) 
            as approved_staff_total
            "),
        \DB::raw("
            (
            (special_school_mappings.teaching_approved_staff_strength 
            + special_school_mappings.non_teaching_approved_staff_strength)
            -
            (
            SELECT COUNT(*) 
            FROM special_school_staff ss 
            WHERE ss.special_school_id = special_school_mappings.special_school_id 
            AND ss.status = 1
            )
            ) as staff_gap
            "),
        \DB::raw("
            CASE 
            WHEN 
            (special_school_mappings.teaching_approved_staff_strength 
            + special_school_mappings.non_teaching_approved_staff_strength) = 0 
            THEN 0
            ELSE ROUND(
            (
            (
            SELECT COUNT(*) 
            FROM special_school_staff ss 
            WHERE ss.special_school_id = special_school_mappings.special_school_id 
            AND ss.status = 1
            ) * 100.0
            ) /
            (
            special_school_mappings.teaching_approved_staff_strength 
            + special_school_mappings.non_teaching_approved_staff_strength
            ), 2
            )
            END as staff_utilization
            "),
        \DB::raw("
            (
            SELECT COUNT(*) 
            FROM special_school_staff ss 
            WHERE ss.special_school_id = special_school_mappings.special_school_id 
            AND ss.status = 1
            ) as staff_count
            "),
        \DB::raw("
            CONCAT(
            IF(villages.village_name IS NOT NULL, CONCAT('Village: ', villages.village_name, ', '), ''),
            IF(grampanchayats.gp_name IS NOT NULL, CONCAT('GP: ', grampanchayats.gp_name, ', '), ''),
            IF(blocks.block_name IS NOT NULL, CONCAT('Block: ', blocks.block_name, ', '), ''),
            IF(municipalities.municipality_name IS NOT NULL, CONCAT('Municipality: ', municipalities.municipality_name, ', '), ''),
            IF(districts.district_name IS NOT NULL, CONCAT('District: ', districts.district_name), '')
            ) as full_address
            ")
    )->distinct();


    if (in_array($userRole, [4, 6])) {
        $query->where('special_schools.block_id', $user->posted_block);
    }

    elseif ($userRole == 5) {
        $query->where('special_schools.municipality_id', $user->posted_municipality);
    }

    elseif (in_array($userRole, [8, 10])) {

        $blockIds = Block::where('subdivision_id', $user->posted_subdiv)
        ->pluck('block_id');

        $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)
        ->pluck('municipality_id');

        $query->where(function ($q) use ($blockIds, $municipalityIds) {
            $q->whereIn('special_schools.block_id', $blockIds)
            ->orWhereIn('special_schools.municipality_id', $municipalityIds);
        });
    }

    elseif (in_array($userRole, [9, 11])) {
        $query->where('special_school_mappings.district_id', $user->posted_district);
    }

    elseif ($userRole == 22) {

        $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->where('status', 1)->firstOrFail();

        $specialSchool = SpecialSchoolMapping::where('special_school_mappings.status', 1)
        ->leftJoin('special_schools', function ($join) {
            $join->on('special_school_mappings.special_school_id', '=', 'special_schools.special_school_id')
            ->where('special_schools.status', 1);
        })
        ->where('special_school_mappings.user_table_id', $user->user_table_id)
        ->select(
            'special_school_mappings.*',
            'special_schools.special_school_management_name',
            'special_schools.school_establishment_date',
            'special_schools.school_category',
            'special_schools.school_type',
            'special_schools.act_reg_no',
            'special_schools.file_act_reg'
        )->distinct()->get();

        if ($specialSchool->isEmpty()) {
            return redirect()->route('admin.specialschool.create')
            ->with('info', 'Kindly provide the basic information of the school to proceed further.');
        }

        return view('dashboard.special_school.index', compact('specialSchool', 'specialSchoolMapping'));
    }

    $specialSchool = $query->orderBy('districts.district_name', 'asc')->orderBy('special_school_mappings.which_govt', 'asc')->get();

    return view('dashboard.special_school.special_school_list', compact('specialSchool', 'specialSchoolMapping'));
}

}