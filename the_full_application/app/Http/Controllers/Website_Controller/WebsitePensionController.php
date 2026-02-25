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
    BankMaster,
    District,
    Block,
    Subdivision,
    Municipality,
    Grampanchayat,
    WardMaster,
    Village,
    VisitorCount,
    User
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
        VisitorCount::create([
            'ip_address' => request()->ip(),
            'visit_date' => now('Asia/Kolkata')->toDateString(),
            'visit_time' => now('Asia/Kolkata')->toTimeString(),
        ]);

        $visitorCount = VisitorCount::count();
        return view('website.pension.index', compact('pensionType', 'visitorCount'));
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
        $schemeId = (int) $request->pension_type_id;

        $disabilitySchemes = [3,4,6];
        $widowSchemes      = [2,5];
        $tgScheme          = 9;

        $validator = Validator::make($request->all(), [
            'pension_type_id' => ['required','integer'],
            'beneficiary_image' => [
                'required','file','mimes:jpg,jpeg,png','max:300'
            ],
            'applicant_first_name'  => ['required','regex:/^[A-Z ]+$/','min:13','max:20'],
            'applicant_middle_name' => ['required','regex:/^[A-Z ]+$/','min:3','max:20'],
            'applicant_last_name'   => ['required','regex:/^[A-Z ]+$/','min:3','max:20'],
            'applicant_name'        => ['required','regex:/^[A-Z ]+$/','min:3','max:100'],
            'gender_id' => ['required', Rule::in([1,2,3])],
            'dob' => ['required','date','before:today'],
            'age' => ['required','integer'],
            'aadhaar_no' => ['required','digits:12'],
            'verified_aadhar' => ['required', Rule::in([1])],
            'verified_aadhar_remarks' => ['required','string'],
            'guardian_type' => ['required', Rule::in([1,2])],
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
                'nullable',
                'string',
                'max:30'
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
                'nullable',
                'date',
                'before_or_equal:today'
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
        ]);

/*if ($validator->fails()) {return back()->withErrors($validator)->withInput();}*/

$validator = Validator::make($request->all(), [ /* rules */ ]);

if ($validator->fails()) {
    return response()->json([
        'status' => false,
        'errors' => $validator->errors()
    ], 422);
}

return response()->json([
    'status' => true,
    'message' => 'Validation successful',
    'data' => $validator->validated()
], 200);
}



private function isValidAadhaar($aadhaar)
{
    if (!preg_match('/^[0-9]{12}$/', $aadhaar)) {
        return false;
    }

        // Implement Verhoeff algorithm if required
    return true;
}

}
