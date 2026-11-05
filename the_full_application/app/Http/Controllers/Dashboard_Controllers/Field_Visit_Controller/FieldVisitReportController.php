<?php

namespace App\Http\Controllers\Dashboard_Controllers\Field_Visit_Controller;

use App\Http\Controllers\Controller;
use App\Models\FieldVisit;
use App\Models\FieldVisitInstitute;
use App\Models\District;
use App\Models\Subdivision;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\Grampanchayat;
use App\Models\WardMaster;
use App\Services\FieldVisitReportService;
use App\Exports\FieldVisit\FieldVisitSummaryExport;
use App\Exports\FieldVisit\FieldVisitGeographicalExport;
use App\Exports\FieldVisit\FieldVisitDetailedExport;
use App\Exports\FieldVisit\FieldVisitCoverageExport;
use App\Exports\FieldVisit\FieldVisitDatewiseExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FieldVisitReportController extends Controller
{
    protected FieldVisitReportService $reportService;

    public function __construct(FieldVisitReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Report 1: Field Visit Summary Dashboard
     */
    public function summary(Request $request): View
    {
        $user = Auth::user();

        $query = FieldVisit::query();
        $this->reportService->applyVisitScope($query, $user);
        $this->reportService->applyFilters($query, $request, $user);

        $kpis = $this->reportService->getSummaryKPIs($query);
        $charts = $this->reportService->getSummaryChartData($query, $user);
        $breakdownRows = $this->reportService->getSummaryBreakdown($query, $user);
        $filterOptions = $this->reportService->getFilterOptions($user, $request);

        return view('dashboard.field_visit.reports.summary', compact(
            'kpis',
            'charts',
            'breakdownRows',
            'filterOptions'
        ));
    }

    /**
     * Excel Export: Field Visit Summary
     */
    public function exportSummary(Request $request): BinaryFileResponse
    {
        $user = Auth::user();

        $query = FieldVisit::query();
        $this->reportService->applyVisitScope($query, $user);
        $this->reportService->applyFilters($query, $request, $user);

        $breakdownRows = $this->reportService->getSummaryBreakdown($query, $user);
        $jurisdiction = $this->reportService->getUserJurisdiction($user);
        $title = ucfirst($jurisdiction['level'] === 'state' ? 'District' : $jurisdiction['level']);

        return Excel::download(
            new FieldVisitSummaryExport($breakdownRows, $title),
            'field_visit_summary_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Report 2: Geographical Analysis
     */
    public function geographical(Request $request): View
    {
        $user = Auth::user();
        $geoData = $this->reportService->getGeographicalAnalysis($request, $user);
        $filterOptions = $this->reportService->getFilterOptions($user, $request);

        return view('dashboard.field_visit.reports.geographical', array_merge($geoData, compact('filterOptions')));
    }

    /**
     * Excel Export: Geographical Analysis
     */
    public function exportGeographical(Request $request): BinaryFileResponse
    {
        $user = Auth::user();
        $geoData = $this->reportService->getGeographicalAnalysis($request, $user);

        if (!empty($geoData['visits'])) {
            $visits = $geoData['visits']->getCollection();
            return Excel::download(
                new FieldVisitDetailedExport($visits),
                'geographical_visits_' . date('Ymd_His') . '.xlsx'
            );
        }

        $items = $geoData['items'] ?? [];
        $title = ucfirst(str_replace('_', ' ', $geoData['current_level'] ?? 'Geographical Unit'));

        return Excel::download(
            new FieldVisitGeographicalExport($items, $title),
            'geographical_analysis_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Report 3: Detailed Visit Report
     */
    public function detailed(Request $request): View|JsonResponse
    {
        $user = Auth::user();

        $query = FieldVisit::with([
            'visitType',
            'district',
            'block',
            'municipality',
            'grampanchayat',
            'ward',
            'visitingOfficer',
            'institute',
            'evidences',
        ])
        ->orderByDesc('visit_date')
        ->orderByDesc('id');

        $this->reportService->applyVisitScope($query, $user);
        $this->reportService->applyFilters($query, $request, $user);

        if ($request->ajax()) {
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('visit_reference_no', function ($v) {
                    return '<a href="' . route('admin.field_visits.show', $v->id) . '" class="ref-badge text-primary text-decoration-none font-monospace fw-bold">' . e($v->visit_reference_no) . '</a>';
                })
                ->editColumn('visit_date', function ($v) {
                    return $v->visit_date ? $v->visit_date->format('d-M-Y') : 'N/A';
                })
                ->editColumn('visit_type', function ($v) {
                    return $v->visitType ? '<span class="badge bg-secondary-subtle text-dark border">' . e($v->visitType->name) . '</span>' : 'N/A';
                })
                ->editColumn('institution_name', function ($v) {
                    $cat = $v->institute ? $v->institute->category_name : ($v->institution_type ?: 'General');
                    return '<div class="fw-bold text-dark">' . e($v->institution_name) . '</div>' .
                           '<div class="small text-muted">Category: <span class="fw-semibold">' . e($cat) . '</span></div>';
                })
                ->editColumn('location', function ($v) {
                    $blockOrUlb = $v->address_type === 1
                        ? ($v->block ? $v->block->block_name : 'N/A')
                        : ($v->municipality ? $v->municipality->municipality_name : 'N/A');
                    $gpOrWard = $v->address_type === 1
                        ? ($v->grampanchayat ? $v->grampanchayat->gp_name : 'N/A')
                        : ($v->ward ? ($v->ward->ward_name ?: 'Ward ' . $v->ward_id) : 'N/A');
                    $dist = $v->district ? $v->district->district_name : 'N/A';

                    if ($v->address_type === 1) {
                        return '<span class="badge bg-success-subtle text-success border border-success"><i class="fas fa-tree me-1"></i>Rural</span>' .
                               '<div class="small text-dark fw-semibold mt-1">' . e($dist) . '</div>' .
                               '<div class="small text-muted">' . e($blockOrUlb) . ' › ' . e($gpOrWard) . '</div>';
                    } else {
                        return '<span class="badge bg-info-subtle text-info border border-info"><i class="fas fa-city me-1"></i>Urban</span>' .
                               '<div class="small text-dark fw-semibold mt-1">' . e($dist) . '</div>' .
                               '<div class="small text-muted">' . e($blockOrUlb) . ' › ' . e($gpOrWard) . '</div>';
                    }
                })
                ->editColumn('visiting_officer', function ($v) {
                    $officer = $v->visiting_officer_name ?: ($v->visitingOfficer ? $v->visitingOfficer->name : 'Officer #' . $v->visiting_officer_id);
                    $desig = $v->visiting_officer_designation === 'Other' && !empty($v->other_designation)
                        ? $v->other_designation
                        : $v->visiting_officer_designation;
                    $html = '<div class="fw-bold">' . e($officer) . '</div>';
                    if (!empty($desig)) {
                        $html .= '<span class="badge bg-secondary font-monospace" style="font-size: 0.65rem;">' . e($desig) . '</span>';
                    }
                    return $html;
                })
                ->editColumn('observations', function ($v) {
                    $html = '';
                    if (!empty($v->key_observations)) {
                        $html .= '<div class="small text-dark text-truncate" style="max-width:240px;" title="' . e($v->key_observations) . '"><strong>Obs:</strong> ' . e(Str::limit($v->key_observations, 75)) . '</div>';
                    }
                    if (!empty($v->recommendations)) {
                        $html .= '<div class="small text-muted text-truncate" style="max-width:240px;" title="' . e($v->recommendations) . '"><strong>Rec:</strong> ' . e(Str::limit($v->recommendations, 75)) . '</div>';
                    }
                    return $html ?: '<span class="text-muted small">—</span>';
                })
                ->editColumn('status', function ($v) {
                    return $v->status_badge;
                })
                ->addColumn('action', function ($v) {
                    return '<a href="' . route('admin.field_visits.show', $v->id) . '" class="btn btn-xs btn-outline-primary" title="View Visit Details"><i class="fas fa-eye me-1"></i> Details</a>';
                })
                ->rawColumns(['visit_reference_no', 'visit_type', 'institution_name', 'location', 'visiting_officer', 'observations', 'status', 'action'])
                ->make(true);
        }

        $visits = $query->paginate(20)->withQueryString();
        $filterOptions = $this->reportService->getFilterOptions($user, $request);

        return view('dashboard.field_visit.reports.detailed', compact('visits', 'filterOptions'));
    }

    /**
     * Excel Export: Detailed Visit Report
     */
    public function exportDetailed(Request $request): BinaryFileResponse
    {
        $user = Auth::user();

        $query = FieldVisit::with([
            'visitType',
            'district',
            'block',
            'municipality',
            'grampanchayat',
            'ward',
            'visitingOfficer',
            'institute',
        ])
        ->orderByDesc('visit_date')
        ->orderByDesc('id');

        $this->reportService->applyVisitScope($query, $user);
        $this->reportService->applyFilters($query, $request, $user);

        $visits = $query->get();

        return Excel::download(
            new FieldVisitDetailedExport($visits),
            'field_visits_detailed_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Report 4: Institution Coverage Report
     */
    public function coverage(Request $request): View|JsonResponse
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $institutesBaseQuery = FieldVisitInstitute::query();
            $this->reportService->applyInstituteScope($institutesBaseQuery, $user);

            if ($request->filled('category_id')) {
                $institutesBaseQuery->where('category_id', $request->input('category_id'));
            }
            if ($request->filled('district_id')) {
                $institutesBaseQuery->where('district_id', $request->input('district_id'));
            }
            if ($request->filled('block_id')) {
                $institutesBaseQuery->where('block_id', $request->input('block_id'));
            }
            if ($request->filled('municipality_id')) {
                $institutesBaseQuery->where('municipality_id', $request->input('municipality_id'));
            }

            $searchVal = $request->input('search_keyword');
            if (empty($searchVal) && $request->filled('search')) {
                $rawSearch = $request->input('search');
                $searchVal = is_array($rawSearch) ? ($rawSearch['value'] ?? null) : $rawSearch;
            }
            if (!empty($searchVal) && is_string($searchVal)) {
                $s = trim($searchVal);
                $institutesBaseQuery->where(function ($q) use ($s) {
                    $q->where('institute_name', 'like', "%{$s}%")
                      ->orWhere('institute_code', 'like', "%{$s}%")
                      ->orWhere('category_name', 'like', "%{$s}%");
                });
            }

            // Get visited institute IDs within user's visit scope
            $visitsBaseQuery = FieldVisit::query()->whereNotNull('field_visits.institution_id');
            $this->reportService->applyVisitScope($visitsBaseQuery, $user);

            if ($request->filled('date_from')) {
                $visitsBaseQuery->whereDate('field_visits.visit_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $visitsBaseQuery->whereDate('field_visits.visit_date', '<=', $request->input('date_to'));
            }

            $visitedInstituteIds = (clone $visitsBaseQuery)
                ->distinct()
                ->pluck('institution_id')
                ->toArray();

            $institutesListQuery = (clone $institutesBaseQuery)
                ->with(['district', 'block', 'municipality'])
                ->withCount(['fieldVisits as visit_count' => function ($q) use ($user) {
                    $this->reportService->applyVisitScope($q, $user);
                }])
                ->withMax(['fieldVisits as last_visit_date' => function ($q) use ($user) {
                    $this->reportService->applyVisitScope($q, $user);
                }], 'visit_date');

            if ($request->input('coverage_filter') === 'visited') {
                $institutesListQuery->whereIn('id', $visitedInstituteIds);
            } elseif ($request->input('coverage_filter') === 'unvisited') {
                $institutesListQuery->whereNotIn('id', $visitedInstituteIds);
            }

            return DataTables::eloquent($institutesListQuery)
                ->addIndexColumn()
                ->editColumn('institute_code', function ($inst) {
                    return '<span class="font-monospace small fw-bold">' . e($inst->institute_code) . '</span>';
                })
                ->editColumn('institute_name', function ($inst) {
                    return '<span class="fw-bold text-dark">' . e($inst->institute_name) . '</span>';
                })
                ->editColumn('category_name', function ($inst) {
                    return '<span class="badge bg-light text-dark border">' . e($inst->category_name) . '</span>';
                })
                ->editColumn('location', function ($inst) {
                    $dist = $inst->district ? $inst->district->district_name : ($inst->district_name ?: 'N/A');
                    $blockOrUlb = $inst->block ? $inst->block->block_name : ($inst->municipality ? $inst->municipality->municipality_name : $inst->block_name);
                    return '<div class="small fw-semibold">' . e($dist) . '</div><div class="small text-muted">' . e($blockOrUlb ?: '—') . '</div>';
                })
                ->editColumn('coverage_status', function ($inst) {
                    $isVisited = ($inst->visit_count ?? 0) > 0;
                    return $isVisited
                        ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Visited</span>'
                        : '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Not Visited</span>';
                })
                ->editColumn('visit_count', function ($inst) {
                    return '<span class="fw-bold">' . number_format($inst->visit_count ?? 0) . '</span>';
                })
                ->editColumn('last_visit_date', function ($inst) {
                    return !empty($inst->last_visit_date)
                        ? '<span class="fw-semibold text-dark">' . date('d-M-Y', strtotime($inst->last_visit_date)) . '</span>'
                        : '<span class="text-muted small">Never</span>';
                })
                ->addColumn('action', function ($inst) {
                    $isVisited = ($inst->visit_count ?? 0) > 0;
                    if ($isVisited) {
                        return '<a href="' . route('admin.field_visit_reports.detailed', ['institution_id' => $inst->id]) . '" class="btn btn-xs btn-outline-info" title="View visits to this institution"><i class="fas fa-eye me-1"></i> Visits</a>';
                    }
                    return '<span class="text-muted small">—</span>';
                })
                ->rawColumns(['institute_code', 'institute_name', 'category_name', 'location', 'coverage_status', 'visit_count', 'last_visit_date', 'action'])
                ->make(true);
        }

        $coverageData = $this->reportService->getInstitutionCoverage($request, $user);
        $filterOptions = $this->reportService->getFilterOptions($user, $request);

        return view('dashboard.field_visit.reports.coverage', array_merge($coverageData, compact('filterOptions')));
    }

    /**
     * Excel Export: Institution Coverage Report
     */
    public function exportCoverage(Request $request): BinaryFileResponse
    {
        $user = Auth::user();

        $institutesBaseQuery = FieldVisitInstitute::query();
        $this->reportService->applyInstituteScope($institutesBaseQuery, $user);

        if ($request->filled('category_id')) {
            $institutesBaseQuery->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('district_id')) {
            $institutesBaseQuery->where('district_id', $request->input('district_id'));
        }
        if ($request->filled('block_id')) {
            $institutesBaseQuery->where('block_id', $request->input('block_id'));
        }
        if ($request->filled('municipality_id')) {
            $institutesBaseQuery->where('municipality_id', $request->input('municipality_id'));
        }

        $institutes = $institutesBaseQuery
            ->with(['district', 'block', 'municipality'])
            ->withCount(['fieldVisits as visit_count' => function ($q) use ($user) {
                $this->reportService->applyVisitScope($q, $user);
            }])
            ->withMax(['fieldVisits as last_visit_date' => function ($q) use ($user) {
                $this->reportService->applyVisitScope($q, $user);
            }], 'visit_date')
            ->orderBy('institute_name')
            ->get();

        return Excel::download(
            new FieldVisitCoverageExport($institutes),
            'institution_coverage_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Report 5: Date-wise Visit Report
     */
    public function datewise(Request $request): View|JsonResponse
    {
        $user = Auth::user();

        $query = FieldVisit::with([
            'visitType',
            'district',
            'block',
            'municipality',
            'visitingOfficer',
            'institute',
        ])
        ->orderByDesc('visit_date')
        ->orderByDesc('id');

        $this->reportService->applyVisitScope($query, $user);
        $this->reportService->applyFilters($query, $request, $user);

        if ($request->ajax()) {
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('visit_reference_no', function ($v) {
                    return '<a href="' . route('admin.field_visits.show', $v->id) . '" class="fw-bold text-primary font-monospace">' . e($v->visit_reference_no) . '</a>';
                })
                ->editColumn('visit_date', function ($v) {
                    return $v->visit_date ? $v->visit_date->format('d-M-Y') : 'N/A';
                })
                ->editColumn('visit_type', function ($v) {
                    return $v->visitType ? e($v->visitType->name) : 'N/A';
                })
                ->editColumn('institution_name', function ($v) {
                    $sub = $v->institution_type ? '<br><span class="badge bg-light text-dark border">' . e($v->institution_type) . '</span>' : '';
                    return '<span class="fw-semibold">' . e($v->institution_name) . '</span>' . $sub;
                })
                ->editColumn('location', function ($v) {
                    $dist = $v->district ? $v->district->district_name : '';
                    if ($v->address_type === 1) {
                        $block = $v->block ? $v->block->block_name : '';
                        return '<span class="badge bg-success-subtle text-success border border-success"><i class="fas fa-tree me-1"></i>Rural</span>' .
                               '<div class="small text-muted">' . e($dist) . ' &bull; ' . e($block) . '</div>';
                    } else {
                        $muni = $v->municipality ? $v->municipality->municipality_name : '';
                        return '<span class="badge bg-info-subtle text-info border border-info"><i class="fas fa-city me-1"></i>Urban</span>' .
                               '<div class="small text-muted">' . e($dist) . ' &bull; ' . e($muni) . '</div>';
                    }
                })
                ->editColumn('visiting_officer', function ($v) {
                    $name = $v->visiting_officer_name ?: ($v->visitingOfficer ? $v->visitingOfficer->name : 'Officer #' . $v->visiting_officer_id);
                    $desig = $v->visiting_officer_designation ? '<br><span class="badge bg-secondary font-monospace" style="font-size: 0.65rem;">' . e($v->visiting_officer_designation) . '</span>' : '';
                    return '<span class="fw-semibold">' . e($name) . '</span>' . $desig;
                })
                ->editColumn('status', function ($v) {
                    return $v->status_badge;
                })
                ->addColumn('action', function ($v) {
                    return '<a href="' . route('admin.field_visits.show', $v->id) . '" class="btn btn-xs btn-outline-info" title="View Visit Details"><i class="fas fa-eye me-1"></i> View</a>';
                })
                ->rawColumns(['visit_reference_no', 'institution_name', 'location', 'visiting_officer', 'status', 'action'])
                ->make(true);
        }

        $datewiseData = $this->reportService->getDatewiseReport($query, $request);
        $filterOptions = $this->reportService->getFilterOptions($user, $request);

        return view('dashboard.field_visit.reports.datewise', array_merge($datewiseData, compact('filterOptions')));
    }

    /**
     * Excel Export: Date-wise Visit Report
     */
    public function exportDatewise(Request $request): BinaryFileResponse
    {
        $user = Auth::user();

        $query = FieldVisit::query();
        $this->reportService->applyVisitScope($query, $user);
        $this->reportService->applyFilters($query, $request, $user);

        $datewiseData = $this->reportService->getDatewiseReport($query, $request);

        return Excel::download(
            new FieldVisitDatewiseExport($datewiseData['daily_totals']),
            'datewise_visits_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * AJAX endpoint: Subdivisions by District
     */
    public function ajaxSubdivisions(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jurisdiction = $this->reportService->getUserJurisdiction($user);

        $districtId = $request->input('district_id');
        if ($jurisdiction['level'] === 'district') {
            $districtId = $jurisdiction['district_id'];
        }

        if (empty($districtId)) {
            return response()->json([]);
        }

        $subdivisions = Subdivision::where('district_id', $districtId)
            ->orderBy('subdivision_name')
            ->get(['subdivision_id', 'subdivision_name']);

        return response()->json($subdivisions);
    }

    /**
     * AJAX endpoint: Blocks by District or Subdivision
     */
    public function ajaxBlocks(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jurisdiction = $this->reportService->getUserJurisdiction($user);

        $query = Block::orderBy('block_name');

        if ($jurisdiction['level'] === 'block') {
            $query->where('block_id', $jurisdiction['block_id']);
        } elseif ($jurisdiction['level'] === 'subdivision') {
            $query->where('subdivision_id', $jurisdiction['subdivision_id']);
        } elseif ($jurisdiction['level'] === 'district') {
            $query->where('district_id', $jurisdiction['district_id']);
            if ($request->filled('subdivision_id')) {
                $query->where('subdivision_id', $request->input('subdivision_id'));
            }
        } else {
            if ($request->filled('subdivision_id')) {
                $query->where('subdivision_id', $request->input('subdivision_id'));
            } elseif ($request->filled('district_id')) {
                $query->where('district_id', $request->input('district_id'));
            }
        }

        return response()->json($query->get(['block_id', 'block_name']));
    }

    /**
     * AJAX endpoint: Municipalities by District or Subdivision
     */
    public function ajaxMunicipalities(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jurisdiction = $this->reportService->getUserJurisdiction($user);

        $query = Municipality::orderBy('municipality_name');

        if ($jurisdiction['level'] === 'ulb') {
            $query->where('municipality_id', $jurisdiction['municipality_id']);
        } elseif ($jurisdiction['level'] === 'subdivision') {
            $query->where('subdivision_id', $jurisdiction['subdivision_id']);
        } elseif ($jurisdiction['level'] === 'district') {
            $query->where('district_id', $jurisdiction['district_id']);
            if ($request->filled('subdivision_id')) {
                $query->where('subdivision_id', $request->input('subdivision_id'));
            }
        } else {
            if ($request->filled('subdivision_id')) {
                $query->where('subdivision_id', $request->input('subdivision_id'));
            } elseif ($request->filled('district_id')) {
                $query->where('district_id', $request->input('district_id'));
            }
        }

        return response()->json($query->get(['municipality_id', 'municipality_name']));
    }

    /**
     * AJAX endpoint: Gram Panchayats by Block
     */
    public function ajaxGps(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jurisdiction = $this->reportService->getUserJurisdiction($user);

        $blockId = $request->input('block_id');
        if ($jurisdiction['level'] === 'block') {
            $blockId = $jurisdiction['block_id'];
        }

        if (empty($blockId)) {
            return response()->json([]);
        }

        $gps = Grampanchayat::where('block_id', $blockId)
            ->orderBy('gp_name')
            ->get(['gp_id', 'gp_name']);

        return response()->json($gps);
    }

    /**
     * AJAX endpoint: Wards by Municipality
     */
    public function ajaxWards(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jurisdiction = $this->reportService->getUserJurisdiction($user);

        $municipalityId = $request->input('municipality_id');
        if ($jurisdiction['level'] === 'ulb') {
            $municipalityId = $jurisdiction['municipality_id'];
        }

        if (empty($municipalityId)) {
            return response()->json([]);
        }

        $wards = WardMaster::where('municipal_area_code', $municipalityId)
            ->orderBy('ward_name')
            ->get(['ward_code', 'ward_name']);

        return response()->json($wards);
    }
}

