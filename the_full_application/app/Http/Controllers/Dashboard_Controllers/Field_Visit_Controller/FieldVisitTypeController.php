<?php

namespace App\Http\Controllers\Dashboard_Controllers\Field_Visit_Controller;

use App\Http\Controllers\Controller;
use App\Models\FieldVisitType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class FieldVisitTypeController extends Controller
{
    /**
     * Set up permissions and access control for Field Visit Type master.
     */
    public function __construct()
    {
        $this->middleware(
            'permission:field-visit-type-access|field-visit-type-list|field-visit-type-show|field-visit-type-create|field-visit-type-edit|field-visit-type-delete',
            ['only' => ['index', 'show']]
        );
        $this->middleware('permission:field-visit-type-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:field-visit-type-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:field-visit-type-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the visit types.
     * Supports both Yajra DataTables AJAX and Blade server pagination.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = FieldVisitType::with(['creator', 'updater'])->select('field_visit_types.*');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return e($row->name);
                })
                ->editColumn('code', function ($row) {
                    return '<span class="badge bg-secondary font-monospace">' . e($row->code) . '</span>';
                })
                ->editColumn('description', function ($row) {
                    return $row->description ? Str::limit(e($row->description), 60) : '<span class="text-muted">N/A</span>';
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>'
                        : '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Inactive</span>';
                })
                ->editColumn('sort_order', function ($row) {
                    return '<span class="badge bg-light text-dark border">' . (int)$row->sort_order . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? Carbon::parse($row->created_at)->format('d-M-Y h:i A') : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>';
                    $actions .= '<div class="dropdown-menu dropdown-menu-end">';

                    if (auth()->user()->can('field-visit-type-show')) {
                        $actions .= '<a class="dropdown-item" href="' . route('admin.field_visit_types.show', $row->id) . '"><i class="fas fa-eye text-info me-2"></i> View</a>';
                    }

                    if (auth()->user()->can('field-visit-type-edit')) {
                        $actions .= '<a class="dropdown-item" href="' . route('admin.field_visit_types.edit', $row->id) . '"><i class="fas fa-edit text-warning me-2"></i> Edit</a>';
                    }

                    if (auth()->user()->can('field-visit-type-delete')) {
                        $actions .= '<a class="dropdown-item text-danger" href="' . route('admin.field_visit_types.destroy', $row->id) . '" id="delete"><i class="fas fa-trash-alt text-danger me-2"></i> Delete</a>';
                    }

                    $actions .= '</div></div>';
                    return $actions;
                })
                ->rawColumns(['code', 'description', 'is_active', 'sort_order', 'action'])
                ->make(true);
        }

        // Standard server-rendered fallback / initial render query
        $query = FieldVisitType::with(['creator', 'updater']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === '1' || $status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === '0' || $status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $visitTypes = $query->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.field_visit.visit_types.index', compact('visitTypes'));
    }

    /**
     * Show the form for creating a new visit type.
     */
    public function create(): View
    {
        $nextSortOrder = (FieldVisitType::max('sort_order') ?? 0) + 1;
        return view('dashboard.field_visit.visit_types.create', compact('nextSortOrder'));
    }

    /**
     * Store a newly created visit type in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Mandatory Audit Requirement: Verify authenticated user's user_table_id
        $user = Auth::user();
        $userTableId = $user ? $user->user_table_id : null;

        if (empty($userTableId)) {
            Log::warning('Visit type creation rejected: user_table_id is missing for authenticated user.', [
                'user_id' => $user ? $user->id : null,
                'email'   => $user ? $user->email : null,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Operation denied: The authenticated user profile is missing an assigned user_table_id.');
        }

        // Normalize code input
        if ($request->filled('code')) {
            $normalizedCode = Str::upper(Str::slug($request->input('code'), '_'));
            $request->merge(['code' => $normalizedCode]);
        } elseif ($request->filled('name')) {
            $generatedCode = Str::upper(Str::slug($request->input('name'), '_'));
            $request->merge(['code' => $generatedCode]);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:150|unique:field_visit_types,name',
            'code'        => 'required|string|max:50|unique:field_visit_types,code|regex:/^[A-Z0-9_]+$/',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'nullable|in:0,1',
            'sort_order'  => 'nullable|integer|min:0|max:9999',
        ], [
            'name.required'     => 'Please provide a visit type name.',
            'name.unique'       => 'A visit type with this name already exists.',
            'code.required'     => 'A unique code is required for the visit type.',
            'code.unique'       => 'This code is already assigned to another visit type.',
            'code.regex'        => 'Code must contain only uppercase letters, numbers, and underscores.',
            'sort_order.min'    => 'Sort order cannot be negative.',
        ]);

        try {
            DB::beginTransaction();

            $visitType = new FieldVisitType();
            $visitType->name        = trim($validated['name']);
            $visitType->code        = trim($validated['code']);
            $visitType->description = isset($validated['description']) ? trim($validated['description']) : null;
            $visitType->is_active   = $request->has('is_active') ? (bool)$request->input('is_active') : true;
            $visitType->sort_order  = isset($validated['sort_order']) ? (int)$validated['sort_order'] : 0;
            
            // Strictly set created_by to user_table_id (never Auth::id(), $user->id, or form input)
            $visitType->created_by  = $userTableId;
            $visitType->updated_by  = null;
            $visitType->save();

            DB::commit();

            return redirect()->route('admin.field_visit_types.index')
                ->with('success', 'Visit Type "' . $visitType->name . '" created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create Visit Type: ' . $e->getMessage(), [
                'exception'     => $e,
                'user_table_id' => $userTableId,
                'input'         => $request->except(['_token']),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the visit type. Please try again.');
        }
    }

    /**
     * Display the specified visit type details.
     */
    public function show($id): View
    {
        $visitType = FieldVisitType::with(['creator', 'updater'])->findOrFail($id);
        return view('dashboard.field_visit.visit_types.show', compact('visitType'));
    }

    /**
     * Show the form for editing the specified visit type.
     */
    public function edit($id): View
    {
        $visitType = FieldVisitType::findOrFail($id);
        return view('dashboard.field_visit.visit_types.edit', compact('visitType'));
    }

    /**
     * Update the specified visit type in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Mandatory Audit Requirement: Verify authenticated user's user_table_id
        $user = Auth::user();
        $userTableId = $user ? $user->user_table_id : null;

        if (empty($userTableId)) {
            Log::warning('Visit type update rejected: user_table_id is missing for authenticated user.', [
                'user_id' => $user ? $user->id : null,
                'email'   => $user ? $user->email : null,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Operation denied: The authenticated user profile is missing an assigned user_table_id.');
        }

        $visitType = FieldVisitType::findOrFail($id);

        if ($request->filled('code')) {
            $normalizedCode = Str::upper(Str::slug($request->input('code'), '_'));
            $request->merge(['code' => $normalizedCode]);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:150|unique:field_visit_types,name,' . $id . ',id',
            'code'        => 'required|string|max:50|regex:/^[A-Z0-9_]+$/|unique:field_visit_types,code,' . $id . ',id',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'nullable|in:0,1',
            'sort_order'  => 'nullable|integer|min:0|max:9999',
        ], [
            'name.required'  => 'Please provide a visit type name.',
            'name.unique'    => 'Another visit type with this name already exists.',
            'code.required'  => 'A unique code is required.',
            'code.unique'    => 'This code is already assigned to another visit type.',
            'code.regex'     => 'Code must contain only uppercase letters, numbers, and underscores.',
            'sort_order.min' => 'Sort order cannot be negative.',
        ]);

        try {
            DB::beginTransaction();

            $visitType->name        = trim($validated['name']);
            $visitType->code        = trim($validated['code']);
            $visitType->description = isset($validated['description']) ? trim($validated['description']) : null;
            $visitType->is_active   = $request->has('is_active') ? (bool)$request->input('is_active') : false;
            $visitType->sort_order  = isset($validated['sort_order']) ? (int)$validated['sort_order'] : 0;
            
            // Strictly set updated_by to user_table_id (never Auth::id(), $user->id, or form input)
            $visitType->updated_by  = $userTableId;
            $visitType->save();

            DB::commit();

            return redirect()->route('admin.field_visit_types.index')
                ->with('success', 'Visit Type "' . $visitType->name . '" updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update Visit Type #' . $id . ': ' . $e->getMessage(), [
                'exception'     => $e,
                'user_table_id' => $userTableId,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the visit type. Please try again.');
        }
    }

    /**
     * Remove the specified visit type from storage.
     * Enforces dependency safety and logs actions.
     */
    public function destroy($id): RedirectResponse
    {
        $visitType = FieldVisitType::findOrFail($id);

        // Safe dependency protection check
        if ($visitType->hasDependencies()) {
            return redirect()->route('admin.field_visit_types.index')
                ->with('error', 'Cannot delete "' . $visitType->name . '" because related field visit records depend on it.');
        }

        try {
            DB::beginTransaction();

            $name = $visitType->name;
            $visitType->delete();

            DB::commit();

            return redirect()->route('admin.field_visit_types.index')
                ->with('warning', 'Visit Type "' . $name . '" deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete Visit Type #' . $id . ': ' . $e->getMessage(), [
                'exception' => $e,
                'user_id'   => Auth::id(),
            ]);

            return redirect()->route('admin.field_visit_types.index')
                ->with('error', 'Could not delete visit type due to a database constraint or server error.');
        }
    }
}
