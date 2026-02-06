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

class WebsiteFrontController extends Controller
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
        return view('website.index', compact('district', 'area', 'visitorCount'));
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
