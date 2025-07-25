<?php

namespace App\Http\Controllers\Frontend_Controller;

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

class NgoRegdFrontendController extends Controller
{
    public function initial_part_one_store(Request $request)
    {
        $validationRules = [
            'ngo_registration_type' => 'required',
            'ngo_category' => 'required',
            'ngo_org_name' => 'required',
            'ngo_org_pan' => 'required|alpha_num|size:10',
            'ngo_org_email' => 'required|email',
            'ngo_org_phone' => 'required|digits:10',
        ];

        $addressMessage = '';
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

            $previousNgoOrgId = NgoRegistration::max('ngo_org_id');
            $currentNgoOrgId = $previousNgoOrgId ? $previousNgoOrgId + 1 : 1;

            $NgoRegistration = new NgoRegistration();
            $NgoRegistration->ngo_org_id = $currentNgoOrgId;
            $NgoRegistration->ngo_registration_type = $validatedData['ngo_registration_type'];
            $NgoRegistration->ngo_category = $validatedData['ngo_category'];
            $NgoRegistration->ngo_org_name = $validatedData['ngo_org_name'];
            $NgoRegistration->ngo_org_pan = $validatedData['ngo_org_pan'];
            $NgoRegistration->ngo_org_email = $validatedData['ngo_org_email'];
            $NgoRegistration->ngo_org_phone = $validatedData['ngo_org_phone'];
            $NgoRegistration->is_active = 'active';
            $NgoRegistration->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $NgoRegistration->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $NgoRegistration->created_by = Auth::id() ?? null;
            $NgoRegistration->application_stage_id = 1;
            $NgoRegistration->no_of_form_completed = 0;
            $NgoRegistration->user_table_id = 0;
            $NgoRegistration->status = 1;
            $NgoRegistration->save();

            $UserRegistration = new User();
            $UserRegistration->name = $validatedData['ngo_org_name'];
            $UserRegistration->email = $validatedData['ngo_org_email'];
            $UserRegistration->user_id = $finalGeneratedCode;
            $UserRegistration->mobile_no = $validatedData['ngo_org_phone'];
            $UserRegistration->profile_photo = 'https://w7.pngwing.com/pngs/821/542/png-transparent-invisible-disability-accessibility-wheelchair-learning-disability-wheelchair-blue-text-logo.png';
            $UserRegistration->profile_photo_path = 'https://w7.pngwing.com/pngs/821/542/png-transparent-invisible-disability-accessibility-wheelchair-learning-disability-wheelchair-blue-text-logo.png';
            $UserRegistration->email_verified_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
            $UserRegistration->password = Hash::make('123456');
            $UserRegistration->created_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
            $UserRegistration->updated_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
            $UserRegistration->save();

            $NgoRegistration->user_table_id = $UserRegistration->id;
            $NgoRegistration->save();

            $role = Role::find(16);
            if ($role) {
                $UserRegistration->assignRole($role->name);
            } else {
                throw new Exception("Role with ID 16 does not exist.");
            }

            $applicationstagehistory = new ApplicationStageHistory();
            $applicationstagehistory->department_scheme_id = 1;
            $applicationstagehistory->model_name = 'NgoRegistration';
            $applicationstagehistory->model_table_id = $NgoRegistration->id;
            $applicationstagehistory->stage_id = 1;
            $applicationstagehistory->stage_name = 'Pending for final submit';
            $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();;
            $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $applicationstagehistory->created_by = Auth::id() ?? null;
            $applicationstagehistory->created_by_remarks = 'NGO Registered from the public.';
            $ipAddress = request()->ip();
            $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
            $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
            $applicationstagehistory->save();
            DB::commit();
            $ngoData = [
                'ngoRegdPhase' => 'initial',
                'registrationNumber' => $ngoSystemGeneratedRegNo,
                'ngoOrgName' => $validatedData['ngo_org_name'],
                'ngoOrgEmail' => $validatedData['ngo_org_email'],
                'ngoOrgPhone' => $validatedData['ngo_org_phone'],
                'ngoUserId' => $finalGeneratedCode,
                'ngoUserPassword' => '123456',
            ];
            Mail::to($validatedData['ngo_org_email'])->send(new NgoRegistrationMail($ngoData));
            return redirect()->route('admin.ngo.index')->with('success', 'NGO 1st step initial registration successful.')->with('addressMessage', $addressMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            $errorResponse = [ 'status' => 'error', 'message' => 'NGO 1st step initial registration failed.', 'error_details' => $e->getMessage(), 'timestamp' => now()->toDateTimeString(),];

            \Log::error(json_encode($errorResponse, JSON_PRETTY_PRINT));

            return redirect()->back()
            ->withErrors(['error' => json_encode(['status' => 'error', 'message' => 'Something went wrong.', 'details' => 'Please try again.'], JSON_PRETTY_PRINT)])->withInput();
        }
    }

    public function part_one_store(Request $request)
    {
        $validationRules = [
            'ngo_category' => 'required',
            'ngo_org_name' => 'required',
            'ngo_org_pan' => 'required|alpha_num|size:10',
            'ngo_org_pan_file' => 'required|file|mimes:pdf|max:2048',
            'ngo_org_email' => 'required|email',
            'ngo_org_phone' => 'required|digits:10',
            'ngo_org_website' => 'required|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|max:255',
            'ngo_address_type' => 'required|in:1,2',
            'ngo_registered_with' => 'required',
            'ngo_other_reg_act' => 'nullable|string',
            'ngo_type_of_vo_or_ngo' => 'required',
            'ngo_reg_no' => 'required|max:50',
            'ngo_file_rc' => 'required|file|mimes:pdf|max:2048',
            'ngo_organisation_type' => 'required',
            'ngo_file_byelaws' => 'required|file|mimes:pdf|max:2048',
            'ngo_reg_velidity_available' => 'required|in:1,0',
            'ngo_date_of_registration' => 'required|date|before_or_equal:today',
            'nature_of_organisation' => 'required|array',
            'nature_of_organisation_other' => 'nullable|string',
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
                'ngo_postal_address_at' => 'required',
                'ngo_postal_address_post' => 'required',
                'ngo_postal_address_ps' => 'required',
                'ngo_postal_address_pin' => 'required|digits:6',
            ]);
        } elseif ($request->ngo_address_type === "2") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'municipality' => 'required',
                'pin' => 'required',
                'ngo_postal_address_at' => 'required',
                'ngo_postal_address_post' => 'required',
                'ngo_postal_address_ps' => 'required',
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

            if (!file_exists($folderPath)) { mkdir($folderPath, 0755, true);}
            if (!file_exists($externalPath)) { mkdir($externalPath, 0755, true);}

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
            $NgoRegistration->ngo_postal_address_ps = $validatedData['ngo_postal_address_ps'];
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
            $UserRegistration->profile_photo = 'profile_pic.jpg';
            $UserRegistration->profile_photo_path = 'storage/profile-photos/profile_pic.jpg';
            $UserRegistration->email_verified_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
            $UserRegistration->password = Hash::make('123456');
            $UserRegistration->created_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
            $UserRegistration->updated_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
            $UserRegistration->save();

            $NgoRegistration->user_table_id = $UserRegistration->id;
            $NgoRegistration->save();

            $role = Role::find(16);
            if ($role) {
                $UserRegistration->assignRole($role->name);
            } else {
                throw new Exception("Role with ID 16 does not exist.");
            }

            $applicationstagehistory = new ApplicationStageHistory();
            $applicationstagehistory->department_scheme_id = 1;
            $applicationstagehistory->model_name = 'NgoRegistration';
            $applicationstagehistory->model_table_id = $NgoRegistration->id;
            $applicationstagehistory->stage_id = 1;
            $applicationstagehistory->stage_name = 'Pending for final submit';
            $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();;
            $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $applicationstagehistory->created_by = Auth::id() ?? null;
            $applicationstagehistory->created_by_remarks = 'NGO Registered from the public.';
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
            \Log::error("NGO Part One Registration failed: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
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
}
