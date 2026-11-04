<?php

namespace App\Http\Controllers\Dashboard_Controllers\Field_Visit_Controller;

use App\Http\Controllers\Controller;
use App\Models\FieldVisit;
use App\Models\FieldVisitEvidence;
use App\Models\FieldVisitDocument;
use App\Models\FieldVisitType;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\SpecialSchool;
use App\Models\DdrcDetails;
use App\Models\PiaInstituteMaster;
use App\Models\FieldVisitInstitute;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class FieldVisitController extends Controller
{
    /**
     * Enforce Spatie permissions across all controller endpoints.
     */
    public function __construct()
    {
        $this->middleware(
            'permission:field-visit-access|field-visit-list|field-visit-show|field-visit-create|field-visit-edit|field-visit-delete|field-visit-submit',
            ['only' => ['index']]
        );
        $this->middleware(
            'permission:field-visit-access|field-visit-show|field-visit-list|field-visit-edit|field-visit-delete',
            ['only' => ['show']]
        );
        $this->middleware('permission:field-visit-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:field-visit-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:field-visit-delete', ['only' => ['destroy']]);
        $this->middleware(
            'permission:field-visit-evidence-view|field-visit-show|field-visit-access',
            ['only' => ['viewEvidence']]
        );
        $this->middleware(
            'permission:field-visit-show|field-visit-access',
            ['only' => ['downloadDocument']]
        );
        $this->middleware(
            'permission:field-visit-export|field-visit-list|field-visit-access',
            ['only' => ['export']]
        );
    }

    /**
     * Display a paginated, filterable listing of field visits.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = FieldVisit::with(['visitType', 'district', 'block', 'visitingOfficer', 'evidences'])
                ->select('field_visits.*');

            // Apply geographical role-based restrictions
            $this->applyGeographicalScope($query, Auth::user());

            if ($request->filled('field_visit_type_id')) {
                $query->where('field_visit_type_id', $request->input('field_visit_type_id'));
            }
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            if ($request->filled('district_id')) {
                $query->where('district_id', $request->input('district_id'));
            }
            if ($request->filled('institution_type')) {
                $query->where('institution_type', $request->input('institution_type'));
            }
            if ($request->filled('is_follow_up_required')) {
                $query->where('is_follow_up_required', (bool)$request->input('is_follow_up_required'));
            }
            if ($request->filled('date_from')) {
                $query->whereDate('visit_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('visit_date', '<=', $request->input('date_to'));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('visit_reference_no', function ($row) {
                    return '<a href="' . route('admin.field_visits.show', $row->id) . '" class="fw-bold text-primary text-decoration-none font-monospace">'
                        . e($row->visit_reference_no) . '</a>';
                })
                ->editColumn('visit_date', function ($row) {
                    return $row->visit_date ? $row->visit_date->format('d-M-Y') : 'N/A';
                })
                ->editColumn('visit_type', function ($row) {
                    return $row->visitType ? e($row->visitType->name) : '<span class="text-muted">N/A</span>';
                })
                ->editColumn('institution_name', function ($row) {
                    $typeBadge = '<span class="badge bg-light text-dark border me-1">' . e($row->institution_type) . '</span>';
                    return $typeBadge . '<br><span class="fw-semibold">' . e(Str::limit($row->institution_name, 35)) . '</span>';
                })
                ->editColumn('district', function ($row) {
                    return $row->district ? e($row->district->district_name) : '—';
                })
                ->editColumn('visiting_officer', function ($row) {
                    $officerName = $row->visiting_officer_name ?: ($row->visitingOfficer ? $row->visitingOfficer->name : 'Officer #' . $row->visiting_officer_id);
                    $desig = $row->visiting_officer_designation === 'Other' && !empty($row->other_designation)
                        ? $row->other_designation
                        : $row->visiting_officer_designation;
                    $html = '<span class="fw-semibold">' . e($officerName) . '</span>';
                    if (!empty($desig)) {
                        $html .= '<br><span class="badge bg-secondary font-monospace" style="font-size:0.65rem;">' . e($desig) . '</span>';
                    }
                    return $html;
                })
                ->editColumn('photos_count', function ($row) {
                    $count = $row->evidences->count();
                    return $count > 0 
                        ? '<span class="badge bg-info text-dark"><i class="fas fa-camera me-1"></i> ' . $count . '</span>'
                        : '<span class="text-muted">—</span>';
                })
                ->editColumn('status', function ($row) {
                    return $row->status_badge;
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>';
                    $actions .= '<div class="dropdown-menu dropdown-menu-end">';

                    if (auth()->user()->can('field-visit-show')) {
                        $actions .= '<a class="dropdown-item" href="' . route('admin.field_visits.show', $row->id) . '"><i class="fas fa-eye text-info me-2"></i> View Details</a>';
                    }
                    if (auth()->user()->can('field-visit-edit')) {
                        $actions .= '<a class="dropdown-item" href="' . route('admin.field_visits.edit', $row->id) . '"><i class="fas fa-edit text-warning me-2"></i> Edit Visit</a>';
                    }
                    if (auth()->user()->can('field-visit-delete')) {
                        $actions .= '<a class="dropdown-item text-danger" href="' . route('admin.field_visits.destroy', $row->id) . '" id="delete"><i class="fas fa-trash-alt text-danger me-2"></i> Delete</a>';
                    }

                    $actions .= '</div></div>';
                    return $actions;
                })
                ->rawColumns(['visit_reference_no', 'visit_type', 'institution_name', 'visiting_officer', 'photos_count', 'status', 'action'])
                ->make(true);
        }

        // Server-side filtered query for Blade view
        $query = FieldVisit::with(['visitType', 'district', 'block', 'visitingOfficer', 'evidences'])
            ->orderBy('visit_date', 'desc')
            ->orderBy('id', 'desc');

        // Apply geographical role-based restrictions
        $this->applyGeographicalScope($query, Auth::user());

        if ($request->filled('search')) {
            $query->search(trim($request->input('search')));
        }
        if ($request->filled('field_visit_type_id')) {
            $query->where('field_visit_type_id', $request->input('field_visit_type_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }
        if ($request->filled('institution_type')) {
            $query->where('institution_type', $request->input('institution_type'));
        }
        if ($request->filled('is_follow_up_required')) {
            $query->where('is_follow_up_required', (bool)$request->input('is_follow_up_required'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('visit_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('visit_date', '<=', $request->input('date_to'));
        }

        $visits = $query->paginate(15)->withQueryString();
        $visitTypes = FieldVisitType::where('is_active', true)->orderBy('sort_order')->get();

        // Scope districts dropdown based on user's posting if applicable
        $user = Auth::user();
        $userRole = (int)($user->role_id ?? ($user->roles()->exists() ? $user->roles()->first()->id : 0));
        if (in_array($userRole, [9, 11]) && !empty($user->posted_district)) {
            $districts = District::where('district_id', $user->posted_district)->orderBy('district_name')->get();
        } elseif (in_array($userRole, [4, 6]) && !empty($user->posted_block)) {
            $blockDistId = Block::where('block_id', $user->posted_block)->value('district_id');
            $districts = $blockDistId ? District::where('district_id', $blockDistId)->get() : District::orderBy('district_name')->get();
        } elseif ($userRole === 5 && !empty($user->posted_municipality)) {
            $muniDistId = Municipality::where('municipality_id', $user->posted_municipality)->value('district_id');
            $districts = $muniDistId ? District::where('district_id', $muniDistId)->get() : District::orderBy('district_name')->get();
        } elseif (in_array($userRole, [8, 10]) && !empty($user->posted_subdiv)) {
            $subdivDistId = Block::where('subdivision_id', $user->posted_subdiv)->value('district_id')
                ?: Municipality::where('subdivision_id', $user->posted_subdiv)->value('district_id');
            $districts = $subdivDistId ? District::where('district_id', $subdivDistId)->get() : District::orderBy('district_name')->get();
        } else {
            $districts = District::orderBy('district_name')->get();
        }

        return view('dashboard.field_visit.visits.index', compact('visits', 'visitTypes', 'districts'));
    }

    /**
     * Show the form for creating a new field visit.
     */
    public function create(): View
    {
        $visitTypes = FieldVisitType::where('is_active', true)->orderBy('sort_order')->get();
        
        // Distribute all districts to the view without user-specific district restriction
        $user = Auth::user();
        $userRole = (int)($user->role_id ?? ($user->roles()->exists() ? $user->roles()->first()->id : 0));
        $districts = District::orderBy('district_name')->get();

        // Preview reference number for display
        $todayPrefix = 'FV-' . date('Ymd') . '-';
        $lastToday = FieldVisit::withTrashed()
            ->where('visit_reference_no', 'like', $todayPrefix . '%')
            ->orderByDesc('id')
            ->first();
        $nextNum = 1;
        if ($lastToday) {
            $parts = explode('-', $lastToday->visit_reference_no);
            $nextNum = ((int)end($parts)) + 1;
        }
        $previewReferenceNo = $todayPrefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $categories = FieldVisitInstitute::query()
            ->select('category_id', 'category_name')
            ->distinct()
            ->whereNotNull('category_id')
            ->whereNotNull('category_name')
            ->orderByRaw("CASE WHEN category_id = 3 THEN 1 ELSE 0 END ASC")
            ->orderBy('category_name', 'ASC')
            ->get()
            ->unique('category_name')
            ->values();

        $designations = FieldVisit::getDesignations();

        return view('dashboard.field_visit.visits.create', compact('visitTypes', 'districts', 'categories', 'previewReferenceNo', 'designations', 'userRole', 'user'));
    }

    /**
     * Store a newly created field visit with geotagged photos and reference documents.
     */
    public function store(Request $request): RedirectResponse
    {
        // Mandatory Audit Rule: Verify authenticated user's user_table_id
        $user = Auth::user();
        $userTableId = $user ? $user->user_table_id : null;

        if (empty($userTableId)) {
            Log::warning('Field visit creation denied: user_table_id is missing on authenticated user profile.', [
                'user_id' => $user ? $user->id : null,
                'email'   => $user ? $user->email : null,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Operation denied: The authenticated user profile is missing an assigned user_table_id.');
        }

        // Resolve category record from field_visit_institutes
        $catInput = $request->input('category_id');
        $categoryRecord = FieldVisitInstitute::where('category_id', $catInput)->first();
        if (!$categoryRecord) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'category_id'    => 'Please select a valid Office/Institution category.',
                    'institution_id' => 'The selected institute does not belong to the selected Office/Institution category.',
                ]);
        }
        $categoryId   = (int)$categoryRecord->category_id;
        $categoryName = $categoryRecord->category_name;

        // Determine input mode
        $isManualName = in_array($categoryId, [2, 3]) || in_array($categoryName, ['SSEPD Field Office', 'Other']);

        $rules = [
            'visit_date'                   => 'required|date',
            'start_time'                   => 'nullable|string',
            'end_time'                     => 'nullable|string',
            'field_visit_type_id'          => 'required|exists:field_visit_types,id',
            'status'                       => 'required|in:draft,submitted,reviewed,closed',
            'visit_purpose'                => 'required|string|max:255',
            'visiting_officer_name'        => 'required|string|max:255',
            'visiting_officer_designation' => 'required|string|max:255',
            'other_designation'            => 'nullable|required_if:visiting_officer_designation,Other|string|max:255',
            'institution_type'             => 'nullable|string|max:100',
            'category_id'                  => 'required|integer',
            'contact_person'               => 'nullable|string|max:150',
            'contact_number'               => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'address_type'                 => 'required|in:1,2',
            'state'                        => 'nullable|integer',
            'district'                     => 'nullable|integer',
            'district_id'                  => 'nullable|integer',
            'block'                        => 'nullable|integer',
            'block_id'                     => 'nullable|integer',
            'municipality'                 => 'nullable|integer',
            'municipality_id'              => 'nullable|integer',
            'grampanchayat'                => 'nullable|integer',
            'gp_id'                        => 'nullable|integer',
            'village'                      => 'nullable|integer',
            'village_id'                   => 'nullable|integer',
            'ward'                         => 'nullable',
            'ward_id'                      => 'nullable',
            'pin'                          => 'nullable|string|max:10',
            'institution_address'          => 'nullable|string|max:1000',
            'areas_inspected'              => 'nullable|string|max:2000',
            'key_observations'             => 'nullable|string|max:500',
            'findings'                     => 'nullable|string',
            'issues_identified'            => 'nullable|string',
            'recommendations'              => 'nullable|string',
            'corrective_actions'           => 'nullable|string',
            'is_follow_up_required'        => 'nullable|boolean',
            'follow_up_date'               => 'nullable|required_if:is_follow_up_required,1|date',
            'overall_remarks'              => 'nullable|string',
            'latitude'                     => 'nullable|numeric|between:-90,90',
            'longitude'                    => 'nullable|numeric|between:-180,180',
            'gps_accuracy'                 => 'nullable|numeric|min:0',
            'location_address'             => 'nullable|string|max:500',
            'photos'                       => 'nullable|array|max:10',
            'photos.*.image_data'          => 'required|string',
            'photos.*.caption'             => 'nullable|string|max:255',
            'documents'                    => 'nullable|array|max:5',
            'documents.*'                  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:1024',
            'document_names.*'             => 'nullable|string|max:255',
        ];

        if ($isManualName) {
            $rules['institution_name'] = 'required|string|max:255';
            $rules['institution_id']   = 'nullable';
        } else {
            $rules['institution_id']   = 'required|integer|exists:field_visit_institutes,id';
            $rules['institution_name'] = 'nullable|string|max:255';
        }

        $addressType = (int)$request->input('address_type');

        $messages = [
            'category_id.required'                  => 'Please select an Office/Institution.',
            'institution_id.required'               => 'Please select an Office / Institution Name.',
            'institution_name.required'             => 'The Office / Institution Name is required.',
            'visiting_officer_name.required'        => 'The Visiting Officer Name is required.',
            'visiting_officer_designation.required' => 'Please select a Visiting Officer Designation.',
            'other_designation.required_if'         => 'The Other Designation is required when Other is selected.',
            'key_observations.max'                  => 'Key Observations, Recommendations & Suggestions must not exceed 500 characters.',
            'contact_number.required'               => 'The Contact Number is required.',
            'contact_number.regex'                  => 'The Contact Number must contain exactly 10 numeric digits.',
            'address_type.required'                 => 'Please select Address Type.',
            'state.required'                        => 'The State is required.',
            'district.required'                     => 'The District is required.',
            'block.required'                        => 'The Block is required.',
            'municipality.required'                 => 'The Municipality is required.',
            'photos.max'                            => 'A maximum of 10 photographs can be uploaded per field visit.',
            'documents.max'                         => 'A maximum of 5 reference documents can be uploaded per field visit.',
            'documents.*.max'                       => 'Each reference document must not exceed 1 MB (1024 KB) in size.',
        ];

        $validated = $request->validate($rules, $messages);

        // Validate photograph payload size (max 1MB per individual photo, max 30MB combined)
        if ($request->has('photos') && is_array($request->input('photos'))) {
            $totalPhotosSize = 0;
            foreach ($request->input('photos') as $idx => $photoData) {
                if (!empty($photoData['image_data'])) {
                    $rawImage = $photoData['image_data'];
                    // Approximate binary size: base64 is ~4/3 of binary
                    $approxBytes = (int)(strlen($rawImage) * 0.75);
                    $totalPhotosSize += $approxBytes;

                    if ($approxBytes > 1048576) {
                        return redirect()->back()->withInput()->withErrors([
                            'photos' => 'Individual photograph #' . ($idx + 1) . ' exceeds the maximum allowed size of 1 MB.'
                        ]);
                    }
                }
            }
            if ($totalPhotosSize > 30 * 1024 * 1024) {
                return redirect()->back()->withInput()->withErrors([
                    'photos' => 'The combined size of all uploaded photographs (' . round($totalPhotosSize / 1048576, 1) . 'MB) exceeds the supported limit of 30MB. Please reduce or compress your images before uploading.'
                ]);
            }
        }

        // Validate cumulative reference documents payload size (max 30MB combined)
        if ($request->hasFile('documents')) {
            $totalDocsSize = 0;
            foreach ($request->file('documents') as $docFile) {
                if ($docFile && $docFile->isValid()) {
                    $totalDocsSize += $docFile->getSize();
                }
            }
            if ($totalDocsSize > 30 * 1024 * 1024) {
                return redirect()->back()->withInput()->withErrors([
                    'documents' => 'The combined size of all reference documents (' . round($totalDocsSize / 1048576, 1) . 'MB) exceeds the supported limit of 30MB.'
                ]);
            }
        }

        $institute       = null;
        $institutionId   = null;
        $institutionName = null;
        $orgCode         = null;

        if ($isManualName) {
            $institutionName = trim($request->input('institution_name') ?: $request->input('institution_name_manual'));
            $institutionId   = null;
            $orgCode         = null;
        } else {
            $institute = FieldVisitInstitute::where('id', $validated['institution_id'])
                ->where('category_id', $categoryId)
                ->first();

            if (!$institute) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['institution_id' => 'The selected institute does not belong to the selected Office/Institution category.']);
            }
            $institutionId   = $institute->id;
            $institutionName = $institute->institute_name;
            $orgCode         = $institute->institute_code;
        }

        // Validate required location fields based on Address Type (for all categories)
        $hasDistrict = $request->filled('district') || $request->filled('district_id') || ($institute && $institute->district_id);
        if (!$hasDistrict) {
            return redirect()->back()->withInput()->withErrors(['district' => 'The District is required.']);
        }
        if ($addressType === 1) {
            $hasBlock = $request->filled('block') || $request->filled('block_id') || ($institute && $institute->block_id);
            if (!$hasBlock) {
                return redirect()->back()->withInput()->withErrors(['block' => 'The Block is required.']);
            }
            $hasGp = $request->filled('grampanchayat') || $request->filled('gp_id') || ($institute && $institute->gp_id);
            if (!$hasGp) {
                return redirect()->back()->withInput()->withErrors(['grampanchayat' => 'The Grampanchayat is required.']);
            }
            $hasVillage = $request->filled('village') || $request->filled('village_id') || ($institute && $institute->village_id);
            if (!$hasVillage) {
                return redirect()->back()->withInput()->withErrors(['village_id' => 'The Village is required.']);
            }
        } elseif ($addressType === 2) {
            $hasMuni = $request->filled('municipality') || $request->filled('municipality_id') || ($institute && $institute->municipality_id);
            if (!$hasMuni) {
                return redirect()->back()->withInput()->withErrors(['municipality' => 'The Municipality is required.']);
            }
            $hasWard = $request->filled('ward') || $request->filled('ward_id') || ($institute && $institute->ward_id);
            if (!$hasWard) {
                return redirect()->back()->withInput()->withErrors(['ward' => 'The Ward is required.']);
            }
        }

        // Resolve geographical hierarchy
        $districtId  = $request->input('district') ?: $request->input('district_id') ?: ($institute ? $institute->district_id : null);
        $blockId     = null;
        $gpId        = null;
        $villageId   = null;
        $muniId      = null;
        $wardId      = null;

        if ($addressType === 1) {
            $blockId   = $request->input('block') ?: $request->input('block_id') ?: ($institute ? $institute->block_id : null);
            $gpId      = $request->input('grampanchayat') ?: $request->input('gp_id') ?: ($institute ? $institute->gp_id : null);
            $villageId = $request->input('village_id') ?: $request->input('village') ?: ($institute ? $institute->village_id : null);
        } elseif ($addressType === 2) {
            $muniId = $request->input('municipality') ?: $request->input('municipality_id') ?: ($institute ? $institute->municipality_id : null);
            $wardId = $request->input('ward') ?: $request->input('ward_id') ?: ($institute ? $institute->ward_id : null);
        }

        // Geographical jurisdiction authorization validation
        $userRole = (int)($user->role_id ?? ($user->roles()->exists() ? $user->roles()->first()->id : 0));
        if (!in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {
            if (in_array($userRole, [4, 6])) {
                if ($addressType !== 1 || empty($blockId) || (int)$blockId !== (int)$user->posted_block) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'block' => 'You are only authorized to record field visits within your assigned block jurisdiction.'
                        ]);
                }
            } elseif ($userRole === 5) {
                if ($addressType !== 2 || empty($muniId) || (int)$muniId !== (int)$user->posted_municipality) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'municipality' => 'You are only authorized to record field visits within your assigned municipality jurisdiction.'
                        ]);
                }
            } elseif (in_array($userRole, [8, 10])) {
                $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id')->toArray();
                $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id')->toArray();
                $isValidSubdiv = ($addressType === 1 && !empty($blockId) && in_array((int)$blockId, $blockIds))
                    || ($addressType === 2 && !empty($muniId) && in_array((int)$muniId, $municipalityIds));
                if (!$isValidSubdiv) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'address_type' => 'You are only authorized to record field visits within your assigned subdivision jurisdiction.'
                        ]);
                }
            } elseif (in_array($userRole, [9, 11])) {
                if (empty($districtId) || (int)$districtId !== (int)$user->posted_district) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'district' => 'You are only authorized to record field visits within your assigned district jurisdiction.'
                        ]);
                }
            }
        }

        try {
            DB::beginTransaction();

            // Concurrency-safe unique reference number generation
            $todayPrefix = 'FV-' . date('Ymd') . '-';
            $latestVisit = DB::table('field_visits')
                ->where('visit_reference_no', 'like', $todayPrefix . '%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $sequence = 1;
            if ($latestVisit) {
                $parts = explode('-', $latestVisit->visit_reference_no);
                $sequence = ((int)end($parts)) + 1;
            }
            $referenceNo = $todayPrefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Instantiate and save FieldVisit
            $visit = new FieldVisit();
            $visit->visit_reference_no          = $referenceNo;
            $visit->field_visit_type_id         = $validated['field_visit_type_id'];
            $visit->visit_date                  = $validated['visit_date'];
            $visit->start_time                  = !empty($validated['start_time']) ? $validated['start_time'] : now()->format('H:i:s');
            $visit->end_time                    = !empty($validated['end_time']) ? $validated['end_time'] : now()->format('H:i:s');
            $visit->status                      = $validated['status'];
            $visit->visit_purpose               = trim($validated['visit_purpose']);
            $visit->visiting_officer_name       = trim($validated['visiting_officer_name']);
            $desig = trim($validated['visiting_officer_designation']);
            $visit->visiting_officer_designation = $desig;
            $visit->other_designation           = ($desig === 'Other' && !empty($validated['other_designation'])) ? trim($validated['other_designation']) : null;
            $visit->institution_type            = $request->input('institution_type') ?: $categoryName;
            $visit->category_id                 = $categoryId;
            $visit->institution_id              = $institutionId;
            $visit->institution_name            = $institutionName;
            $visit->organization_code           = $request->input('organization_code') ?: $orgCode;
            $visit->district_id                 = !empty($districtId) ? (int)$districtId : null;
            $visit->block_id                    = !empty($blockId) ? (int)$blockId : null;
            $visit->municipality_id             = !empty($muniId) ? (int)$muniId : null;
            $visit->gp_id                       = !empty($gpId) ? (int)$gpId : null;
            $visit->village_id                  = !empty($villageId) ? (int)$villageId : null;
            $visit->ward_id                     = !empty($wardId) ? (int)$wardId : null;
            $visit->address_type                = $addressType;
            $visit->institution_address         = !empty($validated['institution_address']) ? trim($validated['institution_address']) : ($institute ? $institute->address : null);
            $visit->contact_person              = !empty($validated['contact_person']) ? trim($validated['contact_person']) : ($institute ? $institute->contact_person : null);
            $visit->contact_number              = $validated['contact_number'];
            $visit->areas_inspected             = !empty($validated['areas_inspected']) ? trim($validated['areas_inspected']) : null;
            $visit->key_observations            = !empty($validated['key_observations']) ? trim($validated['key_observations']) : null;
            $visit->findings                    = !empty($validated['findings']) ? trim($validated['findings']) : null;
            $visit->issues_identified           = !empty($validated['issues_identified']) ? trim($validated['issues_identified']) : null;
            $visit->recommendations             = !empty($validated['recommendations']) ? trim($validated['recommendations']) : null;
            $visit->corrective_actions          = !empty($validated['corrective_actions']) ? trim($validated['corrective_actions']) : null;
            $visit->is_follow_up_required       = $request->boolean('is_follow_up_required');
            $visit->follow_up_date              = $visit->is_follow_up_required ? ($validated['follow_up_date'] ?? null) : null;
            $visit->overall_remarks             = !empty($validated['overall_remarks']) ? trim($validated['overall_remarks']) : null;
            
            // Site Geolocation
            $visit->latitude                    = $validated['latitude'] ?? null;
            $visit->longitude                   = $validated['longitude'] ?? null;
            $visit->gps_accuracy                = $validated['gps_accuracy'] ?? null;
            $visit->location_captured_at        = (!empty($visit->latitude) && !empty($visit->longitude)) ? now() : null;
            $visit->location_address            = !empty($validated['location_address']) ? trim($validated['location_address']) : null;

            // Mandatory Audit Rule: store user_table_id in visiting_officer_id (represents logged-in officer)
            $visit->visiting_officer_id         = $userTableId;
            $visit->created_by                  = $userTableId;
            $visit->updated_by                  = null;
            $visit->save();

            // Process Geo-Tagged Evidence Photographs
            $this->storeEvidences($request, $visit, $userTableId);

            // Process Reference Documents
            $this->storeDocuments($request, $visit, $userTableId);

            DB::commit();

            return redirect()->route('admin.field_visits.show', $visit->id)
                ->with('success', 'Field Visit "' . $visit->visit_reference_no . '" recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Field visit save failed: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'user_id' => $userTableId,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error recording field visit: ' . $e->getMessage());
        }
    }

    /**
     * Display complete details of a specific field visit.
     */
    public function show($id): View
    {
        $visit = FieldVisit::with([
            'visitType',
            'district',
            'block',
            'municipality',
            'grampanchayat',
            'ward',
            'visitingOfficer',
            'creator',
            'updater',
            'evidences.creator',
            'documents.creator',
            'institute',
        ])->findOrFail($id);

        if (!$this->userCanAccessVisit($visit, Auth::user())) {
            abort(403, 'Unauthorized access: You do not have permission to view field visits outside your assigned geographical jurisdiction.');
        }

        return view('dashboard.field_visit.visits.show', compact('visit'));
    }

    /**
     * Show the edit form for an existing field visit.
     */
    public function edit($id): View
    {
        $user = Auth::user();
        $userRole = (int)($user->role_id ?? ($user->roles()->exists() ? $user->roles()->first()->id : 0));

        $visit = FieldVisit::with(['evidences', 'documents', 'institute'])->findOrFail($id);

        if (!$this->userCanAccessVisit($visit, $user)) {
            abort(403, 'Unauthorized access: You do not have permission to edit field visits outside your assigned geographical jurisdiction.');
        }

        $visitTypes = FieldVisitType::where('is_active', true)->orderBy('sort_order')->get();
        $districts = District::orderBy('district_name')->get();
        $designations = FieldVisit::getDesignations();

        $categories = FieldVisitInstitute::query()
            ->select('category_id', 'category_name')
            ->distinct()
            ->whereNotNull('category_id')
            ->whereNotNull('category_name')
            ->orderByRaw("CASE WHEN category_id = 3 THEN 1 ELSE 0 END ASC")
            ->orderBy('category_name', 'ASC')
            ->get()
            ->unique('category_name')
            ->values();

        $savedCategoryId = $visit->category_id ?? ($visit->institute ? $visit->institute->category_id : null);
        if (empty($savedCategoryId) && !empty($visit->institution_type)) {
            $savedCategoryId = FieldVisitInstitute::where('category_name', $visit->institution_type)->value('category_id');
        }

        $savedCategoryRecord = $savedCategoryId ? FieldVisitInstitute::where('category_id', $savedCategoryId)->first() : null;
        $savedCategoryName = $savedCategoryRecord ? $savedCategoryRecord->category_name : '';
        $isSavedManual = in_array((int)$savedCategoryId, [2, 3]) || in_array($savedCategoryName, ['SSEPD Field Office', 'Other']);

        $institutes = collect();
        if ($savedCategoryId && !$isSavedManual) {
            $institutes = FieldVisitInstitute::where('category_id', $savedCategoryId)
                ->where('is_active', true)
                ->orderBy('institute_name')
                ->get(['id', 'institute_name', 'institute_code']);
        }

        $savedAddressType = $visit->address_type;
        if (empty($savedAddressType)) {
            if (!empty($visit->block_id)) {
                $savedAddressType = 1;
            } elseif (!empty($visit->municipality_id)) {
                $savedAddressType = 2;
            } elseif ($visit->institute && $visit->institute->address_type) {
                $savedAddressType = $visit->institute->address_type;
            }
        }

        $savedDistrictId     = $visit->district_id ?? ($visit->institute ? $visit->institute->district_id : null);
        $savedStateId        = $visit->institute && $visit->institute->state_id
            ? $visit->institute->state_id
            : ($savedDistrictId ? (District::where('district_id', $savedDistrictId)->value('state_id') ?: 228) : 228);
        $savedBlockId        = $visit->block_id ?? ($visit->institute ? $visit->institute->block_id : null);
        $savedGpId           = $visit->gp_id ?? ($visit->institute ? $visit->institute->gp_id : null);
        $savedVillageId      = $visit->village_id ?? ($visit->institute ? $visit->institute->village_id : null);
        $savedMunicipalityId = $visit->municipality_id ?? ($visit->institute ? $visit->institute->municipality_id : null);
        $savedWardId         = $visit->ward_id ?? ($visit->institute ? $visit->institute->ward_id : null);

        $blocks = collect();
        $municipalities = collect();
        if ($savedDistrictId) {
            $blocks = Block::where('district_id', $savedDistrictId)->orderBy('block_name')->get();
            $municipalities = Municipality::where('district_id', $savedDistrictId)->orderBy('municipality_name')->get();
        }

        return view('dashboard.field_visit.visits.edit', compact(
            'visit',
            'visitTypes',
            'districts',
            'categories',
            'institutes',
            'savedCategoryId',
            'savedCategoryName',
            'isSavedManual',
            'savedAddressType',
            'savedStateId',
            'savedDistrictId',
            'savedBlockId',
            'savedGpId',
            'savedVillageId',
            'savedMunicipalityId',
            'savedWardId',
            'blocks',
            'municipalities',
            'designations',
            'userRole',
            'user'
        ));
    }

    /**
     * Update an existing field visit in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Mandatory Audit Rule: Verify authenticated user's user_table_id
        $user = Auth::user();
        $userTableId = $user ? $user->user_table_id : null;

        if (empty($userTableId)) {
            Log::warning('Field visit update denied: user_table_id is missing on authenticated user profile.', [
                'user_id' => $user ? $user->id : null,
                'email'   => $user ? $user->email : null,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Operation denied: The authenticated user profile is missing an assigned user_table_id.');
        }

        $visit = FieldVisit::findOrFail($id);

        if (!$this->userCanAccessVisit($visit, $user)) {
            abort(403, 'Unauthorized access: You do not have permission to modify field visits outside your assigned geographical jurisdiction.');
        }

        // Resolve category record from field_visit_institutes
        $catInput = $request->input('category_id');
        $categoryRecord = FieldVisitInstitute::where('category_id', $catInput)->first();
        if (!$categoryRecord) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'category_id'    => 'Please select a valid Office/Institution category.',
                    'institution_id' => 'The selected institute does not belong to the selected Office/Institution category.',
                ]);
        }
        $categoryId   = (int)$categoryRecord->category_id;
        $categoryName = $categoryRecord->category_name;

        // Determine input mode
        $isManualName = in_array($categoryId, [2, 3]) || in_array($categoryName, ['SSEPD Field Office', 'Other']);

        $rules = [
            'visit_date'                   => 'required|date',
            'start_time'                   => 'nullable|string',
            'end_time'                     => 'nullable|string',
            'field_visit_type_id'          => 'required|exists:field_visit_types,id',
            'status'                       => 'required|in:draft,submitted,reviewed,closed',
            'visit_purpose'                => 'required|string|max:255',
            'visiting_officer_name'        => 'required|string|max:255',
            'visiting_officer_designation' => 'required|string|max:255',
            'other_designation'            => 'nullable|required_if:visiting_officer_designation,Other|string|max:255',
            'institution_type'             => 'nullable|string|max:100',
            'category_id'                  => 'required|integer',
            'contact_person'               => 'nullable|string|max:150',
            'contact_number'               => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'address_type'                 => 'required|in:1,2',
            'state'                        => 'nullable|integer',
            'district'                     => 'nullable|integer',
            'district_id'                  => 'nullable|integer',
            'block'                        => 'nullable|integer',
            'block_id'                     => 'nullable|integer',
            'municipality'                 => 'nullable|integer',
            'municipality_id'              => 'nullable|integer',
            'grampanchayat'                => 'nullable|integer',
            'gp_id'                        => 'nullable|integer',
            'village'                      => 'nullable|integer',
            'village_id'                   => 'nullable|integer',
            'ward'                         => 'nullable',
            'ward_id'                      => 'nullable',
            'pin'                          => 'nullable|string|max:10',
            'institution_address'          => 'nullable|string|max:1000',
            'areas_inspected'              => 'nullable|string|max:2000',
            'key_observations'             => 'nullable|string|max:500',
            'findings'                     => 'nullable|string',
            'issues_identified'            => 'nullable|string',
            'recommendations'              => 'nullable|string',
            'corrective_actions'           => 'nullable|string',
            'is_follow_up_required'        => 'nullable|boolean',
            'follow_up_date'               => 'nullable|required_if:is_follow_up_required,1|date',
            'overall_remarks'              => 'nullable|string',
            'latitude'                     => 'nullable|numeric|between:-90,90',
            'longitude'                    => 'nullable|numeric|between:-180,180',
            'gps_accuracy'                 => 'nullable|numeric|min:0',
            'location_address'             => 'nullable|string|max:500',
            'remove_evidence_ids'          => 'nullable|array',
            'remove_document_ids'          => 'nullable|array',
            'photos'                       => 'nullable|array|max:10',
            'photos.*.image_data'          => 'required|string',
            'photos.*.caption'             => 'nullable|string|max:255',
            'documents'                    => 'nullable|array|max:5',
            'documents.*'                  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:1024',
            'document_names.*'             => 'nullable|string|max:255',
        ];

        if ($isManualName) {
            $rules['institution_name'] = 'required|string|max:255';
            $rules['institution_id']   = 'nullable';
        } else {
            $rules['institution_id']   = 'required|integer|exists:field_visit_institutes,id';
            $rules['institution_name'] = 'nullable|string|max:255';
        }

        $addressType = (int)$request->input('address_type');

        $messages = [
            'category_id.required'                  => 'Please select an Office/Institution.',
            'institution_id.required'               => 'Please select an Office / Institution Name.',
            'institution_name.required'             => 'The Office / Institution Name is required.',
            'visiting_officer_name.required'        => 'The Visiting Officer Name is required.',
            'visiting_officer_designation.required' => 'Please select a Visiting Officer Designation.',
            'other_designation.required_if'         => 'The Other Designation is required when Other is selected.',
            'key_observations.max'                  => 'Key Observations, Recommendations & Suggestions must not exceed 500 characters.',
            'contact_number.required'               => 'The Contact Number is required.',
            'contact_number.regex'                  => 'The Contact Number must contain exactly 10 numeric digits.',
            'address_type.required'                 => 'Please select Address Type.',
            'state.required'                        => 'The State is required.',
            'district.required'                     => 'The District is required.',
            'block.required'                        => 'The Block is required.',
            'municipality.required'                 => 'The Municipality is required.',
            'photos.max'                            => 'A maximum of 10 photographs can be uploaded per field visit.',
            'documents.max'                         => 'A maximum of 5 reference documents can be uploaded per field visit.',
            'documents.*.max'                       => 'Each reference document must not exceed 1 MB (1024 KB) in size.',
        ];

        $validated = $request->validate($rules, $messages);

        // Validate photograph payload size (max 1MB per individual photo, max 30MB combined)
        if ($request->has('photos') && is_array($request->input('photos'))) {
            $totalPhotosSize = 0;
            foreach ($request->input('photos') as $idx => $photoData) {
                if (!empty($photoData['image_data'])) {
                    $rawImage = $photoData['image_data'];
                    // Approximate binary size: base64 is ~4/3 of binary
                    $approxBytes = (int)(strlen($rawImage) * 0.75);
                    $totalPhotosSize += $approxBytes;

                    if ($approxBytes > 1048576) {
                        return redirect()->back()->withInput()->withErrors([
                            'photos' => 'Individual photograph #' . ($idx + 1) . ' exceeds the maximum allowed size of 1 MB.'
                        ]);
                    }
                }
            }
            if ($totalPhotosSize > 30 * 1024 * 1024) {
                return redirect()->back()->withInput()->withErrors([
                    'photos' => 'The combined size of all uploaded photographs (' . round($totalPhotosSize / 1048576, 1) . 'MB) exceeds the supported limit of 30MB. Please reduce or compress your images before uploading.'
                ]);
            }
        }

        // Validate cumulative reference documents payload size (max 30MB combined)
        if ($request->hasFile('documents')) {
            $totalDocsSize = 0;
            foreach ($request->file('documents') as $docFile) {
                if ($docFile && $docFile->isValid()) {
                    $totalDocsSize += $docFile->getSize();
                }
            }
            if ($totalDocsSize > 30 * 1024 * 1024) {
                return redirect()->back()->withInput()->withErrors([
                    'documents' => 'The combined size of all reference documents (' . round($totalDocsSize / 1048576, 1) . 'MB) exceeds the supported limit of 30MB.'
                ]);
            }
        }

        $institute       = null;
        $institutionId   = null;
        $institutionName = null;
        $orgCode         = null;

        if ($isManualName) {
            $institutionName = trim($request->input('institution_name') ?: $request->input('institution_name_manual'));
            $institutionId   = null;
            $orgCode         = null;
        } else {
            $institute = FieldVisitInstitute::where('id', $validated['institution_id'])
                ->where('category_id', $categoryId)
                ->first();

            if (!$institute) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['institution_id' => 'The selected institute does not belong to the selected Office/Institution category.']);
            }
            $institutionId   = $institute->id;
            $institutionName = $institute->institute_name;
            $orgCode         = $institute->institute_code;
        }

        // Validate required location fields based on Address Type (for all categories)
        $hasDistrict = $request->filled('district') || $request->filled('district_id') || ($institute && $institute->district_id) || $visit->district_id;
        if (!$hasDistrict) {
            return redirect()->back()->withInput()->withErrors(['district' => 'The District is required.']);
        }
        if ($addressType === 1) {
            $hasBlock = $request->filled('block') || $request->filled('block_id') || ($institute && $institute->block_id) || $visit->block_id;
            if (!$hasBlock) {
                return redirect()->back()->withInput()->withErrors(['block' => 'The Block is required.']);
            }
            $hasGp = $request->filled('grampanchayat') || $request->filled('gp_id') || ($institute && $institute->gp_id) || $visit->gp_id;
            if (!$hasGp) {
                return redirect()->back()->withInput()->withErrors(['grampanchayat' => 'The Grampanchayat is required.']);
            }
            $hasVillage = $request->filled('village') || $request->filled('village_id') || ($institute && $institute->village_id) || $visit->village_id;
            if (!$hasVillage) {
                return redirect()->back()->withInput()->withErrors(['village_id' => 'The Village is required.']);
            }
        } elseif ($addressType === 2) {
            $hasMuni = $request->filled('municipality') || $request->filled('municipality_id') || ($institute && $institute->municipality_id) || $visit->municipality_id;
            if (!$hasMuni) {
                return redirect()->back()->withInput()->withErrors(['municipality' => 'The Municipality is required.']);
            }
            $hasWard = $request->filled('ward') || $request->filled('ward_id') || ($institute && $institute->ward_id) || $visit->ward_id;
            if (!$hasWard) {
                return redirect()->back()->withInput()->withErrors(['ward' => 'The Ward is required.']);
            }
        }

        // Resolve geographical hierarchy
        $districtId  = $request->input('district') ?: $request->input('district_id') ?: ($institute ? $institute->district_id : null);
        $blockId     = null;
        $gpId        = null;
        $villageId   = null;
        $muniId      = null;
        $wardId      = null;

        if ($addressType === 1) {
            $blockId   = $request->input('block') ?: $request->input('block_id') ?: ($institute ? $institute->block_id : null);
            $gpId      = $request->input('grampanchayat') ?: $request->input('gp_id') ?: ($institute ? $institute->gp_id : null);
            $villageId = $request->input('village_id') ?: $request->input('village') ?: ($institute ? $institute->village_id : null);
        } elseif ($addressType === 2) {
            $muniId = $request->input('municipality') ?: $request->input('municipality_id') ?: ($institute ? $institute->municipality_id : null);
            $wardId = $request->input('ward') ?: $request->input('ward_id') ?: ($institute ? $institute->ward_id : null);
        }

        // Geographical jurisdiction authorization validation
        $userRole = (int)($user->role_id ?? ($user->roles()->exists() ? $user->roles()->first()->id : 0));
        if (!in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {
            if (in_array($userRole, [4, 6])) {
                if ($addressType !== 1 || empty($blockId) || (int)$blockId !== (int)$user->posted_block) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'block' => 'You are only authorized to record field visits within your assigned block jurisdiction.'
                        ]);
                }
            } elseif ($userRole === 5) {
                if ($addressType !== 2 || empty($muniId) || (int)$muniId !== (int)$user->posted_municipality) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'municipality' => 'You are only authorized to record field visits within your assigned municipality jurisdiction.'
                        ]);
                }
            } elseif (in_array($userRole, [8, 10])) {
                $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id')->toArray();
                $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id')->toArray();
                $isValidSubdiv = ($addressType === 1 && !empty($blockId) && in_array((int)$blockId, $blockIds))
                    || ($addressType === 2 && !empty($muniId) && in_array((int)$muniId, $municipalityIds));
                if (!$isValidSubdiv) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'address_type' => 'You are only authorized to record field visits within your assigned subdivision jurisdiction.'
                        ]);
                }
            } elseif (in_array($userRole, [9, 11])) {
                if (empty($districtId) || (int)$districtId !== (int)$user->posted_district) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'district' => 'You are only authorized to record field visits within your assigned district jurisdiction.'
                        ]);
                }
            }
        }

        try {
            DB::beginTransaction();

            $visit->field_visit_type_id   = $validated['field_visit_type_id'];
            $visit->visit_date            = $validated['visit_date'];

            // Preserve historical timestamps if start_time / end_time are omitted or empty
            if ($request->filled('start_time')) {
                $visit->start_time = $request->input('start_time');
            } elseif (empty($visit->start_time)) {
                $visit->start_time = now()->format('H:i:s');
            }

            if ($request->filled('end_time')) {
                $visit->end_time = $request->input('end_time');
            } elseif (empty($visit->end_time)) {
                $visit->end_time = now()->format('H:i:s');
            }

            $visit->status                = $validated['status'];
            $visit->visit_purpose         = trim($validated['visit_purpose']);
            $visit->visiting_officer_name = trim($validated['visiting_officer_name']);
            $desig = trim($validated['visiting_officer_designation']);
            $visit->visiting_officer_designation = $desig;
            $visit->other_designation     = ($desig === 'Other' && !empty($validated['other_designation'])) ? trim($validated['other_designation']) : null;
            $visit->institution_type      = $request->input('institution_type') ?: $categoryName;
            $visit->category_id           = $categoryId;
            $visit->institution_id        = $institutionId;
            $visit->institution_name      = $institutionName;
            $visit->organization_code     = $request->input('organization_code') ?: $orgCode;
            $visit->district_id           = !empty($districtId) ? (int)$districtId : null;
            $visit->block_id              = !empty($blockId) ? (int)$blockId : null;
            $visit->municipality_id       = !empty($muniId) ? (int)$muniId : null;
            $visit->gp_id                 = !empty($gpId) ? (int)$gpId : null;
            $visit->village_id            = !empty($villageId) ? (int)$villageId : null;
            $visit->ward_id               = !empty($wardId) ? (int)$wardId : null;
            $visit->address_type          = $addressType;
            $visit->institution_address   = !empty($validated['institution_address']) ? trim($validated['institution_address']) : ($institute ? $institute->address : null);
            $visit->contact_person        = !empty($validated['contact_person']) ? trim($validated['contact_person']) : ($institute ? $institute->contact_person : null);
            $visit->contact_number        = $validated['contact_number'];

            // Preserve the 6 hidden fields if not sent or empty
            if ($request->has('areas_inspected') && $request->input('areas_inspected') !== null) {
                $visit->areas_inspected = trim($request->input('areas_inspected'));
            }
            $visit->key_observations      = !empty($validated['key_observations']) ? trim($validated['key_observations']) : null;
            if ($request->has('findings') && $request->input('findings') !== null) {
                $visit->findings = trim($request->input('findings'));
            }
            if ($request->has('issues_identified') && $request->input('issues_identified') !== null) {
                $visit->issues_identified = trim($request->input('issues_identified'));
            }
            if ($request->has('recommendations') && $request->input('recommendations') !== null) {
                $visit->recommendations = trim($request->input('recommendations'));
            }
            if ($request->has('corrective_actions') && $request->input('corrective_actions') !== null) {
                $visit->corrective_actions = trim($request->input('corrective_actions'));
            }
            $visit->is_follow_up_required = $request->boolean('is_follow_up_required');
            $visit->follow_up_date        = $visit->is_follow_up_required ? ($validated['follow_up_date'] ?? null) : null;
            if ($request->has('overall_remarks') && $request->input('overall_remarks') !== null) {
                $visit->overall_remarks = trim($request->input('overall_remarks'));
            }

            if ($request->filled('latitude') && $request->filled('longitude')) {
                $visit->latitude             = $validated['latitude'];
                $visit->longitude            = $validated['longitude'];
                $visit->gps_accuracy         = $validated['gps_accuracy'] ?? null;
                $visit->location_captured_at = now();
                $visit->location_address     = !empty($validated['location_address']) ? trim($validated['location_address']) : null;
            }

            // Mandatory Audit Rule: store user_table_id in updated_by
            $visit->updated_by = $userTableId;
            $visit->save();

            // Handle removal of selected existing evidences
            if ($request->has('remove_evidence_ids')) {
                $toRemove = FieldVisitEvidence::where('field_visit_id', $visit->id)
                    ->whereIn('id', $request->input('remove_evidence_ids'))
                    ->get();
                foreach ($toRemove as $ev) {
                    $this->deleteStoredFile($ev->photo_path);
                    $ev->delete();
                }
            }

            // Handle removal of selected existing documents
            if ($request->has('remove_document_ids')) {
                $toRemoveDocs = FieldVisitDocument::where('field_visit_id', $visit->id)
                    ->whereIn('id', $request->input('remove_document_ids'))
                    ->get();
                foreach ($toRemoveDocs as $doc) {
                    $this->deleteStoredFile($doc->file_path);
                    $doc->delete();
                }
            }

            // Append newly captured evidences
            $this->storeEvidences($request, $visit, $userTableId);

            // Append newly uploaded reference documents
            $this->storeDocuments($request, $visit, $userTableId);

            DB::commit();

            return redirect()->route('admin.field_visits.show', $visit->id)
                ->with('success', 'Field Visit "' . $visit->visit_reference_no . '" updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Field visit update failed: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'user_id' => $userTableId,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating field visit: ' . $e->getMessage());
        }
    }

    /**
     * Soft-delete a field visit.
     */
    public function destroy($id): RedirectResponse
    {
        $user = Auth::user();
        $userTableId = $user ? $user->user_table_id : null;

        $visit = FieldVisit::findOrFail($id);

        if (!$this->userCanAccessVisit($visit, $user)) {
            abort(403, 'Unauthorized access: You do not have permission to delete field visits outside your assigned geographical jurisdiction.');
        }

        $visit->deleted_by = $userTableId;
        $visit->save();
        $visit->delete();

        return redirect()->route('admin.field_visits.index')
            ->with('success', 'Field Visit "' . $visit->visit_reference_no . '" archived successfully.');
    }

    /**
     * View an evidence photograph through an access-controlled streaming response.
     */
    public function viewEvidence($id): BinaryFileResponse
    {
        $evidence = FieldVisitEvidence::with('fieldVisit')->findOrFail($id);

        if ($evidence->fieldVisit && !$this->userCanAccessVisit($evidence->fieldVisit, Auth::user())) {
            abort(403, 'Unauthorized access: You do not have permission to view evidence files outside your assigned geographical jurisdiction.');
        }

        $fullPath = storage_path('app/' . $evidence->photo_path);

        if (!file_exists($fullPath)) {
            abort(404, 'Evidence photograph file not found on server.');
        }

        return response()->file($fullPath, [
            'Content-Type'        => $evidence->mime_type ?? 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . $evidence->original_filename . '"',
            'Cache-Control'       => 'private, max-age=3600',
        ]);
    }

    /**
     * Download a reference document through an authorized route.
     */
    public function downloadDocument($id): BinaryFileResponse
    {
        $document = FieldVisitDocument::with('fieldVisit')->findOrFail($id);

        if ($document->fieldVisit && !$this->userCanAccessVisit($document->fieldVisit, Auth::user())) {
            abort(403, 'Unauthorized access: You do not have permission to download documents outside your assigned geographical jurisdiction.');
        }

        $fullPath = storage_path('app/' . $document->file_path);

        if (!file_exists($fullPath)) {
            abort(404, 'Reference document file not found on server.');
        }

        return response()->download($fullPath, $document->original_filename, [
            'Content-Type'  => $document->mime_type,
            'Cache-Control' => 'private, no-cache',
        ]);
    }

    /**
     * Export filtered field visits list to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = FieldVisit::with(['visitType', 'district', 'block', 'visitingOfficer'])
            ->orderBy('visit_date', 'desc');

        // Apply geographical role-based restrictions
        $this->applyGeographicalScope($query, Auth::user());

        if ($request->filled('search')) {
            $query->search(trim($request->input('search')));
        }
        if ($request->filled('field_visit_type_id')) {
            $query->where('field_visit_type_id', $request->input('field_visit_type_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('visit_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('visit_date', '<=', $request->input('date_to'));
        }

        $visits = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="field_visits_' . date('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($visits) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Visit Ref No',
                'Visit Date',
                'Visit Type',
                'Status',
                'Loggedin Officer',
                'Visiting Officer Name',
                'Visiting Officer Designation',
                'Institution Type',
                'Institution Name',
                'District',
                'Block',
                'Latitude',
                'Longitude',
                'GPS Accuracy (m)',
                'Follow-up Required',
                'Follow-up Date',
                'Created At',
            ]);

            foreach ($visits as $v) {
                $desig = $v->visiting_officer_designation === 'Other' && !empty($v->other_designation)
                    ? $v->other_designation
                    : $v->visiting_officer_designation;

                fputcsv($handle, [
                    $v->visit_reference_no,
                    $v->visit_date ? $v->visit_date->format('Y-m-d') : '',
                    $v->visitType ? $v->visitType->name : '',
                    strtoupper($v->status),
                    $v->visitingOfficer ? $v->visitingOfficer->name : ('ID #' . $v->visiting_officer_id),
                    $v->visiting_officer_name ?: '',
                    $desig ?: '',
                    $v->institution_type,
                    $v->institution_name,
                    $v->district ? $v->district->district_name : '',
                    $v->block ? $v->block->block_name : '',
                    $v->latitude,
                    $v->longitude,
                    $v->gps_accuracy,
                    $v->is_follow_up_required ? 'YES' : 'NO',
                    $v->follow_up_date ? $v->follow_up_date->format('Y-m-d') : '',
                    $v->created_at ? $v->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * AJAX endpoint to fetch institutions based on selected category and district.
     */
    public function getInstitutions(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $categoryId = $request->input('category_id');
        $districtId = $request->input('district_id');

        $institutions = [];

        if (!empty($categoryId)) {
            $q = FieldVisitInstitute::query()->where('is_active', true)->where('category_id', $categoryId);
            if (!empty($districtId)) {
                $q->where('district_id', $districtId);
            }
            $institutions = $q->orderBy('institute_name')->get([
                'id',
                'institute_name as name',
                'institute_code as code',
                'category_id',
                'category_name',
                'address_type',
                'state_id',
                'district_id',
                'district_name',
                'block_id',
                'block_name',
                'gp_id',
                'gp_name',
                'village_id',
                'village_name',
                'municipality_id',
                'municipality_name',
                'ward_id',
                'ward_name',
                'address',
                'pin_code',
                'contact_person',
                'contact_number as contact',
                'email'
            ]);
        } elseif ($type === 'SPECIAL_SCHOOL') {
            $q = SpecialSchool::query()->where('status', 1);
            if (!empty($districtId)) {
                $q->where('district_id', $districtId);
            }
            $institutions = $q->orderBy('special_school_name')->get(['id', 'special_school_name as name', 'school_system_gen_reg_no as code', 'district_id', 'block_id', 'municipality_id', 'gp_id', 'ward_id', 'school_postal_address_at as address', 'school_mobile_no as contact']);
        } elseif ($type === 'DDRC') {
            $q = DdrcDetails::query()->where('status', 1);
            if (!empty($districtId)) {
                $q->where('district_id', $districtId);
            }
            $institutions = $q->orderBy('ddrc_name')->get(['id', 'ddrc_name as name', 'ddrc_system_gen_reg_no as code', 'district_id', 'block_id', 'municipality_id', 'gp_id', 'ward_id', 'ddrc_postal_address_at as address']);
        } elseif ($type === 'PIA_INSTITUTE') {
            $q = PiaInstituteMaster::query();
            if (!empty($districtId)) {
                $q->where('district_id', $districtId);
            }
            $institutions = $q->orderBy('institute_name')->get(['id', 'institute_name as name', 'institute_system_gen_reg_no as code', 'district_id', 'block_id', 'municipality_id', 'gp_id', 'ward_id', 'institute_address as address', 'nodal_officer_name as contact_person', 'nodal_officer_contact_number as contact']);
        } elseif ($type === 'INSTITUTE_MASTER' || empty($type)) {
            $q = FieldVisitInstitute::query()->where('is_active', true);
            if (!empty($districtId)) {
                $q->where('district_id', $districtId);
            }
            $institutions = $q->orderBy('institute_name')->get([
                'id',
                'institute_name as name',
                'institute_code as code',
                'category_id',
                'category_name',
                'address_type',
                'state_id',
                'district_id',
                'district_name',
                'block_id',
                'block_name',
                'gp_id',
                'gp_name',
                'village_id',
                'village_name',
                'municipality_id',
                'municipality_name',
                'ward_id',
                'ward_name',
                'address',
                'pin_code',
                'contact_person',
                'contact_number as contact',
                'email'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $institutions,
        ]);
    }

    /**
     * Helper to store geotagged evidence photographs.
     */
    protected function storeEvidences(Request $request, FieldVisit $visit, int $userTableId): void
    {
        if (!$request->has('photos') || !is_array($request->input('photos'))) {
            return;
        }

        $baseDir = 'field_visits/evidence/' . $visit->visit_reference_no;

        foreach ($request->input('photos') as $photoData) {
            if (empty($photoData['image_data'])) {
                continue;
            }

            $rawImageString = $photoData['image_data'];
            $binaryData = null;
            $extension = 'jpg';
            $mimeType = 'image/jpeg';

            // Check if input is base64 data URL from camera
            if (preg_match('/^data:image\/(\w+);base64,/', $rawImageString, $typeMatch)) {
                $extension = strtolower($typeMatch[1]);
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }
                $binaryData = base64_decode(substr($rawImageString, strpos($rawImageString, ',') + 1));
                $mimeType = 'image/' . ($extension === 'jpg' ? 'jpeg' : $extension);
            } else {
                $binaryData = base64_decode($rawImageString);
            }

            if (!$binaryData) {
                continue;
            }

            // Calculate SHA-256 checksum from original bytes
            $checksum = hash('sha256', $binaryData);
            $fileSize = strlen($binaryData);
            $filename = 'EVIDENCE_' . date('Ymd_His') . '_' . Str::random(12) . '.' . $extension;
            $relativePath = $baseDir . '/' . $filename;

            // Save binary to private application storage
            Storage::disk('local')->put($relativePath, $binaryData);

            // Create evidence record
            $evidence = new FieldVisitEvidence();
            $evidence->field_visit_id        = $visit->id;
            $evidence->photo_path            = $relativePath;
            $evidence->original_filename     = $filename;
            $evidence->file_size             = $fileSize;
            $evidence->mime_type             = $mimeType;
            $evidence->file_checksum         = $checksum;
            $evidence->latitude              = !empty($photoData['latitude']) ? (float)$photoData['latitude'] : ($visit->latitude ?? 0);
            $evidence->longitude             = !empty($photoData['longitude']) ? (float)$photoData['longitude'] : ($visit->longitude ?? 0);
            $evidence->gps_accuracy          = !empty($photoData['gps_accuracy']) ? (float)$photoData['gps_accuracy'] : ($visit->gps_accuracy ?? null);
            $evidence->client_capture_time   = !empty($photoData['capture_time']) ? Carbon::parse($photoData['capture_time']) : now();
            $evidence->location_reading_time = now();
            $evidence->capture_source        = !empty($photoData['capture_source']) ? $photoData['capture_source'] : 'DEVICE_CAMERA';
            $evidence->caption               = !empty($photoData['caption']) ? trim($photoData['caption']) : null;
            $evidence->created_by            = $userTableId;
            $evidence->updated_by            = null;
            $evidence->save();
        }
    }

    /**
     * Helper to store reference documents.
     */
    protected function storeDocuments(Request $request, FieldVisit $visit, int $userTableId): void
    {
        if (!$request->hasFile('documents')) {
            return;
        }

        $baseDir = 'field_visits/documents/' . $visit->visit_reference_no;
        $documentNames = $request->input('document_names', []);

        foreach ($request->file('documents') as $index => $file) {
            if (!$file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            // Calculate SHA-256 hash of original file bytes
            $checksum = hash_file('sha256', $file->getRealPath());

            // Safe server filename
            $safeName = 'DOC_' . date('Ymd_His') . '_' . Str::random(12) . '.' . $extension;
            $relativePath = $baseDir . '/' . $safeName;

            // Store in private storage
            Storage::disk('local')->putFileAs($baseDir, $file, $safeName);

            $label = !empty($documentNames[$index]) ? trim($documentNames[$index]) : pathinfo($originalName, PATHINFO_FILENAME);

            $document = new FieldVisitDocument();
            $document->field_visit_id    = $visit->id;
            $document->document_name     = $label;
            $document->file_path         = $relativePath;
            $document->original_filename = $originalName;
            $document->file_size         = $fileSize;
            $document->mime_type         = $mimeType;
            $document->file_checksum     = $checksum;
            $document->created_by        = $userTableId;
            $document->updated_by        = null;
            $document->save();
        }
    }

    /**
     * Helper to remove physical file safely.
     */
    protected function deleteStoredFile(?string $relativePath): void
    {
        if (!empty($relativePath) && Storage::disk('local')->exists($relativePath)) {
            Storage::disk('local')->delete($relativePath);
        }
    }

    /**
     * Apply role-based geographical restrictions to a query.
     */
    protected function applyGeographicalScope($query, $user): void
    {
        if (!$user) {
            $query->whereRaw('1 = 0');
            return;
        }

        $userRole = (int)($user->role_id ?? ($user->roles()->exists() ? $user->roles()->first()->id : 0));

        // Unrestricted roles: 1, 2, 12, 13, 14, 15, 25
        if (in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {
            return;
        }

        // Roles 4, 6: Only user's posted_block (Rural Block jurisdiction)
        if (in_array($userRole, [4, 6])) {
            if (!empty($user->posted_block)) {
                $query->where(function ($q) use ($user) {
                    $q->where('field_visits.block_id', $user->posted_block)
                      ->orWhereHas('institute', function ($iq) use ($user) {
                          $iq->where('block_id', $user->posted_block);
                      });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // Role 5: Only user's posted_municipality (Urban ULB jurisdiction)
        elseif ($userRole === 5) {
            if (!empty($user->posted_municipality)) {
                $query->where(function ($q) use ($user) {
                    $q->where('field_visits.municipality_id', $user->posted_municipality)
                      ->orWhereHas('institute', function ($iq) use ($user) {
                          $iq->where('municipality_id', $user->posted_municipality);
                      });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // Roles 8, 10: Blocks & municipalities under user's posted_subdiv
        elseif (in_array($userRole, [8, 10])) {
            if (!empty($user->posted_subdiv)) {
                $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id')->toArray();
                $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id')->toArray();
                $query->where(function ($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('field_visits.block_id', $blockIds)
                      ->orWhereIn('field_visits.municipality_id', $municipalityIds)
                      ->orWhereHas('institute', function ($iq) use ($blockIds, $municipalityIds) {
                          $iq->whereIn('block_id', $blockIds)
                             ->orWhereIn('municipality_id', $municipalityIds);
                      });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // Roles 9, 11: Only user's posted_district
        elseif (in_array($userRole, [9, 11])) {
            if (!empty($user->posted_district)) {
                $query->where(function ($q) use ($user) {
                    $q->where('field_visits.district_id', $user->posted_district)
                      ->orWhereHas('institute', function ($iq) use ($user) {
                          $iq->where('district_id', $user->posted_district);
                      });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // Fallback for any other restricted role: view only self-created or visiting officer visits
        else {
            $userTableId = $user->user_table_id;
            if (!empty($userTableId)) {
                $query->where(function ($q) use ($userTableId) {
                    $q->where('field_visits.visiting_officer_id', $userTableId)
                      ->orWhere('field_visits.created_by', $userTableId);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }
    }

    /**
     * Check if authenticated user has geographical permission to access a specific field visit.
     */
    protected function userCanAccessVisit(FieldVisit $visit, $user): bool
    {
        if (!$user) {
            return false;
        }

        $userRole = (int)($user->role_id ?? ($user->roles()->exists() ? $user->roles()->first()->id : 0));

        // Unrestricted roles: 1, 2, 12, 13, 14, 15, 25
        if (in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {
            return true;
        }

        // Resolve effective location hierarchy: direct field visit column takes precedence,
        // with fallback to the linked institute's geographical values.
        $effectiveBlockId = $visit->block_id ?: ($visit->institute ? $visit->institute->block_id : null);
        $effectiveMunicipalityId = $visit->municipality_id ?: ($visit->institute ? $visit->institute->municipality_id : null);
        $effectiveDistrictId = $visit->district_id ?: ($visit->institute ? $visit->institute->district_id : null);

        // Roles 4, 6: Only user's posted_block (Rural)
        if (in_array($userRole, [4, 6])) {
            return !empty($user->posted_block) && !empty($effectiveBlockId) && (int)$effectiveBlockId === (int)$user->posted_block;
        }

        // Role 5: Only user's posted_municipality (Urban)
        if ($userRole === 5) {
            return !empty($user->posted_municipality) && !empty($effectiveMunicipalityId) && (int)$effectiveMunicipalityId === (int)$user->posted_municipality;
        }

        // Roles 8, 10: Blocks & municipalities under user's posted_subdiv
        if (in_array($userRole, [8, 10])) {
            if (empty($user->posted_subdiv)) {
                return false;
            }
            $blockIds = Block::where('subdivision_id', $user->posted_subdiv)->pluck('block_id')->toArray();
            $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id')->toArray();

            $matchBlock = !empty($effectiveBlockId) && in_array((int)$effectiveBlockId, $blockIds);
            $matchMuni  = !empty($effectiveMunicipalityId) && in_array((int)$effectiveMunicipalityId, $municipalityIds);

            return $matchBlock || $matchMuni;
        }

        // Roles 9, 11: Only user's posted_district
        if (in_array($userRole, [9, 11])) {
            return !empty($user->posted_district) && !empty($effectiveDistrictId) && (int)$effectiveDistrictId === (int)$user->posted_district;
        }

        // Fallback for any other restricted role: check if visiting officer or creator
        $userTableId = $user->user_table_id;
        if (!empty($userTableId)) {
            return (int)$visit->visiting_officer_id === (int)$userTableId || (int)$visit->created_by === (int)$userTableId;
        }

        return false;
    }
}
