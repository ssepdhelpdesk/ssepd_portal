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
        $user = auth()->user();
        $userRole = $user->role_id;

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|date_format:H:i',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'redirect_url' => 'nullable|url|max:500',
        ]);

        $startDateTime = strtotime($request->start_date . ' ' . $request->start_time);
        $endDateTime = strtotime($request->end_date . ' ' . $request->end_time);

        if ($endDateTime <= $startDateTime) {
            return back()->withErrors(['end_date' => 'End date/time must be after start date/time.'])->withInput();
        }

        $notification = new SsepdNotification([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type ?? null,
            'priority' => $request->priority,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'redirect_url' => null,
            'attachment' => null,
            'for_which_page' => null,
            'for_the_month' => null,
            'state_id' => null,
            'district_id' => null,
            'block_id' => null,
            'gp_id' => null,
            'village_id' => null,
            'municipality_id' => null,
            'ward_id' => null,
            'read_status' => false,
            'created_date'      => now()->setTimezone('Asia/Kolkata')->toDateString(),
            'created_time'      => now()->setTimezone('Asia/Kolkata')->toTimeString(),
            'created_by'        => $user->user_table_id ?? 1,
            'updated_date'      => now()->setTimezone('Asia/Kolkata')->toDateString(),
            'updated_time'      => now()->setTimezone('Asia/Kolkata')->toTimeString(),
            'updated_by'        => $user->user_table_id ?? 1,
        ]);

        $notification->save();

        return redirect()->route('admin.ssepdnotification.create')->with('success', 'Notification created successfully!');
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
