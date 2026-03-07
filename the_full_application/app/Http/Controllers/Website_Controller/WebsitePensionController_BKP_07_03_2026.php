<?php

namespace App\Http\Controllers\Website_Controller;

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
    NsapPortal27Jan2026Csv,
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
    Habitation
};

use App\Models\Pension\{
    PensionType,
    SsepdPension
};

class WebsitePensionController extends Controller
{
/**
* Display a listing of the resource.
*/
public function index()
{
    $pensionType = PensionType::where('db_status', 1)->get();
    $district = District::where('is_active', 'active')->orderBy('district_name')->get();
    $gender = Gender::where('status', 1)->get();
    $bank = BankMaster::where('status', 1)->orderBy('bank_ifsc')->get();
    VisitorCount::create([
        'ip_address' => request()->ip(),
        'visit_date' => now('Asia/Kolkata')->toDateString(),
        'visit_time' => now('Asia/Kolkata')->toTimeString(),
    ]);

    $visitorCount = VisitorCount::count();
    return view('website.pension.index', compact('pensionType', 'visitorCount', 'gender', 'district', 'bank'));
}

public function benf_aadhar_verification(Request $request)
{
    $request->validate([
        'aadhaar_no' => 'required|digits:12',
        'applicant_name' => 'required|string',
    ]);

    try {
        $response = Http::withOptions([
            'verify' => false,
            'timeout' => 60,
            'connect_timeout' => 20,
            'curl' => [
                CURLOPT_SSLVERSION   => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            ],
        ])
        ->withHeaders([
            'Accept' => 'application/json',
            'User-Agent' => 'PostmanRuntime/7.36.0',
        ])
        ->asForm()
        ->post('https://ssepd.gov.in:8443/swp/api/nfbs/requestToUid', [
            'aadhaar_no' => trim($request->aadhaar_no),
            'name'       => trim($request->applicant_name),
        ]);

        if ($response->successful()) {
            return response()->json([
                'status' => true,
                'data'   => trim($response->body()),
            ]);
        }

        return response()->json([
            'status' => false,
            'http_code' => $response->status(),
            'response'  => $response->body(),
        ], 422);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'exception' => $e->getMessage(),
        ], 500);
    }
}

public function benf_udid_verification(Request $request)
{
    $request->validate([
        'udid' => 'required|string',
        'dob'  => 'required|date'
    ]);

    try {

        $response = Http::timeout(15)
        ->asForm()
        ->post(
            'http://117.211.75.216:8080/swp/api/nfbs/requestToUdid',
            [
                'udid' => $request->udid,
                'dob'  => $request->dob
            ]
        );

        if (!$response->successful()) {

            Log::error('UDID API HTTP Failure', [
                'status_code' => $response->status(),
                'body'        => $response->body()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'UDID service unavailable'
            ]);
        }

        $result = $response->json();

        if (isset($result['status']) && $result['status'] === "true") {

            return response()->json([
                'status' => true,
                'data'   => $result['result']
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => $result['message'] ?? 'Verification failed'
        ]);

    } catch (\Throwable $e) {

        Log::error('UDID API Exception', [
            'error' => $e->getMessage(),
            'line'  => $e->getLine(),
            'file'  => $e->getFile()
        ]);

        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong while verifying UDID'
        ]);
    }
}

public function store(Request $request)
{
    try{
        Log::info('SSEPD Pension Store Request Started: ' . json_encode([
            'request_data' => $request->all(),
            'files' => $request->allFiles(),
            'user_id' => Auth::id(),
            'ip' => $request->ip()
        ], JSON_PRETTY_PRINT));
        $schemeId = (int) $request->pension_type_id;

        $disabilitySchemes = [3,4,6];
        $widowSchemes      = [2,5];
        $tgScheme          = 9;

        $validator = Validator::make($request->all(), [
            'pension_type_id' => ['required','integer'],
            'beneficiary_image' => [
                'required','file','mimes:jpg,jpeg,png','max:300'
            ],
            'applicant_first_name'  => ['required','regex:/^[A-Z ]+$/','min:3','max:20'],
            'applicant_middle_name' => ['required','regex:/^[A-Z ]+$/','min:3','max:20'],
            'applicant_last_name'   => ['required','regex:/^[A-Z ]+$/','min:3','max:20'],
            'applicant_name'        => ['required','regex:/^[A-Z ]+$/','min:3','max:100'],
            'gender_id' => ['required', Rule::in([1,2,3])],
            'dob' => ['required','date','before:today'],
            'age' => ['required','integer'],
            'aadhaar_no' => ['required','digits:12', Rule::unique('ssepd_pensions','aadhar_number')],
            'verified_aadhar' => ['required', Rule::in([1])],
            'verified_aadhar_remarks' => ['required','string'],
            'guardian_type' => ['required', Rule::in([1,2,3])],
            'guardian_name' => ['required','string','min:3','max:100'],
            'caste_id' => ['required', Rule::in([1,2,3,4,5])],
            'mobile_no' => ['required','digits:10'],
            'district_id'      => ['required','integer'],
            'sub_division_id'  => ['required','integer'],
            'address_type_id'  => ['required', Rule::in([1,2])],
            'block_id' => [
                Rule::requiredIf(fn() => $request->address_type_id == 2),
                'nullable','integer'
            ],
            'gp_id' => [
                Rule::requiredIf(fn() => $request->address_type_id == 2),
                'nullable','integer'
            ],
            'village_id' => [
                Rule::requiredIf(fn() => $request->address_type_id == 2),
                'nullable','integer'
            ],
            'municipality_id' => [
                Rule::requiredIf(fn() => $request->address_type_id == 1),
                'nullable','integer'
            ],
            'ward_id' => [
                Rule::requiredIf(fn() => $request->address_type_id == 1),
                'nullable','integer'
            ],
            'house_no' => ['required','string','max:50'],
            'pin_code' => ['required','digits:6'],
            'udid_no' => [
                Rule::requiredIf(fn() => in_array($schemeId, $disabilitySchemes)),
                'nullable', 'string', 'max:30'
            ],
            'disability_category_id' => [
                Rule::requiredIf(fn() => in_array($schemeId,$disabilitySchemes)),
                'nullable','integer'
            ],
            'disability_percentage' => [
                Rule::requiredIf(fn() => in_array($schemeId,$disabilitySchemes)),
                'nullable','integer','between:40,100'
            ],
            'disability_type_condition_id' => [
                Rule::requiredIf(fn() => in_array($schemeId, $disabilitySchemes)),
                'nullable',
                Rule::in([1,2]),
            ],
            'disability_validity_date' => [
                Rule::requiredIf(fn() => in_array($schemeId, $disabilitySchemes) && $request->disability_type_condition_id == 1),
                'nullable', 'date', 'after_or_equal:today'
            ],
            'disability_document' => [
                Rule::requiredIf(fn() => in_array($schemeId,$disabilitySchemes)),
                'nullable','file','mimes:pdf','max:300'
            ],
            'death_self_certificate' => [
                Rule::requiredIf(fn() => in_array($schemeId,$widowSchemes)),
                'nullable','file','mimes:pdf','max:300'
            ],
            'tg_registration_no' => [
                Rule::requiredIf(fn() => $schemeId == $tgScheme),
                'nullable','string','max:50'
            ],
            'tg_certificate' => [
                Rule::requiredIf(fn() => $schemeId == $tgScheme),
                'nullable','file','mimes:pdf','max:300'
            ],
            'bank_account_type' => ['required', Rule::in([1,2])],
            'account_holder_name' => ['required','string','max:150'],
            'second_account_holder_name' => [
                Rule::requiredIf(fn() => $request->bank_account_type == 2),
                'nullable',
                'string',
                'max:100'
            ],
            'account_number' => ['required','regex:/^[0-9]{9,18}$/'],
            'ifsc_code' => [
                'required'
            ],

            'income_certificate'  => ['required','file','mimes:pdf','max:300'],
            'thumb_signature'     => ['required','file','mimes:pdf','max:300'],
            'aadhaar_document'    => ['required','file','mimes:pdf','max:300'],
            'age_proof'           => ['required','file','mimes:pdf','max:300'],
            'passbook_document'   => ['required','file','mimes:pdf','max:300'],
            'additional_document' => ['required','file','mimes:pdf','max:300'],
        ], [
            'pension_type_id.required' => 'Please select Pension Scheme.',
            'pension_type_id.integer'  => 'Invalid Pension Scheme selected.',
            'beneficiary_image.required' => 'Beneficiary photo is mandatory.',
            'beneficiary_image.mimes'    => 'Photo must be JPG or PNG format.',
            'beneficiary_image.max'      => 'Photo size must not exceed 300 KB.',
            'applicant_first_name.required' => 'First name is required.',
            'applicant_first_name.regex'    => 'First name must contain only capital letters.',
            'applicant_first_name.min'      => 'First name must be at least 3 characters.',
            'applicant_first_name.max'      => 'First name must not exceed 20 characters.',
            'applicant_middle_name.required' => 'Middle name is required.',
            'applicant_middle_name.regex'    => 'Middle name must contain only capital letters.',
            'applicant_middle_name.min'      => 'Middle name must be at least 3 characters.',
            'applicant_middle_name.max'      => 'Middle name must not exceed 20 characters.',
            'applicant_last_name.required' => 'Last name is required.',
            'applicant_last_name.regex'    => 'Last name must contain only capital letters.',
            'applicant_last_name.min'      => 'Last name must be at least 3 characters.',
            'applicant_last_name.max'      => 'Last name must not exceed 20 characters.',
            'applicant_name.required' => 'Full applicant name is required.',
            'applicant_name.regex'    => 'Full name must contain only capital letters.',
            'applicant_name.min'      => 'Full name must be at least 3 characters.',
            'applicant_name.max'      => 'Full name must not exceed 100 characters.',
            'gender_id.required' => 'Please select Gender.',
            'gender_id.in'       => 'Invalid Gender selected.',
            'dob.required' => 'Date of Birth is required.',
            'dob.date'     => 'Invalid Date of Birth.',
            'dob.before'   => 'Date of Birth must be before today.',
            'age.required' => 'Age must be provided.',
            'age.integer'  => 'Age must be a valid number.',
            'aadhaar_no.required' => 'Aadhaar number is required.',
            'aadhaar_no.digits'   => 'Aadhaar number must be exactly 12 digits.',
            'verified_aadhar.required' => 'Aadhaar verification is required.',
            'verified_aadhar.in'       => 'Invalid Aadhaar verification selection.',
            'verified_aadhar_remarks.required' => 'Aadhaar verification remarks are required.',
            'guardian_type.required' => 'Please select Guardian Type.',
            'guardian_type.in'       => 'Invalid Guardian Type selected.',
            'guardian_name.required' => 'Guardian Name is required.',
            'guardian_name.min'      => 'Guardian Name must be at least 3 characters.',
            'guardian_name.max'      => 'Guardian Name must not exceed 100 characters.',
            'caste_id.required' => 'Please select Caste.',
            'caste_id.in'       => 'Invalid Caste selected.',
            'mobile_no.required' => 'Mobile number is required.',
            'mobile_no.digits'   => 'Mobile number must be exactly 10 digits.',
            'district_id.required'     => 'Please select District.',
            'sub_division_id.required' => 'Please select Sub Division.',
            'address_type_id.required' => 'Please select Address Type.',
            'address_type_id.in'       => 'Invalid Address Type selected.',
            'block_id.required' => 'Block is required for Rural Address.',
            'gp_id.required'    => 'Gram Panchayat is required for Rural Address.',
            'village_id.required' => 'Village is required for Rural Address.',
            'municipality_id.required' => 'Municipality is required for Urban Address.',
            'ward_id.required'         => 'Ward is required for Urban Address.',
            'house_no.required' => 'House number is required.',
            'pin_code.required' => 'PIN Code is required.',
            'pin_code.digits'   => 'PIN Code must be 6 digits.',
            'udid_no.required' => 'UDID number is required for Disability Pension.',
            'disability_category_id.required' => 'Disability Category is required.',
            'disability_percentage.required'  => 'Disability Percentage is required.',
            'disability_percentage.between'   => 'Disability Percentage must be between 40% and 100%.',
            'disability_type_condition_id.required' => 'Disability Type Condition is required.',
            'disability_type_condition_id.in' => 'Invalid Disability Type Condition selected.',
            'disability_validity_date.required' => 'Disability Validity Date is required for Temporary Disability.',
            'disability_validity_date.date' => 'Invalid Disability Validity Date.',
            'disability_validity_date.before_or_equal' => 'Disability Validity Date cannot be in the future.',
            'disability_document.required' => 'Disability Certificate document is required.',
            'disability_document.mimes'    => 'Disability document must be PDF format.',
            'disability_document.max'      => 'Disability document size must not exceed 300 KB.',
            'death_self_certificate.required' => 'Death Self Certificate is required for Widow Pension.',
            'death_self_certificate.mimes'    => 'Death Self Certificate must be PDF format.',
            'death_self_certificate.max'      => 'Death Self Certificate size must not exceed 300 KB.',
            'tg_registration_no.required' => 'TG Registration Number is required.',
            'tg_certificate.required'     => 'TG Certificate is required.',
            'tg_certificate.mimes'        => 'TG Certificate must be PDF format.',
            'tg_certificate.max'          => 'TG Certificate size must not exceed 300 KB.',
            'bank_account_type.required' => 'Please select Bank Account Type.',
            'bank_account_type.in'       => 'Invalid Bank Account Type selected.',
            'account_holder_name.required' => 'Account Holder Name is required.',
            'second_account_holder_name.required' => 'Second Account Holder Name is required for Joint Account.',
            'account_number.required' => 'Bank Account Number is required.',
            'account_number.regex'    => 'Account number must be between 9 and 18 digits.',
            'ifsc_code.required'      => 'IFSC Code is required.',
            'income_certificate.required'  => 'Income Certificate must be uploaded.',
            'income_certificate.mimes'     => 'Income Certificate must be PDF format.',
            'income_certificate.max'       => 'Income Certificate size must not exceed 300 KB.',
            'thumb_signature.required' => 'Thumb Impression / Signature is required.',
            'thumb_signature.mimes'    => 'Thumb Impression / Signature must be PDF format.',
            'thumb_signature.max'      => 'Thumb Impression / Signature size must not exceed 300 KB.',
            'aadhaar_document.required' => 'Aadhaar Document must be uploaded.',
            'aadhaar_document.mimes'    => 'Aadhaar Document must be PDF format.',
            'aadhaar_document.max'      => 'Aadhaar Document size must not exceed 300 KB.',
            'age_proof.required' => 'Age Proof document is required.',
            'age_proof.mimes'    => 'Age Proof document must be PDF format.',
            'age_proof.max'      => 'Age Proof document size must not exceed 300 KB.',
            'passbook_document.required' => 'Passbook copy must be uploaded.',
            'passbook_document.mimes'    => 'Passbook copy must be PDF format.',
            'passbook_document.max'      => 'Passbook copy size must not exceed 300 KB.',
            'additional_document.required' => 'Additional Document is required.',
            'additional_document.mimes'    => 'Additional Document must be PDF format.',
            'additional_document.max'      => 'Additional Document size must not exceed 300 KB.',
        ]);
Log::info("Validation Input Data:\n" . json_encode($validator->getData(), JSON_PRETTY_PRINT));
if ($validator->fails()) {
    return response()->json([
        'status' => false,
        'errors' => $validator->errors()
    ], 422);
}

$validated = $validator->validated();

$aadhaarExists = SsepdPension::where(function ($query) use ($validated) {
    $query->where('aadhar_number', $validated['aadhaar_no'])
    ->orWhere('aadhaar_no_by_user', $validated['aadhaar_no']);
})
->exists();

Log::info(
    "Aadhaar Check Result:\n" .
    json_encode([
        'aadhaar_no' => $validated['aadhaar_no'],
        'already_registered' => $aadhaarExists,
        'checked_columns' => ['aadhar_number', 'aadhaar_no_by_user'],
        'user_id' => Auth::id(),
        'ip' => request()->ip()
    ], JSON_PRETTY_PRINT)
);

if ($aadhaarExists) {
    return response()->json([
        'status' => false,
        'errors' => [
            'aadhaar_no' => ['Aadhaar already registered.']
        ]
    ], 422);
}

$previousId = SsepdPension::latest()->value('id') ?? 0;
$currentDate = now()->format('d/m/Y');
$randomNumber = mt_rand(1000, 9999);
$pensionSystemGeneratedRegNo = "SSEPD/PENSION/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
$pensionSystemGenRegNo = str_replace('/', '_', $pensionSystemGeneratedRegNo);

Log::info(
    "Generated Registration Number:\n" . json_encode([
        'application_no' => $pensionSystemGeneratedRegNo,
        'folder_name' => $pensionSystemGenRegNo
    ], JSON_PRETTY_PRINT)
);

$folderPath = public_path("pension_files/{$pensionSystemGenRegNo}");
/*A folder i.e. storage/pension_files is created inside the root directory ssepd_ngo_working_portal/storage/pension_files*/
$externalBasePath = dirname(base_path());
$externalPath = $externalBasePath . "/storage/pension_files/{$pensionSystemGenRegNo}";

if (!file_exists($folderPath)) {
    mkdir($folderPath, 0755, true);
}
if (!file_exists($externalPath)) {
    mkdir($externalPath, 0755, true);
}

$benfImageStoredPath = null;
$dpDocStoredPath = null;
$incomeDocStoredPath = null;
$thumbSignStoredPath = null;
$aadhaarDocStoredPath = null;
$ageDocStoredPath = null;
$passbookDocStoredPath = null;
$addlDocStoredPath = null;
$tgDocStoredPath = null;
$deathSelfCerDocStoredPath = null;

if ($request->hasFile('beneficiary_image')) {
    $benfImageFile = $request->file('beneficiary_image');
    $benfImageExtension = $benfImageFile->getClientOriginalExtension();
    $benfImageRandomName = 'BENEFICIARY_IMAGE_' . Str::random(7) . '.' . $benfImageExtension;

    $benfImageStoredPath = $benfImageFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $benfImageRandomName, 'public');
    copy(storage_path("app/public/{$benfImageStoredPath}"), "{$folderPath}/{$benfImageRandomName}");
    copy(storage_path("app/public/{$benfImageStoredPath}"), "{$externalPath}/{$benfImageRandomName}");

    Log::info('Beneficiary Image Details', [
        'original_name' => $benfImageFile->getClientOriginalName(),
        'size' => $benfImageFile->getSize()
    ]);
}

if ($request->hasFile('disability_document')) {
    $dpDocFile = $request->file('disability_document');
    $dpDocExtension = $dpDocFile->getClientOriginalExtension();
    $dpDocRandomName = 'DISABILITY_DOCUMENT_' . Str::random(7) . '.' . $dpDocExtension;

    $dpDocStoredPath = $dpDocFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $dpDocRandomName, 'public');
    copy(storage_path("app/public/{$dpDocStoredPath}"), "{$folderPath}/{$dpDocRandomName}");
    copy(storage_path("app/public/{$dpDocStoredPath}"), "{$externalPath}/{$dpDocRandomName}");
}

if ($request->hasFile('income_certificate')) {
    $incomeDocFile = $request->file('income_certificate');
    $incomeDocExtension = $incomeDocFile->getClientOriginalExtension();
    $incomeDocRandomName = 'INCOME_CERTIFICATE_' . Str::random(7) . '.' . $incomeDocExtension;

    $incomeDocStoredPath = $incomeDocFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $incomeDocRandomName, 'public');
    copy(storage_path("app/public/{$incomeDocStoredPath}"), "{$folderPath}/{$incomeDocRandomName}");
    copy(storage_path("app/public/{$incomeDocStoredPath}"), "{$externalPath}/{$incomeDocRandomName}");
}

if ($request->hasFile('thumb_signature')) {
    $thumbSignFile = $request->file('thumb_signature');
    $thumbSignExtension = $thumbSignFile->getClientOriginalExtension();
    $thumbSignRandomName = 'THUMB_SIGNATURE_' . Str::random(7) . '.' . $thumbSignExtension;

    $thumbSignStoredPath = $thumbSignFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $thumbSignRandomName, 'public');
    copy(storage_path("app/public/{$thumbSignStoredPath}"), "{$folderPath}/{$thumbSignRandomName}");
    copy(storage_path("app/public/{$thumbSignStoredPath}"), "{$externalPath}/{$thumbSignRandomName}");
}

if ($request->hasFile('aadhaar_document')) {
    $aadhaarDocFile = $request->file('aadhaar_document');
    $aadhaarDocExtension = $aadhaarDocFile->getClientOriginalExtension();
    $aadhaarDocRandomName = 'AADHAAR_DOCUMENT_' . Str::random(7) . '.' . $aadhaarDocExtension;

    $aadhaarDocStoredPath = $aadhaarDocFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $aadhaarDocRandomName, 'public');
    copy(storage_path("app/public/{$aadhaarDocStoredPath}"), "{$folderPath}/{$aadhaarDocRandomName}");
    copy(storage_path("app/public/{$aadhaarDocStoredPath}"), "{$externalPath}/{$aadhaarDocRandomName}");
}

if ($request->hasFile('age_proof')) {
    $ageDocFile = $request->file('age_proof');
    $ageDocExtension = $ageDocFile->getClientOriginalExtension();
    $ageDocRandomName = 'AGE_PROOF_' . Str::random(7) . '.' . $ageDocExtension;

    $ageDocStoredPath = $ageDocFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $ageDocRandomName, 'public');
    copy(storage_path("app/public/{$ageDocStoredPath}"), "{$folderPath}/{$ageDocRandomName}");
    copy(storage_path("app/public/{$ageDocStoredPath}"), "{$externalPath}/{$ageDocRandomName}");
}

if ($request->hasFile('passbook_document')) {
    $passbookDocFile = $request->file('passbook_document');
    $passbookDocExtension = $passbookDocFile->getClientOriginalExtension();
    $passbookDocRandomName = 'PASSBOOK_DOCUMENT_' . Str::random(7) . '.' . $passbookDocExtension;

    $passbookDocStoredPath = $passbookDocFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $passbookDocRandomName, 'public');
    copy(storage_path("app/public/{$passbookDocStoredPath}"), "{$folderPath}/{$passbookDocRandomName}");
    copy(storage_path("app/public/{$passbookDocStoredPath}"), "{$externalPath}/{$passbookDocRandomName}");
}

if ($request->hasFile('additional_document')) {
    $addlDocFile = $request->file('additional_document');
    $addlDocExtension = $addlDocFile->getClientOriginalExtension();
    $addlDocRandomName = 'ADDITIONAL_DOCUMENT_' . Str::random(7) . '.' . $addlDocExtension;

    $addlDocStoredPath = $addlDocFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $addlDocRandomName, 'public');
    copy(storage_path("app/public/{$addlDocStoredPath}"), "{$folderPath}/{$addlDocRandomName}");
    copy(storage_path("app/public/{$addlDocStoredPath}"), "{$externalPath}/{$addlDocRandomName}");
}

if ($request->hasFile('tg_certificate')) {
    $tgDocFile = $request->file('tg_certificate');
    $tgDocExtension = $tgDocFile->getClientOriginalExtension();
    $tgDocRandomName = 'TG_DOCUMENT_' . Str::random(7) . '.' . $tgDocExtension;

    $tgDocStoredPath = $tgDocFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $tgDocRandomName, 'public');
    copy(storage_path("app/public/{$tgDocStoredPath}"), "{$folderPath}/{$tgDocRandomName}");
    copy(storage_path("app/public/{$tgDocStoredPath}"), "{$externalPath}/{$tgDocRandomName}");
}

if ($request->hasFile('death_self_certificate')) {
    $deathSelfCerDocFile = $request->file('death_self_certificate');
    $deathSelfCerDocExtension = $deathSelfCerDocFile->getClientOriginalExtension();
    $deathSelfCerDocRandomName = 'DEATH_SELF_CER_DOCUMENT_' . Str::random(7) . '.' . $deathSelfCerDocExtension;

    $deathSelfCerDocStoredPath = $deathSelfCerDocFile->storeAs("pension_files/{$pensionSystemGenRegNo}", $deathSelfCerDocRandomName, 'public');
    copy(storage_path("app/public/{$deathSelfCerDocStoredPath}"), "{$folderPath}/{$deathSelfCerDocRandomName}");
    copy(storage_path("app/public/{$deathSelfCerDocStoredPath}"), "{$externalPath}/{$deathSelfCerDocRandomName}");
}

$applicationStage = ApplicationStage::where('stage_id', 3)->value('stage_name');
Log::info(
    "Application Stage Name:\n" . json_encode([
        'application_no' => $applicationStage
    ], JSON_PRETTY_PRINT)
);

$ssepdpension = new SsepdPension();
$ssepdpension->uuid = Str::uuid();
$ssepdpension->pension_type_id = $validated['pension_type_id'];
$ssepdpension->mbpy_application_id = null;
$ssepdpension->nsap_application_id = null;
$ssepdpension->mbpy_application_migration_Id = null;
$ssepdpension->nsap_application_migration_Id = null;
$ssepdpension->mbpy_id = null;
$ssepdpension->nsap_id = null;
$ssepdpension->pension_scheme_id = null;
$ssepdpension->mbpy_scheme_id = null;
$ssepdpension->nsap_scheme_id = null;
$ssepdpension->stage_id = '3';
$ssepdpension->status = $applicationStage;
$ssepdpension->application_active = 'ACTIVE';
$ssepdpension->verify_for_sanction = '0';
$ssepdpension->which_govt = '1';
$ssepdpension->applicant_name = strtoupper(preg_replace('/^[^A-Z]+|[^A-Z ]+$/', '', trim($validated['applicant_name'])));
$ssepdpension->guardian_type = $validated['guardian_type'];
$ssepdpension->father_husband_name = strtoupper(trim($validated['guardian_name']));
$ssepdpension->father_name = null;
$ssepdpension->mother_name = null;
$ssepdpension->first_name = strtoupper(preg_replace('/^[^A-Z]+|[^A-Z ]+$/', '', trim($validated['applicant_first_name'])));
$ssepdpension->middle_name = strtoupper(preg_replace('/^[^A-Z]+|[^A-Z ]+$/', '', trim($validated['applicant_middle_name'])));
$ssepdpension->last_name = strtoupper(preg_replace('/^[^A-Z]+|[^A-Z ]+$/', '', trim($validated['applicant_last_name'])));
$ssepdpension->applicant_dob = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['dob'])->format('Y-m-d');
$ssepdpension->age = $validated['age'];
$ssepdpension->applicant_age = $validated['age'];
$ssepdpension->gender_id = $validated['gender_id'];
$ssepdpension->caste_id = $validated['caste_id'];
$ssepdpension->mobile_number = $validated['mobile_no'];
$ssepdpension->district_id = $validated['district_id'];
$ssepdpension->subdivision_id = $validated['sub_division_id'];
$ssepdpension->block_id = $validated['block_id'] ?? null;
$ssepdpension->gp_id = $validated['gp_id'] ?? null;
$ssepdpension->village_id = $validated['village_id'] ?? null;
$ssepdpension->municipality_id = $validated['municipality_id'] ?? null;
$ssepdpension->ward_id = $validated['ward_id'] ?? null;
$ssepdpension->habitation_id = null;
$ssepdpension->address_type = $validated['address_type_id'];
$ssepdpension->ulb_type_id = null;
$ssepdpension->district_name = null;
$ssepdpension->block_name = null;
$ssepdpension->municipality_name = null;
$ssepdpension->gp_name = null;
$ssepdpension->ward_name = null;
$ssepdpension->ward_number = null;
$ssepdpension->city_town_name = null;
$ssepdpension->house_plot_number = $validated['house_no'];
$ssepdpension->pin_number = $validated['pin_code'];
$ssepdpension->withdraw_through_id = null;
$ssepdpension->uid_number = null;
$ssepdpension->aadhar_number = $validated['aadhaar_no'];
$ssepdpension->aadhaar_no_by_user = $validated['aadhaar_no'];
$ssepdpension->aadhaar_hash = hash('sha256', $validated['aadhaar_no']);
$ssepdpension->aadhaar_encrypted = null;
$ssepdpension->verified_aadhar = $validated['verified_aadhar'];
$ssepdpension->verified_aadhar_remarks = trim($validated['verified_aadhar_remarks']);
$ssepdpension->bank_tbl_id = null;
$ssepdpension->bank_account_po_number = $validated['account_number'];
$ssepdpension->bank_po_account_number = null;
$ssepdpension->ifsc_code = $validated['ifsc_code'];
$ssepdpension->sanction_amount = '0';
$ssepdpension->application_number = $pensionSystemGeneratedRegNo;
$ssepdpension->sanction_order_number = null;
$ssepdpension->mbpy_sanction_order_no = null;
$ssepdpension->nsap_sanction_order_no = null;
$ssepdpension->sanction_date = null;
$ssepdpension->disbursement_date = null;
$ssepdpension->pension_with_effect_from = null;
$ssepdpension->disability_type_id = $validated['disability_category_id'] ?? null;
$ssepdpension->disability_percentage = $validated['disability_percentage'] ?? 0;
$ssepdpension->disability_document_path = $dpDocStoredPath;
$ssepdpension->disability_type_Condition_date = $validated['disability_validity_date'] ?? null;
$ssepdpension->bpl_number = null;
$ssepdpension->bpl_scan_copy_path = null;
$ssepdpension->ration_card_no = null;
$ssepdpension->ration_card_scan_copy_path = null;
$ssepdpension->upload_image = $benfImageStoredPath;
$ssepdpension->upload_signature_thumb = $thumbSignStoredPath;
$ssepdpension->aadhar_scan_copy_path = $aadhaarDocStoredPath;
$ssepdpension->age_proof_scan_copy_Path = $ageDocStoredPath;
$ssepdpension->income_certificate_path = $incomeDocStoredPath;
$ssepdpension->additional_certificate_path = $addlDocStoredPath;
$ssepdpension->death_certificate_path = $deathSelfCerDocStoredPath;
$ssepdpension->attach_Addtional_Document = $addlDocStoredPath;
$ssepdpension->tg_certf_path = $tgDocStoredPath;
$ssepdpension->tg_reg_no = $validated['tg_registration_no'] ?? null;
$ssepdpension->self_declartion_certificate = $deathSelfCerDocStoredPath;
$ssepdpension->misa_certificate = null;
$ssepdpension->is_aadhar_verify = $validated['verified_aadhar'];
$ssepdpension->verification_report = null;
$ssepdpension->udid_cerificateNo = $validated['udid_no'] ?? null;
$ssepdpension->pensioner_sanctionId = null;
$ssepdpension->rejection_message = null;
$ssepdpension->rejected_aadhar = null;
$ssepdpension->without_bpl_remark = null;
$ssepdpension->remark = null;
$ssepdpension->invalid_reason = null;
$ssepdpension->payable_at = null;
$ssepdpension->sanctioned_for_life_time = null;
$ssepdpension->case_record_number = null;
$ssepdpension->serial_number_of_application = null;
$ssepdpension->sub_collector_signature = null;
$ssepdpension->sub_collector_signature_updated_date = null;
$ssepdpension->digitization_status = null;
$ssepdpension->subhadra_id = null;
$ssepdpension->declaration_1 = null;
$ssepdpension->declaration_2 = null;
$ssepdpension->declaration_3 = null;
$ssepdpension->declaration_4 = null;
$ssepdpension->declaration_5 = null;
$ssepdpension->is_active = 'active';
$ssepdpension->db_status = '1';
$ssepdpension->created_by = Auth::id() ?? null;
$ssepdpension->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
$ssepdpension->created_on = now()->setTimezone('Asia/Kolkata')->toDateString();
$ssepdpension->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
$ssepdpension->created_by_user_table_id = Auth::id() ?? 1;
$ssepdpension->updated_by = Auth::id() ?? null;
$ssepdpension->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
$ssepdpension->updated_on = now()->setTimezone('Asia/Kolkata')->toDateString();
$ssepdpension->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
$ssepdpension->created_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
$ssepdpension->updated_at = now()->setTimezone('Asia/Kolkata')->toDateTimeString();
$ssepdpension->save();

Log::info(
    "Pension Application Stored:\n" .
    json_encode([
        'validated_request' => $validated,
        'stored_record' => $ssepdpension->toArray(),
        'application_number' => $pensionSystemGeneratedRegNo,
        'user_id' => Auth::id()
    ], JSON_PRETTY_PRINT)
);

return response()->json([
    'status' => true,
    'message' => 'Application submitted successfully.',
    'data' => $validator->validated()
], 200);

} catch (\Throwable $e) {

    Log::error(
        "SSEPD Pension Store Error:\n" . json_encode([
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ], JSON_PRETTY_PRINT)
    );

    return response()->json([
        'status' => false,
        'message' => 'Internal Server Error',
        'error' => $e->getMessage()
    ], 500);
}
}

public function getSubdivisionsByDistrict($district_id)
{
    $subdivisions = Subdivision::where('district_id', $district_id)
    ->where('is_active', 'active')
    ->orderBy('subdivision_name', 'asc')
    ->get(['subdivision_id', 'subdivision_name']);

    return response()->json([
        'status' => true,
        'data'   => $subdivisions
    ]);
}

public function getBlocksBySubdivision($subdivision_id)
{
    $blocks = Block::where('subdivision_id', $subdivision_id)
    ->where('is_active', 'active')
    ->orderBy('block_name', 'asc')
    ->get(['block_id', 'block_name']);

    return response()->json([
        'status' => true,
        'data'   => $blocks
    ]);
}

public function getGrampanchyatsByBlock($block_id)
{
    $grampanchayats = Grampanchayat::where('block_id', $block_id)
    ->where('is_active', 'active')
    ->orderBy('gp_name', 'asc')
    ->get(['gp_id', 'gp_name']);

    return response()->json([
        'status' => true,
        'data'   => $grampanchayats
    ]);
}

public function getVillagesByGrampanchyat($gp_id)
{
    $villages = Village::where('gp_id', $gp_id)
    ->where('is_active', 'active')
    ->orderBy('village_name', 'asc')
    ->get(['village_id', 'village_name']);

    return response()->json([
        'status' => true,
        'data'   => $villages
    ]);
}

public function getMunicipalitiesBySubdivision($subdivision_id)
{
    $municipalities = Municipality::where('subdivision_id', $subdivision_id)
    ->where('is_active', 'active')
    ->orderBy('municipality_name', 'asc')
    ->get(['municipality_id', 'municipality_name']);

    return response()->json([
        'status' => true,
        'data'   => $municipalities
    ]);
}

public function getWardsByMunicipality($municipality_id)
{
    $wards = WardMaster::where('municipal_area_code', $municipality_id)
    ->where('is_active', '1')
    ->orderBy('ward_code', 'asc')
    ->get(['ward_code', 'ward_name']);

    return response()->json([
        'status' => true,
        'data'   => $wards
    ]);
}

}
