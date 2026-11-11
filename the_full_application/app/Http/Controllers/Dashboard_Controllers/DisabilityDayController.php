<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use App\Mail\StallRegistrationConfirmationMail;
use App\Models\DisabilityDayRegistration;
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
    }

    /**
     * Display a paginated, filterable listing of stall registrations using Yajra DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DisabilityDayRegistration::with(['district', 'state'])
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
                    if ($row->isAllotted()) {
                        return '<span class="badge bg-success font-monospace fs-7 px-2 py-1"><i class="fas fa-store me-1"></i>' . e($row->allotted_stall) . '</span>';
                    }
                    return '<span class="badge bg-secondary px-2 py-1">Not Yet Allotted</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group btn-group-sm" role="group">';

                    if (auth()->user()->can('disability-day-show')) {
                        $actions .= '<a href="' . route('admin.disability_day.show', $row->id) . '" class="btn btn-info text-white" title="View Application Details"><i class="fas fa-eye"></i></a>';
                    }

                    if (auth()->user()->can('disability-day-edit')) {
                        $actions .= '<a href="' . route('admin.disability_day.edit', $row->id) . '" class="btn btn-warning text-dark" title="Edit Registration Details"><i class="fas fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('disability-day-allot-stall')) {
                        $currentStall = e($row->allotted_stall ?? '');
                        $orgNameEscaped = e($row->organization_name ?? '');
                        $actions .= '<button type="button" class="btn btn-success text-white" title="Assign / Update Stall" onclick="openAllotModal(' . $row->id . ', \'' . addslashes($row->registration_no) . '\', \'' . addslashes($currentStall) . '\', \'' . addslashes($orgNameEscaped) . '\')"><i class="fas fa-store-alt"></i></button>';
                    }

                    if (auth()->user()->can('disability-day-delete')) {
                        $actions .= '<button type="button" class="btn btn-danger" title="Delete Registration" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->registration_no) . '\')"><i class="fas fa-trash-alt"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['registration_no', 'organization_name', 'email', 'phone', 'allotted_stall', 'action'])
                ->make(true);
        }

        $districts = District::where('status', 1)->orderBy('district_name')->get();
        $totalCount = DisabilityDayRegistration::count();
        $allottedCount = DisabilityDayRegistration::whereNotNull('allotted_stall')->where('allotted_stall', '!=', '')->count();
        $unallottedCount = $totalCount - $allottedCount;

        return view('dashboard.disability_day.index', compact('districts', 'totalCount', 'allottedCount', 'unallottedCount'));
    }

    /**
     * Display a specific registration's full details.
     */
    public function show($id)
    {
        $registration = DisabilityDayRegistration::with(['district', 'state', 'creator', 'updater'])
            ->findOrFail($id);

        $availableStalls = DisabilityDayRegistration::getAvailableStallNumbers($registration->id);
        $currentStallNumber = $registration->getCurrentStallNumber();

        return view('dashboard.disability_day.show', compact('registration', 'availableStalls', 'currentStallNumber'));
    }

    /**
     * Fetch dynamic available stalls (1-100) excluding stalls assigned to other registrations.
     */
    public function getAvailableStalls($id)
    {
        $registration = DisabilityDayRegistration::findOrFail($id);

        $availableStalls = DisabilityDayRegistration::getAvailableStallNumbers($registration->id);
        $currentStallNumber = $registration->getCurrentStallNumber();

        return response()->json([
            'success'          => true,
            'registration_id'  => $registration->id,
            'registration_no'  => $registration->registration_no,
            'current_stall'    => $currentStallNumber,
            'available_stalls' => $availableStalls,
            'total_available'  => count($availableStalls),
            'is_full'          => empty($availableStalls),
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

        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255',
            'contact_person'    => 'required|string|max:255',
            'email'             => 'required|string|email|max:150',
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

        // Update ONLY permitted fields — NEVER touch registration_no or applied_at
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
     * Assign or update stall allotment with duplicate prevention and locking.
     */
    public function allotStall(Request $request, $id)
    {
        $registration = DisabilityDayRegistration::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'allotted_stall' => 'nullable|integer|between:1,100',
        ], [
            'allotted_stall.integer' => 'Stall number must be an integer between 1 and 100.',
            'allotted_stall.between' => 'Stall number must be between 1 and 100.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('allotted_stall')
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $stallVal = $request->input('allotted_stall');

        // Handle clearing allotment
        if ($stallVal === null || $stallVal === '') {
            $registration->allotted_stall = null;
            $registration->updated_by = Auth::id();
            $registration->save();

            $msg = "Stall allotment cleared for registration {$registration->registration_no}.";
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        }

        $stallNumber = (int) $stallVal;

        // Use DB transaction with lockForUpdate to prevent race conditions & duplicate assignments
        $allotmentResult = \DB::transaction(function () use ($registration, $stallNumber) {
            $conflict = DisabilityDayRegistration::where('id', '!=', $registration->id)
                ->whereNotNull('allotted_stall')
                ->where('allotted_stall', '!=', '')
                ->lockForUpdate()
                ->get()
                ->first(function ($record) use ($stallNumber) {
                    return $record->getCurrentStallNumber() === $stallNumber;
                });

            if ($conflict) {
                return [
                    'success' => false,
                    'message' => "Stall {$stallNumber} is already assigned to {$conflict->organization_name} ({$conflict->registration_no}). Please choose another stall."
                ];
            }

            $registration->allotted_stall = (string) $stallNumber;
            $registration->updated_by = Auth::id();
            $registration->save();

            return [
                'success' => true,
                'message' => "Stall {$stallNumber} successfully assigned to {$registration->registration_no}."
            ];
        });

        if (!$allotmentResult['success']) {
            if ($request->ajax()) {
                return response()->json($allotmentResult, 422);
            }
            return redirect()->back()->with('error', $allotmentResult['message'])->withInput();
        }

        if ($request->ajax()) {
            return response()->json($allotmentResult);
        }

        return redirect()->back()->with('success', $allotmentResult['message']);
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

