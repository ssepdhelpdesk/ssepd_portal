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
use Illuminate\Support\Facades\Route;

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

class WebsiteNsapDumpCOntroller extends Controller
{
/**
* Display a listing of the resource.
*/
public function index()
{
    $district = NsapPortal27Jan2026Csv::query()->select('district')->whereNotNull('district')->distinct()->orderBy('district')->get();
    $area = NsapPortal27Jan2026Csv::query() ->selectRaw(" CASE WHEN UPPER(TRIM(area)) IN ('R', 'RURAL') THEN 'R' WHEN UPPER(TRIM(area)) IN ('U', 'URBAN') THEN 'U' END AS area")->whereNotNull('area')->distinct()->orderBy('area')->get();
    VisitorCount::create([
        'ip_address' => request()->ip(),
        'visit_date' => now('Asia/Kolkata')->toDateString(),
        'visit_time' => now('Asia/Kolkata')->toTimeString(),
    ]);

    $visitorCount = VisitorCount::count();

    return view('website.dbtconsent.index', compact('district', 'area', 'visitorCount'));
}

public function datatable(Request $request)
{
    if (!$request->district || !$request->area || !$request->block) {
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'counters' => [
                'totalActive' => 0,
                'schemeCountOap' => 0,
                'schemeCountDp' => 0,
                'schemeCountOther' => 0,
            ]
        ]);
    }

    $baseQuery = NsapPortal27Jan2026Csv::query()
    ->whereNotIn('scheme', ['IGNOAPS', 'IGNDPS', 'IGNWPS', 'NFBS'])
    ->whereNot('disbursement_mode', 'BANK')
    ->where('marked_for_dbt', '0')
    ->where('district', $request->district)
    ->where('area', $request->area)
    ->where('sub_district_municipality', $request->block)
    ->orderBy('scheme', 'asc')
    ->orderBy('applicant_name', 'asc');

    if ($request->gp) {
        $baseQuery->where('gram_panchayat_ward', $request->gp);
    }

    $totalActive = (clone $baseQuery)->where('status', 'Active')->count();

    $schemeCountOap = (clone $baseQuery)
    ->whereIn('scheme', ['MBPOAP'])
    ->count();

    $schemeCountDp = (clone $baseQuery)
    ->whereIn('scheme', ['MBPDP', 'MBPSDP'])
    ->count();

    $schemeCountOther = (clone $baseQuery)
    ->whereNotIn('scheme', ['MBPOAP', 'MBPDP', 'MBPSDP'])->count();

    return DataTables::of(
        $baseQuery->select([
            'id',
            'uuid',
            'applicant_name',
            'father_husband_name',
            'scheme',
            'sanction_date',
            'sanction_order_no',
            'disbursement_mode',
            'disbursement_upto',
            'district',
            'area',
            'sub_district_municipality',
            'gram_panchayat_ward',
            'aadhaar_no_by_user',
            'status'
        ])
    )
    ->filter(function ($query) use ($request) {
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                ->orWhere('father_husband_name', 'like', "%{$search}%")
                ->orWhere('sanction_order_no', 'like', "%{$search}%")
                ->orWhere('aadhaar_no_by_user', 'like', "%{$search}%");
            });
        }
    })
    ->addIndexColumn()
    ->editColumn('area', fn($r) =>
        strtoupper(trim($r->area)) === 'R' ? 'Rural' : 'Urban'
    )
    ->editColumn('sanction_date', fn($r) =>
        is_numeric($r->sanction_date)
        ? Carbon::create(1899, 12, 30)->addDays((int)$r->sanction_date)->diffForHumans()
        : ($r->sanction_date ? Carbon::parse($r->sanction_date)->diffForHumans() : '-')
    )
    ->editColumn('disbursement_upto', fn($r) =>
        is_numeric($r->disbursement_upto)
        ? Carbon::create(1899, 12, 30)->addDays((int)$r->disbursement_upto)->format('d M Y')
        : ($r->disbursement_upto ? Carbon::parse($r->disbursement_upto)->format('d M Y') : '-')
    )
    ->addColumn('action', function ($row) {
        $url = route('website.pensioners.dbt_consent_form', $row->uuid);
        // use -> '.$url.' instead of javascript:void(0);

        return '
        <div class="d-flex align-items-center">
        <a href="'.$url.'" target="_blank" rel="noopener noreferrer" class="d-inline-flex fs-14 me-1 action-icon"><i class="isax isax-eye"></i></a>
        </div>
        ';
    })
    ->rawColumns(['action'])
    ->with([
        'counters' => [
            'totalActive' => $totalActive,
            'schemeCountOap' => $schemeCountOap,
            'schemeCountDp' => $schemeCountDp,
            'schemeCountOther' => $schemeCountOther,
        ]
    ])
    ->toJson();
}

public function dbt_consent_form(Request $request, string $uuid)
{
    $nsapPortal27Jan2026CsvData = NsapPortal27Jan2026Csv::where('uuid', $uuid)->firstOrFail();
    $bankMaster = BankMaster::where('is_active', 1)->orderBy('bank_ifsc')->get();
    $block = Block::where('district_id', $nsapPortal27Jan2026CsvData->district_id)->where('is_active', 'active')->orderBy('block_name')->get();
    $municipality = Municipality::where('district_id', $nsapPortal27Jan2026CsvData->district_id)->where('is_active', 'active')->orderBy('municipality_name')->get();
    return view('website.dbtconsent.dbt_consent_form', compact('nsapPortal27Jan2026CsvData', 'bankMaster', 'block', 'municipality', 'uuid'));
}

public function checkAadhaarDuplicate(Request $request)
{
    $request->validate([
        'aadhaar_no' => 'required|digits:12',
        'uuid' => 'nullable|string'
    ]);

    $aadhaar = trim($request->aadhaar_no);
    $uuid = trim($request->uuid);

    $duplicate = NsapPortal27Jan2026Csv::where('aadhaar_no_by_user', $aadhaar)
    ->where('uuid', '!=', $uuid)
    ->exists();

    if ($duplicate) {
        return response()->json([
            'status' => false,
            'message' => 'This Aadhaar number is already submitted.'
        ]);
    }

    return response()->json([
        'status' => true,
        'message' => 'Aadhaar is available.'
    ]);
}

public function dbt_consent_store_form(Request $request, $uuid)
{
    Log::info('DBT Consent submission started', ['uuid' => $uuid]);

    $validationRules = [
        'name_of_the_beneficiary' => 'required|string|max:150',
        'gender' => 'required|in:M,F,O',
        'dob' => 'required|date',
        'aadhaar_no' => 'required|digits:12',
        'verified_aadhar' => 'required|in:1',
        'verified_aadhar_remarks' => 'required',
        'address_type' => 'required|in:1,2',
        'block_id' => 'nullable|required_if:address_type,1',
        'gp_id' => 'nullable|required_if:address_type,1',
        'village_id' => 'nullable|required_if:address_type,1',
        'ulb_id' => 'nullable|required_if:address_type,2',
        'ward_id' => 'nullable|required_if:address_type,2',
        'pin' => 'required|digits:6',
        'ifsc' => 'required|string',
        'bank_po_account' => 'required|digits_between:9,20',
        'bank_account_file' => 'required|file|mimes:pdf|max:300',
    ];

    $messages = [
        'verified_aadhar.required' => 'Please verify Aadhaar before submitting.',
        'verified_aadhar.in' => 'Demographic mismatch detected. Please verify that the Aadhaar number and beneficiary details are correct.',
    ];

    $validatedData = $request->validate($validationRules, $messages);

    Log::info('Validation successful', ['uuid' => $uuid]);

    DB::beginTransaction();

    try {

        $village = null;

        if ($validatedData['address_type'] == 1) {
            $village = Village::where('village_id', $validatedData['village_id'])
            ->value('village_name');

            Log::info('Village fetched', [
                'village_id' => $validatedData['village_id'],
                'village_name' => $village
            ]);
        }

        $folderPath = public_path("dbt_consent");
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/dbt_consent";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
            Log::info('Public folder created', ['path' => $folderPath]);
        }

        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
            Log::info('External storage folder created', ['path' => $externalPath]);
        }

        $bankAccountStoredPath = null;

        if ($request->hasFile('bank_account_file')) {

            Log::info('Bank account file upload started');

            $bankAccountFile = $request->file('bank_account_file');
            $bankAccountExtension = $bankAccountFile->getClientOriginalExtension();

            $bankAccountRandomName = 'BANK_ACCOUNT_' . Str::random(40) . '.' . $bankAccountExtension;

            $bankAccountStoredPath = $bankAccountFile->storeAs("dbt_consent", $bankAccountRandomName, 'public');

            Log::info('File stored in storage', [
                'file_name' => $bankAccountRandomName,
                'path' => $bankAccountStoredPath
            ]);

            copy(storage_path("app/public/{$bankAccountStoredPath}"), "{$folderPath}/{$bankAccountRandomName}");
            copy(storage_path("app/public/{$bankAccountStoredPath}"), "{$externalPath}/{$bankAccountRandomName}");

            Log::info('File copied to public and external directories');
        }

        $nsapPortal27Jan2026Csv = NsapPortal27Jan2026Csv::where('uuid', $uuid)->firstOrFail();

        Log::info('Beneficiary record fetched', ['uuid' => $uuid]);

        $nsapPortal27Jan2026Csv->name_as_per_aadhaar = $validatedData['name_of_the_beneficiary'];
        $nsapPortal27Jan2026Csv->gender_by_user = $validatedData['gender'];
        $nsapPortal27Jan2026Csv->dob = $validatedData['dob'];
        $nsapPortal27Jan2026Csv->age_by_user = Carbon::parse($validatedData['dob'])->age;

        $nsapPortal27Jan2026Csv->aadhaar_no_by_user = $validatedData['aadhaar_no'];
        $nsapPortal27Jan2026Csv->aadhaar_hash = hash('sha256', $validatedData['aadhaar_no']);
        $nsapPortal27Jan2026Csv->aadhaar_encrypted = Crypt::encryptString($validatedData['aadhaar_no']);

        Log::info('Aadhaar processed (hashed & encrypted)', ['uuid' => $uuid]);

        $nsapPortal27Jan2026Csv->verified_aadhar = $validatedData['verified_aadhar'];
        $nsapPortal27Jan2026Csv->verified_aadhar_remarks = $validatedData['verified_aadhar_remarks'];

        $nsapPortal27Jan2026Csv->block_or_ulb = $validatedData['address_type'];

        $nsapPortal27Jan2026Csv->block_id = $validatedData['block_id'] ?? null;
        $nsapPortal27Jan2026Csv->gp_id = $validatedData['gp_id'] ?? null;
        $nsapPortal27Jan2026Csv->village_id = $validatedData['village_id'] ?? null;
        $nsapPortal27Jan2026Csv->village = $village;

        $nsapPortal27Jan2026Csv->municipality_id = $validatedData['ulb_id'] ?? null;
        $nsapPortal27Jan2026Csv->ward_id = $validatedData['ward_id'] ?? null;

        $nsapPortal27Jan2026Csv->pin = $validatedData['pin'];
        $nsapPortal27Jan2026Csv->ifsc_code = $validatedData['ifsc'];
        $nsapPortal27Jan2026Csv->bank_po_account = $validatedData['bank_po_account'];
        $nsapPortal27Jan2026Csv->bank_account_file = $bankAccountStoredPath;

        $nsapPortal27Jan2026Csv->disbursement_mode = "Bank";
        $nsapPortal27Jan2026Csv->marked_for_dbt = 1;

        $nsapPortal27Jan2026Csv->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $nsapPortal27Jan2026Csv->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();

        $nsapPortal27Jan2026Csv->save();

        Log::info('DBT Consent saved successfully', ['uuid' => $uuid]);

        DB::commit();
        return redirect()->route('website.pensioners.index')->with('success', 'DBT Consent Form submitted successfully.');

        /*return redirect()->back()->with('success', 'DBT Consent Form submitted successfully.');*/

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('DBT Consent submission failed', [
            'uuid' => $uuid,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        dd($e->getMessage(), $e->getLine(), $e->getFile());
    }
}

public function getGpsByBlock($block_id)
{
    $gps = Grampanchayat::where('block_id', $block_id)
    ->where('is_active', 'active')
    ->orderBy('gp_name')
    ->get();

    return response()->json($gps);
}

public function getVillagesByGp($gp_id)
{
    $villages = Village::where('gp_id', $gp_id)
    ->where('is_active', 'active')
    ->orderBy('village_name')
    ->get();

    return response()->json($villages);
}

public function getWardsByUlb($id)
{
    $wards = WardMaster::where('municipal_area_code', $id)
    ->where('is_active', 1)
    ->orderBy('ward_code', 'ASC')
    ->get(['ward_code', 'ward_name']);

    return response()->json($wards);
}

public function index_BKP_BASIC_DATATABLE(Request $request)
{
    if (empty($request->district) || empty($request->area) || empty($request->block)) {
        return DataTables::of(collect())->make(true);
    }

    $query = NsapPortal27Jan2026Csv::query()
    ->select([
        'id',
        'applicant_name',
        'father_husband_name',
        'scheme',
        'sanction_date',
        'sanction_order_no',
        'disbursement_mode',
        'disbursement_upto',
        'district',
        'area',
        'sub_district_municipality',
        'gram_panchayat_ward',
        'status'
    ]);

    if ($request->district) {
        $query->where('district', $request->district);
    }

    if ($request->area) {
        $query->where('area', $request->area);
    }

    if ($request->block) {
        $query->where('sub_district_municipality', $request->block);
    }

    if ($request->gp) {
        $query->where('gram_panchayat_ward', $request->gp);
    }

    return DataTables::of($query)
    ->addIndexColumn()
    ->editColumn('area', function($r) {
        $value = trim(strtoupper($r->area));
        return $value === 'R' ? 'Rural' : 'Urban';
    })
    ->editColumn('sanction_date', function ($row) {
        $value = $row->sanction_date;
        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int)$value)->diffForHumans();
        } elseif (!empty($value)) {
            return Carbon::parse($value)->diffForHumans();
        } else {
            return '-';
        }
    })
    ->editColumn('disbursement_upto', function ($row) {
        $value = $row->disbursement_upto;
        if (is_numeric($value)) {
            $date = Carbon::create(1899, 12, 30)->addDays((int)$value);
            return $date->format('d M Y');
        } elseif (!empty($value)) {
            return Carbon::parse($value)->format('d M Y');
        } else {
            return '-';
        }
    })
    ->make(true);
}

public function getBlocksByDistrictArea(Request $request)
{
    return NsapPortal27Jan2026Csv::where('district', $request->district)
    ->where('area', $request->area)
    ->whereNotNull('sub_district_municipality')
    ->distinct()
    ->orderBy('sub_district_municipality')
    ->pluck('sub_district_municipality');
}

public function getGpsByDistrictAreaBlock(Request $request)
{
    return NsapPortal27Jan2026Csv::where('district', $request->district)
    ->where('area', $request->area)
    ->where('sub_district_municipality', $request->block)
    ->whereNotNull('gram_panchayat_ward')
    ->distinct()
    ->orderBy('gram_panchayat_ward')
    ->pluck('gram_panchayat_ward');
}

/*public function getBlocksByDistrictArea(Request $request)
{
    $request->validate([
        'district' => 'required|string',
        'area'     => 'required|string|in:R,U',
    ]);

    $blocks = NsapPortal27Jan2026Csv::query()
    ->where('district', $request->district)
    ->where('area', $request->area)
    ->whereNotNull('sub_district_municipality')
    ->select('sub_district_municipality')
    ->distinct()
    ->orderBy('sub_district_municipality')
    ->pluck('sub_district_municipality');

    return response()->json($blocks);
}

public function getGpsByDistrictAreaBlock(Request $request)
{
    $request->validate([
        'district' => 'required|string',
        'area'     => 'required|in:R,U',
        'block'    => 'required|string',
    ]);

    $gps = NsapPortal27Jan2026Csv::query()
    ->where('district', $request->district)
    ->where('area', $request->area)
    ->where('sub_district_municipality', $request->block)
    ->whereNotNull('gram_panchayat_ward')
    ->select('gram_panchayat_ward')
    ->distinct()
    ->orderBy('gram_panchayat_ward')
    ->pluck('gram_panchayat_ward');

    return response()->json($gps);
}*/

public function filter(Request $request)
{
    $query = NsapPortal27Jan2026Csv::query();

    if ($request->district) {
        $query->where('district', $request->district);
    }

    if ($request->area) {
        $query->where('area', $request->area);
    }

    if ($request->block) {
        $query->where('sub_district_municipality', $request->block);
    }

    if ($request->gp) {
        $query->where('gram_panchayat_ward', $request->gp);
    }

    $nsapDump = $query->get();

    return view('website.nsap_rows', compact('nsapDump'));
}

public function consent_aadhar_verification_process(Request $request)
{
    $request->validate([
        'aadhaar_no' => 'required|digits:12',
        'name_of_the_beneficiary' => 'required|string',
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
            'name'       => trim($request->name_of_the_beneficiary),
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
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'exception' => $e->getMessage(),
        ], 500);
    }
}

public function update_the_data_using_nsap_api_testing()
{
    try {

        $encryptedResponse = "qOMMoH/03VU92OH9Ae/ooi4ruBilaLkHRstjX4ZSfeGSCjvM/YQem+KL1HRMB/3phLmMQA7BJwq2RH3QBGp6K/kVAv68CwuyPssgQJ27C6YftRFPW+tt3Y7q0dF88sR/SzOnpGdjFPvaSI33f/1dKAs55MltrWuWRgAhlcWQvZB0tVKZcpBasY4lt9hngr4o6NGQPEP/qdbgLDcE1sM/ccp98MNRfJJ/dpzin6wd6O5dFMQEEkBCAXP00MzreJdr";

        // Base64 encoded key from NIC
        $base64Key = "TQAjADEATQBSAEkARwBTAEAAIwBTAHAAJQAxADIAcAA=";

        // Decode the key
        $key = base64_decode($base64Key);

        // Same IV used in Java
        $iv = pack(
            'C*',
            1,2,3,4,
            5,6,6,5,
            4,3,2,1,
            7,7,7,7
        );

        // Decrypt
        $decrypted = openssl_decrypt(
            base64_decode($encryptedResponse),
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        dd([
            'Encrypted' => $encryptedResponse,
            'Decrypted' => $decrypted,
            'JSON'       => json_decode($decrypted, true),
        ]);

    } catch (\Exception $e) {

        dd($e->getMessage());

    }
}


/******************************From here We have Started the NSAP Aadhaar Fetch API Work***************************************************************/
/**
 * Generate NSAP Authentication Token
 *
 * @return string
 * @throws \Exception
 */
private function getNsapBearerToken(): string
{
    Log::info('========== NSAP AUTH START ==========');

    $response = Http::withoutVerifying()
    ->timeout(60)
    ->connectTimeout(15)
    ->withHeaders([
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
    ])
    ->withBody(
        'c0HM1M0rmfCaZOvXdCOE2dDlPxIQUTQB7ip9faHrGpDmQputcJar3YGlnWyoDsyGiVod1cSkskr8B+FXt+qrKg==',
        'application/json'
    )
    ->post('https://nsap.dord.gov.in/nsapservices/authenticate');

    Log::info('Auth HTTP Status', [
        'status' => $response->status()
    ]);

    Log::info('Auth Response', [
        'body' => $response->body()
    ]);

    $json = $response->json();

    Log::info('Generated Token', [
        'token' => 'Bearer ' . $json['token'] ?? null
    ]);

    return $json['token'];
}

/**
 * Fetch first 100 pending beneficiaries
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
private function getPendingBeneficiaries()
{
    $beneficiaries = NsapPortal27Jan2026Csv::select([
        'id',
        'sanction_order_no'
    ])
    ->where('created_by',0)
    ->whereNotNull('sanction_order_no')
    ->where('sanction_order_no','<>','')
    ->orderBy('id')
    ->limit(100)
    ->get();

    Log::info('Pending Beneficiaries',[
        'count'=>$beneficiaries->count(),
        'data'=>$beneficiaries->toArray()
    ]);

    return $beneficiaries;
}

/**
 * Build NSAP API Request Payload
 *
 * @param \Illuminate\Support\Collection $beneficiaries
 * @return array
 */
private function buildNsapPayload($beneficiaries): array
{
    $payload = [
        'stateCode' => '24',
        'sordernos' => [],
    ];

    foreach ($beneficiaries as $beneficiary) {

        $payload['sordernos'][] = [
            'sanctionOrderNo' => trim($beneficiary->sanction_order_no),
        ];

    }

    Log::info('NSAP Payload', $payload);

    return $payload;
}

/**
 * Call NSAP Beneficiary API
 *
 * @param string $token
 * @param array $payload
 * @return string
 * @throws \Exception
 */
/**
 * Call NSAP Beneficiary API
 *
 * @param string $token
 * @param array $payload
 * @return string
 * @throws \Exception
 */
private function callNsapBeneficiaryApi(string $token, array $payload): string
{
    Log::info('================ NSAP Beneficiary API Request Started ================');

    Log::info('NSAP API URL', [
        'url' => 'https://nsap.dord.gov.in/nsapservices/encryptBenDataApi'
    ]);

    Log::info('Bearer Token', [
        // Do not log the complete token in production
        'token' => substr($token, 0, 50) . '...'
    ]);

    Log::info('Request Payload', $payload);

    try {

        $response = Http::withoutVerifying()
        ->timeout(60)
        ->connectTimeout(15)
        ->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])
        ->post(
            'https://nsap.dord.gov.in/nsapservices/encryptBenDataApi',
            $payload
        );

        Log::info('NSAP API HTTP Response', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'headers' => $response->headers(),
        ]);

        Log::info('Encrypted Response Received', [
            'response' => $response->body(),
            'length'   => strlen($response->body()),
        ]);

        if (!$response->successful()) {

            throw new \Exception(
                'NSAP API Request Failed. HTTP Status : ' .
                $response->status() .
                ' | Response : ' .
                $response->body()
            );

        }

        // API returns encrypted text
        $encryptedResponse = trim($response->body());

        if ($encryptedResponse === '') {

            throw new \Exception(
                'NSAP API returned an empty response.'
            );

        }

        Log::info('================ NSAP Beneficiary API Request Completed ================');

        return $encryptedResponse;

    } catch (\Throwable $e) {

        Log::error('NSAP Beneficiary API Exception', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        throw $e;
    }
}
/**
 * Decrypt NSAP API Response
 *
 * @param string $encryptedResponse
 * @return array
 * @throws \Exception
 */
/**
 * Decrypt NSAP API Response
 *
 * @param string $encryptedResponse
 * @return array
 * @throws \Exception
 */
private function decryptNsapResponse(string $encryptedResponse): array
{
    Log::info('================ NSAP Response Decryption Started ================');

    Log::info('Encrypted Response Received', [
        'length' => strlen($encryptedResponse),
        'response' => $encryptedResponse,
    ]);

    // Base64 encoded AES key provided by NSAP
    $base64Key = "TQAjADEATQBSAEkARwBTAEAAIwBTAHAAJQAxADIAcAA=";

    // Decode the key
    $key = base64_decode($base64Key);

    Log::info('AES Configuration', [
        'algorithm' => 'AES-256-CBC',
        'key_length' => strlen($key),
    ]);

    // Initialization Vector (IV)
    $iv = pack(
        'C*',
        1, 2, 3, 4,
        5, 6, 6, 5,
        4, 3, 2, 1,
        7, 7, 7, 7
    );

    try {

        // Decrypt the encrypted response
        $decrypted = openssl_decrypt(
            base64_decode($encryptedResponse),
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {

            Log::error('OpenSSL Decryption Failed');

            throw new \Exception(
                'Unable to decrypt NSAP response.'
            );
        }

        Log::info('Decrypted JSON String', [
            'json' => $decrypted,
        ]);

        // Decode JSON
        $response = json_decode($decrypted, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            Log::error('JSON Decode Failed', [
                'error' => json_last_error_msg(),
                'json'  => $decrypted,
            ]);

            throw new \Exception(
                'Invalid JSON after decryption. Error : ' .
                json_last_error_msg()
            );
        }

        Log::info('Decoded NSAP Response', $response);

        // Validate response structure
        if (!isset($response['status'])) {

            Log::error('Invalid NSAP Response Structure', $response);

            throw new \Exception(
                'Invalid NSAP response structure.'
            );
        }

        Log::info('NSAP Response Status', [
            'status'  => $response['status'],
            'message' => $response['message'] ?? null,
        ]);

        // NSAP returned failure
        if ($response['status'] != '1') {

            Log::warning('NSAP Returned Failure', [
                'status'  => $response['status'],
                'message' => $response['message'] ?? null,
            ]);

            throw new \Exception(
                $response['message'] ?? 'NSAP returned failed status.'
            );
        }

        // Validate beneficiary list
        if (
            !isset($response['list']) ||
            !is_array($response['list'])
        ) {

            Log::error('Beneficiary List Missing', $response);

            throw new \Exception(
                'Beneficiary list not found in NSAP response.'
            );
        }

        Log::info('Beneficiary List Received', [
            'count' => count($response['list']),
            'list'  => $response['list'],
        ]);

        Log::info('================ NSAP Response Decryption Completed Successfully ================');

        return $response;

    } catch (\Throwable $e) {

        Log::error('NSAP Response Decryption Exception', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        throw $e;
    }
}

/**
 * Update Beneficiary Records from NSAP API Response
 *
 * @param array $beneficiaries
 * @return int
 */
/**
 * Update Beneficiary Records from NSAP API Response
 *
 * @param array $beneficiaries
 * @return int
 */
private function updateBeneficiaryRecords(array $beneficiaries): int
{
    Log::info('================ Updating Beneficiary Records Started ================');

    Log::info('Total Beneficiaries Received', [
        'count' => count($beneficiaries)
    ]);

    $updatedCount = 0;

    foreach ($beneficiaries as $index => $beneficiary) {

        Log::info('Processing Beneficiary', [
            'index' => $index + 1,
            'data'  => $beneficiary,
        ]);

        try {

            // Skip invalid response data
            if (empty($beneficiary['sanctionOrderNo'])) {

                Log::warning('Skipped Beneficiary - sanctionOrderNo is missing.', [
                    'beneficiary' => $beneficiary
                ]);

                continue;
            }

            $sanctionOrderNo = trim($beneficiary['sanctionOrderNo']);

            Log::info('Searching Database Record', [
                'sanction_order_no' => $sanctionOrderNo
            ]);

            $record = NsapPortal27Jan2026Csv::where(
                'sanction_order_no',
                $sanctionOrderNo
            )
            ->where('created_by', 0)
            ->first();

            if (!$record) {

                Log::warning('Database Record Not Found', [
                    'sanction_order_no' => $sanctionOrderNo
                ]);

                continue;
            }

            Log::info('Database Record Found', [
                'id' => $record->id,
                'sanction_order_no' => $record->sanction_order_no,
            ]);

            Log::info('Existing Database Values', [
                'bank_po_account' => $record->bank_po_account,
                'ifsc_code'       => $record->ifsc_code,
                'aadhar_no'       => $record->aadhar_no,
                'mobile_no'       => $record->mobile_no,
                'created_by'      => $record->created_by,
            ]);

            Log::info('NSAP Values To Update', [
                'bank_po_account' => $beneficiary['bankAccountNo'] ?? null,
                'ifsc_code'       => $beneficiary['ifscCode'] ?? null,
                'aadhar_no'       => $beneficiary['uid'] ?? null,
                'mobile_no'       => $beneficiary['mobileNo'] ?? null,
            ]);

            // Update database
            $record->bank_po_account = $beneficiary['bankAccountNo'] ?? null;
            $record->ifsc_code       = $beneficiary['ifscCode'] ?? null;
            $record->aadhar_no       = $beneficiary['uid'] ?? null;
            $record->mobile_no       = $beneficiary['mobileNo'] ?? null;

            $record->created_by = 1;

            $record->updated_date = date('Y-m-d');
            $record->updated_time = date('H:i:s');

            $record->save();

            Log::info('Database Record Updated Successfully', [
                'id' => $record->id,
                'sanction_order_no' => $record->sanction_order_no,
            ]);

            $updatedCount++;

        } catch (\Throwable $e) {

            Log::error('Error Updating Beneficiary', [
                'sanction_order_no' => $beneficiary['sanctionOrderNo'] ?? null,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Continue with the next beneficiary
            continue;
        }
    }

    Log::info('Beneficiary Update Summary', [
        'total_received' => count($beneficiaries),
        'updated_records' => $updatedCount,
    ]);

    Log::info('================ Updating Beneficiary Records Completed ================');

    return $updatedCount;
}

/**
 * Fetch Aadhaar, IFSC, Mobile and Bank Account details from NSAP
 * and update the local database.
 *
 * @return \Illuminate\Http\JsonResponse
 */
/**
 * Synchronize Beneficiary Data from NSAP API
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function update_the_data_using_nsap_api()
{
    // Record start time
    $startTime = microtime(true);

    $startDateTime = now()
        ->timezone('Asia/Kolkata')
        ->format('Y-m-d H:i:s');

    Log::info('================ NSAP Synchronization Started ================', [
        'started_at' => $startDateTime
    ]);

    try {

        /*
        |--------------------------------------------------------------------------
        | Step 1 : Generate NSAP Bearer Token
        |--------------------------------------------------------------------------
        */

        Log::info('Step 1 : Generating NSAP Bearer Token');

        $token = $this->getNsapBearerToken();

        Log::info('Step 1 Completed', [
            'token_prefix' => substr($token, 0, 40) . '...'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Step 2 : Fetch Pending Beneficiaries
        |--------------------------------------------------------------------------
        */

        Log::info('Step 2 : Fetching Pending Beneficiaries');

        $beneficiaries = $this->getPendingBeneficiaries();

        Log::info('Step 2 Completed', [
            'records_found' => $beneficiaries->count()
        ]);

        if ($beneficiaries->isEmpty()) {

            Log::info('No Pending Beneficiaries Found');

            return response()->json([
                'status' => true,
                'message' => 'No pending beneficiaries found.',
                'records_sent' => 0,
                'records_received' => 0,
                'records_updated' => 0,
                'started_at' => $startDateTime,
                'completed_at' => now()->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'execution_time_seconds' => round(microtime(true) - $startTime, 3),
                'execution_time_milliseconds' => round((microtime(true) - $startTime) * 1000, 2),
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Step 3 : Build NSAP Payload
        |--------------------------------------------------------------------------
        */

        Log::info('Step 3 : Building NSAP Payload');

        $payload = $this->buildNsapPayload($beneficiaries);

        Log::info('Step 3 Completed', [
            'payload' => $payload
        ]);


        /*
        |--------------------------------------------------------------------------
        | Step 4 : Call NSAP API
        |--------------------------------------------------------------------------
        */

        Log::info('Step 4 : Calling NSAP Beneficiary API');

        $encryptedResponse = $this->callNsapBeneficiaryApi(
            $token,
            $payload
        );

        Log::info('Step 4 Completed', [
            'encrypted_response_length' => strlen($encryptedResponse)
        ]);


        /*
        |--------------------------------------------------------------------------
        | Step 5 : Decrypt Response
        |--------------------------------------------------------------------------
        */

        Log::info('Step 5 : Decrypting NSAP Response');

        $response = $this->decryptNsapResponse(
            $encryptedResponse
        );

        Log::info('Step 5 Completed', [
            'status' => $response['status'] ?? null,
            'message' => $response['message'] ?? null,
            'records_received' => count($response['list'] ?? [])
        ]);


        /*
        |--------------------------------------------------------------------------
        | Step 6 : Update Database
        |--------------------------------------------------------------------------
        */

        Log::info('Step 6 : Updating Database Records');

        $updatedCount = $this->updateBeneficiaryRecords(
            $response['list']
        );

        Log::info('Step 6 Completed', [
            'records_updated' => $updatedCount
        ]);


        /*
        |--------------------------------------------------------------------------
        | Calculate Execution Time
        |--------------------------------------------------------------------------
        */

        $endTime = microtime(true);

        $endDateTime = now()
            ->timezone('Asia/Kolkata')
            ->format('Y-m-d H:i:s');

        $executionSeconds = round($endTime - $startTime, 3);

        $executionMilliseconds = round(($endTime - $startTime) * 1000, 2);


        /*
        |--------------------------------------------------------------------------
        | Synchronization Completed
        |--------------------------------------------------------------------------
        */

        Log::info('================ NSAP Synchronization Completed Successfully ================', [
            'started_at' => $startDateTime,
            'completed_at' => $endDateTime,
            'execution_time_seconds' => $executionSeconds,
            'execution_time_milliseconds' => $executionMilliseconds,
            'records_sent' => $beneficiaries->count(),
            'records_received' => count($response['list']),
            'records_updated' => $updatedCount,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Synchronization completed successfully.',

            'records_sent' => $beneficiaries->count(),
            'records_received' => count($response['list']),
            'records_updated' => $updatedCount,

            'started_at' => $startDateTime,
            'completed_at' => $endDateTime,

            'execution_time_seconds' => $executionSeconds,
            'execution_time_milliseconds' => $executionMilliseconds,
        ]);

    } catch (\Throwable $e) {

        $endTime = microtime(true);

        $endDateTime = now()
            ->timezone('Asia/Kolkata')
            ->format('Y-m-d H:i:s');

        $executionSeconds = round($endTime - $startTime, 3);

        $executionMilliseconds = round(($endTime - $startTime) * 1000, 2);

        Log::error('================ NSAP Synchronization Failed ================', [
            'started_at' => $startDateTime,
            'completed_at' => $endDateTime,
            'execution_time_seconds' => $executionSeconds,
            'execution_time_milliseconds' => $executionMilliseconds,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),

            'started_at' => $startDateTime,
            'completed_at' => $endDateTime,

            'execution_time_seconds' => $executionSeconds,
            'execution_time_milliseconds' => $executionMilliseconds,
        ]);

    }
}

}