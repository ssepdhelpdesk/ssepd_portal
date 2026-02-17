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
    PensionType
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
