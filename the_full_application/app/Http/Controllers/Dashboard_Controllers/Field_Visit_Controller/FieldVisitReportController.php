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
    public function detailed(Request $request): View
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
    public function coverage(Request $request): View
    {
        $user = Auth::user();
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
    public function datewise(Request $request): View
    {
        $user = Auth::user();

        $query = FieldVisit::query();
        $this->reportService->applyVisitScope($query, $user);
        $this->reportService->applyFilters($query, $request, $user);

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

