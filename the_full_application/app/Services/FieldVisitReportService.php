<?php

namespace App\Services;

use App\Models\FieldVisit;
use App\Models\FieldVisitInstitute;
use App\Models\FieldVisitType;
use App\Models\FieldVisitEvidence;
use App\Models\District;
use App\Models\Subdivision;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\Grampanchayat;
use App\Models\WardMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class FieldVisitReportService
{
    /**
     * Unrestricted state-level roles.
     * Roles: 1 (SuperAdmin), 2 (Admin), 12 (HO), 13 (BO), 14 (Director), 15 (Secretary), 25 (PensionRole)
     */
    public const STATE_ROLES = [1, 2, 12, 13, 14, 15, 25];

    /**
     * District-level roles (DSSO, Collector).
     */
    public const DISTRICT_ROLES = [9, 11];

    /**
     * Subdivision-level roles (SSSO, SubCollector).
     */
    public const SUBDIVISION_ROLES = [8, 10];

    /**
     * Block-level roles (BSSO, BDO).
     */
    public const BLOCK_ROLES = [4, 6];

    /**
     * ULB / Municipality-level roles (MEO/MSSO).
     */
    public const ULB_ROLES = [5];

    /**
     * Resolve the user's role ID.
     */
    public function getUserRole(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        return (int)($user->role_id ?? ($user->roles()->exists() ? $user->roles()->first()->id : 0));
    }

    /**
     * Get the user's geographical jurisdiction context.
     * Returns an array with role level and posted location identifiers.
     */
    public function getUserJurisdiction(?User $user): array
    {
        if (!$user) {
            return [
                'level'          => 'none',
                'role_id'        => 0,
                'district_id'    => null,
                'subdivision_id' => null,
                'block_id'       => null,
                'municipality_id'=> null,
                'user_table_id'  => null,
            ];
        }

        $roleId = $this->getUserRole($user);

        if (in_array($roleId, self::STATE_ROLES, true)) {
            return [
                'level'          => 'state',
                'role_id'        => $roleId,
                'district_id'    => null,
                'subdivision_id' => null,
                'block_id'       => null,
                'municipality_id'=> null,
                'user_table_id'  => $user->user_table_id,
            ];
        }

        if (in_array($roleId, self::DISTRICT_ROLES, true)) {
            return [
                'level'          => 'district',
                'role_id'        => $roleId,
                'district_id'    => $user->posted_district ? (int)$user->posted_district : null,
                'subdivision_id' => null,
                'block_id'       => null,
                'municipality_id'=> null,
                'user_table_id'  => $user->user_table_id,
            ];
        }

        if (in_array($roleId, self::SUBDIVISION_ROLES, true)) {
            return [
                'level'          => 'subdivision',
                'role_id'        => $roleId,
                'district_id'    => null,
                'subdivision_id' => $user->posted_subdiv ? (int)$user->posted_subdiv : null,
                'block_id'       => null,
                'municipality_id'=> null,
                'user_table_id'  => $user->user_table_id,
            ];
        }

        if (in_array($roleId, self::BLOCK_ROLES, true)) {
            return [
                'level'          => 'block',
                'role_id'        => $roleId,
                'district_id'    => null,
                'subdivision_id' => null,
                'block_id'       => $user->posted_block ? (int)$user->posted_block : null,
                'municipality_id'=> null,
                'user_table_id'  => $user->user_table_id,
            ];
        }

        if (in_array($roleId, self::ULB_ROLES, true)) {
            return [
                'level'          => 'ulb',
                'role_id'        => $roleId,
                'district_id'    => null,
                'subdivision_id' => null,
                'block_id'       => null,
                'municipality_id'=> $user->posted_municipality ? (int)$user->posted_municipality : null,
                'user_table_id'  => $user->user_table_id,
            ];
        }

        return [
            'level'          => 'restricted',
            'role_id'        => $roleId,
            'district_id'    => null,
            'subdivision_id' => null,
            'block_id'       => null,
            'municipality_id'=> null,
            'user_table_id'  => $user->user_table_id,
        ];
    }

    /**
     * Apply server-side geographical access restrictions to a FieldVisit query.
     */
    public function applyVisitScope(Builder $query, ?User $user): Builder
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        $jurisdiction = $this->getUserJurisdiction($user);

        switch ($jurisdiction['level']) {
            case 'state':
                // Unrestricted access across entire state
                return $query;

            case 'district':
                if (empty($jurisdiction['district_id'])) {
                    return $query->whereRaw('1 = 0');
                }
                $distId = $jurisdiction['district_id'];
                return $query->where(function (Builder $q) use ($distId) {
                    $q->where('field_visits.district_id', $distId)
                      ->orWhereHas('institute', fn(Builder $iq) => $iq->where('district_id', $distId));
                });

            case 'subdivision':
                if (empty($jurisdiction['subdivision_id'])) {
                    return $query->whereRaw('1 = 0');
                }
                $subdivId = $jurisdiction['subdivision_id'];
                $blockIds = Block::where('subdivision_id', $subdivId)->pluck('block_id')->toArray();
                $municipalityIds = Municipality::where('subdivision_id', $subdivId)->pluck('municipality_id')->toArray();

                return $query->where(function (Builder $q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('field_visits.block_id', $blockIds)
                      ->orWhereIn('field_visits.municipality_id', $municipalityIds)
                      ->orWhereHas('institute', function (Builder $iq) use ($blockIds, $municipalityIds) {
                          $iq->whereIn('block_id', $blockIds)
                             ->orWhereIn('municipality_id', $municipalityIds);
                      });
                });

            case 'block':
                if (empty($jurisdiction['block_id'])) {
                    return $query->whereRaw('1 = 0');
                }
                $blockId = $jurisdiction['block_id'];
                return $query->where(function (Builder $q) use ($blockId) {
                    $q->where('field_visits.block_id', $blockId)
                      ->orWhereHas('institute', fn(Builder $iq) => $iq->where('block_id', $blockId));
                });

            case 'ulb':
                if (empty($jurisdiction['municipality_id'])) {
                    return $query->whereRaw('1 = 0');
                }
                $muniId = $jurisdiction['municipality_id'];
                return $query->where(function (Builder $q) use ($muniId) {
                    $q->where('field_visits.municipality_id', $muniId)
                      ->orWhereHas('institute', fn(Builder $iq) => $iq->where('municipality_id', $muniId));
                });

            case 'restricted':
            default:
                $userTableId = $jurisdiction['user_table_id'];
                if (!empty($userTableId)) {
                    return $query->where(function (Builder $q) use ($userTableId) {
                        $q->where('field_visits.visiting_officer_id', $userTableId)
                          ->orWhere('field_visits.created_by', $userTableId);
                    });
                }
                return $query->whereRaw('1 = 0');
        }
    }

    /**
     * Apply server-side geographical access restrictions to a FieldVisitInstitute master query.
     */
    public function applyInstituteScope(Builder $query, ?User $user): Builder
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        $jurisdiction = $this->getUserJurisdiction($user);

        switch ($jurisdiction['level']) {
            case 'state':
                return $query;

            case 'district':
                if (empty($jurisdiction['district_id'])) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->where('field_visit_institutes.district_id', $jurisdiction['district_id']);

            case 'subdivision':
                if (empty($jurisdiction['subdivision_id'])) {
                    return $query->whereRaw('1 = 0');
                }
                $subdivId = $jurisdiction['subdivision_id'];
                $blockIds = Block::where('subdivision_id', $subdivId)->pluck('block_id')->toArray();
                $municipalityIds = Municipality::where('subdivision_id', $subdivId)->pluck('municipality_id')->toArray();

                return $query->where(function (Builder $q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('field_visit_institutes.block_id', $blockIds)
                      ->orWhereIn('field_visit_institutes.municipality_id', $municipalityIds);
                });

            case 'block':
                if (empty($jurisdiction['block_id'])) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->where('field_visit_institutes.block_id', $jurisdiction['block_id']);

            case 'ulb':
                if (empty($jurisdiction['municipality_id'])) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->where('field_visit_institutes.municipality_id', $jurisdiction['municipality_id']);

            case 'restricted':
            default:
                return $query->whereRaw('1 = 0');
        }
    }

    /**
     * Apply common report filters with strict server-side validation against user jurisdiction.
     */
    public function applyFilters(Builder $query, Request $request, ?User $user): Builder
    {
        $jurisdiction = $this->getUserJurisdiction($user);

        // Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('field_visits.visit_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('field_visits.visit_date', '<=', $request->input('date_to'));
        }

        // Visit Type
        if ($request->filled('field_visit_type_id')) {
            $query->where('field_visits.field_visit_type_id', $request->input('field_visit_type_id'));
        }

        // Category
        if ($request->filled('category_id')) {
            $query->where('field_visits.category_id', $request->input('category_id'));
        }

        // Address Type (1=Rural, 2=Urban)
        if ($request->filled('address_type')) {
            $query->where('field_visits.address_type', (int)$request->input('address_type'));
        }

        // Institution
        if ($request->filled('institution_id')) {
            $query->where('field_visits.institution_id', $request->input('institution_id'));
        } elseif ($request->filled('institution_name')) {
            $query->where('field_visits.institution_name', 'like', '%' . trim($request->input('institution_name')) . '%');
        }

        // Visiting Officer
        if ($request->filled('visiting_officer_name')) {
            $query->where('field_visits.visiting_officer_name', 'like', '%' . trim($request->input('visiting_officer_name')) . '%');
        }
        if ($request->filled('visiting_officer_designation')) {
            $query->where('field_visits.visiting_officer_designation', $request->input('visiting_officer_designation'));
        }

        // Status
        if ($request->filled('status')) {
            $query->where('field_visits.status', $request->input('status'));
        }

        // Keyword Search
        $rawSearch = $request->input('search');
        $searchVal = is_array($rawSearch) ? ($rawSearch['value'] ?? null) : $rawSearch;
        if (!empty($searchVal) && is_string($searchVal)) {
            $search = trim($searchVal);
            $query->where(function (Builder $q) use ($search) {
                $q->where('field_visits.visit_reference_no', 'like', "%{$search}%")
                  ->orWhere('field_visits.institution_name', 'like', "%{$search}%")
                  ->orWhere('field_visits.visit_purpose', 'like', "%{$search}%")
                  ->orWhere('field_visits.visiting_officer_name', 'like', "%{$search}%")
                  ->orWhere('field_visits.key_observations', 'like', "%{$search}%");
            });
        }

        // Geographical Filters with Strict Jurisdiction Authorization:

        // 1. District Filter: Only State users can pick any district.
        // District/Subdiv/Block/ULB users are locked to their own.
        if ($jurisdiction['level'] === 'state') {
            if ($request->filled('district_id')) {
                $query->where('field_visits.district_id', $request->input('district_id'));
            }
        }

        // 2. Subdivision Filter: Allowed for State and District users.
        if (in_array($jurisdiction['level'], ['state', 'district'], true) && $request->filled('subdivision_id')) {
            $subdivId = (int)$request->input('subdivision_id');
            // If district user, verify this subdivision belongs to their district
            $subdiv = Subdivision::find($subdivId);
            if ($subdiv) {
                if ($jurisdiction['level'] === 'district' && (int)$subdiv->district_id !== (int)$jurisdiction['district_id']) {
                    // Unauthorized subdivision requested; clamp to empty
                    $query->whereRaw('1 = 0');
                } else {
                    $blockIds = Block::where('subdivision_id', $subdivId)->pluck('block_id')->toArray();
                    $muniIds = Municipality::where('subdivision_id', $subdivId)->pluck('municipality_id')->toArray();
                    $query->where(function (Builder $q) use ($blockIds, $muniIds) {
                        $q->whereIn('field_visits.block_id', $blockIds)
                          ->orWhereIn('field_visits.municipality_id', $muniIds);
                    });
                }
            }
        }

        // 3. Block Filter: Allowed for State, District, and Subdivision users.
        if (in_array($jurisdiction['level'], ['state', 'district', 'subdivision'], true) && $request->filled('block_id')) {
            $blockId = (int)$request->input('block_id');
            $block = Block::find($blockId);
            if ($block) {
                $valid = true;
                if ($jurisdiction['level'] === 'district' && (int)$block->district_id !== (int)$jurisdiction['district_id']) {
                    $valid = false;
                }
                if ($jurisdiction['level'] === 'subdivision' && (int)$block->subdivision_id !== (int)$jurisdiction['subdivision_id']) {
                    $valid = false;
                }
                if ($valid) {
                    $query->where('field_visits.block_id', $blockId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        // 4. Municipality / ULB Filter: Allowed for State, District, and Subdivision users.
        if (in_array($jurisdiction['level'], ['state', 'district', 'subdivision'], true) && $request->filled('municipality_id')) {
            $muniId = (int)$request->input('municipality_id');
            $muni = Municipality::find($muniId);
            if ($muni) {
                $valid = true;
                if ($jurisdiction['level'] === 'district' && (int)$muni->district_id !== (int)$jurisdiction['district_id']) {
                    $valid = false;
                }
                if ($jurisdiction['level'] === 'subdivision' && (int)$muni->subdivision_id !== (int)$jurisdiction['subdivision_id']) {
                    $valid = false;
                }
                if ($valid) {
                    $query->where('field_visits.municipality_id', $muniId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        // 5. GP Filter: Valid for State, District, Subdiv, and Block users.
        if ($request->filled('gp_id')) {
            $gpId = (int)$request->input('gp_id');
            $gp = Grampanchayat::find($gpId);
            if ($gp) {
                $valid = true;
                if ($jurisdiction['level'] === 'block' && (int)$gp->block_id !== (int)$jurisdiction['block_id']) {
                    $valid = false;
                }
                if ($jurisdiction['level'] === 'district' && (int)$gp->district_id !== (int)$jurisdiction['district_id']) {
                    $valid = false;
                }
                if ($valid) {
                    $query->where('field_visits.gp_id', $gpId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        // 6. Ward Filter: Valid for State, District, Subdiv, and ULB users.
        if ($request->filled('ward_id')) {
            $wardCode = (int)$request->input('ward_id');
            $ward = WardMaster::where('ward_code', $wardCode)->first();
            if ($ward) {
                $valid = true;
                if ($jurisdiction['level'] === 'ulb' && (int)$ward->municipal_area_code !== (int)$jurisdiction['municipality_id']) {
                    $valid = false;
                }
                if ($valid) {
                    $query->where('field_visits.ward_id', $wardCode);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        return $query;
    }

    /**
     * Get allowed dropdown filter options tailored to the authenticated user's jurisdiction.
     */
    public function getFilterOptions(?User $user, Request $request): array
    {
        $jurisdiction = $this->getUserJurisdiction($user);
        $level = $jurisdiction['level'];

        // Districts list
        $districtsQuery = District::orderBy('district_name');
        if (in_array($level, ['district', 'subdivision', 'block', 'ulb'], true)) {
            $effectiveDistId = $jurisdiction['district_id'];
            if (!$effectiveDistId && $jurisdiction['subdivision_id']) {
                $effectiveDistId = Subdivision::where('subdivision_id', $jurisdiction['subdivision_id'])->value('district_id');
            }
            if (!$effectiveDistId && $jurisdiction['block_id']) {
                $effectiveDistId = Block::where('block_id', $jurisdiction['block_id'])->value('district_id');
            }
            if (!$effectiveDistId && $jurisdiction['municipality_id']) {
                $effectiveDistId = Municipality::where('municipality_id', $jurisdiction['municipality_id'])->value('district_id');
            }
            if ($effectiveDistId) {
                $districtsQuery->where('district_id', $effectiveDistId);
            }
        }
        $districts = $districtsQuery->get(['district_id', 'district_name']);

        // Subdivisions list
        $subdivisions = collect();
        if (in_array($level, ['state', 'district'], true)) {
            $subdivQuery = Subdivision::orderBy('subdivision_name');
            if ($level === 'district' && $jurisdiction['district_id']) {
                $subdivQuery->where('district_id', $jurisdiction['district_id']);
            } elseif ($request->filled('district_id')) {
                $subdivQuery->where('district_id', $request->input('district_id'));
            }
            $subdivisions = $subdivQuery->get(['subdivision_id', 'subdivision_name', 'district_id']);
        } elseif ($level === 'subdivision' && $jurisdiction['subdivision_id']) {
            $subdivisions = Subdivision::where('subdivision_id', $jurisdiction['subdivision_id'])->get(['subdivision_id', 'subdivision_name', 'district_id']);
        }

        // Blocks list
        $blocks = collect();
        if (in_array($level, ['state', 'district', 'subdivision'], true)) {
            $blockQuery = Block::orderBy('block_name');
            if ($level === 'subdivision' && $jurisdiction['subdivision_id']) {
                $blockQuery->where('subdivision_id', $jurisdiction['subdivision_id']);
            } elseif ($level === 'district' && $jurisdiction['district_id']) {
                $blockQuery->where('district_id', $jurisdiction['district_id']);
            }
            if ($request->filled('district_id') && $level === 'state') {
                $blockQuery->where('district_id', $request->input('district_id'));
            }
            if ($request->filled('subdivision_id')) {
                $blockQuery->where('subdivision_id', $request->input('subdivision_id'));
            }
            $blocks = $blockQuery->get(['block_id', 'block_name', 'district_id', 'subdivision_id']);
        } elseif ($level === 'block' && $jurisdiction['block_id']) {
            $blocks = Block::where('block_id', $jurisdiction['block_id'])->get(['block_id', 'block_name', 'district_id', 'subdivision_id']);
        }

        // Municipalities list
        $municipalities = collect();
        if (in_array($level, ['state', 'district', 'subdivision'], true)) {
            $muniQuery = Municipality::orderBy('municipality_name');
            if ($level === 'subdivision' && $jurisdiction['subdivision_id']) {
                $muniQuery->where('subdivision_id', $jurisdiction['subdivision_id']);
            } elseif ($level === 'district' && $jurisdiction['district_id']) {
                $muniQuery->where('district_id', $jurisdiction['district_id']);
            }
            if ($request->filled('district_id') && $level === 'state') {
                $muniQuery->where('district_id', $request->input('district_id'));
            }
            if ($request->filled('subdivision_id')) {
                $muniQuery->where('subdivision_id', $request->input('subdivision_id'));
            }
            $municipalities = $muniQuery->get(['municipality_id', 'municipality_name', 'district_id', 'subdivision_id']);
        } elseif ($level === 'ulb' && $jurisdiction['municipality_id']) {
            $municipalities = Municipality::where('municipality_id', $jurisdiction['municipality_id'])->get(['municipality_id', 'municipality_name', 'district_id', 'subdivision_id']);
        }

        // Categories from master
        $categories = FieldVisitInstitute::query()
            ->select('category_id', 'category_name')
            ->distinct()
            ->whereNotNull('category_id')
            ->whereNotNull('category_name')
            ->orderBy('category_name')
            ->get();

        // Visit Types
        $visitTypes = FieldVisitType::where('is_active', true)->orderBy('sort_order')->get();

        // Designations
        $designations = FieldVisit::getDesignations();

        return compact(
            'jurisdiction',
            'districts',
            'subdivisions',
            'blocks',
            'municipalities',
            'categories',
            'visitTypes',
            'designations'
        );
    }

    /**
     * Compute KPI cards for Field Visit Summary.
     */
    public function getSummaryKPIs(Builder $scopedQuery): array
    {
        // Clone query for counting
        $baseQuery = clone $scopedQuery;

        $totalVisits = (clone $baseQuery)->count();
        $ruralVisits = (clone $baseQuery)->where('field_visits.address_type', 1)->count();
        $urbanVisits = (clone $baseQuery)->where('field_visits.address_type', 2)->count();

        // Unique Institutions visited (either via institution_id or unique institution_name)
        $uniqueInstitutions = (clone $baseQuery)
            ->where(function (Builder $q) {
                $q->whereNotNull('field_visits.institution_id')
                  ->orWhere(function (Builder $q2) {
                      $q2->whereNotNull('field_visits.institution_name')
                         ->where('field_visits.institution_name', '!=', '');
                  });
            })
            ->distinct()
            ->count(DB::raw('COALESCE(CAST(field_visits.institution_id AS CHAR), field_visits.institution_name)'));

        // Unique Visiting Officers
        $uniqueOfficers = (clone $baseQuery)
            ->where(function (Builder $q) {
                $q->whereNotNull('field_visits.visiting_officer_id')
                  ->orWhere(function (Builder $q2) {
                      $q2->whereNotNull('field_visits.visiting_officer_name')
                         ->where('field_visits.visiting_officer_name', '!=', '');
                  });
            })
            ->distinct()
            ->count(DB::raw('COALESCE(CAST(field_visits.visiting_officer_id AS CHAR), field_visits.visiting_officer_name)'));

        // Uploaded evidences attached to the scoped visits
        $visitIds = (clone $baseQuery)->pluck('field_visits.id');
        $totalEvidences = FieldVisitEvidence::whereIn('field_visit_id', $visitIds)->count();

        return [
            'total_visits'        => $totalVisits,
            'rural_visits'        => $ruralVisits,
            'urban_visits'        => $urbanVisits,
            'unique_institutions' => $uniqueInstitutions,
            'unique_officers'     => $uniqueOfficers,
            'total_evidences'     => $totalEvidences,
        ];
    }

    /**
     * Compute analytical chart datasets for Field Visit Summary.
     */
    public function getSummaryChartData(Builder $scopedQuery, ?User $user): array
    {
        $jurisdiction = $this->getUserJurisdiction($user);

        // 1. Rural vs Urban
        $ruralCount = (clone $scopedQuery)->where('field_visits.address_type', 1)->count();
        $urbanCount = (clone $scopedQuery)->where('field_visits.address_type', 2)->count();
        $ruralVsUrban = [
            'labels' => ['Rural', 'Urban'],
            'data'   => [$ruralCount, $urbanCount],
            'colors' => ['#28a745', '#17a2b8'],
        ];

        // 2. Visit Type distribution
        $typeDistribution = (clone $scopedQuery)
            ->leftJoin('field_visit_types', 'field_visits.field_visit_type_id', '=', 'field_visit_types.id')
            ->select(
                DB::raw("COALESCE(field_visit_types.name, 'Unspecified') as type_name"),
                DB::raw('COUNT(field_visits.id) as visit_count')
            )
            ->groupBy('field_visit_types.name')
            ->orderByDesc('visit_count')
            ->get();

        $visitTypesChart = [
            'labels' => $typeDistribution->pluck('type_name')->toArray(),
            'data'   => $typeDistribution->pluck('visit_count')->map(fn($v) => (int)$v)->toArray(),
        ];

        // 3. Institution Category distribution
        $categoryDistribution = (clone $scopedQuery)
            ->leftJoin('field_visit_institutes', 'field_visits.institution_id', '=', 'field_visit_institutes.id')
            ->select(
                DB::raw("COALESCE(field_visit_institutes.category_name, 'Other / Uncategorized') as cat_name"),
                DB::raw('COUNT(field_visits.id) as visit_count')
            )
            ->groupBy('cat_name')
            ->orderByDesc('visit_count')
            ->take(10)
            ->get();

        $categoryChart = [
            'labels' => $categoryDistribution->pluck('cat_name')->toArray(),
            'data'   => $categoryDistribution->pluck('visit_count')->map(fn($v) => (int)$v)->toArray(),
        ];

        // 4. Monthly trend (last 12 months or active data)
        $monthlyTrend = (clone $scopedQuery)
            ->whereNotNull('field_visits.visit_date')
            ->select(
                DB::raw("DATE_FORMAT(field_visits.visit_date, '%Y-%m') as ym"),
                DB::raw("DATE_FORMAT(field_visits.visit_date, '%b %Y') as month_label"),
                DB::raw('COUNT(field_visits.id) as visit_count')
            )
            ->groupBy('ym', 'month_label')
            ->orderBy('ym', 'asc')
            ->get();

        $monthlyTrendChart = [
            'labels' => $monthlyTrend->pluck('month_label')->toArray(),
            'data'   => $monthlyTrend->pluck('visit_count')->map(fn($v) => (int)$v)->toArray(),
        ];

        // 5. Geographical Distribution tailored to user jurisdiction
        $geoChartLabels = [];
        $geoChartData = [];

        if ($jurisdiction['level'] === 'state') {
            $distData = (clone $scopedQuery)
                ->leftJoin('districts', 'field_visits.district_id', '=', 'districts.district_id')
                ->select(
                    DB::raw("COALESCE(districts.district_name, 'Unassigned') as geo_name"),
                    DB::raw('COUNT(field_visits.id) as cnt')
                )
                ->groupBy('districts.district_name')
                ->orderByDesc('cnt')
                ->take(15)
                ->get();
            $geoChartLabels = $distData->pluck('geo_name')->toArray();
            $geoChartData = $distData->pluck('cnt')->map(fn($v) => (int)$v)->toArray();
        } elseif ($jurisdiction['level'] === 'district') {
            $subdivData = (clone $scopedQuery)
                ->leftJoin('blocks', 'field_visits.block_id', '=', 'blocks.block_id')
                ->leftJoin('subdivisions', 'blocks.subdivision_id', '=', 'subdivisions.subdivision_id')
                ->select(
                    DB::raw("COALESCE(subdivisions.subdivision_name, 'Unassigned') as geo_name"),
                    DB::raw('COUNT(field_visits.id) as cnt')
                )
                ->groupBy('subdivisions.subdivision_name')
                ->orderByDesc('cnt')
                ->get();
            $geoChartLabels = $subdivData->pluck('geo_name')->toArray();
            $geoChartData = $subdivData->pluck('cnt')->map(fn($v) => (int)$v)->toArray();
        } else {
            // Block / ULB / GP level
            $unitData = (clone $scopedQuery)
                ->leftJoin('blocks', 'field_visits.block_id', '=', 'blocks.block_id')
                ->leftJoin('municipalities', 'field_visits.municipality_id', '=', 'municipalities.municipality_id')
                ->select(
                    DB::raw("COALESCE(blocks.block_name, municipalities.municipality_name, 'Other') as geo_name"),
                    DB::raw('COUNT(field_visits.id) as cnt')
                )
                ->groupBy('geo_name')
                ->orderByDesc('cnt')
                ->get();
            $geoChartLabels = $unitData->pluck('geo_name')->toArray();
            $geoChartData = $unitData->pluck('cnt')->map(fn($v) => (int)$v)->toArray();
        }

        $geoChart = [
            'labels' => $geoChartLabels,
            'data'   => $geoChartData,
        ];

        return [
            'rural_vs_urban'  => $ruralVsUrban,
            'visit_types'     => $visitTypesChart,
            'categories'      => $categoryChart,
            'monthly_trend'   => $monthlyTrendChart,
            'geographical'    => $geoChart,
        ];
    }

    /**
     * Compute summary table breakdown rows tailored to the user's role hierarchy.
     * State -> District-wise
     * District -> Subdivision-wise
     * Subdivision -> Block & ULB-wise
     * Block -> GP-wise
     * ULB -> Ward-wise
     */
    public function getSummaryBreakdown(Builder $scopedQuery, ?User $user): array
    {
        $jurisdiction = $this->getUserJurisdiction($user);
        $level = $jurisdiction['level'];

        $rows = [];

        if ($level === 'state') {
            $districts = District::orderBy('district_name')->get();
            foreach ($districts as $d) {
                $q = (clone $scopedQuery)->where('field_visits.district_id', $d->district_id);
                $total = (clone $q)->count();
                if ($total === 0 && empty(request()->all())) {
                    // Include all districts even if 0 visits
                }
                $rural = (clone $q)->where('field_visits.address_type', 1)->count();
                $urban = (clone $q)->where('field_visits.address_type', 2)->count();
                $uniqueInst = (clone $q)->whereNotNull('field_visits.institution_id')->distinct()->count('field_visits.institution_id');
                $uniqueOff = (clone $q)->whereNotNull('field_visits.visiting_officer_name')->distinct()->count('field_visits.visiting_officer_name');

                $rows[] = [
                    'id'               => $d->district_id,
                    'name'             => $d->district_name,
                    'type'             => 'District',
                    'total_visits'     => $total,
                    'rural_visits'     => $rural,
                    'urban_visits'     => $urban,
                    'unique_institutes'=> $uniqueInst,
                    'unique_officers'  => $uniqueOff,
                    'drilldown_param'  => ['district_id' => $d->district_id],
                ];
            }
        } elseif ($level === 'district') {
            $distId = $jurisdiction['district_id'];
            $subdivisions = Subdivision::where('district_id', $distId)->orderBy('subdivision_name')->get();
            foreach ($subdivisions as $sub) {
                $blockIds = Block::where('subdivision_id', $sub->subdivision_id)->pluck('block_id')->toArray();
                $muniIds = Municipality::where('subdivision_id', $sub->subdivision_id)->pluck('municipality_id')->toArray();

                $q = (clone $scopedQuery)->where(function (Builder $b) use ($blockIds, $muniIds) {
                    $b->whereIn('field_visits.block_id', $blockIds)
                      ->orWhereIn('field_visits.municipality_id', $muniIds);
                });

                $total = (clone $q)->count();
                $rural = (clone $q)->where('field_visits.address_type', 1)->count();
                $urban = (clone $q)->where('field_visits.address_type', 2)->count();
                $uniqueInst = (clone $q)->whereNotNull('field_visits.institution_id')->distinct()->count('field_visits.institution_id');
                $uniqueOff = (clone $q)->whereNotNull('field_visits.visiting_officer_name')->distinct()->count('field_visits.visiting_officer_name');

                $rows[] = [
                    'id'               => $sub->subdivision_id,
                    'name'             => $sub->subdivision_name,
                    'type'             => 'Subdivision',
                    'total_visits'     => $total,
                    'rural_visits'     => $rural,
                    'urban_visits'     => $urban,
                    'unique_institutes'=> $uniqueInst,
                    'unique_officers'  => $uniqueOff,
                    'drilldown_param'  => ['subdivision_id' => $sub->subdivision_id],
                ];
            }
        } elseif ($level === 'subdivision') {
            $subId = $jurisdiction['subdivision_id'];
            $blocks = Block::where('subdivision_id', $subId)->orderBy('block_name')->get();
            foreach ($blocks as $b) {
                $q = (clone $scopedQuery)->where('field_visits.block_id', $b->block_id);
                $total = (clone $q)->count();
                $rural = (clone $q)->where('field_visits.address_type', 1)->count();
                $uniqueInst = (clone $q)->whereNotNull('field_visits.institution_id')->distinct()->count('field_visits.institution_id');
                $uniqueOff = (clone $q)->whereNotNull('field_visits.visiting_officer_name')->distinct()->count('field_visits.visiting_officer_name');

                $rows[] = [
                    'id'               => $b->block_id,
                    'name'             => $b->block_name . ' (Block)',
                    'type'             => 'Block (Rural)',
                    'total_visits'     => $total,
                    'rural_visits'     => $rural,
                    'urban_visits'     => 0,
                    'unique_institutes'=> $uniqueInst,
                    'unique_officers'  => $uniqueOff,
                    'drilldown_param'  => ['block_id' => $b->block_id],
                ];
            }
            $munis = Municipality::where('subdivision_id', $subId)->orderBy('municipality_name')->get();
            foreach ($munis as $m) {
                $q = (clone $scopedQuery)->where('field_visits.municipality_id', $m->municipality_id);
                $total = (clone $q)->count();
                $urban = (clone $q)->where('field_visits.address_type', 2)->count();
                $uniqueInst = (clone $q)->whereNotNull('field_visits.institution_id')->distinct()->count('field_visits.institution_id');
                $uniqueOff = (clone $q)->whereNotNull('field_visits.visiting_officer_name')->distinct()->count('field_visits.visiting_officer_name');

                $rows[] = [
                    'id'               => $m->municipality_id,
                    'name'             => $m->municipality_name . ' (ULB)',
                    'type'             => 'Municipality (Urban)',
                    'total_visits'     => $total,
                    'rural_visits'     => 0,
                    'urban_visits'     => $urban,
                    'unique_institutes'=> $uniqueInst,
                    'unique_officers'  => $uniqueOff,
                    'drilldown_param'  => ['municipality_id' => $m->municipality_id],
                ];
            }
        } elseif ($level === 'block') {
            $blockId = $jurisdiction['block_id'];
            $gps = Grampanchayat::where('block_id', $blockId)->orderBy('gp_name')->get();
            foreach ($gps as $gp) {
                $q = (clone $scopedQuery)->where('field_visits.gp_id', $gp->gp_id);
                $total = (clone $q)->count();
                $uniqueInst = (clone $q)->whereNotNull('field_visits.institution_id')->distinct()->count('field_visits.institution_id');
                $uniqueOff = (clone $q)->whereNotNull('field_visits.visiting_officer_name')->distinct()->count('field_visits.visiting_officer_name');

                $rows[] = [
                    'id'               => $gp->gp_id,
                    'name'             => $gp->gp_name,
                    'type'             => 'Gram Panchayat',
                    'total_visits'     => $total,
                    'rural_visits'     => $total,
                    'urban_visits'     => 0,
                    'unique_institutes'=> $uniqueInst,
                    'unique_officers'  => $uniqueOff,
                    'drilldown_param'  => ['gp_id' => $gp->gp_id],
                ];
            }
        } elseif ($level === 'ulb') {
            $muniId = $jurisdiction['municipality_id'];
            $wards = WardMaster::where('municipal_area_code', $muniId)->orderBy('ward_name')->get();
            foreach ($wards as $w) {
                $q = (clone $scopedQuery)->where('field_visits.ward_id', $w->ward_code);
                $total = (clone $q)->count();
                $uniqueInst = (clone $q)->whereNotNull('field_visits.institution_id')->distinct()->count('field_visits.institution_id');
                $uniqueOff = (clone $q)->whereNotNull('field_visits.visiting_officer_name')->distinct()->count('field_visits.visiting_officer_name');

                $rows[] = [
                    'id'               => $w->ward_code,
                    'name'             => $w->ward_name ?: ('Ward ' . $w->ward_code),
                    'type'             => 'Ward',
                    'total_visits'     => $total,
                    'rural_visits'     => 0,
                    'urban_visits'     => $total,
                    'unique_institutes'=> $uniqueInst,
                    'unique_officers'  => $uniqueOff,
                    'drilldown_param'  => ['ward_id' => $w->ward_code],
                ];
            }
        }

        return $rows;
    }

    /**
     * Compute hierarchical drill-down data for Report 2: Geographical Analysis.
     */
    public function getGeographicalAnalysis(Request $request, ?User $user): array
    {
        $jurisdiction = $this->getUserJurisdiction($user);
        $level = $jurisdiction['level'];

        // Determine current view level based on query parameters and user constraints
        $districtId     = $request->input('drill_district_id');
        $subdivisionId  = $request->input('drill_subdivision_id');
        $blockId        = $request->input('drill_block_id');
        $municipalityId = $request->input('drill_municipality_id');
        $gpId           = $request->input('drill_gp_id');
        $wardId         = $request->input('drill_ward_id');

        // Security clamp: restrict lower roles from drilling outside their domain
        if ($level === 'district') {
            $districtId = $jurisdiction['district_id'];
        } elseif ($level === 'subdivision') {
            $subdivisionId = $jurisdiction['subdivision_id'];
            $districtId = Subdivision::where('subdivision_id', $subdivisionId)->value('district_id');
        } elseif ($level === 'block') {
            $blockId = $jurisdiction['block_id'];
            $districtId = Block::where('block_id', $blockId)->value('district_id');
            $subdivisionId = Block::where('block_id', $blockId)->value('subdivision_id');
        } elseif ($level === 'ulb') {
            $municipalityId = $jurisdiction['municipality_id'];
            $districtId = Municipality::where('municipality_id', $municipalityId)->value('district_id');
            $subdivisionId = Municipality::where('municipality_id', $municipalityId)->value('subdivision_id');
        }

        $baseVisitQuery = FieldVisit::query();
        $this->applyVisitScope($baseVisitQuery, $user);

        $breadcrumbs = [];
        $currentLevel = 'state';
        $items = [];

        // Determine current drill stage
        if ($gpId) {
            $currentLevel = 'gp_visits';
            $gp = Grampanchayat::find($gpId);
            $breadcrumbs[] = ['title' => 'GP: ' . ($gp ? $gp->gp_name : $gpId), 'url' => null];
            $visits = (clone $baseVisitQuery)
                ->where('field_visits.gp_id', $gpId)
                ->with(['visitType', 'district', 'block', 'visitingOfficer', 'institute'])
                ->orderByDesc('visit_date')
                ->paginate(20)
                ->withQueryString();

            return [
                'current_level' => $currentLevel,
                'breadcrumbs'   => $breadcrumbs,
                'visits'        => $visits,
                'items'         => [],
            ];
        }

        if ($wardId) {
            $currentLevel = 'ward_visits';
            $ward = WardMaster::where('ward_code', $wardId)->first();
            $breadcrumbs[] = ['title' => 'Ward: ' . ($ward ? $ward->ward_name : $wardId), 'url' => null];
            $visits = (clone $baseVisitQuery)
                ->where('field_visits.ward_id', $wardId)
                ->with(['visitType', 'district', 'municipality', 'visitingOfficer', 'institute'])
                ->orderByDesc('visit_date')
                ->paginate(20)
                ->withQueryString();

            return [
                'current_level' => $currentLevel,
                'breadcrumbs'   => $breadcrumbs,
                'visits'        => $visits,
                'items'         => [],
            ];
        }

        if ($blockId) {
            $currentLevel = 'block_gps';
            $block = Block::find($blockId);
            $breadcrumbs[] = ['title' => 'Block: ' . ($block ? $block->block_name : $blockId), 'url' => null];
            $gps = Grampanchayat::where('block_id', $blockId)->orderBy('gp_name')->get();
            foreach ($gps as $gp) {
                $q = (clone $baseVisitQuery)->where('field_visits.gp_id', $gp->gp_id);
                $total = (clone $q)->count();
                $rural = (clone $q)->where('field_visits.address_type', 1)->count();
                $items[] = [
                    'id'           => $gp->gp_id,
                    'name'         => $gp->gp_name,
                    'type'         => 'Gram Panchayat',
                    'total_visits' => $total,
                    'rural_visits' => $rural,
                    'urban_visits' => 0,
                    'next_param'   => ['drill_gp_id' => $gp->gp_id],
                ];
            }

            return [
                'current_level' => $currentLevel,
                'breadcrumbs'   => $breadcrumbs,
                'items'         => $items,
                'visits'        => null,
            ];
        }

        if ($municipalityId) {
            $currentLevel = 'ulb_wards';
            $muni = Municipality::find($municipalityId);
            $breadcrumbs[] = ['title' => 'ULB: ' . ($muni ? $muni->municipality_name : $municipalityId), 'url' => null];
            $wards = WardMaster::where('municipal_area_code', $municipalityId)->orderBy('ward_name')->get();
            foreach ($wards as $ward) {
                $q = (clone $baseVisitQuery)->where('field_visits.ward_id', $ward->ward_code);
                $total = (clone $q)->count();
                $urban = (clone $q)->where('field_visits.address_type', 2)->count();
                $items[] = [
                    'id'           => $ward->ward_code,
                    'name'         => $ward->ward_name ?: ('Ward ' . $ward->ward_code),
                    'type'         => 'Ward',
                    'total_visits' => $total,
                    'rural_visits' => 0,
                    'urban_visits' => $urban,
                    'next_param'   => ['drill_ward_id' => $ward->ward_code],
                ];
            }

            return [
                'current_level' => $currentLevel,
                'breadcrumbs'   => $breadcrumbs,
                'items'         => $items,
                'visits'        => null,
            ];
        }

        if ($subdivisionId) {
            $currentLevel = 'subdivision_units';
            $subdiv = Subdivision::find($subdivisionId);
            $breadcrumbs[] = ['title' => 'Subdivision: ' . ($subdiv ? $subdiv->subdivision_name : $subdivisionId), 'url' => null];
            $blocks = Block::where('subdivision_id', $subdivisionId)->orderBy('block_name')->get();
            foreach ($blocks as $b) {
                $q = (clone $baseVisitQuery)->where('field_visits.block_id', $b->block_id);
                $items[] = [
                    'id'           => $b->block_id,
                    'name'         => $b->block_name,
                    'type'         => 'Block (Rural)',
                    'total_visits' => (clone $q)->count(),
                    'rural_visits' => (clone $q)->where('field_visits.address_type', 1)->count(),
                    'urban_visits' => 0,
                    'next_param'   => ['drill_block_id' => $b->block_id],
                ];
            }
            $munis = Municipality::where('subdivision_id', $subdivisionId)->orderBy('municipality_name')->get();
            foreach ($munis as $m) {
                $q = (clone $baseVisitQuery)->where('field_visits.municipality_id', $m->municipality_id);
                $items[] = [
                    'id'           => $m->municipality_id,
                    'name'         => $m->municipality_name,
                    'type'         => 'Municipality (Urban)',
                    'total_visits' => (clone $q)->count(),
                    'rural_visits' => 0,
                    'urban_visits' => (clone $q)->where('field_visits.address_type', 2)->count(),
                    'next_param'   => ['drill_municipality_id' => $m->municipality_id],
                ];
            }

            return [
                'current_level' => $currentLevel,
                'breadcrumbs'   => $breadcrumbs,
                'items'         => $items,
                'visits'        => null,
            ];
        }

        if ($districtId) {
            $currentLevel = 'district_subdivisions';
            $dist = District::find($districtId);
            $breadcrumbs[] = ['title' => 'District: ' . ($dist ? $dist->district_name : $districtId), 'url' => null];
            $subdivisions = Subdivision::where('district_id', $districtId)->orderBy('subdivision_name')->get();
            foreach ($subdivisions as $sub) {
                $blockIds = Block::where('subdivision_id', $sub->subdivision_id)->pluck('block_id')->toArray();
                $muniIds = Municipality::where('subdivision_id', $sub->subdivision_id)->pluck('municipality_id')->toArray();

                $q = (clone $baseVisitQuery)->where(function (Builder $b) use ($blockIds, $muniIds) {
                    $b->whereIn('field_visits.block_id', $blockIds)
                      ->orWhereIn('field_visits.municipality_id', $muniIds);
                });

                $items[] = [
                    'id'           => $sub->subdivision_id,
                    'name'         => $sub->subdivision_name,
                    'type'         => 'Subdivision',
                    'total_visits' => (clone $q)->count(),
                    'rural_visits' => (clone $q)->where('field_visits.address_type', 1)->count(),
                    'urban_visits' => (clone $q)->where('field_visits.address_type', 2)->count(),
                    'next_param'   => ['drill_subdivision_id' => $sub->subdivision_id],
                ];
            }

            return [
                'current_level' => $currentLevel,
                'breadcrumbs'   => $breadcrumbs,
                'items'         => $items,
                'visits'        => null,
            ];
        }

        // Top level: State (Districts list)
        $currentLevel = 'state_districts';
        $districts = District::orderBy('district_name')->get();
        foreach ($districts as $d) {
            $q = (clone $baseVisitQuery)->where('field_visits.district_id', $d->district_id);
            $items[] = [
                'id'           => $d->district_id,
                'name'         => $d->district_name,
                'type'         => 'District',
                'total_visits' => (clone $q)->count(),
                'rural_visits' => (clone $q)->where('field_visits.address_type', 1)->count(),
                'urban_visits' => (clone $q)->where('field_visits.address_type', 2)->count(),
                'next_param'   => ['drill_district_id' => $d->district_id],
            ];
        }

        return [
            'current_level' => $currentLevel,
            'breadcrumbs'   => $breadcrumbs,
            'items'         => $items,
            'visits'        => null,
        ];
    }

    /**
     * Compute data for Report 4: Institution Coverage Report.
     */
    public function getInstitutionCoverage(Request $request, ?User $user): array
    {
        $institutesBaseQuery = FieldVisitInstitute::query();
        $this->applyInstituteScope($institutesBaseQuery, $user);

        // Filter institutes by category, district, block, etc. if selected
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

        $totalInstitutes = (clone $institutesBaseQuery)->count();

        // Get visited institute IDs within user's visit scope
        $visitsBaseQuery = FieldVisit::query()->whereNotNull('field_visits.institution_id');
        $this->applyVisitScope($visitsBaseQuery, $user);

        // Date range filter for coverage if applied
        if ($request->filled('date_from')) {
            $visitsBaseQuery->whereDate('field_visits.visit_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $visitsBaseQuery->whereDate('field_visits.visit_date', '<=', $request->input('date_to'));
        }

        // Distinct institution IDs visited
        $visitedInstituteIds = (clone $visitsBaseQuery)
            ->distinct()
            ->pluck('institution_id')
            ->toArray();

        // Count eligible institutes visited
        $visitedCount = (clone $institutesBaseQuery)->whereIn('id', $visitedInstituteIds)->count();
        $unvisitedCount = max(0, $totalInstitutes - $visitedCount);
        $coveragePercent = $totalInstitutes > 0 ? round(($visitedCount / $totalInstitutes) * 100, 1) : 0.0;

        // Unmatched/Ad-hoc visits where institution_id is null
        $unlinkedVisitsQuery = FieldVisit::query()->whereNull('field_visits.institution_id');
        $this->applyVisitScope($unlinkedVisitsQuery, $user);
        $unlinkedVisitsCount = $unlinkedVisitsQuery->count();

        // Category-wise Coverage breakdown
        $categories = (clone $institutesBaseQuery)
            ->select('category_id', 'category_name', DB::raw('COUNT(id) as total_cat_institutes'))
            ->groupBy('category_id', 'category_name')
            ->orderBy('category_name')
            ->get();

        $categoryBreakdown = [];
        foreach ($categories as $cat) {
            $catTotal = $cat->total_cat_institutes;
            $catVisited = FieldVisitInstitute::where('category_id', $cat->category_id)
                ->whereIn('id', $visitedInstituteIds)
                ->count();
            $catUnvisited = max(0, $catTotal - $catVisited);
            $catPct = $catTotal > 0 ? round(($catVisited / $catTotal) * 100, 1) : 0.0;

            $categoryBreakdown[] = [
                'category_id'    => $cat->category_id,
                'category_name'  => $cat->category_name,
                'total'          => $catTotal,
                'visited'        => $catVisited,
                'unvisited'      => $catUnvisited,
                'coverage_pct'   => $catPct,
            ];
        }

        // Paginated list of institutes with visit counts and last visit date
        $institutesListQuery = (clone $institutesBaseQuery)
            ->with(['district', 'block', 'municipality'])
            ->withCount(['fieldVisits as visit_count' => function ($q) use ($user) {
                $this->applyVisitScope($q, $user);
            }])
            ->withMax(['fieldVisits as last_visit_date' => function ($q) use ($user) {
                $this->applyVisitScope($q, $user);
            }], 'visit_date');

        if ($request->input('coverage_filter') === 'visited') {
            $institutesListQuery->whereIn('id', $visitedInstituteIds);
        } elseif ($request->input('coverage_filter') === 'unvisited') {
            $institutesListQuery->whereNotIn('id', $visitedInstituteIds);
        }

        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $institutesListQuery->where(function (Builder $q) use ($s) {
                $q->where('institute_name', 'like', "%{$s}%")
                  ->orWhere('institute_code', 'like', "%{$s}%")
                  ->orWhere('category_name', 'like', "%{$s}%");
            });
        }

        $institutes = $institutesListQuery->orderBy('institute_name')->paginate(20)->withQueryString();

        return [
            'total_institutes'    => $totalInstitutes,
            'visited_count'       => $visitedCount,
            'unvisited_count'     => $unvisitedCount,
            'coverage_percent'    => $coveragePercent,
            'unlinked_visits_count' => $unlinkedVisitsCount,
            'category_breakdown'  => $categoryBreakdown,
            'institutes'          => $institutes,
        ];
    }

    /**
     * Compute data for Report 5: Date-wise Visit Report.
     */
    public function getDatewiseReport(Builder $scopedQuery, Request $request): array
    {
        // Daily visit totals
        $dailyTotals = (clone $scopedQuery)
            ->whereNotNull('field_visits.visit_date')
            ->select(
                DB::raw('DATE(field_visits.visit_date) as date_val'),
                DB::raw('COUNT(field_visits.id) as total_visits'),
                DB::raw('SUM(CASE WHEN field_visits.address_type = 1 THEN 1 ELSE 0 END) as rural_visits'),
                DB::raw('SUM(CASE WHEN field_visits.address_type = 2 THEN 1 ELSE 0 END) as urban_visits'),
                DB::raw('COUNT(DISTINCT field_visits.institution_id) as unique_institutions'),
                DB::raw('COUNT(DISTINCT field_visits.visiting_officer_name) as unique_officers')
            )
            ->groupBy(DB::raw('DATE(field_visits.visit_date)'))
            ->orderByDesc('date_val')
            ->get();

        // Monthly visit totals
        $monthlyTotals = (clone $scopedQuery)
            ->whereNotNull('field_visits.visit_date')
            ->select(
                DB::raw("DATE_FORMAT(field_visits.visit_date, '%Y-%m') as month_key"),
                DB::raw("DATE_FORMAT(field_visits.visit_date, '%M %Y') as month_name"),
                DB::raw('COUNT(field_visits.id) as total_visits'),
                DB::raw('SUM(CASE WHEN field_visits.address_type = 1 THEN 1 ELSE 0 END) as rural_visits'),
                DB::raw('SUM(CASE WHEN field_visits.address_type = 2 THEN 1 ELSE 0 END) as urban_visits'),
                DB::raw('COUNT(DISTINCT field_visits.institution_id) as unique_institutions'),
                DB::raw('COUNT(DISTINCT field_visits.visiting_officer_name) as unique_officers')
            )
            ->groupBy('month_key', 'month_name')
            ->orderByDesc('month_key')
            ->get();

        // Detailed visits paginated listing
        $detailedVisits = (clone $scopedQuery)
            ->with(['visitType', 'district', 'block', 'municipality', 'visitingOfficer', 'institute'])
            ->orderByDesc('visit_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return [
            'daily_totals'    => $dailyTotals,
            'monthly_totals'  => $monthlyTotals,
            'detailed_visits' => $detailedVisits,
        ];
    }
}

