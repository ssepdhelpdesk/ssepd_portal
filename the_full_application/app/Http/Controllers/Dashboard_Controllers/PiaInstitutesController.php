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
    PiaInstituteBenfDetails,
};

class PiaInstitutesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pia_institute_basic_details()
    {
        $userId = Auth::user();
        $piainstitute = PiaInstituteMaster::where('user_table_id', $userId->user_table_id)->where('status', 'Active')->firstOrFail();
        $gender = Gender::where('status', 1)->get();
        $bankmaster = BankMaster::where('is_active', 1)->orderBy('bank_ifsc', 'asc')->get();        
        if($piainstitute->basic_details_completed != 1) {
            return view('dashboard.pia_institutes.pia_institute_basic_details', compact('piainstitute', 'gender', 'bankmaster'));
        }
        return view('dashboard.pia_institutes.index', compact('piainstitute', 'gender', 'bankmaster'));
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
            $piainstitutemaster = PiaInstituteMaster::whereId($id)->firstOrFail();
            $previousId = PiaInstituteMaster::latest()->value('id') ?? 0;
            $currentDate = now()->format('d/m/Y');
            $randomNumber = mt_rand(1000, 9999);
            $piaSystemGeneratedRegNo = "SSEPD/PIA/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
            $piaSystemGenRegNo = str_replace('/', '_', $piaSystemGeneratedRegNo);
            $instituteSystemGeneratedRegNo = "SSEPD/INSTITUTE/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
            $instituteSystemGenRegNo = str_replace('/', '_', $instituteSystemGeneratedRegNo);

            $folderPath = public_path("pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}");
            /*A folder i.e. storage/pia_institute_files is created inside the root directory ssepd_ngo_working_portal/storage/pia_institute_files*/
            $externalBasePath = dirname(base_path());
            $externalPath = $externalBasePath . "/storage/pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}";

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

                $regCertStoredPath = $regCertFile->storeAs("pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}", $regCertRandomName, 'public');
                copy(storage_path("app/public/{$regCertStoredPath}"), "{$folderPath}/{$regCertRandomName}");
                copy(storage_path("app/public/{$regCertStoredPath}"), "{$externalPath}/{$regCertRandomName}");
            }

            $instituteTypes = [
                1 => 'Geriatric Center',
                2 => 'Disha Center',
                3 => 'Sahaya Center',
                4 => 'Old Age Home',
                5 => 'Half Way Home',
                6 => 'Therapeutic Center',
            ];
            $selectedId = $validatedData['institute_type_id'] ?? null;

            $piainstitutemaster->institute_name = $validatedData['institute_name'];
            $piainstitutemaster->institute_id = $piainstitutemaster->excel_institute_id;
            $piainstitutemaster->institute_master_id = $piainstitutemaster->excel_institute_master_id;
            $piainstitutemaster->institute_user_id = $user->user_table_id;
            $piainstitutemaster->institute_system_email_id = $user->email;
            $piainstitutemaster->institute_type = $instituteTypes[$selectedId] ?? null;
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
            $piainstitutemaster->pia_id = $piainstitutemaster->excel_pia_id;
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

            $user_table = User::where('user_table_id', $piainstitutemaster->user_table_id)->firstOrFail();
            $user_table->email = $validatedData['institute_email_id'];
            $user_table->mobile_no = $validatedData['nodal_officer_contact_number'];
            $user_table->email_verified_at = Carbon::now('Asia/Kolkata');
            $user_table->entry_dt = Carbon::now('Asia/Kolkata');
            $user_table->posted_district = $validatedData['district'];
            $user_table->last_req_time = Carbon::now('Asia/Kolkata');
            $user_table->save();

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

        $exists = PiaInstituteMaster::where('status', 'Active')->where('registration_no', $registration_no)->exists();
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

        $exists = PiaInstituteMaster::where('status', 'Active')->where('grantee_code', $grantee_code)->exists();
        if ($exists) {
            return response()->json(1);
        } else {
            return response()->json(0);
        }
    }

    public function check_institute_email_id(Request $request)
    {
        $institute_email_id = trim($request->institute_email_id);
        if (!$institute_email_id) {
            return response()->json(1);
        }

        $exists = PiaInstituteMaster::where('institute_email_id', $institute_email_id)->where('status', 'Active')->exists();
        if ($exists) {
            return response()->json(1);
        } else {
            return response()->json(0);
        }
    }

    public function index()
    {
        $userId = Auth::user();
        $piainstitute = PiaInstituteMaster::where('user_table_id', $userId->user_table_id)->where('status', 'Active')->firstOrFail();
        $gender = Gender::where('status', 1)->get();
        $bankmaster = BankMaster::where('is_active', 1)->orderBy('bank_ifsc', 'asc')->get();        
        if($piainstitute->basic_details_completed != 1) {
            return view('dashboard.pia_institutes.pia_institute_basic_details', compact('piainstitute', 'gender', 'bankmaster'));
        }
        return view('dashboard.pia_institutes.index', compact('piainstitute', 'gender', 'bankmaster'));
    }

    public function pia_institute_benf_details_store(Request $request, string $id)
    {
        $validationRules = [
            'name_of_the_beneficiary'   => 'required|string|max:255',
            'father_or_husband_name'    => 'required|string|max:255',
            'date_of_birth'             => 'required|date|before_or_equal:today',
            'date_of_joining'           => 'required|date|after:date_of_birth|before_or_equal:today',
            'age'                       => 'required|numeric|min:0',
            'beneficiary_mobile'        => 'required|digits:10',
            'gender'                   => 'required|exists:genders,id',
            'aadhaar_no'               => 'required|digits:12|unique:pia_institute_benf_details,aadhaar_no',
            'verified_aadhar'          => 'required|in:1',
            'verified_aadhar_remarks'  => 'nullable|string',
            'aadhaar_file'             => 'required|file|mimes:pdf|max:2048',
            'beneficiary_file'         => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'bank_ac_no'               => 'required|digits_between:6,20',
            'bank_ifsc'                => 'required|exists:bank_masters,bank_id',
            'beneficiary_bank_file'    => 'required|file|mimes:pdf|max:2048',
            'is_disabled'              => 'required|in:1,2',
            'udid_no'                  => 'required_if:is_disabled,1|nullable|string|max:50',
            'beneficiary_udid_file'    => 'required_if:is_disabled,1|nullable|file|mimes:pdf|max:2048',
            'disability_category'      => 'required_if:is_disabled,1|nullable|string|max:255',
            'ngo_address_type'         => 'required|in:1,2',
        ];

        $messages = [
            'name_of_the_beneficiary.required' => 'Please enter Beneficiary Name.',
            'father_or_husband_name.required'  => 'Please enter Father/Husband Name.',
            'date_of_birth.required'           => 'Please select DOB.',
            'date_of_birth.before_or_equal'    => 'DOB cannot be in future.',
            'date_of_joining.required'         => 'Please select Date of Joining.',
            'date_of_joining.after'            => 'Date of Joining must be after DOB.',
            'beneficiary_mobile.required'      => 'Please enter Mobile Number.',
            'beneficiary_mobile.digits'        => 'Mobile number must be 10 digits.',
            'gender.required'                 => 'Please select Gender.',
            'aadhaar_no.required'             => 'Please enter Aadhaar Number.',
            'aadhaar_no.digits'               => 'Aadhaar must be 12 digits.',
            'aadhaar_no.unique'               => 'This Aadhaar is already registered.',
            'verified_aadhar.required'        => 'Please verify Aadhaar before submitting.',
            'aadhaar_file.required'           => 'Please upload Aadhaar file.',
            'aadhaar_file.mimes'              => 'Only PDF allowed for Aadhaar.',
            'aadhaar_file.max'                => 'Aadhaar file must be less than 2MB.',
            'beneficiary_file.required'       => 'Please upload Beneficiary Image.',
            'beneficiary_file.image'          => 'File must be an image.',
            'beneficiary_file.mimes'          => 'Only JPG, JPEG, PNG allowed.',
            'beneficiary_file.max'            => 'Image must be less than 2MB.',
            'beneficiary_bank_file.required'  => 'Please upload Bank Passbook.',
            'beneficiary_bank_file.mimes'     => 'Only PDF allowed for Bank Passbook.',
            'bank_ac_no.required'             => 'Please enter Bank Account Number.',
            'bank_ac_no.digits_between'       => 'Bank Account must be between 6 to 20 digits.',
            'bank_ifsc.required'              => 'Please select IFSC Code.',
            'is_disabled.required'            => 'Please select disability status.',
            'udid_no.required_if'             => 'Please enter UDID Number.',
            'beneficiary_udid_file.required_if'=> 'Please upload UDID Certificate.',
            'disability_category.required_if' => 'Please enter Disability Category.',
            'ngo_address_type.required'       => 'Please select Address Type.',
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
            ]);
        } elseif ($request->ngo_address_type === "2") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'municipality' => 'required',
                'ward' => 'required',
                'pin' => 'required',
            ]);
        }
        $validatedData = $request->validate($validationRules, $messages);

        DB::beginTransaction();
        try {
        $user = auth()->user();
        $piainstitutemaster = PiaInstituteMaster::whereId($id)->firstOrFail();
        $user = auth()->user();
        $piainstitutemaster = PiaInstituteMaster::where('user_table_id', $user->user_table_id)->firstOrFail();
        $previousId = PiaInstituteBenfDetails::latest()->value('id') ?? 0;
        $currentDate = now()->format('d/m/Y');
        $randomNumber = mt_rand(1000, 9999);

        $piaSystemGenRegNo = str_replace('/', '_', $piainstitutemaster->pia_system_gen_reg_no);
        $instituteSystemGenRegNo = str_replace('/', '_', $piainstitutemaster->institute_system_gen_reg_no);


        $benfSystemGeneratedRegNo = "SSEPD/INSTBENF/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
        $benfSystemGenRegNo = str_replace('/', '_', $benfSystemGeneratedRegNo);

        $folderPath = public_path("pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}/{$benfSystemGenRegNo}");
        /*A folder i.e. storage/pia_institute_files is created inside the root directory ssepd_ngo_working_portal/storage/pia_institute_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}/{$benfSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('aadhaar_file')) {
            $aadhaarFile = $request->file('aadhaar_file');
            $aadhaarExtension = $aadhaarFile->getClientOriginalExtension();
            $aadhaarRandomName = 'PIA_INST_REG_CERT_' . Str::random(40) . '.' . $aadhaarExtension;

            $aadhaarStoredPath = $aadhaarFile->storeAs("pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}/{$benfSystemGenRegNo}", $aadhaarRandomName, 'public');
            copy(storage_path("app/public/{$aadhaarStoredPath}"), "{$folderPath}/{$aadhaarRandomName}");
            copy(storage_path("app/public/{$aadhaarStoredPath}"), "{$externalPath}/{$aadhaarRandomName}");
        }

        if ($request->hasFile('beneficiary_file')) {
            $benfImgFile = $request->file('beneficiary_file');
            $benfImgExtension = $benfImgFile->getClientOriginalExtension();
            $benfImgRandomName = 'PIA_INST_REG_CERT_' . Str::random(40) . '.' . $benfImgExtension;

            $benfImgStoredPath = $benfImgFile->storeAs("pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}/{$benfSystemGenRegNo}", $benfImgRandomName, 'public');
            copy(storage_path("app/public/{$benfImgStoredPath}"), "{$folderPath}/{$benfImgRandomName}");
            copy(storage_path("app/public/{$benfImgStoredPath}"), "{$externalPath}/{$benfImgRandomName}");
        }

        if ($request->hasFile('beneficiary_bank_file')) {
            $benfBankFile = $request->file('beneficiary_bank_file');
            $benfBankExtension = $benfBankFile->getClientOriginalExtension();
            $benfBankRandomName = 'PIA_INST_REG_CERT_' . Str::random(40) . '.' . $benfBankExtension;

            $benfBankStoredPath = $benfBankFile->storeAs("pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}/{$benfSystemGenRegNo}", $benfBankRandomName, 'public');
            copy(storage_path("app/public/{$benfBankStoredPath}"), "{$folderPath}/{$benfBankRandomName}");
            copy(storage_path("app/public/{$benfBankStoredPath}"), "{$externalPath}/{$benfBankRandomName}");
        }

        if ($request->hasFile('beneficiary_udid_file')) {
            $benfUdidFile = $request->file('beneficiary_udid_file');
            $benfUdidExtension = $benfUdidFile->getClientOriginalExtension();
            $benfUdidRandomName = 'PIA_INST_REG_CERT_' . Str::random(40) . '.' . $benfUdidExtension;

            $benfUdidStoredPath = $benfUdidFile->storeAs("pia_institute_files/{$piaSystemGenRegNo}/{$instituteSystemGenRegNo}/{$benfSystemGenRegNo}", $benfUdidRandomName, 'public');
            copy(storage_path("app/public/{$benfUdidStoredPath}"), "{$folderPath}/{$benfUdidRandomName}");
            copy(storage_path("app/public/{$benfUdidStoredPath}"), "{$externalPath}/{$benfUdidRandomName}");
        }

        $pia_institute_benf_details = new PiaInstituteBenfDetails();
        $pia_institute_benf_details->uuid = (string) Str::uuid();
        $pia_institute_benf_details->name_of_the_beneficiary = $validatedData['name_of_the_beneficiary'] ?? null;
        $pia_institute_benf_details->benf_system_gen_reg_no = $benfSystemGeneratedRegNo;
        $pia_institute_benf_details->father_or_husband_name = $validatedData['father_or_husband_name'] ?? null;
        $pia_institute_benf_details->date_of_birth = $validatedData['date_of_birth'] ?? null;
        $pia_institute_benf_details->age = $validatedData['age'] ?? null;
        $pia_institute_benf_details->beneficiary_mobile = $validatedData['beneficiary_mobile'] ?? null;
        $pia_institute_benf_details->gender = $validatedData['gender'] ?? null;
        $pia_institute_benf_details->aadhaar_no = $validatedData['aadhaar_no'] ?? null;
        $pia_institute_benf_details->verified_aadhar = $validatedData['verified_aadhar'] ?? null;
        $pia_institute_benf_details->verified_aadhar_remarks = $validatedData['verified_aadhar_remarks'] ?? null;
        $pia_institute_benf_details->aadhaar_hash = isset($validatedData['aadhaar_no']) ? hash('sha256', $validatedData['aadhaar_no']) : null;
        $pia_institute_benf_details->aadhaar_encrypted = isset($validatedData['aadhaar_no']) ? Crypt::encryptString($validatedData['aadhaar_no']) : null;
        $pia_institute_benf_details->aadhaar_file = $aadhaarStoredPath ?? null;
        $pia_institute_benf_details->beneficiary_file = $benfImgStoredPath ?? null;
        $pia_institute_benf_details->date_of_joining = $validatedData['date_of_joining'] ?? null;
        $pia_institute_benf_details->bank_ac_no = $validatedData['bank_ac_no'] ?? null;
        $pia_institute_benf_details->bank_ifsc = $validatedData['bank_ifsc'] ?? null;
        $pia_institute_benf_details->beneficiary_bank_file = $benfBankStoredPath ?? null;
        $pia_institute_benf_details->is_disabled = $validatedData['is_disabled'] ?? null;
        $pia_institute_benf_details->udid_no = $validatedData['udid_no'] ?? null;
        $pia_institute_benf_details->beneficiary_udid_file = $benfUdidStoredPath ?? null;
        $pia_institute_benf_details->disability_category = $validatedData['disability_category'] ?? null;
        $pia_institute_benf_details->benf_address_type = $validatedData['ngo_address_type'] ?? null;
        $pia_institute_benf_details->state_id = $validatedData['state'] ?? null;
        $pia_institute_benf_details->district_id = $validatedData['district'] ?? null;
        $pia_institute_benf_details->municipality_id = $request->input('municipality', null);
        $pia_institute_benf_details->ward_id = $request->input('ward', null);
        $pia_institute_benf_details->block_id = $request->input('block', null);
        $pia_institute_benf_details->gp_id = $request->input('grampanchayat', null);
        $pia_institute_benf_details->village_id = $request->input('village', null);
        $pia_institute_benf_details->pin = $validatedData['pin'] ?? null;
        $pia_institute_benf_details->benf_postal_address_at = $validatedData['ngo_postal_address_at'] ?? null;
        $pia_institute_benf_details->benf_postal_address_post = $validatedData['ngo_postal_address_post'] ?? null;
        $pia_institute_benf_details->benf_postal_address_via = $validatedData['ngo_postal_address_via'] ?? null;
        $pia_institute_benf_details->benf_postal_address_ps = $validatedData['ngo_postal_address_ps'] ?? null;
        $pia_institute_benf_details->benf_postal_address_district = $validatedData['ngo_postal_address_district'] ?? null;
        $pia_institute_benf_details->benf_postal_address_pin = $validatedData['ngo_postal_address_pin'] ?? null;
        $pia_institute_benf_details->pia_institute_master_institute_id = $piainstitutemaster->institute_master_id ?? null;
        $pia_institute_benf_details->pia_institute_master_institute_type_id = $piainstitutemaster->institute_type_id ?? null;
        $pia_institute_benf_details->is_active = 'active';
        $pia_institute_benf_details->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $pia_institute_benf_details->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $pia_institute_benf_details->created_by = Auth::id() ?? null;
        $pia_institute_benf_details->user_table_id = $piainstitutemaster->user_table_id;
        $pia_institute_benf_details->status = 1;
        $pia_institute_benf_details->save();

        $applicationstagehistory = new ApplicationStageHistory();
        $applicationstagehistory->department_scheme_id = 4;
        $applicationstagehistory->model_name = 'PiaInstituteBenfDetails';
        $applicationstagehistory->model_table_id = $pia_institute_benf_details->id;
        $applicationstagehistory->initial_model_table_id = $pia_institute_benf_details->id;
        $applicationstagehistory->stage_id = 37;
        $applicationstagehistory->stage_name = 'Application updated by User';
        $applicationstagehistory->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $applicationstagehistory->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $applicationstagehistory->created_by = Auth::id();
        $applicationstagehistory->created_by_remarks = 'PIA Institute Beneficiary Basic Details have been submitted';
        $ipAddress = request()->ip();
        $applicationstagehistory->created_by_ip_v_four = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ipAddress : null;
        $applicationstagehistory->created_by_ip_v_six = filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ipAddress : null;
        $applicationstagehistory->save();

        DB::commit();
        return redirect()->route('admin.piainstitutes.index')->with('success', 'Beneficiary Details have been submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🏫 The Beneficiary Details form of Pia/Institute has not been submitted..", [
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

    public function check_benf_aadhar(Request $request): JsonResponse
    {
        $aadhaar_no = $request->get('aadhaar_no');
        if (empty($aadhaar_no)) {
            return response()->json(2);
        }
        $aadharExistsInNgo = PiaInstituteBenfDetails::where('status', 1)->where('aadhaar_no', $aadhaar_no)->exists();
        return response()->json($aadharExistsInNgo ? 1 : 0);
    }

    public function check_benf_udid(Request $request)
    {
        $udid_no = trim($request->udid_no);
        if (!$udid_no) {
            return response()->json(1);
        }

        $exists = PiaInstituteBenfDetails::where('status', 1)->where('udid_no', $udid_no)->exists();
        if ($exists) {
            return response()->json(1);
        } else {
            return response()->json(0);
        }
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
