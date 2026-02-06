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

    return view('website.dbtconsent.index', compact('district', 'area'));
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
            'status'
        ])
    )
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
        'verified_aadhar.in' => 'Demographic mismatch detected. Please verify that the Aadhaar number and beneficiary details (name, DOB, etc.) are entered correctly.',
    ];

    $validatedData = $request->validate($validationRules, $messages);

    try {
        $village = null;
        $dob = trim($validatedData['dob']);

        if (trim($validatedData['address_type']) == 1) {
            $village = Village::where('village_id', trim($validatedData['village_id']))
            ->value('village_name');
        }

        $folderPath = public_path("dbt_consent");
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/dbt_consent";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        $bankAccountStoredPath = null;

        if ($request->hasFile('bank_account_file')) {
            $bankAccountFile = $request->file('bank_account_file');
            $bankAccountExtension = $bankAccountFile->getClientOriginalExtension();
            $bankAccountRandomName = 'BANK_ACCOUNT_' . Str::random(40) . '.' . $bankAccountExtension;

            $bankAccountStoredPath = $bankAccountFile->storeAs("dbt_consent", $bankAccountRandomName, 'public');

            copy(storage_path("app/public/{$bankAccountStoredPath}"), "{$folderPath}/{$bankAccountRandomName}");
            copy(storage_path("app/public/{$bankAccountStoredPath}"), "{$externalPath}/{$bankAccountRandomName}");
        }

        $nsapPortal27Jan2026Csv = NsapPortal27Jan2026Csv::where('uuid', $uuid)->firstOrFail();

        $nsapPortal27Jan2026Csv->name_as_per_aadhaar = trim($validatedData['name_of_the_beneficiary']);
        $nsapPortal27Jan2026Csv->gender_by_user = trim($validatedData['gender']);
        $nsapPortal27Jan2026Csv->dob = trim($validatedData['dob']);
        $nsapPortal27Jan2026Csv->age_by_user = Carbon::parse(trim($validatedData['dob']))->age;

        $nsapPortal27Jan2026Csv->aadhaar_no_by_user = trim($validatedData['aadhaar_no']);
        $nsapPortal27Jan2026Csv->aadhaar_hash = hash('sha256', trim($validatedData['aadhaar_no']));
        $nsapPortal27Jan2026Csv->aadhaar_encrypted = Crypt::encryptString(trim($validatedData['aadhaar_no']));

        $nsapPortal27Jan2026Csv->verified_aadhar = trim($validatedData['verified_aadhar']);
        $nsapPortal27Jan2026Csv->verified_aadhar_remarks = trim($validatedData['verified_aadhar_remarks']);

        $nsapPortal27Jan2026Csv->block_or_ulb = trim($validatedData['address_type']);

        $nsapPortal27Jan2026Csv->block_id = $validatedData['block_id'] ? trim($validatedData['block_id']) : null;
        $nsapPortal27Jan2026Csv->gp_id = $validatedData['gp_id'] ? trim($validatedData['gp_id']) : null;
        $nsapPortal27Jan2026Csv->village_id = $validatedData['village_id'] ? trim($validatedData['village_id']) : null;
        $nsapPortal27Jan2026Csv->village = $village;

        $nsapPortal27Jan2026Csv->municipality_id = $validatedData['ulb_id'] ? trim($validatedData['ulb_id']) : null;
        $nsapPortal27Jan2026Csv->ward_id = $validatedData['ward_id'] ? trim($validatedData['ward_id']) : null;

        $nsapPortal27Jan2026Csv->pin = trim($validatedData['pin']);
        $nsapPortal27Jan2026Csv->ifsc_code = trim($validatedData['ifsc']);
        $nsapPortal27Jan2026Csv->bank_po_account = trim($validatedData['bank_po_account']);
        $nsapPortal27Jan2026Csv->bank_account_file = $bankAccountStoredPath;

        $nsapPortal27Jan2026Csv->disbursement_mode = "Bank";
        $nsapPortal27Jan2026Csv->marked_for_dbt = 1;

        $nsapPortal27Jan2026Csv->updated_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $nsapPortal27Jan2026Csv->updated_time = now()->setTimezone('Asia/Kolkata')->toTimeString();

        $nsapPortal27Jan2026Csv->save();
        return redirect()->back()->with('success', 'DBT Consent Form submitted successfully.');

    } catch (\Exception $e) {
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
    ->orderBy('ward_name', 'ASC')
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

}