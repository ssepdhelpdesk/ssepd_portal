<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use App\Models\DisabilityDayEscort;
use App\Models\DisabilityDayParticipantRegistration;
use App\Models\District;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DisabilityDayParticipantAdminController extends Controller
{
    /**
     * Enforce Spatie permissions across all participant dashboard operations.
     */
    public function __construct()
    {
        $this->middleware('permission:disability-day-access|disability-day-list', ['only' => ['index']]);
        $this->middleware('permission:disability-day-show', ['only' => ['show']]);
        $this->middleware('permission:disability-day-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a paginated, filterable listing of participant registrations using Yajra DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = DisabilityDayParticipantRegistration::with(['escorts', 'district', 'state'])
                ->select('disability_day_participant_registrations.*');

            // Search by keyword (reg no, participant name, school name, escort name/mobile)
            $searchVal = $request->input('search_keyword');
            if (empty($searchVal) && $request->filled('search')) {
                $rawSearch = $request->input('search');
                $searchVal = is_array($rawSearch) ? ($rawSearch['value'] ?? null) : $rawSearch;
            }
            if (!empty($searchVal) && is_string($searchVal)) {
                $term = '%' . trim($searchVal) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('registration_no', 'like', $term)
                      ->orWhere('participant_name', 'like', $term)
                      ->orWhere('school_name', 'like', $term)
                      ->orWhereHas('escorts', function ($eq) use ($term) {
                          $eq->where('name', 'like', $term)
                             ->orWhere('mobile', 'like', $term);
                      });
                });
            }

            // Filter by School / Institute
            if ($request->filled('institute_id')) {
                $query->where('institute_id', $request->input('institute_id'));
            }

            // Filter by Gender
            if ($request->filled('gender')) {
                $query->where('gender', $request->input('gender'));
            }

            // Filter by Participation Category
            if ($request->filled('participation_in')) {
                $query->where('participation_in', $request->input('participation_in'));
            }

            // Filter by T-Shirt Size
            if ($request->filled('tshirt_size')) {
                $query->where('tshirt_size', $request->input('tshirt_size'));
            }

            // Filter by Status
            if ($request->filled('status')) {
                $query->where('status', (int) $request->input('status'));
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
                    return '<a href="' . route('admin.disability_day_participant.show', $row->id) . '" class="fw-bold text-primary font-monospace text-decoration-none" title="View Full Details">'
                        . e($row->registration_no) . '</a>';
                })
                ->editColumn('participant_name', function ($row) {
                    return '<span class="fw-semibold text-dark">' . e($row->participant_name) . '</span>';
                })
                ->editColumn('school_name', function ($row) {
                    return '<span class="small text-muted" title="' . e($row->school_name) . '"><i class="fas fa-school me-1 text-secondary"></i>' . e(Str::limit($row->school_name, 35)) . '</span>';
                })
                ->editColumn('gender', function ($row) {
                    $badgeClass = match($row->gender) {
                        'Male'   => 'bg-info text-dark',
                        'Female' => 'bg-pink text-white',
                        default  => 'bg-secondary text-white',
                    };
                    return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . e($row->gender) . '</span>';
                })
                ->editColumn('tshirt_size', function ($row) {
                    return '<span class="badge bg-dark font-monospace fs-7 px-2 py-1">' . e($row->tshirt_size) . '</span>';
                })
                ->editColumn('number_of_escorts', function ($row) {
                    $escCount = (int) $row->number_of_escorts;
                    if ($escCount === 0) {
                        return '<span class="badge bg-light text-muted border px-2 py-1">0 Escorts</span>';
                    }
                    return '<span class="badge bg-primary text-white px-2 py-1"><i class="fas fa-user-friends me-1"></i>' . $escCount . ' Escort' . ($escCount > 1 ? 's' : '') . '</span>';
                })
                ->editColumn('participation_in', function ($row) {
                    $badge = match($row->participation_in) {
                        'Cultural' => 'bg-purple text-white',
                        'Sports'   => 'bg-success text-white',
                        default    => 'bg-warning text-dark',
                    };
                    $otherDesc = $row->participation_in === 'Others' && !empty($row->participation_other)
                        ? ' title="' . e($row->participation_other) . '"'
                        : '';
                    return '<span class="badge ' . $badge . ' px-2 py-1"' . $otherDesc . '>' . e($row->participation_in) . '</span>';
                })
                ->editColumn('applied_at', function ($row) {
                    return $row->applied_at ? $row->applied_at->format('d-M-Y h:i A') : 'N/A';
                })
                ->editColumn('status', function ($row) {
                    if ($row->status === 1) {
                        return '<span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i>Registered</span>';
                    }
                    return '<span class="badge bg-secondary px-2 py-1">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group btn-group-sm" role="group">';
                    $user = auth()->user();

                    if ($user && $user->can('disability-day-show')) {
                        $actions .= '<a href="' . route('admin.disability_day_participant.show', $row->id) . '" class="btn btn-info text-white" title="View Full Participant Details"><i class="fas fa-eye"></i></a>';
                    }

                    if ($user && $user->can('disability-day-delete')) {
                        $actions .= '<button type="button" class="btn btn-danger" title="Delete Participant Record" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->registration_no) . '\')"><i class="fas fa-trash-alt"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['registration_no', 'participant_name', 'school_name', 'gender', 'tshirt_size', 'number_of_escorts', 'participation_in', 'status', 'action'])
                ->make(true);
        }

        // Metrics for summary cards
        $totalParticipants = DisabilityDayParticipantRegistration::count();
        $totalEscorts = (int) DisabilityDayParticipantRegistration::sum('number_of_escorts');
        $culturalCount = DisabilityDayParticipantRegistration::where('participation_in', 'Cultural')->count();
        $sportsCount = DisabilityDayParticipantRegistration::where('participation_in', 'Sports')->count();
        $othersCount = DisabilityDayParticipantRegistration::where('participation_in', 'Others')->count();

        // Dropdown filter options
        $institutes = DB::table('field_visit_institutes')
            ->select('id', 'institute_name')
            ->whereNotNull('institute_name')
            ->where('institute_name', '!=', '')
            ->orderBy('institute_name', 'asc')
            ->get();

        return view('dashboard.disability_day_participant.index', compact(
            'totalParticipants',
            'totalEscorts',
            'culturalCount',
            'sportsCount',
            'othersCount',
            'institutes'
        ));
    }

    /**
     * Display a specific participant's full details including address hierarchy and escorts.
     */
    public function show($id)
    {
        $participant = DisabilityDayParticipantRegistration::with(['escorts', 'district', 'state'])
            ->findOrFail($id);

        return view('dashboard.disability_day_participant.show', compact('participant'));
    }

    /**
     * Soft delete a participant record and cascade to escort records.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $participant = DisabilityDayParticipantRegistration::findOrFail($id);
            $regNo = $participant->registration_no;
            
            DB::transaction(function () use ($participant) {
                // Delete associated escorts
                $participant->escorts()->delete();
                // Delete participant record
                $participant->delete();
            });

            Log::info("Disability Day 2026: Participant registration #{$regNo} deleted by Admin User ID " . auth()->id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Participant registration #{$regNo} has been deleted successfully."
                ]);
            }

            return redirect()
                ->route('admin.disability_day_participant.index')
                ->with('success', "Participant registration #{$regNo} has been deleted successfully.");

        } catch (\Throwable $e) {
            Log::error("Failed to delete participant registration #{$id}: " . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to delete the record due to a server error.'
                ], 500);
            }

            return redirect()
                ->route('admin.disability_day_participant.index')
                ->with('error', 'Unable to delete the record due to a server error.');
        }
    }
}
