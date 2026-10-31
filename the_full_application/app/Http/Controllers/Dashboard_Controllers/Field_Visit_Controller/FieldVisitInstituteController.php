<?php

namespace App\Http\Controllers\Dashboard_Controllers\Field_Visit_Controller;

use App\Http\Controllers\Controller;
use App\Models\FieldVisitInstitute;
use App\Models\State;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\Grampanchayat;
use App\Models\Village;
use App\Models\WardMaster;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Exception;

class FieldVisitInstituteController extends Controller
{
    /**
     * Standard institutional categories mapped from official master data.
     */
    protected array $standardCategories = [
        1  => 'Special School',
        2  => 'Old Age Home',
        3  => 'Therapeutic & Rehabilitation Centre',
        5  => 'SIDR',
        6  => 'DDRC',
        7  => 'SIEP',
        8  => 'Advanced Rehabilitation Center',
        9  => 'Beggar Rehabilitation Centre',
        10 => 'DISHAA Recovery Centre',
        11 => 'Geriatric Disability Center',
    ];

    /**
     * Set up permissions and access control for Institute Master.
     */
    public function __construct()
    {
        $this->middleware(
            'permission:field-visit-institute-access|field-visit-institute-list|field-visit-institute-show|field-visit-institute-create|field-visit-institute-edit|field-visit-institute-delete',
            ['only' => ['index', 'show', 'getLocations']]
        );
        $this->middleware('permission:field-visit-institute-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:field-visit-institute-edit', ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware('permission:field-visit-institute-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the institutes.
     * Supports both Yajra DataTables AJAX and Blade server pagination.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = FieldVisitInstitute::with(['state', 'district', 'block', 'municipality', 'creator'])
                ->select('field_visit_institutes.*');

            // Apply Filters
            if ($request->filled('district_id')) {
                $query->where('district_id', $request->input('district_id'));
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            if ($request->filled('which_govt')) {
                $query->where('which_govt', $request->input('which_govt'));
            }

            if ($request->filled('status')) {
                $status = $request->input('status');
                if ($status === '1' || $status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === '0' || $status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('institute_code', function ($row) {
                    return '<span class="badge bg-primary font-monospace">' . e($row->institute_code) . '</span>';
                })
                ->editColumn('institute_name', function ($row) {
                    $html = '<strong>' . e($row->institute_name) . '</strong>';
                    if (!empty($row->management_name)) {
                        $html .= '<br><small class="text-muted"><i class="fas fa-building me-1"></i> ' . e(Str::limit($row->management_name, 45)) . '</small>';
                    }
                    return $html;
                })
                ->editColumn('category_name', function ($row) {
                    return '<span class="badge bg-info text-dark">' . e($row->category_name) . '</span>';
                })
                ->editColumn('district_name', function ($row) {
                    $location = e($row->district_name ?? 'N/A');
                    if ($row->address_type == 1) {
                        $location .= ' <span class="badge bg-light text-success border">Rural</span>';
                    } elseif ($row->address_type == 2) {
                        $location .= ' <span class="badge bg-light text-primary border">Urban</span>';
                    }
                    if (!empty($row->block_name)) {
                        $location .= '<br><small class="text-muted">' . e($row->block_name) . ' (Block)</small>';
                        if (!empty($row->village_name)) {
                            $location .= '<br><small class="text-muted"><i class="fas fa-home me-1"></i> ' . e($row->village_name) . ' (Village)</small>';
                        }
                    } elseif (!empty($row->municipality_name)) {
                        $location .= '<br><small class="text-muted">' . e($row->municipality_name) . ' (ULB)</small>';
                    }
                    return $location;
                })
                ->editColumn('which_govt', function ($row) {
                    return $row->which_govt == 2
                        ? '<span class="badge bg-secondary">Govt. of India</span>'
                        : '<span class="badge bg-success">Govt. of Odisha</span>';
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>'
                        : '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>';
                    $actions .= '<div class="dropdown-menu dropdown-menu-end">';

                    if (auth()->user()->can('field-visit-institute-show')) {
                        $actions .= '<a class="dropdown-item" href="' . route('admin.field_visit_institutes.show', $row->id) . '"><i class="fas fa-eye text-info me-2"></i> View</a>';
                    }

                    if (auth()->user()->can('field-visit-institute-edit')) {
                        $actions .= '<a class="dropdown-item" href="' . route('admin.field_visit_institutes.edit', $row->id) . '"><i class="fas fa-edit text-warning me-2"></i> Edit</a>';
                    }

                    if (auth()->user()->can('field-visit-institute-delete')) {
                        $actions .= '<a class="dropdown-item text-danger" href="' . route('admin.field_visit_institutes.destroy', $row->id) . '" id="delete"><i class="fas fa-trash-alt text-danger me-2"></i> Delete</a>';
                    }

                    $actions .= '</div></div>';
                    return $actions;
                })
                ->rawColumns(['institute_code', 'institute_name', 'category_name', 'district_name', 'which_govt', 'is_active', 'action'])
                ->make(true);
        }

        // Standard server-rendered fallback query
        $query = FieldVisitInstitute::with(['district', 'block', 'municipality', 'creator']);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('district_id')) {
            $query->filterByDistrict($request->input('district_id'));
        }

        if ($request->filled('category_id')) {
            $query->filterByCategory($request->input('category_id'));
        }

        if ($request->filled('which_govt')) {
            $query->filterByGovt($request->input('which_govt'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === '1' || $status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === '0' || $status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $institutes = $query->orderBy('district_name', 'asc')
            ->orderBy('institute_name', 'asc')
            ->paginate(15)
            ->withQueryString();

        $districts  = District::orderBy('district_name', 'asc')->get();
        $categories = $this->getCategoriesList();

        return view('dashboard.field_visit.institutes.index', compact('institutes', 'districts', 'categories'));
    }

    /**
     * Show the form for creating a new institute.
     */
    public function create(): View
    {
        $states = State::orderBy('state_name', 'asc')->get();
        $districts = District::orderBy('district_name', 'asc')->get();
        $categories = $this->getCategoriesList();

        // Calculate next auto-generated code suggestion
        $maxId = FieldVisitInstitute::max('id') ?? 0;
        $suggestedCode = 'INST-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

        return view('dashboard.field_visit.institutes.create', compact('states', 'districts', 'categories', 'suggestedCode'));
    }

    /**
     * Store a newly created institute in database.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Mandatory Audit Rule: Resolve authenticated officer's user_table_id
        $user = Auth::user();
        $userTableId = $user ? $user->user_table_id : null;

        if (empty($userTableId)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Operational audit failure: Authenticated officer record does not possess a valid user_table_id business identifier.');
        }

        // 2. Build dynamic or legacy validation rules
        $validationRules = [
            'institute_code'  => 'required|string|max:50|unique:field_visit_institutes,institute_code|regex:/^[A-Z0-9\-_]+$/i',
            'institute_name'  => 'required|string|max:255',
            'category_id'     => 'required|integer',
            'management_name' => 'nullable|string|max:255',
            'which_govt'      => 'required|in:1,2',
            'is_active'       => 'required|in:0,1',
            'contact_person'  => 'nullable|string|max:150',
            'contact_number'  => 'nullable|string|max:25',
            'email'           => 'nullable|email|max:150',
            'remarks'         => 'nullable|string',
        ];

        $addressType = $request->input('address_type') ?: $request->input('ngo_address_type');

        if ($addressType === '1') {
            // Rural: State -> District -> Block -> GP -> Village + PIN (Postal Address UI removed)
            $validationRules = array_merge($validationRules, [
                'address_type'                => 'nullable|in:1,2',
                'ngo_address_type'            => 'nullable|in:1,2',
                'state'                       => 'required|integer|exists:states,state_id',
                'district'                    => 'required|integer|exists:districts,district_id',
                'block'                       => 'required|integer|exists:blocks,block_id',
                'grampanchayat'               => 'required|integer|exists:grampanchayats,gp_id',
                'village'                     => 'required_without:village_id|nullable|integer|exists:villages,village_id',
                'village_id'                  => 'required_without:village|nullable|integer|exists:villages,village_id',
                'pin'                         => 'required|string|max:10|regex:/^[0-9]{6}$/',
                'ngo_postal_address_at'       => 'nullable|string|max:255',
                'ngo_postal_address_post'     => 'nullable|string|max:255',
                'ngo_postal_address_via'      => 'nullable|string|max:255',
                'ngo_postal_address_ps'       => 'nullable|string|max:255',
                'ngo_postal_address_district' => 'nullable|string|max:255',
                'ngo_postal_address_pin'      => 'nullable|string|max:10',
            ]);
        } elseif ($addressType === '2') {
            // Urban: State -> District -> Municipality -> Ward + PIN (Postal Address UI removed)
            $validationRules = array_merge($validationRules, [
                'address_type'                => 'nullable|in:1,2',
                'ngo_address_type'            => 'nullable|in:1,2',
                'state'                       => 'required|integer|exists:states,state_id',
                'district'                    => 'required|integer|exists:districts,district_id',
                'municipality'                => 'required|integer|exists:municipalities,municipality_id',
                'ward'                        => 'required',
                'pin'                         => 'required|string|max:10|regex:/^[0-9]{6}$/',
                'ngo_postal_address_at'       => 'nullable|string|max:255',
                'ngo_postal_address_post'     => 'nullable|string|max:255',
                'ngo_postal_address_via'      => 'nullable|string|max:255',
                'ngo_postal_address_ps'       => 'nullable|string|max:255',
                'ngo_postal_address_district' => 'nullable|string|max:255',
                'ngo_postal_address_pin'      => 'nullable|string|max:10',
            ]);
        } else {
            // Legacy / Direct input fallback
            $validationRules = array_merge($validationRules, [
                'address_type'    => 'nullable|in:1,2',
                'ngo_address_type'=> 'nullable|in:1,2',
                'state_id'        => 'nullable|integer|exists:states,state_id',
                'district_id'     => 'required|integer|exists:districts,district_id',
                'block_id'        => 'nullable|integer',
                'municipality_id' => 'nullable|integer',
                'gp_name'         => 'nullable|string|max:100',
                'ward_name'       => 'nullable|string|max:100',
                'address'         => 'nullable|string',
                'pin_code'        => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            ]);
        }

        $validated = $request->validate($validationRules, [
            'institute_code.regex'         => 'The institute code may only contain letters, numbers, hyphens, and underscores.',
            'pin.regex'                    => 'The Geographic PIN must be a valid 6-digit postal code.',
            'ngo_postal_address_pin.regex' => 'The Postal PIN code must be a valid 6-digit postal code.',
            'pin_code.regex'               => 'The PIN code must be a valid 6-digit postal code.',
            'state.exists'                 => 'The selected state does not exist in the official master.',
            'district.exists'              => 'The selected district does not exist in the official master.',
            'district_id.exists'           => 'The selected district does not exist in the official master.',
            'block.exists'                 => 'The selected block does not exist in the official master.',
            'grampanchayat.exists'         => 'The selected gram panchayat does not exist in the official master.',
            'village.required_without'     => 'Please select Village.',
            'village_id.required_without'  => 'Please select Village.',
            'village.exists'               => 'The selected village does not exist in the official master.',
            'village_id.exists'            => 'The selected village does not exist in the official master.',
            'municipality.exists'          => 'The selected municipality does not exist in the official master.',
        ]);

        // Resolve category name from list or DB
        $categoryName = $this->standardCategories[$validated['category_id']] 
            ?? FieldVisitInstitute::where('category_id', $validated['category_id'])->value('category_name') 
            ?? 'General Welfare Institute';

        // Resolve district name
        $districtId = $request->input('district') ?? $request->input('district_id');
        $district = District::where('district_id', $districtId)->first();
        $districtName = $district ? $district->district_name : null;

        // Resolve state_id
        $stateId = $request->input('state') ?: $request->input('state_id');
        if (empty($stateId) && !empty($districtId)) {
            $stateId = District::where('district_id', $districtId)->value('state_id') ?? 228;
        } elseif (empty($stateId)) {
            $stateId = 228;
        }

        $blockId = null;
        $blockName = null;
        $muniId = null;
        $muniName = null;
        $gpId = null;
        $gpName = null;
        $villageId = null;
        $villageName = null;
        $wardId = null;
        $wardName = null;
        $pinCode = null;
        $address = null;

        if ($addressType === '1') {
            // Rural: District -> Block -> GP -> Village
            $blockId = $request->input('block');
            if ($blockId) {
                $block = Block::where('block_id', $blockId)->first();
                $blockName = $block ? $block->block_name : null;
            }

            $gpId = $request->input('grampanchayat');
            if ($gpId) {
                $gp = Grampanchayat::where('gp_id', $gpId)->first();
                $gpName = $gp ? $gp->gp_name : null;
            }

            $villageId = $request->input('village_id') ?: $request->input('village');
            $village = null;
            if ($villageId) {
                $villageQuery = Village::where('village_id', $villageId);
                if ($gpId) {
                    $villageQuery->where('gp_id', $gpId);
                }
                if ($blockId) {
                    $villageQuery->where('block_id', $blockId);
                }
                $village = $villageQuery->first() ?? Village::where('village_id', $villageId)->first();
            }
            $villageName = $village ? $village->village_name : null;

            $pinCode = $request->input('pin') ?: $request->input('ngo_postal_address_pin');

            $addressParts = [];
            if ($request->filled('ngo_postal_address_at')) {
                $addressParts[] = 'At - ' . trim($request->input('ngo_postal_address_at'));
            }
            if ($villageName) {
                $addressParts[] = 'Village - ' . $villageName;
            }
            if ($gpName) {
                $addressParts[] = 'GP - ' . $gpName;
            }
            if ($request->filled('ngo_postal_address_post')) {
                $addressParts[] = 'Po - ' . trim($request->input('ngo_postal_address_post'));
            }
            if ($request->filled('ngo_postal_address_via')) {
                $addressParts[] = 'Via - ' . trim($request->input('ngo_postal_address_via'));
            }
            if ($request->filled('ngo_postal_address_ps')) {
                $addressParts[] = 'PS - ' . trim($request->input('ngo_postal_address_ps'));
            }
            if ($blockName) {
                $addressParts[] = 'Block - ' . $blockName;
            }
            if ($districtName) {
                $addressParts[] = 'Dist - ' . $districtName;
            }
            if ($pinCode) {
                $addressParts[] = 'PIN - ' . $pinCode;
            }
            $address = !empty($addressParts) ? implode(', ', $addressParts) : $request->input('address');

        } elseif ($addressType === '2') {
            // Urban: District -> Municipality -> Ward
            $muniId = $request->input('municipality');
            if ($muniId) {
                $muni = Municipality::where('municipality_id', $muniId)->first();
                $muniName = $muni ? $muni->municipality_name : null;
            }

            $wardCode = $request->input('ward');
            if ($wardCode) {
                $ward = WardMaster::where('ward_code', $wardCode)->first();
                if ($ward) {
                    $wardId = $ward->ward_code ?? $ward->id;
                    $wardName = $ward->ward_name;
                } else {
                    $wardId = is_numeric($wardCode) ? (int)$wardCode : null;
                    $wardName = 'Ward ' . $wardCode;
                }
            }

            $pinCode = $request->input('pin') ?: $request->input('ngo_postal_address_pin');

            $addressParts = [];
            if ($request->filled('ngo_postal_address_at')) {
                $addressParts[] = 'At - ' . trim($request->input('ngo_postal_address_at'));
            }
            if ($wardName) {
                $addressParts[] = $wardName;
            }
            if ($muniName) {
                $addressParts[] = 'ULB - ' . $muniName;
            }
            if ($request->filled('ngo_postal_address_post')) {
                $addressParts[] = 'Po - ' . trim($request->input('ngo_postal_address_post'));
            }
            if ($request->filled('ngo_postal_address_via')) {
                $addressParts[] = 'Via - ' . trim($request->input('ngo_postal_address_via'));
            }
            if ($request->filled('ngo_postal_address_ps')) {
                $addressParts[] = 'PS - ' . trim($request->input('ngo_postal_address_ps'));
            }
            if ($districtName) {
                $addressParts[] = 'Dist - ' . $districtName;
            }
            if ($pinCode) {
                $addressParts[] = 'PIN - ' . $pinCode;
            }
            $address = !empty($addressParts) ? implode(', ', $addressParts) : $request->input('address');

        } else {
            // Legacy inputs
            $blockId = $request->input('block_id');
            if ($blockId) {
                $block = Block::where('block_id', $blockId)->first();
                $blockName = $block ? $block->block_name : null;
            }

            $muniId = $request->input('municipality_id');
            if ($muniId) {
                $muni = Municipality::where('municipality_id', $muniId)->first();
                $muniName = $muni ? $muni->municipality_name : null;
            }

            $gpName = $request->input('gp_name');
            $wardName = $request->input('ward_name');
            $pinCode = $request->input('pin_code');
            $address = $request->input('address');
            $addressType = !empty($blockId) ? '1' : (!empty($muniId) ? '2' : null);
        }

        $status = (int)$validated['is_active'];

        DB::beginTransaction();
        try {
            $institute = new FieldVisitInstitute();
            $institute->institute_code    = Str::upper(trim($validated['institute_code']));
            $institute->institute_name    = trim($validated['institute_name']);
            $institute->category_id       = (int)$validated['category_id'];
            $institute->category_name     = $categoryName;
            $institute->management_name   = !empty($validated['management_name']) ? trim($validated['management_name']) : null;
            $institute->address_type      = !empty($addressType) ? (int)$addressType : null;
            $institute->state_id          = !empty($stateId) ? (int)$stateId : null;
            $institute->district_id       = (int)$districtId;
            $institute->district_name     = $districtName;
            $institute->block_id          = !empty($blockId) ? (int)$blockId : null;
            $institute->block_name        = $blockName;
            $institute->municipality_id   = !empty($muniId) ? (int)$muniId : null;
            $institute->municipality_name = $muniName;
            $institute->gp_id             = !empty($gpId) ? (int)$gpId : null;
            $institute->gp_name           = $gpName;
            $institute->village_id        = !empty($villageId) ? (int)$villageId : null;
            $institute->village_name      = $villageName;
            $institute->ward_id           = !empty($wardId) ? (int)$wardId : null;
            $institute->ward_name         = $wardName;
            $institute->address           = !empty($address) ? trim($address) : null;
            $institute->pin_code          = !empty($pinCode) ? trim($pinCode) : null;
            $institute->contact_person    = !empty($validated['contact_person']) ? trim($validated['contact_person']) : null;
            $institute->contact_number    = !empty($validated['contact_number']) ? trim($validated['contact_number']) : null;
            $institute->email             = !empty($validated['email']) ? trim($validated['email']) : null;
            $institute->which_govt        = (int)$validated['which_govt'];
            $institute->status            = $status;
            $institute->is_active         = ($status === 1);
            $institute->remarks           = !empty($validated['remarks']) ? trim($validated['remarks']) : null;

            // Strict audit attribution
            $institute->created_by = $userTableId;
            $institute->updated_by = null;

            $institute->save();

            DB::commit();

            return redirect()->route('admin.field_visit_institutes.index')
                ->with('success', "Institute '{$institute->institute_name}' [{$institute->institute_code}] created successfully.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Institute store failure: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating institute record: ' . $e->getMessage());
        }
    }

    /**
     * Display details of a specific institute.
     */
    public function show($id): View
    {
        $institute = FieldVisitInstitute::with(['state', 'district', 'block', 'grampanchayat', 'village', 'municipality', 'creator', 'updater', 'fieldVisits'])
            ->findOrFail($id);

        return view('dashboard.field_visit.institutes.show', compact('institute'));
    }

    /**
     * Show the form for editing an existing institute.
     */
    public function edit($id): View
    {
        $institute = FieldVisitInstitute::findOrFail($id);
        $states     = State::orderBy('state_name', 'asc')->get();
        $districts  = District::orderBy('district_name', 'asc')->get();
        $categories = $this->getCategoriesList();

        // Get blocks and municipalities for the current institute's district
        $blocks = Block::where('district_id', $institute->district_id)
            ->where('is_active', 'active')
            ->orderBy('block_name', 'asc')
            ->get();

        $municipalities = Municipality::where('district_id', $institute->district_id)
            ->where('is_active', 'active')
            ->orderBy('municipality_name', 'asc')
            ->get();

        // Resolve default / saved state ID
        $savedStateId = $institute->state_id;
        if (empty($savedStateId) && !empty($institute->district_id)) {
            $savedStateId = District::where('district_id', $institute->district_id)->value('state_id') ?? 228;
        } elseif (empty($savedStateId)) {
            $savedStateId = 228;
        }

        // Resolve saved address type: 1 = Rural, 2 = Urban
        $savedAddressType = $institute->address_type;
        if (empty($savedAddressType)) {
            if (!empty($institute->block_id)) {
                $savedAddressType = 1;
            } elseif (!empty($institute->municipality_id)) {
                $savedAddressType = 2;
            }
        }

        $savedDistrictId     = $institute->district_id;
        $savedBlockId        = $institute->block_id;
        $savedGpId           = $institute->gp_id;
        $savedMunicipalityId = $institute->municipality_id;
        $savedWardId         = $institute->ward_id;
        $savedPin            = $institute->pin_code;

        // Resolve saved village ID if Rural
        $savedVillageId = $institute->village_id;
        if (empty($savedVillageId) && $savedAddressType == 1 && !empty($savedGpId)) {
            if (!empty($institute->address) && preg_match('/Village\s*-\s*([^,]+)/i', $institute->address, $matches)) {
                $extractedVillage = trim($matches[1]);
                $savedVillageId = Village::where('gp_id', $savedGpId)
                    ->where('village_name', $extractedVillage)
                    ->value('village_id');
            }
            if (!$savedVillageId && !empty($institute->address)) {
                $villagesInGp = Village::where('gp_id', $savedGpId)->get();
                foreach ($villagesInGp as $v) {
                    if (stripos($institute->address, $v->village_name) !== false) {
                        $savedVillageId = $v->village_id;
                        break;
                    }
                }
            }
        }

        return view('dashboard.field_visit.institutes.edit', compact(
            'institute',
            'states',
            'districts',
            'categories',
            'blocks',
            'municipalities',
            'savedStateId',
            'savedAddressType',
            'savedDistrictId',
            'savedBlockId',
            'savedGpId',
            'savedVillageId',
            'savedMunicipalityId',
            'savedWardId',
            'savedPin'
        ));
    }

    /**
     * Update an existing institute in database.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $institute = FieldVisitInstitute::findOrFail($id);

        // 1. Mandatory Audit Rule: Resolve authenticated officer's user_table_id
        $user = Auth::user();
        $userTableId = $user ? $user->user_table_id : null;

        if (empty($userTableId)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Operational audit failure: Authenticated officer record does not possess a valid user_table_id business identifier.');
        }

        // 2. Validate request (ignore current record for unique code)
        $validationRules = [
            'institute_code'  => "required|string|max:50|unique:field_visit_institutes,institute_code,{$id},id|regex:/^[A-Z0-9\-_]+$/i",
            'institute_name'  => 'required|string|max:255',
            'category_id'     => 'required|integer',
            'management_name' => 'nullable|string|max:255',
            'which_govt'      => 'required|in:1,2',
            'is_active'       => 'required|in:0,1',
            'contact_person'  => 'nullable|string|max:150',
            'contact_number'  => 'nullable|string|max:25',
            'email'           => 'nullable|email|max:150',
            'remarks'         => 'nullable|string',
        ];

        $addressType = $request->input('address_type') ?: $request->input('ngo_address_type');
        if (empty($addressType) && !$request->has('district_id')) {
            $addressType = $institute->address_type ? (string)$institute->address_type : null;
        }

        if ($addressType === '1') {
            // Rural: State -> District -> Block -> GP -> Village + PIN (Postal Address UI removed)
            $validationRules = array_merge($validationRules, [
                'address_type'                => 'nullable|in:1,2',
                'ngo_address_type'            => 'nullable|in:1,2',
                'state'                       => 'required|integer|exists:states,state_id',
                'district'                    => 'required|integer|exists:districts,district_id',
                'block'                       => 'required|integer|exists:blocks,block_id',
                'grampanchayat'               => 'required|integer|exists:grampanchayats,gp_id',
                'village'                     => 'required_without:village_id|nullable|integer|exists:villages,village_id',
                'village_id'                  => 'required_without:village|nullable|integer|exists:villages,village_id',
                'pin'                         => 'required|string|max:10|regex:/^[0-9]{6}$/',
                'ngo_postal_address_at'       => 'nullable|string|max:255',
                'ngo_postal_address_post'     => 'nullable|string|max:255',
                'ngo_postal_address_via'      => 'nullable|string|max:255',
                'ngo_postal_address_ps'       => 'nullable|string|max:255',
                'ngo_postal_address_district' => 'nullable|string|max:255',
                'ngo_postal_address_pin'      => 'nullable|string|max:10',
            ]);
        } elseif ($addressType === '2') {
            // Urban: State -> District -> Municipality -> Ward + PIN (Postal Address UI removed)
            $validationRules = array_merge($validationRules, [
                'address_type'                => 'nullable|in:1,2',
                'ngo_address_type'            => 'nullable|in:1,2',
                'state'                       => 'required|integer|exists:states,state_id',
                'district'                    => 'required|integer|exists:districts,district_id',
                'municipality'                => 'required|integer|exists:municipalities,municipality_id',
                'ward'                        => 'required',
                'pin'                         => 'required|string|max:10|regex:/^[0-9]{6}$/',
                'ngo_postal_address_at'       => 'nullable|string|max:255',
                'ngo_postal_address_post'     => 'nullable|string|max:255',
                'ngo_postal_address_via'      => 'nullable|string|max:255',
                'ngo_postal_address_ps'       => 'nullable|string|max:255',
                'ngo_postal_address_district' => 'nullable|string|max:255',
                'ngo_postal_address_pin'      => 'nullable|string|max:10',
            ]);
        } else {
            // Legacy / Direct input fallback
            $validationRules = array_merge($validationRules, [
                'address_type'    => 'nullable|in:1,2',
                'ngo_address_type'=> 'nullable|in:1,2',
                'state_id'        => 'nullable|integer|exists:states,state_id',
                'district_id'     => 'required|integer|exists:districts,district_id',
                'block_id'        => 'nullable|integer',
                'municipality_id' => 'nullable|integer',
                'gp_name'         => 'nullable|string|max:100',
                'ward_name'       => 'nullable|string|max:100',
                'address'         => 'nullable|string',
                'pin_code'        => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            ]);
        }

        $validated = $request->validate($validationRules, [
            'institute_code.regex'         => 'The institute code may only contain letters, numbers, hyphens, and underscores.',
            'pin.regex'                    => 'The Geographic PIN must be a valid 6-digit postal code.',
            'ngo_postal_address_pin.regex' => 'The Postal PIN code must be a valid 6-digit postal code.',
            'pin_code.regex'               => 'The PIN code must be a valid 6-digit postal code.',
            'state.exists'                 => 'The selected state does not exist in the official master.',
            'district.exists'              => 'The selected district does not exist in the official master.',
            'district_id.exists'           => 'The selected district does not exist in the official master.',
            'block.exists'                 => 'The selected block does not exist in the official master.',
            'grampanchayat.exists'         => 'The selected gram panchayat does not exist in the official master.',
            'village.required_without'     => 'Please select Village.',
            'village_id.required_without'  => 'Please select Village.',
            'village.exists'               => 'The selected village does not exist in the official master.',
            'village_id.exists'            => 'The selected village does not exist in the official master.',
            'municipality.exists'          => 'The selected municipality does not exist in the official master.',
        ]);

        // Resolve category name
        $categoryName = $this->standardCategories[$validated['category_id']] 
            ?? FieldVisitInstitute::where('category_id', $validated['category_id'])->value('category_name') 
            ?? $institute->category_name;

        // Resolve district name
        $districtId = $request->input('district') ?? $request->input('district_id') ?? $institute->district_id;
        $district = District::where('district_id', $districtId)->first();
        $districtName = $district ? $district->district_name : $institute->district_name;

        // Resolve state_id
        $stateId = $request->input('state') ?: $request->input('state_id') ?: $institute->state_id;
        if (empty($stateId) && !empty($districtId)) {
            $stateId = District::where('district_id', $districtId)->value('state_id') ?? 228;
        } elseif (empty($stateId)) {
            $stateId = 228;
        }

        $blockId = null;
        $blockName = null;
        $muniId = null;
        $muniName = null;
        $gpId = null;
        $gpName = null;
        $villageId = null;
        $villageName = null;
        $wardId = null;
        $wardName = null;
        $pinCode = null;
        $address = null;

        if ($addressType === '1') {
            // Rural: District -> Block -> GP -> Village
            $blockId = $request->input('block');
            if ($blockId) {
                $block = Block::where('block_id', $blockId)->first();
                $blockName = $block ? $block->block_name : null;
            }

            $gpId = $request->input('grampanchayat');
            if ($gpId) {
                $gp = Grampanchayat::where('gp_id', $gpId)->first();
                $gpName = $gp ? $gp->gp_name : null;
            }

            $villageId = $request->input('village_id') ?: $request->input('village');
            $village = null;
            if ($villageId) {
                $villageQuery = Village::where('village_id', $villageId);
                if ($gpId) {
                    $villageQuery->where('gp_id', $gpId);
                }
                if ($blockId) {
                    $villageQuery->where('block_id', $blockId);
                }
                $village = $villageQuery->first() ?? Village::where('village_id', $villageId)->first();
            }
            $villageName = $village ? $village->village_name : null;

            $pinCode = $request->input('pin') ?: $request->input('ngo_postal_address_pin');

            $addressParts = [];
            if ($request->filled('ngo_postal_address_at')) {
                $addressParts[] = 'At - ' . trim($request->input('ngo_postal_address_at'));
            }
            if ($villageName) {
                $addressParts[] = 'Village - ' . $villageName;
            }
            if ($gpName) {
                $addressParts[] = 'GP - ' . $gpName;
            }
            if ($request->filled('ngo_postal_address_post')) {
                $addressParts[] = 'Po - ' . trim($request->input('ngo_postal_address_post'));
            }
            if ($request->filled('ngo_postal_address_via')) {
                $addressParts[] = 'Via - ' . trim($request->input('ngo_postal_address_via'));
            }
            if ($request->filled('ngo_postal_address_ps')) {
                $addressParts[] = 'PS - ' . trim($request->input('ngo_postal_address_ps'));
            }
            if ($blockName) {
                $addressParts[] = 'Block - ' . $blockName;
            }
            if ($districtName) {
                $addressParts[] = 'Dist - ' . $districtName;
            }
            if ($pinCode) {
                $addressParts[] = 'PIN - ' . $pinCode;
            }
            $address = !empty($addressParts) ? implode(', ', $addressParts) : ($request->input('address') ?: $institute->address);

        } elseif ($addressType === '2') {
            // Urban: District -> Municipality -> Ward
            $muniId = $request->input('municipality');
            if ($muniId) {
                $muni = Municipality::where('municipality_id', $muniId)->first();
                $muniName = $muni ? $muni->municipality_name : null;
            }

            $wardCode = $request->input('ward');
            if ($wardCode) {
                $ward = WardMaster::where('ward_code', $wardCode)->first();
                if ($ward) {
                    $wardId = $ward->ward_code ?? $ward->id;
                    $wardName = $ward->ward_name;
                } else {
                    $wardId = is_numeric($wardCode) ? (int)$wardCode : null;
                    $wardName = 'Ward ' . $wardCode;
                }
            }

            $pinCode = $request->input('pin') ?: $request->input('ngo_postal_address_pin');

            $addressParts = [];
            if ($request->filled('ngo_postal_address_at')) {
                $addressParts[] = 'At - ' . trim($request->input('ngo_postal_address_at'));
            }
            if ($wardName) {
                $addressParts[] = $wardName;
            }
            if ($muniName) {
                $addressParts[] = 'ULB - ' . $muniName;
            }
            if ($request->filled('ngo_postal_address_post')) {
                $addressParts[] = 'Po - ' . trim($request->input('ngo_postal_address_post'));
            }
            if ($request->filled('ngo_postal_address_via')) {
                $addressParts[] = 'Via - ' . trim($request->input('ngo_postal_address_via'));
            }
            if ($request->filled('ngo_postal_address_ps')) {
                $addressParts[] = 'PS - ' . trim($request->input('ngo_postal_address_ps'));
            }
            if ($districtName) {
                $addressParts[] = 'Dist - ' . $districtName;
            }
            if ($pinCode) {
                $addressParts[] = 'PIN - ' . $pinCode;
            }
            $address = !empty($addressParts) ? implode(', ', $addressParts) : ($request->input('address') ?: $institute->address);

        } else {
            // Legacy inputs
            $blockId = $request->input('block_id') ?? $institute->block_id;
            if ($blockId) {
                $block = Block::where('block_id', $blockId)->first();
                $blockName = $block ? $block->block_name : $institute->block_name;
            }

            $muniId = $request->input('municipality_id') ?? $institute->municipality_id;
            if ($muniId) {
                $muni = Municipality::where('municipality_id', $muniId)->first();
                $muniName = $muni ? $muni->municipality_name : $institute->municipality_name;
            }

            $gpName = $request->input('gp_name') ?? $institute->gp_name;
            $wardName = $request->input('ward_name') ?? $institute->ward_name;
            $pinCode = $request->input('pin_code') ?? $institute->pin_code;
            $address = $request->input('address') ?? $institute->address;
            $addressType = !empty($blockId) ? '1' : (!empty($muniId) ? '2' : $institute->address_type);
        }

        $status = (int)$validated['is_active'];

        DB::beginTransaction();
        try {
            $institute->institute_code    = Str::upper(trim($validated['institute_code']));
            $institute->institute_name    = trim($validated['institute_name']);
            $institute->category_id       = (int)$validated['category_id'];
            $institute->category_name     = $categoryName;
            $institute->management_name   = !empty($validated['management_name']) ? trim($validated['management_name']) : null;
            $institute->address_type      = !empty($addressType) ? (int)$addressType : null;
            $institute->state_id          = !empty($stateId) ? (int)$stateId : null;
            $institute->district_id       = (int)$districtId;
            $institute->district_name     = $districtName;
            $institute->block_id          = !empty($blockId) ? (int)$blockId : null;
            $institute->block_name        = $blockName;
            $institute->municipality_id   = !empty($muniId) ? (int)$muniId : null;
            $institute->municipality_name = $muniName;
            $institute->gp_id             = !empty($gpId) ? (int)$gpId : null;
            $institute->gp_name           = $gpName;
            $institute->village_id        = !empty($villageId) ? (int)$villageId : null;
            $institute->village_name      = $villageName;
            $institute->ward_id           = !empty($wardId) ? (int)$wardId : null;
            $institute->ward_name         = $wardName;
            $institute->address           = !empty($address) ? trim($address) : null;
            $institute->pin_code          = !empty($pinCode) ? trim($pinCode) : null;
            $institute->contact_person    = !empty($validated['contact_person']) ? trim($validated['contact_person']) : null;
            $institute->contact_number    = !empty($validated['contact_number']) ? trim($validated['contact_number']) : null;
            $institute->email             = !empty($validated['email']) ? trim($validated['email']) : null;
            $institute->which_govt        = (int)$validated['which_govt'];
            $institute->status            = $status;
            $institute->is_active         = ($status === 1);
            $institute->remarks           = !empty($validated['remarks']) ? trim($validated['remarks']) : null;

            // Strict audit attribution
            $institute->updated_by = $userTableId;

            $institute->save();

            DB::commit();

            return redirect()->route('admin.field_visit_institutes.index')
                ->with('success', "Institute '{$institute->institute_name}' updated successfully.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Institute update failure: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating institute record: ' . $e->getMessage());
        }
    }

    /**
     * Safe delete or deactivation of an institute.
     */
    public function destroy($id): RedirectResponse
    {
        $institute = FieldVisitInstitute::findOrFail($id);

        $user = Auth::user();
        $userTableId = $user ? $user->user_table_id : null;

        // Check dependencies with field_visits table
        if ($institute->hasDependencies()) {
            return redirect()->back()->with(
                'error',
                "Cannot delete institute '{$institute->institute_name}' because active Field Visit inspection records depend on it. Please deactivate the institute status instead."
            );
        }

        DB::beginTransaction();
        try {
            $institute->deleted_by = $userTableId;
            $institute->save();
            $institute->delete();

            DB::commit();

            return redirect()->route('admin.field_visit_institutes.index')
                ->with('warning', "Institute '{$institute->institute_name}' [{$institute->institute_code}] has been safely removed.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Institute deletion failure: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Could not delete institute: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint to fetch blocks and municipalities for a district.
     */
    public function getLocations(Request $request): JsonResponse
    {
        $districtId = $request->input('district_id');

        if (empty($districtId)) {
            return response()->json(['blocks' => [], 'municipalities' => []]);
        }

        $blocks = Block::where('district_id', $districtId)
            ->where('is_active', 'active')
            ->orderBy('block_name', 'asc')
            ->get(['block_id', 'block_name']);

        $municipalities = Municipality::where('district_id', $districtId)
            ->where('is_active', 'active')
            ->orderBy('municipality_name', 'asc')
            ->get(['municipality_id', 'municipality_name']);

        return response()->json([
            'blocks' => $blocks,
            'municipalities' => $municipalities,
        ]);
    }

    /**
     * Helper to retrieve distinct categories list.
     */
    protected function getCategoriesList(): array
    {
        $dbCats = FieldVisitInstitute::select('category_id', 'category_name')
            ->distinct()
            ->orderBy('category_id')
            ->get();

        $categories = [];
        foreach ($dbCats as $cat) {
            $categories[$cat->category_id] = $cat->category_name;
        }

        if (empty($categories)) {
            return $this->standardCategories;
        }

        return $categories;
    }
}
