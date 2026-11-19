<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use App\Mail\StallAllotmentConfirmationMail;
use App\Mail\StallRegistrationConfirmationMail;
use App\Models\DisabilityDayRegistration;
use App\Models\DisabilityDayStallAllotment;
use App\Models\District;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DisabilityDayController extends Controller
{
    /**
     * Enforce Spatie permissions across all dashboard operations.
     */
    public function __construct()
    {
        $this->middleware('permission:disability-day-access|disability-day-list', ['only' => ['index']]);
        $this->middleware('permission:disability-day-show', ['only' => ['show']]);
        $this->middleware('permission:disability-day-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:disability-day-delete', ['only' => ['destroy']]);
        $this->middleware('permission:disability-day-allot-stall', ['only' => ['allotStall']]);
        $this->middleware('permission:disability-day-access|disability-day-list|disability-day-allot-stall', ['only' => ['getAvailableStalls']]);
        $this->middleware('permission:disability-day-access|disability-day-show', ['only' => ['resendConfirmationEmail']]);
        $this->middleware('permission:disability-day-allot-stall|disability-day-show', ['only' => ['sendAllotmentEmail']]);
    }

    /**
     * Display a paginated, filterable listing of stall registrations using Yajra DataTables.
     * Enforces status = 1 filter so inactive registrations are never displayed.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = DisabilityDayRegistration::with(['district', 'state', 'stallAllotments'])
                ->where('status', 1)
                ->select('disability_day_registrations.*');

            // Search by keyword
            $searchVal = $request->input('search_keyword');
            if (empty($searchVal) && $request->filled('search')) {
                $rawSearch = $request->input('search');
                $searchVal = is_array($rawSearch) ? ($rawSearch['value'] ?? null) : $rawSearch;
            }
            if (!empty($searchVal) && is_string($searchVal)) {
                $query->search(trim($searchVal));
            }

            // Filter by District
            if ($request->filled('district_id')) {
                $query->where('district_id', $request->input('district_id'));
            }

            // Filter by Financial Year
            if ($request->filled('financial_year')) {
                $query->where('financial_year', $request->input('financial_year'));
            }

            // Filter by Allotment State
            if ($request->filled('allotment_status')) {
                $query->filterAllotment($request->input('allotment_status'));
            }

            // Filter by Applied Date Range
            if ($request->filled('date_from')) {
                $query->whereDate('applied_at', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('applied_at', '<=', $request->input('date_to'));
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('registration_no', function ($row) {
                    return '<a href="' . route('admin.disability_day.show', $row->id) . '" class="fw-bold text-primary font-monospace text-decoration-none">'
                        . e($row->registration_no) . '</a>';
                })
                ->editColumn('organization_name', function ($row) {
                    return '<span class="fw-semibold text-dark">' . e(Str::limit($row->organization_name, 35)) . '</span>';
                })
                ->editColumn('contact_person', function ($row) {
                    return e($row->contact_person);
                })
                ->editColumn('email', function ($row) {
                    return '<a href="mailto:' . e($row->email) . '" class="text-decoration-none small text-muted"><i class="fas fa-envelope me-1"></i>' . e($row->email) . '</a>';
                })
                ->editColumn('phone', function ($row) {
                    return '<span class="small font-monospace"><i class="fas fa-phone-alt me-1 text-muted"></i>+91 ' . e($row->phone) . '</span>';
                })
                ->editColumn('district', function ($row) {
                    return $row->district ? e($row->district->district_name) : (e($row->district_name) ?: '—');
                })
                ->editColumn('applied_at', function ($row) {
                    return $row->applied_at ? $row->applied_at->format('d-M-Y h:i A') : 'N/A';
                })
                ->editColumn('allotted_stall', function ($row) {
                    $stalls = $row->getAllottedStallNumbers();
                    if (!empty($stalls)) {
                        $html = '<div class="d-flex flex-wrap gap-1 justify-content-center">';
                        foreach ($stalls as $stallNo) {
                            $html .= '<span class="badge bg-success font-monospace fs-7 px-2 py-1"><i class="fas fa-store me-1"></i>Stall #' . e($stallNo) . '</span>';
                        }
                        $html .= '</div>';
                        return $html;
                    }
                    return '<span class="badge bg-secondary px-2 py-1">Not Yet Allotted</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group btn-group-sm" role="group">';
                    $user = auth()->user();

                    if ($user && $user->can('disability-day-show')) {
                        $actions .= '<a href="' . route('admin.disability_day.show', $row->id) . '" class="btn btn-info text-white" title="View Application Details"><i class="fas fa-eye"></i></a>';
                    }

                    if ($user && $user->can('disability-day-edit')) {
                        $actions .= '<a href="' . route('admin.disability_day.edit', $row->id) . '" class="btn btn-warning text-dark" title="Edit Registration Details"><i class="fas fa-edit"></i></a>';
                    }

                    if ($user && $user->can('disability-day-allot-stall')) {
                        $currentStallsStr = implode(',', $row->getAllottedStallNumbers());
                        $orgNameEscaped = e($row->organization_name ?? '');
                        $actions .= '<button type="button" class="btn btn-success text-white" title="Assign / Manage Stalls" onclick="openAllotModal(' . $row->id . ', \'' . addslashes($row->registration_no) . '\', \'' . addslashes($currentStallsStr) . '\', \'' . addslashes($orgNameEscaped) . '\')"><i class="fas fa-store-alt"></i></button>';
                    }

                    if ($user && ($user->can('disability-day-allot-stall') || $user->can('disability-day-show'))) {
                        $hasStall = $row->isAllotted() ? '1' : '0';
                        $actions .= '<button type="button" class="btn btn-primary text-white" title="Send Allotment Email" onclick="sendAllotmentEmail(' . $row->id . ', \'' . addslashes($row->registration_no) . '\', \'' . addslashes($row->email) . '\', ' . $hasStall . ')"><i class="fas fa-paper-plane me-1"></i> Send Allotment Email</button>';
                    }

                    if ($user && $user->can('disability-day-delete')) {
                        $actions .= '<button type="button" class="btn btn-danger" title="Delete Registration" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->registration_no) . '\')"><i class="fas fa-trash-alt"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['registration_no', 'organization_name', 'email', 'phone', 'allotted_stall', 'action'])
                ->make(true);
        }

        $districts = District::where('status', 1)->orderBy('district_name')->get();
        $baseQuery = DisabilityDayRegistration::where('status', 1);
        $totalCount = (clone $baseQuery)->count();
        $allottedCount = (clone $baseQuery)->where(function ($q) {
            $q->whereNotNull('allotted_stall')->where('allotted_stall', '!=', '')
              ->orWhereHas('stallAllotments');
        })->count();
        $unallottedCount = $totalCount - $allottedCount;

        $financialYears = DisabilityDayRegistration::where('status', 1)
            ->whereNotNull('financial_year')
            ->distinct()
            ->orderBy('financial_year', 'desc')
            ->pluck('financial_year')
            ->all();

        return view('dashboard.disability_day.index', compact('districts', 'totalCount', 'allottedCount', 'unallottedCount', 'financialYears'));
    }

    /**
     * Display a specific registration's full details.
     */
    public function show($id)
    {
        $registration = DisabilityDayRegistration::with(['district', 'state', 'creator', 'updater', 'stallAllotments'])
            ->findOrFail($id);

        $availableStalls = DisabilityDayRegistration::getAvailableStallNumbers($registration->id);
        $allottedStalls = $registration->getAllottedStallNumbers();
        $currentStallNumber = $registration->getCurrentStallNumber();

        return view('dashboard.disability_day.show', compact('registration', 'availableStalls', 'allottedStalls', 'currentStallNumber'));
    }

    /**
     * Fetch dynamic available stalls (1-100) excluding stalls assigned to other registrations.
     */
    public function getAvailableStalls($id)
    {
        $registration = DisabilityDayRegistration::with('stallAllotments')->findOrFail($id);

        $availableStalls = DisabilityDayRegistration::getAvailableStallNumbers($registration->id);
        $currentStalls = $registration->getAllottedStallNumbers();

        return response()->json([
            'success'          => true,
            'registration_id'  => $registration->id,
            'registration_no'  => $registration->registration_no,
            'organization_name'=> $registration->organization_name,
            'current_stalls'   => $currentStalls,
            'current_stall'    => $currentStalls[0] ?? null,
            'available_stalls' => $availableStalls,
            'total_available'  => count($availableStalls),
            'is_full'          => empty($availableStalls) && empty($currentStalls),
        ]);
    }

    /**
     * Show the edit form for permitted applicant details.
     */
    public function edit($id)
    {
        $registration = DisabilityDayRegistration::findOrFail($id);
        $states = State::where('status', 1)->orderBy('state_name')->get();
        $districts = District::where('status', 1)->orderBy('district_name')->get();

        return view('dashboard.disability_day.edit', compact('registration', 'states', 'districts'));
    }

    /**
     * Update permitted applicant details.
     */
    public function update(Request $request, $id)
    {
        $registration = DisabilityDayRegistration::findOrFail($id);
        $fy = $registration->financial_year ?: DisabilityDayRegistration::calculateFinancialYear($registration->applied_at);

        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255',
            'contact_person'    => 'required|string|max:255',
            'email'             => [
                'required',
                'string',
                'email',
                'max:150',
                function ($attribute, $value, $fail) use ($registration, $fy) {
                    $cleaned = strtolower(trim((string) $value));
                    $already = DisabilityDayRegistration::where('id', '!=', $registration->id)
                        ->whereRaw('LOWER(TRIM(email)) = ?', [$cleaned])
                        ->where('financial_year', $fy)
                        ->exists();
                    if ($already) {
                        $fail('This email ID has already been registered. One organization can submit only one registration.');
                    }
                },
            ],
            'phone'             => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'purpose'           => 'required|string|max:2000',
            'at'                => 'required|string|max:255',
            'po'                => 'required|string|max:255',
            'ps'                => 'required|string|max:255',
            'district_id'       => 'required|exists:districts,id',
            'state_id'          => 'required|exists:states,id',
            'pin'               => ['required', 'regex:/^[1-9][0-9]{5}$/'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $district = District::find($request->input('district_id'));
        $state = State::find($request->input('state_id'));

        // Update ONLY permitted fields — NEVER touch registration_no, applied_at, or financial_year
        $registration->organization_name = trim($request->input('organization_name'));
        $registration->contact_person    = trim($request->input('contact_person'));
        $registration->email             = strtolower(trim($request->input('email')));
        $registration->phone             = trim($request->input('phone'));
        $registration->purpose           = trim($request->input('purpose'));
        $registration->at                = trim($request->input('at'));
        $registration->po                = trim($request->input('po'));
        $registration->ps                = trim($request->input('ps'));
        $registration->district_id       = $district ? $district->id : null;
        $registration->district_name     = $district ? $district->district_name : null;
        $registration->state_id          = $state ? $state->id : null;
        $registration->state_name        = $state ? $state->state_name : null;
        $registration->pin               = trim($request->input('pin'));
        $registration->updated_by        = Auth::id();

        $registration->save();

        return redirect()->route('admin.disability_day.show', $registration->id)
            ->with('success', 'Registration details for ' . $registration->registration_no . ' have been updated successfully.');
    }

    /**
     * Assign or update stall allotments with multiple stall support, duplicate prevention, and locking.
     */
    public function allotStall(Request $request, $id)
    {
        $registration = DisabilityDayRegistration::with('stallAllotments')->findOrFail($id);

        // 1. Check if user requested unassigning/removing a specific stall
        if ($request->filled('remove_stall')) {
            $stallToRemove = (int) $request->input('remove_stall');
            \DB::transaction(function () use ($registration, $stallToRemove) {
                DisabilityDayStallAllotment::where('registration_id', $registration->id)
                    ->where('stall_number', $stallToRemove)
                    ->delete();

                $remaining = DisabilityDayStallAllotment::where('registration_id', $registration->id)
                    ->orderBy('stall_number')
                    ->pluck('stall_number')
                    ->all();

                $registration->allotted_stall = !empty($remaining)
                    ? implode(', ', array_map(fn($n) => "Stall {$n}", $remaining))
                    : null;
                $registration->updated_by = Auth::id();
                $registration->save();
            });

            $msg = "Stall {$stallToRemove} unassigned successfully.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        }

        // 2. Check for explicit full clear
        if ($request->input('clear_all') === '1' || $request->input('clear_all') === true) {
            \DB::transaction(function () use ($registration) {
                DisabilityDayStallAllotment::where('registration_id', $registration->id)->delete();
                $registration->allotted_stall = null;
                $registration->updated_by = Auth::id();
                $registration->save();
            });

            $msg = "All stall allotments cleared for registration {$registration->registration_no}.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        }

        // 3. Multi-stall selection or assignment
        // Support array 'stalls' (from multiple select) or single 'allotted_stall'
        $rawStalls = $request->input('stalls', $request->input('allotted_stall'));
        if (is_string($rawStalls) && str_contains($rawStalls, ',')) {
            $stallsInput = explode(',', $rawStalls);
        } elseif (is_array($rawStalls)) {
            $stallsInput = $rawStalls;
        } elseif ($rawStalls !== null && $rawStalls !== '') {
            $stallsInput = [(int)$rawStalls];
        } else {
            $stallsInput = [];
        }

        // Parse valid integers 1 to 100
        $desiredStallNumbers = [];
        foreach ($stallsInput as $val) {
            if (is_numeric(trim((string)$val))) {
                $n = (int) trim((string)$val);
                if ($n >= 1 && $n <= 100) {
                    $desiredStallNumbers[] = $n;
                }
            }
        }
        $desiredStallNumbers = array_values(array_unique($desiredStallNumbers));
        sort($desiredStallNumbers);

        // If no stalls submitted (and not in add mode), clear allotments
        if (empty($desiredStallNumbers) && $request->input('mode') !== 'add') {
            \DB::transaction(function () use ($registration) {
                DisabilityDayStallAllotment::where('registration_id', $registration->id)->delete();
                $registration->allotted_stall = null;
                $registration->updated_by = Auth::id();
                $registration->save();
            });

            $msg = "Stall allotment cleared for registration {$registration->registration_no}.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        }

        // If mode is 'add', merge newly selected stalls with existing assignments
        if ($request->input('mode') === 'add') {
            $existingStalls = $registration->getAllottedStallNumbers();
            $desiredStallNumbers = array_values(array_unique(array_merge($existingStalls, $desiredStallNumbers)));
            sort($desiredStallNumbers);
        }

        // Execute transaction with lockForUpdate to ensure transactional integrity
        $allotmentResult = \DB::transaction(function () use ($registration, $desiredStallNumbers) {
            // Check conflicts in disability_day_stall_allotments table
            $conflicts = DisabilityDayStallAllotment::where('registration_id', '!=', $registration->id)
                ->whereIn('stall_number', $desiredStallNumbers)
                ->lockForUpdate()
                ->with('registration')
                ->get();

            if ($conflicts->isNotEmpty()) {
                $first = $conflicts->first();
                $conflictOrg = $first->registration ? $first->registration->organization_name : 'another organization';
                $conflictReg = $first->registration ? $first->registration->registration_no : '';
                return [
                    'success' => false,
                    'message' => "Stall {$first->stall_number} is already assigned to {$conflictOrg} ({$conflictReg}). Please choose available stalls only."
                ];
            }

            // Also check conflicts in legacy column of disability_day_registrations
            $otherRegs = DisabilityDayRegistration::where('id', '!=', $registration->id)
                ->whereNotNull('allotted_stall')
                ->where('allotted_stall', '!=', '')
                ->lockForUpdate()
                ->get();

            foreach ($otherRegs as $otherReg) {
                $otherStalls = $otherReg->getAllottedStallNumbers();
                $overlap = array_intersect($desiredStallNumbers, $otherStalls);
                if (!empty($overlap)) {
                    $conflictNum = reset($overlap);
                    return [
                        'success' => false,
                        'message' => "Stall {$conflictNum} is already assigned to {$otherReg->organization_name} ({$otherReg->registration_no}). Please choose available stalls only."
                    ];
                }
            }

            // Sync allotments safely
            // Remove unselected stalls
            DisabilityDayStallAllotment::where('registration_id', $registration->id)
                ->whereNotIn('stall_number', $desiredStallNumbers)
                ->delete();

            // Insert newly added stalls
            $existingInDb = DisabilityDayStallAllotment::where('registration_id', $registration->id)
                ->pluck('stall_number')
                ->all();

            foreach ($desiredStallNumbers as $stallNo) {
                if (!in_array($stallNo, $existingInDb, true)) {
                    DisabilityDayStallAllotment::create([
                        'registration_id' => $registration->id,
                        'stall_number'    => $stallNo,
                        'stall_name'      => "Stall {$stallNo}",
                        'created_by'      => Auth::id(),
                    ]);
                }
            }

            // Synchronize summarized allotted_stall column on registration
            $stallString = implode(', ', array_map(fn($n) => "Stall {$n}", $desiredStallNumbers));
            $registration->allotted_stall = $stallString;
            $registration->updated_by = Auth::id();
            $registration->save();

            $stallSummary = implode(', ', $desiredStallNumbers);
            return [
                'success' => true,
                'message' => "Stall(s) #{$stallSummary} successfully assigned to {$registration->registration_no}."
            ];
        });

        if (!$allotmentResult['success']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($allotmentResult, 422);
            }
            return redirect()->back()->with('error', $allotmentResult['message'])->withInput();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($allotmentResult);
        }

        return redirect()->back()->with('success', $allotmentResult['message']);
    }

    /**
     * Send Stall Allotment confirmation email to organization's registered email ID.
     * Enforces backend authorization and prevents sending if no stall is allotted.
     */
    public function sendAllotmentEmail(Request $request, $id)
    {
        $registration = DisabilityDayRegistration::with(['district', 'state', 'stallAllotments'])->findOrFail($id);

        $allottedStalls = $registration->getAllottedStallNumbers();
        if (empty($allottedStalls)) {
            $msg = 'Cannot send allotment email: No stall has been allotted to this organization yet. Please allot at least one stall before sending confirmation.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if (empty($registration->email) || !filter_var($registration->email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Cannot send allotment email: Organization does not have a valid registered email address.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        try {
            Mail::to($registration->email)->send(new StallAllotmentConfirmationMail($registration, $allottedStalls));

            Log::info('Disability Day Stall Allotment Confirmation email sent successfully', [
                'registration_id' => $registration->id,
                'registration_no' => $registration->registration_no,
                'email'           => $registration->email,
                'allotted_stalls' => $allottedStalls,
                'sent_by'         => Auth::id(),
            ]);

            $stallsFormatted = implode(', ', array_map(fn($n) => "Stall #{$n}", $allottedStalls));
            $msg = "Stall allotment confirmation email sent successfully to {$registration->email} for {$stallsFormatted}.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Disability Day Stall Allotment Confirmation email delivery failed: ' . $e->getMessage(), [
                'registration_id' => $registration->id,
                'email'           => $registration->email,
                'allotted_stalls' => $allottedStalls,
            ]);

            $errMsg = 'Failed to dispatch allotment confirmation email: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errMsg], 500);
            }
            return redirect()->back()->with('error', $errMsg);
        }
    }

    /**
     * Resend registration confirmation email to applicant's current registered email.
     */
    public function resendConfirmationEmail(Request $request, $id)
    {
        $registration = DisabilityDayRegistration::findOrFail($id);

        try {
            Mail::to($registration->email)->send(new StallRegistrationConfirmationMail($registration));
            return redirect()->back()
                ->with('success', "Confirmation email resent successfully to {$registration->email}.");
        } catch (\Throwable $e) {
            Log::error('Disability Day resend confirmation email failed: ' . $e->getMessage(), [
                'registration_id' => $registration->id,
                'email'           => $registration->email,
            ]);
            return redirect()->back()
                ->with('error', 'Failed to dispatch confirmation email: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a registration.
     */
    public function destroy(Request $request, $id)
    {
        $registration = DisabilityDayRegistration::findOrFail($id);
        $regNo = $registration->registration_no;

        $registration->delete();

        return redirect()->route('admin.disability_day.index')
            ->with('success', "Registration record {$regNo} has been deleted successfully.");
    }
}

