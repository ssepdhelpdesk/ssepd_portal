<?php

namespace App\Http\Controllers\Website_Controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

use App\Models\{
    NsapPortal27Jan2026Csv
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

    return view('website.index', compact('district', 'area'));
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
        ->whereNot('scheme', 'NFBS')
        ->where('district', $request->district)
        ->where('area', $request->area)
        ->where('sub_district_municipality', $request->block);

    if ($request->gp) {
        $baseQuery->where('gram_panchayat_ward', $request->gp);
    }

    /* ===== Counters (before DataTables modifies query) ===== */
    $totalActive = (clone $baseQuery)->where('status', 'Active')->count();

    $schemeCountOap = (clone $baseQuery)
        ->whereIn('scheme', ['IGNOAPS', 'MBPOAP'])
        ->count();

    $schemeCountDp = (clone $baseQuery)
        ->whereIn('scheme', ['IGNDPS', 'MBPDP', 'MBPSDP'])
        ->count();

    $schemeCountOther = (clone $baseQuery)
        ->whereNotIn('scheme', [
            'IGNOAPS', 'MBPOAP',
            'IGNDPS', 'MBPDP', 'MBPSDP'
        ])
        ->count();

    return DataTables::of(
        $baseQuery->select([
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


}
