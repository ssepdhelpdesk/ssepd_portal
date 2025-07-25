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

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $specialSchool = SpecialSchool::where('special_school_id', $id)->firstOrFail();
        if (!$specialSchool) {
            return redirect()->route('admin.specialschool.index')->with('danger', 'Something went wrong. Please reach out to your system administrator.');
        }
        $specialSchoolConstruction = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)->get();
        $latestImage = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)
        ->orderBy('created_date', 'desc')
        ->first();

        $monthsAgo = null;
        $timeDifferenceText = null;

        if ($latestImage && $latestImage->created_date) {
            $created = Carbon::parse($latestImage->created_date)->startOfDay();
            $now = Carbon::now()->startOfDay();

            $diff = $created->diff($now);

            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->d;

            $parts = [];

            if ($years > 0) {
                $parts[] = $years . ' ' . Str::plural('year', $years);
            }
            if ($months > 0) {
                $parts[] = $months . ' ' . Str::plural('month', $months);
            }
            if ($days > 0 || empty($parts)) {
                $parts[] = $days . ' ' . Str::plural('day', $days);
            }

            $timeDifferenceText = implode(' ', $parts) . ' ago';
        }
        return view('dashboard.special_school.construction_timeline', compact(
            'specialSchool',
            'specialSchoolConstruction',
            'latestImage',
            'monthsAgo',
            'timeDifferenceText'
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
        $specialSchoolConstruction = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)->get();

        $latestImage = SpecialSchoolConstruction::where('special_school_id', $specialSchool->special_school_id)
        ->orderBy('created_date', 'desc')
        ->first();

        $monthsAgo = null;
        $timeDifferenceText = null;

        if ($latestImage && $latestImage->created_date) {
            $created = Carbon::parse($latestImage->created_date)->startOfDay();
            $now = Carbon::now()->startOfDay();

            $diff = $created->diff($now);

            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->d;

            $parts = [];

            if ($years > 0) {
                $parts[] = $years . ' ' . Str::plural('year', $years);
            }
            if ($months > 0) {
                $parts[] = $months . ' ' . Str::plural('month', $months);
            }
            if ($days > 0 || empty($parts)) {
                $parts[] = $days . ' ' . Str::plural('day', $days);
            }

            $timeDifferenceText = implode(' ', $parts) . ' ago';
        }

        return view('dashboard.special_school.construction_timeline', compact(
            'specialSchoolMapping',
            'specialSchool',
            'specialSchoolConstruction',
            'latestImage',
            'monthsAgo',
            'timeDifferenceText'
        ));
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
    public function construction_timeline_store(Request $request)
    {
        $validationRules = [
            'file_construction_image' => 'required|mimes:jpg,jpeg,png|max:3072',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'any_remarks' => 'nullable|string|max:1000',
        ];
        $customMessages = [
            'file_construction_image.required' => 'Geo tagged image is required.',
            'file_construction_image.mimes' => 'Only JPG, JPEG, or PNG files are allowed.',
            'file_construction_image.max' => 'Image size must not exceed 3MB.',

            'latitude.required' => 'Latitude is required.',
            'latitude.numeric' => 'Latitude must be a number.',
            'latitude.between' => 'Latitude must be between -90 and 90.',

            'longitude.required' => 'Longitude is required.',
            'longitude.numeric' => 'Longitude must be a number.',
            'longitude.between' => 'Longitude must be between -180 and 180.',

            'any_remarks.string' => 'Remarks must be text.',
            'any_remarks.max' => 'Remarks must not exceed 1000 characters.',
        ];
        $validatedData = $request->validate($validationRules, $customMessages);

        $user = auth()->user();
        $specialSchoolMapping = SpecialSchoolMapping::where('user_table_id', $user->user_table_id)->firstOrFail();
        $specialSchool = SpecialSchool::where('user_table_id', $specialSchoolMapping->user_table_id)->first();

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

        if ($request->hasFile('file_construction_image')) {
            $constructionFile = $request->file('file_construction_image');
            $constructionExtension = $constructionFile->getClientOriginalExtension();
            $constructionRandomName = 'CONSTRUCTION_IMAGE_' . Str::random(40) . '.' . $constructionExtension;

            $constructionStoredPath = $constructionFile->storeAs("special_school_files/{$schoolSystemGenRegNo}/construction_images", $constructionRandomName, 'public');
            copy(storage_path("app/public/{$constructionStoredPath}"), "{$constructionImagePath}/{$constructionRandomName}");
            copy(storage_path("app/public/{$constructionStoredPath}"), "{$externalConstructionPath}/{$constructionRandomName}");
        }

        $school_construction = new SpecialSchoolConstruction();
        $school_construction->management_id = $specialSchool->management_id;
        $school_construction->special_school_management_name = $specialSchool->special_school_management_name;
        $school_construction->special_school_id = $specialSchool->special_school_id;
        $school_construction->special_school_name = $specialSchool->special_school_name;
        $school_construction->school_system_gen_reg_no = $specialSchool->school_system_gen_reg_no;
        $school_construction->file_construction_image = $constructionStoredPath;
        $school_construction->latitude = $validatedData['latitude'];
        $school_construction->longitude = $validatedData['longitude'];
        $school_construction->any_remarks =$validatedData['any_remarks'];
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
        $school_construction->no_of_image_uploaded = 1;
        $school_construction->status = 1;
        $school_construction->save();

        return redirect()->route('admin.specialschoolconstructions.construction_timeline')->with('success', 'Image added successfully.');
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
