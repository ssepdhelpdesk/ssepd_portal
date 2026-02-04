<?php

namespace App\Http\Controllers\Website_Controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use App\Models\{
    NsapPortal27Jan2026Csv,
    BankMaster,
    District,
    Block,
    Subdivision,
    Municipality,
    Grampanchayat,
    WardMaster,
    Village
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
    ->where('district', $request->district)
    ->where('area', $request->area)
    ->where('sub_district_municipality', $request->block)
    ->orderBy('scheme', 'asc')
    ->orderBy('applicant_name', 'asc');

    if ($request->gp) {
        $baseQuery->where('gram_panchayat_ward', $request->gp);
    }

    /* ===== Counters (before DataTables modifies query) ===== */
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
    return view('website.dbtconsent.dbt_consent_form', compact('nsapPortal27Jan2026CsvData', 'bankMaster', 'block', 'municipality'));
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
        ->get(['id', 'ward_name']);

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
