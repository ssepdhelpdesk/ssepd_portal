<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\DisabilityDayEscort;
use App\Models\DisabilityDayParticipantRegistration;
use App\Models\District;
use App\Models\Grampanchayat;
use App\Models\Municipality;
use App\Models\State;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
        $this->middleware('permission:disability-day-edit', ['only' => ['create', 'store', 'edit', 'update']]);
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

            if ($request->filled('institute_id')) {
                $query->where('institute_id', $request->input('institute_id'));
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->input('gender'));
            }

            if ($request->filled('participation_in')) {
                $query->where('participation_in', $request->input('participation_in'));
            }

            if ($request->filled('attending_date')) {
                $query->whereJsonContains('attending_dates', $request->input('attending_date'));
            }

            if ($request->filled('transport_mode')) {
                $query->where('transport_mode', $request->input('transport_mode'));
            }

            if ($request->filled('expected_arrival_date')) {
                $query->whereDate('expected_arrival_date', $request->input('expected_arrival_date'));
            }

            if ($request->filled('tshirt_size')) {
                $query->where('tshirt_size', $request->input('tshirt_size'));
            }

            if ($request->filled('status')) {
                $query->where('status', (int) $request->input('status'));
            }

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
                    return '<span class="small text-muted" title="' . e($row->school_name) . '"><i class="fas fa-school me-1 text-secondary"></i>' . e(Str::limit($row->school_name, 30)) . '</span>';
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
                        return '<span class="badge bg-light text-muted border px-2 py-1">0</span>';
                    }
                    return '<span class="badge bg-primary text-white px-2 py-1"><i class="fas fa-user-friends me-1"></i>' . $escCount . '</span>';
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
                ->editColumn('attending_dates', function ($row) {
                    if (empty($row->attending_dates)) {
                        return '<span class="text-muted small">—</span>';
                    }
                    $formatted = DisabilityDayParticipantRegistration::formatAttendingDatesForDisplay($row->attending_dates);
                    if (str_starts_with($formatted, 'All Days')) {
                        return '<span class="badge bg-primary px-2 py-1">' . e($formatted) . '</span>';
                    }
                    return '<span class="small fw-medium">' . e($formatted) . '</span>';
                })
                ->editColumn('transport_mode', function ($row) {
                    if (empty($row->transport_mode)) {
                        return '<span class="text-muted small">—</span>';
                    }
                    $label = e($row->transport_mode);
                    if ($row->transport_mode === 'Other' && !empty($row->transport_mode_other)) {
                        $label .= '<div class="small text-muted">' . e(Str::limit($row->transport_mode_other, 25)) . '</div>';
                    }
                    return '<span class="small fw-medium">' . $label . '</span>';
                })
                ->editColumn('expected_arrival_date', function ($row) {
                    return $row->expected_arrival_date ? $row->expected_arrival_date->format('d-M-Y') : '<span class="text-muted small">—</span>';
                })
                ->editColumn('expected_arrival_time', function ($row) {
                    if (empty($row->expected_arrival_time)) {
                        return '<span class="text-muted small">—</span>';
                    }
                    try {
                        return '<span class="font-monospace small">' . Carbon::parse($row->expected_arrival_time)->format('h:i A') . '</span>';
                    } catch (\Throwable $e) {
                        return e($row->expected_arrival_time);
                    }
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

                    if ($user && $user->can('disability-day-edit')) {
                        $actions .= '<a href="' . route('admin.disability_day_participant.edit', $row->id) . '" class="btn btn-warning text-dark" title="Edit Participant Registration"><i class="fas fa-edit"></i></a>';
                    }

                    if ($user && $user->can('disability-day-delete')) {
                        $actions .= '<button type="button" class="btn btn-danger" title="Delete Participant Record" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->registration_no) . '\')"><i class="fas fa-trash-alt"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns([
                    'registration_no', 'participant_name', 'school_name', 'gender',
                    'tshirt_size', 'number_of_escorts', 'participation_in', 'attending_dates',
                    'transport_mode', 'expected_arrival_date', 'expected_arrival_time',
                    'status', 'action'
                ])
                ->make(true);
        }

        // Metrics for summary cards
        $totalParticipants = DisabilityDayParticipantRegistration::count();
        $totalEscorts = (int) DisabilityDayParticipantRegistration::sum('number_of_escorts');
        $culturalCount = DisabilityDayParticipantRegistration::where('participation_in', 'Cultural')->count();
        $sportsCount = DisabilityDayParticipantRegistration::where('participation_in', 'Sports')->count();
        $othersCount = DisabilityDayParticipantRegistration::where('participation_in', 'Others')->count();

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
     * Show the Admin Create Participant Registration form.
     */
    public function create()
    {
        $institutes = DB::table('field_visit_institutes')
            ->select('id', 'institute_name')
            ->whereNotNull('institute_name')
            ->where('institute_name', '!=', '')
            ->orderBy('institute_name', 'asc')
            ->get();

        $states = State::orderBy('state_name', 'asc')->get(['state_id', 'state_name']);

        return view('dashboard.disability_day_participant.create', compact('institutes', 'states'));
    }

    /**
     * Store a newly created participant registration from Admin Panel.
     */
    public function store(Request $request)
    {
        $validator = $this->validateParticipantRequest($request);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $appliedAt = Carbon::now('Asia/Kolkata');
        $financialYear = DisabilityDayParticipantRegistration::calculateFinancialYear($appliedAt);
        $data = $this->extractCleanPayload($request);
        $numEscorts = $data['number_of_escorts'];

        try {
            $registration = null;
            $maxRetries = 3;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $registration = DB::transaction(function () use ($data, $numEscorts, $request, $appliedAt, $financialYear) {
                        $regNo = DisabilityDayParticipantRegistration::generateUniqueRegistrationNo($appliedAt);

                        $participant = DisabilityDayParticipantRegistration::create(array_merge($data, [
                            'registration_no' => $regNo,
                            'applied_at'      => $appliedAt,
                            'status'          => (int) $request->input('status', 1),
                            'financial_year'  => $financialYear,
                        ]));

                        $this->syncEscorts($participant, $numEscorts, $request->input('escorts', []));

                        return $participant;
                    });
                    break;
                } catch (\Illuminate\Database\QueryException $qe) {
                    if ($qe->getCode() == 23000 && str_contains($qe->getMessage(), 'registration_no') && $attempt < $maxRetries) {
                        continue;
                    }
                    throw $qe;
                }
            }

            Log::info("Disability Day 2026: Participant {$registration->registration_no} created by Admin ID " . Auth::id());

            return redirect()
                ->route('admin.disability_day_participant.show', $registration->id)
                ->with('success', "Participant registration {$registration->registration_no} created successfully.");

        } catch (\Throwable $e) {
            Log::error("Admin Create Participant Failed: " . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Unable to create participant registration due to a server error.')
                ->withInput();
        }
    }

    /**
     * Display a specific participant's full details including address hierarchy, transport, arrival, and escorts.
     */
    public function show($id)
    {
        $participant = DisabilityDayParticipantRegistration::with(['escorts', 'district', 'state'])
            ->findOrFail($id);

        return view('dashboard.disability_day_participant.show', compact('participant'));
    }

    /**
     * Show the Admin Edit Participant Registration form with pre-populated saved values.
     */
    public function edit($id)
    {
        $participant = DisabilityDayParticipantRegistration::with('escorts')->findOrFail($id);

        $institutes = DB::table('field_visit_institutes')
            ->select('id', 'institute_name')
            ->whereNotNull('institute_name')
            ->where('institute_name', '!=', '')
            ->orderBy('institute_name', 'asc')
            ->get();

        $states = State::orderBy('state_name', 'asc')->get(['state_id', 'state_name']);

        // Pre-load dependent dropdowns for the participant's saved (or old) location
        $activeStateId = old('state', $participant->state_id);
        $activeDistrictId = old('district', $participant->district_id);
        $activeBlockId = old('block', $participant->block_id);
        $activeGpId = old('grampanchayat', $participant->gp_id);

        $districts = $activeStateId
            ? District::where('state_id', $activeStateId)->orderBy('district_name', 'asc')->get(['district_id', 'district_name'])
            : collect();

        $blocks = $activeDistrictId
            ? Block::where('district_id', $activeDistrictId)->orderBy('block_name', 'asc')->get(['block_id', 'block_name'])
            : collect();

        $grampanchayats = $activeBlockId
            ? Grampanchayat::where('block_id', $activeBlockId)->orderBy('gp_name', 'asc')->get(['gp_id', 'gp_name'])
            : collect();

        $villages = $activeGpId
            ? Village::where('gp_id', $activeGpId)->orderBy('village_name', 'asc')->get(['village_id', 'village_name'])
            : collect();

        $municipalities = $activeDistrictId
            ? Municipality::where('district_id', $activeDistrictId)->orderBy('municipality_name', 'asc')->get(['municipality_id', 'municipality_name'])
            : collect();

        return view('dashboard.disability_day_participant.edit', compact(
            'participant',
            'institutes',
            'states',
            'districts',
            'blocks',
            'grampanchayats',
            'villages',
            'municipalities'
        ));
    }

    /**
     * Update an existing participant registration record without creating duplicates or altering registration_no.
     */
    public function update(Request $request, $id)
    {
        $participant = DisabilityDayParticipantRegistration::with('escorts')->findOrFail($id);

        $validator = $this->validateParticipantRequest($request);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $this->extractCleanPayload($request);
        $numEscorts = $data['number_of_escorts'];

        try {
            DB::transaction(function () use ($participant, $data, $numEscorts, $request) {
                // Never overwrite registration_no, applied_at, or financial_year
                if ($request->filled('status')) {
                    $data['status'] = (int) $request->input('status');
                }

                $participant->update($data);

                $this->syncEscorts($participant, $numEscorts, $request->input('escorts', []));
            });

            Log::info("Disability Day 2026: Participant {$participant->registration_no} updated by Admin ID " . Auth::id());

            return redirect()
                ->route('admin.disability_day_participant.show', $participant->id)
                ->with('success', "Participant registration {$participant->registration_no} updated successfully.");

        } catch (\Throwable $e) {
            Log::error("Admin Update Participant #{$id} Failed: " . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Unable to update participant registration due to a server error.')
                ->withInput();
        }
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
                $participant->escorts()->delete();
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

    /**
     * Shared validation logic for Admin Create and Update.
     */
    private function validateParticipantRequest(Request $request)
    {
        $addressType = (int) $request->input('address_type');
        $numEscorts = (int) $request->input('number_of_escorts', 0);

        $rules = [
            'participant_name'      => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    DisabilityDayParticipantRegistration::validateSingleParticipantName($value, $fail);
                },
            ],
            'institute_id'          => ['required', 'integer', 'exists:field_visit_institutes,id'],
            'gender'                => ['required', 'string', 'in:Male,Female,Other'],
            'tshirt_size'           => ['required', 'string', 'in:S,M,L,XL,XXL,XXXL'],
            'address_type'          => ['required', 'in:1,2'],
            'state'                 => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('states')->where('state_id', $value)->orWhere('id', $value)->exists();
                    if (!$exists) {
                        $fail('The selected State is invalid.');
                    }
                }
            ],
            'district'              => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $stateVal = $request->input('state');
                    $stateRow = DB::table('states')->where('state_id', $stateVal)->orWhere('id', $stateVal)->first();
                    $districtRow = DB::table('districts')->where('district_id', $value)->orWhere('id', $value)->first();
                    if (!$districtRow) {
                        $fail('The selected District is invalid.');
                        return;
                    }
                    if ($stateRow && $districtRow->state_id != $stateRow->state_id && $districtRow->state_id != $stateRow->id) {
                        $fail('The selected District does not belong to the chosen State.');
                    }
                }
            ],
            'pin'                   => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'number_of_escorts'     => ['required', 'integer', 'min:0', 'max:5'],
            'participation_in'      => ['required', 'string', 'in:Cultural,Sports,Others'],
            'participation_other'   => ['required_if:participation_in,Others', 'nullable', 'string', 'max:255'],
            'attending_dates'       => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    DisabilityDayParticipantRegistration::validateAttendingDates($value, $fail);
                },
            ],
            'transport_mode'        => ['required', 'string', 'in:' . implode(',', DisabilityDayParticipantRegistration::TRANSPORT_MODES)],
            'transport_mode_other'  => ['required_if:transport_mode,Other', 'nullable', 'string', 'max:255'],
            'expected_arrival_date' => ['required', 'date'],
            'expected_arrival_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
        ];

        if ($addressType === 1) {
            $rules['block'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $distVal = $request->input('district');
                    $distRow = DB::table('districts')->where('district_id', $distVal)->orWhere('id', $distVal)->first();
                    $blockRow = DB::table('blocks')->where('block_id', $value)->orWhere('id', $value)->first();
                    if (!$blockRow) {
                        $fail('The selected Block is invalid.');
                        return;
                    }
                    if ($distRow && $blockRow->district_id != $distRow->district_id && $blockRow->district_id != $distRow->id) {
                        $fail('The selected Block does not belong to the chosen District.');
                    }
                }
            ];
            $rules['grampanchayat'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $blockVal = $request->input('block');
                    $blockRow = DB::table('blocks')->where('block_id', $blockVal)->orWhere('id', $blockVal)->first();
                    $gpRow = DB::table('grampanchayats')->where('gp_id', $value)->orWhere('id', $value)->first();
                    if (!$gpRow) {
                        $fail('The selected Grampanchayat is invalid.');
                        return;
                    }
                    if ($blockRow && $gpRow->block_id != $blockRow->block_id && $gpRow->block_id != $blockRow->id) {
                        $fail('The selected Grampanchayat does not belong to the chosen Block.');
                    }
                }
            ];
            $rules['village'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $gpVal = $request->input('grampanchayat');
                    $gpRow = DB::table('grampanchayats')->where('gp_id', $gpVal)->orWhere('id', $gpVal)->first();
                    $villageRow = DB::table('villages')->where('village_id', $value)->orWhere('id', $value)->first();
                    if (!$villageRow) {
                        $fail('The selected Village is invalid.');
                        return;
                    }
                    if ($gpRow && $villageRow->gp_id != $gpRow->gp_id && $villageRow->gp_id != $gpRow->id) {
                        $fail('The selected Village does not belong to the chosen Grampanchayat.');
                    }
                }
            ];
        } elseif ($addressType === 2) {
            $rules['municipality'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $distVal = $request->input('district');
                    $distRow = DB::table('districts')->where('district_id', $distVal)->orWhere('id', $distVal)->first();
                    $muniRow = DB::table('municipalities')->where('municipality_id', $value)->orWhere('id', $value)->first();
                    if (!$muniRow) {
                        $fail('The selected Municipality is invalid.');
                        return;
                    }
                    if ($distRow && $muniRow->district_id != $distRow->district_id && $muniRow->district_id != $distRow->id) {
                        $fail('The selected Municipality does not belong to the chosen District.');
                    }
                }
            ];
        }

        if ($numEscorts === 0) {
            $rules['escorts'] = ['nullable', 'array', 'max:0'];
        } else {
            $rules['escorts'] = ['required', 'array', 'size:' . $numEscorts];
            $rules['escorts.*.name'] = ['required', 'string', 'min:2', 'max:150', 'regex:/^[\p{L}\p{M}\s\.\'-]+$/u'];
            $rules['escorts.*.mobile'] = ['required', 'regex:/^[6-9][0-9]{9}$/'];
            $rules['escorts.*.email'] = ['nullable', 'email', 'max:150'];
            $rules['escorts.*.relationship'] = ['required', 'string', 'min:2', 'max:100'];
        }

        $messages = [
            'escorts.max'                      => 'No escort details should be provided when the number of escorts is 0.',
            'escorts.size'                     => 'The number of submitted escort details must match the declared count of :size.',
            'attending_dates.required'         => 'Please select at least one date of participation.',
            'attending_dates.min'              => 'Please select at least one date of participation.',
            'transport_mode_other.required_if' => 'Please specify the other mode of transportation.',
            'participation_other.required_if'  => 'Please specify the other participation activity.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Extract and normalize clean attributes for creating/updating a participant.
     */
    private function extractCleanPayload(Request $request): array
    {
        $instituteId = $request->input('institute_id');
        $institute = DB::table('field_visit_institutes')->where('id', $instituteId)->first();
        $schoolName = $institute ? $institute->institute_name : 'Unknown Institute';

        $addressType = (int) $request->input('address_type');
        $stateId = $request->input('state');
        $districtId = $request->input('district');
        $blockId = $addressType === 1 ? $request->input('block') : null;
        $gpId = $addressType === 1 ? $request->input('grampanchayat') : null;
        $villageId = $addressType === 1 ? $request->input('village') : null;
        $municipalityId = $addressType === 2 ? $request->input('municipality') : null;

        $participationIn = trim((string) $request->input('participation_in'));
        $transportMode = trim((string) $request->input('transport_mode'));

        return [
            'participant_name'      => DisabilityDayParticipantRegistration::normalizeParticipantName($request->input('participant_name')),
            'institute_id'          => (int) $instituteId,
            'school_name'           => $schoolName,
            'gender'                => trim((string) $request->input('gender')),
            'tshirt_size'           => strtoupper(trim((string) $request->input('tshirt_size'))),
            'address_type'          => $addressType,
            'state_id'              => $stateId ? (int) $stateId : null,
            'state_name'            => State::where('state_id', $stateId)->orWhere('id', $stateId)->value('state_name'),
            'district_id'           => $districtId ? (int) $districtId : null,
            'district_name'         => District::where('district_id', $districtId)->orWhere('id', $districtId)->value('district_name'),
            'block_id'              => $blockId ? (int) $blockId : null,
            'block_name'            => $blockId ? Block::where('block_id', $blockId)->orWhere('id', $blockId)->value('block_name') : null,
            'gp_id'                 => $gpId ? (int) $gpId : null,
            'gp_name'               => $gpId ? Grampanchayat::where('gp_id', $gpId)->orWhere('id', $gpId)->value('gp_name') : null,
            'village_id'            => $villageId ? (int) $villageId : null,
            'village_name'          => $villageId ? Village::where('village_id', $villageId)->orWhere('id', $villageId)->value('village_name') : null,
            'municipality_id'       => $municipalityId ? (int) $municipalityId : null,
            'municipality_name'     => $municipalityId ? Municipality::where('municipality_id', $municipalityId)->orWhere('id', $municipalityId)->value('municipality_name') : null,
            'pin'                   => trim((string) $request->input('pin')),
            'number_of_escorts'     => (int) $request->input('number_of_escorts', 0),
            'participation_in'      => $participationIn,
            'participation_other'   => $participationIn === 'Others' ? trim((string) $request->input('participation_other')) : null,
            'attending_dates'       => DisabilityDayParticipantRegistration::normalizeAttendingDates($request->input('attending_dates', [])),
            'transport_mode'        => $transportMode,
            'transport_mode_other'  => $transportMode === 'Other' ? trim((string) $request->input('transport_mode_other')) : null,
            'expected_arrival_date' => trim((string) $request->input('expected_arrival_date')),
            'expected_arrival_time' => trim((string) $request->input('expected_arrival_time')),
        ];
    }

    /**
     * Synchronize escort records for a participant registration.
     */
    private function syncEscorts(DisabilityDayParticipantRegistration $participant, int $numEscorts, array $escortsInput): void
    {
        $participant->escorts()->delete();

        if ($numEscorts <= 0) {
            return;
        }

        $index = 1;
        foreach ($escortsInput as $escortData) {
            if ($index > $numEscorts) {
                break;
            }

            $escortEmail = isset($escortData['email']) && trim((string) $escortData['email']) !== ''
                ? strtolower(trim((string) $escortData['email']))
                : null;

            DisabilityDayEscort::create([
                'participant_registration_id' => $participant->id,
                'escort_index'                => $index,
                'name'                        => trim((string) ($escortData['name'] ?? '')),
                'mobile'                      => trim((string) ($escortData['mobile'] ?? '')),
                'email'                       => $escortEmail,
                'relationship'                => trim((string) ($escortData['relationship'] ?? '')),
            ]);

            $index++;
        }
    }
}
