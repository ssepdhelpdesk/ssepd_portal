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
public function index($id)
{
    $id = $id;
    $specialSchool = SpecialSchool::where('special_school_id', $id)->firstOrFail();
    if (!$specialSchool) {
        return redirect()->route('admin.specialschool.index')->with('danger', 'Something went wrong. Please reach out to your system administrator.');
    }
    $specialSchoolConstruction = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)->first();
    $phaseNumbers = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)->pluck('phase_no')->unique()->values();

    return view('dashboard.special_school.construction_timeline_for_state', compact(
        'specialSchool',
        'specialSchoolConstruction',
        'phaseNumbers',
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

    $allConstructionRecords = SpecialSchoolConstruction::where('special_school_id', $id)->get();

    $phaseNumbers = $allConstructionRecords->pluck('phase_no')->unique()->sort()->values();

    $constructionByPhase = $allConstructionRecords->keyBy('phase_no');

    $latestImage = $allConstructionRecords->sortByDesc('created_date')->first();

    return view('dashboard.special_school.construction_timeline', compact(
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
        $school_construction->any_remarks =$validatedData['any_remarks'];
        $school_construction->phase_no = $phase_no;
        $school_construction->school_address_type = $specialSchool->school_address_type;
        $school_construction->state_id = $specialSchool->state_id;
        $school_construction->district_id = $specialSchool->district_id;
        $school_construction->municipality_id = $specialSchool->municipality_id;
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
        $school_construction->system_stored_latitude = $request->system_stored_latitude;
        $school_construction->system_stored_longitude = $request->system_stored_longitude;
        $school_construction->is_active = 'active';
        $school_construction->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $school_construction->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $school_construction->created_by = Auth::id() ?? null;
        $school_construction->no_of_image_uploaded = 5;
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
