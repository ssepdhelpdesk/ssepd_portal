<?php

namespace App\Http\Controllers\Website_Controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
    $maxId = NsapPortal27Jan2026Csv::max('id');

    $nsapDump = NsapPortal27Jan2026Csv::where('id', '>=', rand(1, $maxId))->limit(100)->get();

    $district = NsapPortal27Jan2026Csv::query()->select('district')->distinct()->orderBy('district')->get();

    $area = NsapPortal27Jan2026Csv::query()->selectRaw('UPPER(TRIM(area)) as area')->whereNotNull('area')->distinct()->orderBy('area')->get();

    return view('website.index', compact('nsapDump', 'district', 'area'));
}

public function datatable(Request $request)
{
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
    ->editColumn('area', fn ($r) => $r->area === 'R' ? 'Rural' : 'Urban')
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
