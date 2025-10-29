<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB,
    File,
    Hash,
    Mail,
    Validator
};
use Illuminate\Support\{
    Arr,
    Str,
    Collection
};
use Illuminate\Http\{
    RedirectResponse,
    JsonResponse
};
use Illuminate\View\View;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\{
    Role,
    Permission
};

use App\Models\{
    User,
    ApplicationStageHistory,
    PensionFundsRequirement,
    PensionFundRequirementDates,
    PensionDisbursementAuthority,
    MonthlyPensionDisbursemenet,
    DailyPensionDisbursement,
    District,
    Block,
    Municipality,
    Grampanchayat,
    WardMaster,
    SsepdNotification
};

use App\Mail\NgoRegistrationMail;
use App\Helpers\AadhaarVerifier;

class SsepdNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        return view('dashboard.notifications.create', compact('user'));
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
