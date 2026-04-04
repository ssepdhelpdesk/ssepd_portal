<?php

namespace App\Http\Controllers\Dashboard_Controllers\PensionFundManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PensionFundsRequirement;
use App\Models\PensionFundRequirementDates;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class PensionFundRequirementDisbursementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pension_fund_requirement_report_of_districts(Request $request)
    {
        $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('is_active', 'active')
        ->orderBy('id', 'desc')
        ->get();

        $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

        $activeConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('for_the_month', $month)
        ->first();

        if (!$activeConfig) {
            return redirect()->back()->with('error', 'Submission dates are not configured for the selected month. Please contact admin.');
        }

        $approve_status = $request->approve_status;

        if ($request->ajax() || $request->wantsJson()) {


            $query = District::select('districts.district_id', 'districts.district_name',

                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_below_80_years),0) as mbpy_oap_below_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_above_80_years),0) as mbpy_oap_above_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp),0) as mbpy_wp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp),0) as mbpy_dp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_below_80_percent),0) as mbpy_sdp_below_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_above_80_percent),0) as mbpy_sdp_above_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdoap),0) as mbpy_sdoap'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_clp),0) as mbpy_clp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp_aids),0) as mbpy_wp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp_aids),0) as mbpy_dp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_unmarried_women),0) as mbpy_unmarried_women'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_orphan_due_to_covide),0) as mbpy_orphan_due_to_covide'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_widow_due_to_covid),0) as mbpy_widow_due_to_covid'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_divorce_or_destitute),0) as mbpy_divorce_or_destitute'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_transgender),0) as mbpy_transgender')
            )
            ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month, $approve_status) {
                $join->on('districts.district_id', '=', 'pfr.district_id')
                ->where('pfr.for_the_month', $month)
                ->where('pfr.approve_status', $approve_status)
                ->where('pfr.status', 1);
            })
            ->groupBy('districts.district_id', 'districts.district_name')
            ->orderBy('districts.district_name', 'asc');

            $user = auth()->user();
            $userRole = $user->role_id;
            if (in_array($userRole, [1, 2, 12, 13, 14, 15, 25])) {

            } elseif (in_array($userRole, [9, 11])) {
                $query->where('districts.district_id', $user->posted_district);
            }
            return DataTables::of($query)

            ->addIndexColumn()
            ->addColumn('funds_mbpy_oap_below_80_years', fn($row) => $row->mbpy_oap_below_80_years * 1000)
            ->addColumn('funds_mbpy_oap_above_80_years', fn($row) => $row->mbpy_oap_above_80_years * 3500)
            ->addColumn('funds_mbpy_wp', fn($row) => $row->mbpy_wp * 1000)
            ->addColumn('funds_mbpy_dp', fn($row) => $row->mbpy_dp * 1000)
            ->addColumn('funds_mbpy_sdp_below_80_percent', fn($row) => $row->mbpy_sdp_below_80_percent * 1200)
            ->addColumn('funds_mbpy_sdp_above_80_percent', fn($row) => $row->mbpy_sdp_above_80_percent * 3500)
            ->addColumn('funds_mbpy_sdoap', fn($row) => $row->mbpy_sdoap * 3500)
            ->addColumn('funds_mbpy_clp', fn($row) => $row->mbpy_clp * 1000)
            ->addColumn('funds_mbpy_wp_aids', fn($row) => $row->mbpy_wp_aids * 1000)
            ->addColumn('funds_mbpy_dp_aids', fn($row) => $row->mbpy_dp_aids * 1000)
            ->addColumn('funds_mbpy_unmarried_women', fn($row) => $row->mbpy_unmarried_women * 1000)
            ->addColumn('funds_mbpy_orphan_due_to_covide', fn($row) => $row->mbpy_orphan_due_to_covide * 1000)
            ->addColumn('funds_mbpy_widow_due_to_covid', fn($row) => $row->mbpy_widow_due_to_covid * 1000)
            ->addColumn('funds_mbpy_divorce_or_destitute', fn($row) => $row->mbpy_divorce_or_destitute * 1000)
            ->addColumn('funds_mbpy_transgender', fn($row) => $row->mbpy_transgender * 1000)
            ->addColumn('total_beneficiaries', function ($row) {
                return
                ($row->mbpy_oap_below_80_years) +
                ($row->mbpy_oap_above_80_years) +
                ($row->mbpy_wp) +
                ($row->mbpy_dp) +
                ($row->mbpy_sdp_below_80_percent) +
                ($row->mbpy_sdp_above_80_percent) +
                ($row->mbpy_sdoap) +
                ($row->mbpy_clp) +
                ($row->mbpy_wp_aids) +
                ($row->mbpy_dp_aids) +
                ($row->mbpy_unmarried_women) +
                ($row->mbpy_orphan_due_to_covide) +
                ($row->mbpy_widow_due_to_covid) +
                ($row->mbpy_divorce_or_destitute) +
                ($row->mbpy_transgender);
            })
            ->addColumn('total_fund', function ($row) {
                return
                ($row->mbpy_oap_below_80_years * 1000) +
                ($row->mbpy_oap_above_80_years * 3500) +
                ($row->mbpy_wp * 1000) +
                ($row->mbpy_dp * 1000) +
                ($row->mbpy_sdp_below_80_percent * 1200) +
                ($row->mbpy_sdp_above_80_percent * 3500) +
                ($row->mbpy_sdoap * 3500) +
                ($row->mbpy_clp * 1000) +
                ($row->mbpy_wp_aids * 1000) +
                ($row->mbpy_dp_aids * 1000) +
                ($row->mbpy_unmarried_women * 1000) +
                ($row->mbpy_orphan_due_to_covide * 1000) +
                ($row->mbpy_widow_due_to_covid * 1000) +
                ($row->mbpy_divorce_or_destitute * 1000) +
                ($row->mbpy_transgender * 1000);
            })
            ->make(true);
        }

        return view('dashboard.pension.pension_fund_management.pension_fund_requirement_report_of_districts', compact('dateConfig', 'month', 'approve_status'));
    }

    public function pension_fund_requirement_report_of_district(Request $request)
    {
        $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('is_active', 'active')
        ->orderBy('id', 'desc')
        ->get();

        $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

        $activeConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('for_the_month', $month)
        ->first();

        if (!$activeConfig) {
            return redirect()->back()->with('error', 'Submission dates are not configured for the selected month. Please contact admin.');
        }

        $approve_status = $request->approve_status;

        if ($request->ajax() || $request->wantsJson()) {

            $baseQuery = District::select(
                'districts.district_id',
                'districts.district_name',

                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_below_80_years),0) as mbpy_oap_below_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_above_80_years),0) as mbpy_oap_above_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp),0) as mbpy_wp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp),0) as mbpy_dp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_below_80_percent),0) as mbpy_sdp_below_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_above_80_percent),0) as mbpy_sdp_above_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdoap),0) as mbpy_sdoap'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_clp),0) as mbpy_clp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp_aids),0) as mbpy_wp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp_aids),0) as mbpy_dp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_unmarried_women),0) as mbpy_unmarried_women'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_orphan_due_to_covide),0) as mbpy_orphan_due_to_covide'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_widow_due_to_covid),0) as mbpy_widow_due_to_covid'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_divorce_or_destitute),0) as mbpy_divorce_or_destitute'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_transgender),0) as mbpy_transgender')
            )
            ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month, $approve_status) {

                $join->on('districts.district_id', '=', 'pfr.district_id')
                ->where('pfr.for_the_month', $month)
                ->where('pfr.status', 1);

                if ($approve_status !== null && $approve_status !== '') {
                    $join->where('pfr.approve_status', $approve_status);
                }
            })
            ->groupBy('districts.district_id', 'districts.district_name');

            $user = auth()->user();
            if (in_array($user->role_id, [9, 11])) {
                $baseQuery->where('districts.district_id', $user->posted_district);
            }

            $totalsRaw = \DB::table('districts')
            ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month, $approve_status) {

                $join->on('districts.district_id', '=', 'pfr.district_id')
                ->where('pfr.for_the_month', $month)
                ->where('pfr.status', 1);

                if ($approve_status !== null && $approve_status !== '') {
                    $join->where('pfr.approve_status', $approve_status);
                }
            })
            ->when(in_array($user->role_id, [9, 11]), function ($q) use ($user) {
                $q->where('districts.district_id', $user->posted_district);
            })
            ->select(
                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_below_80_years),0) as mbpy_oap_below_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_oap_above_80_years),0) as mbpy_oap_above_80_years'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp),0) as mbpy_wp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp),0) as mbpy_dp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_below_80_percent),0) as mbpy_sdp_below_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_above_80_percent),0) as mbpy_sdp_above_80_percent'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_sdoap),0) as mbpy_sdoap'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_clp),0) as mbpy_clp'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_wp_aids),0) as mbpy_wp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_dp_aids),0) as mbpy_dp_aids'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_unmarried_women),0) as mbpy_unmarried_women'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_orphan_due_to_covide),0) as mbpy_orphan_due_to_covide'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_widow_due_to_covid),0) as mbpy_widow_due_to_covid'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_divorce_or_destitute),0) as mbpy_divorce_or_destitute'),
                \DB::raw('COALESCE(SUM(pfr.mbpy_transgender),0) as mbpy_transgender')
            )
            ->first();

            $totals = [
                'mbpy_oap_below_80_years' => $totalsRaw->mbpy_oap_below_80_years,
                'funds_mbpy_oap_below_80_years' => $totalsRaw->mbpy_oap_below_80_years * 1000,

                'mbpy_oap_above_80_years' => $totalsRaw->mbpy_oap_above_80_years,
                'funds_mbpy_oap_above_80_years' => $totalsRaw->mbpy_oap_above_80_years * 3500,

                'mbpy_wp' => $totalsRaw->mbpy_wp,
                'funds_mbpy_wp' => $totalsRaw->mbpy_wp * 1000,

                'mbpy_dp' => $totalsRaw->mbpy_dp,
                'funds_mbpy_dp' => $totalsRaw->mbpy_dp * 1000,

                'mbpy_sdp_below_80_percent' => $totalsRaw->mbpy_sdp_below_80_percent,
                'funds_mbpy_sdp_below_80_percent' => $totalsRaw->mbpy_sdp_below_80_percent * 1200,

                'mbpy_sdp_above_80_percent' => $totalsRaw->mbpy_sdp_above_80_percent,
                'funds_mbpy_sdp_above_80_percent' => $totalsRaw->mbpy_sdp_above_80_percent * 3500,

                'mbpy_sdoap' => $totalsRaw->mbpy_sdoap,
                'funds_mbpy_sdoap' => $totalsRaw->mbpy_sdoap * 3500,

                'mbpy_clp' => $totalsRaw->mbpy_clp,
                'funds_mbpy_clp' => $totalsRaw->mbpy_clp * 1000,

                'mbpy_wp_aids' => $totalsRaw->mbpy_wp_aids,
                'funds_mbpy_wp_aids' => $totalsRaw->mbpy_wp_aids * 1000,

                'mbpy_dp_aids' => $totalsRaw->mbpy_dp_aids,
                'funds_mbpy_dp_aids' => $totalsRaw->mbpy_dp_aids * 1000,

                'mbpy_unmarried_women' => $totalsRaw->mbpy_unmarried_women,
                'funds_mbpy_unmarried_women' => $totalsRaw->mbpy_unmarried_women * 1000,

                'mbpy_orphan_due_to_covide' => $totalsRaw->mbpy_orphan_due_to_covide,
                'funds_mbpy_orphan_due_to_covide' => $totalsRaw->mbpy_orphan_due_to_covide * 1000,

                'mbpy_widow_due_to_covid' => $totalsRaw->mbpy_widow_due_to_covid,
                'funds_mbpy_widow_due_to_covid' => $totalsRaw->mbpy_widow_due_to_covid * 1000,

                'mbpy_divorce_or_destitute' => $totalsRaw->mbpy_divorce_or_destitute,
                'funds_mbpy_divorce_or_destitute' => $totalsRaw->mbpy_divorce_or_destitute * 1000,

                'mbpy_transgender' => $totalsRaw->mbpy_transgender,
                'funds_mbpy_transgender' => $totalsRaw->mbpy_transgender * 1000,
            ];

            $totals['total_beneficiaries'] = array_sum([
                $totalsRaw->mbpy_oap_below_80_years,
                $totalsRaw->mbpy_oap_above_80_years,
                $totalsRaw->mbpy_wp,
                $totalsRaw->mbpy_dp,
                $totalsRaw->mbpy_sdp_below_80_percent,
                $totalsRaw->mbpy_sdp_above_80_percent,
                $totalsRaw->mbpy_sdoap,
                $totalsRaw->mbpy_clp,
                $totalsRaw->mbpy_wp_aids,
                $totalsRaw->mbpy_dp_aids,
                $totalsRaw->mbpy_unmarried_women,
                $totalsRaw->mbpy_orphan_due_to_covide,
                $totalsRaw->mbpy_widow_due_to_covid,
                $totalsRaw->mbpy_divorce_or_destitute,
                $totalsRaw->mbpy_transgender,
            ]);

            $totals['total_fund'] =
            $totals['funds_mbpy_oap_below_80_years'] +
            $totals['funds_mbpy_oap_above_80_years'] +
            $totals['funds_mbpy_wp'] +
            $totals['funds_mbpy_dp'] +
            $totals['funds_mbpy_sdp_below_80_percent'] +
            $totals['funds_mbpy_sdp_above_80_percent'] +
            $totals['funds_mbpy_sdoap'] +
            $totals['funds_mbpy_clp'] +
            $totals['funds_mbpy_wp_aids'] +
            $totals['funds_mbpy_dp_aids'] +
            $totals['funds_mbpy_unmarried_women'] +
            $totals['funds_mbpy_orphan_due_to_covide'] +
            $totals['funds_mbpy_widow_due_to_covid'] +
            $totals['funds_mbpy_divorce_or_destitute'] +
            $totals['funds_mbpy_transgender'];

            return DataTables::of($baseQuery)
            ->addIndexColumn()

            ->addColumn('funds_mbpy_oap_below_80_years', fn($row) => $row->mbpy_oap_below_80_years * 1000)
            ->addColumn('funds_mbpy_oap_above_80_years', fn($row) => $row->mbpy_oap_above_80_years * 3500)
            ->addColumn('funds_mbpy_wp', fn($row) => $row->mbpy_wp * 1000)
            ->addColumn('funds_mbpy_dp', fn($row) => $row->mbpy_dp * 1000)
            ->addColumn('funds_mbpy_sdp_below_80_percent', fn($row) => $row->mbpy_sdp_below_80_percent * 1200)
            ->addColumn('funds_mbpy_sdp_above_80_percent', fn($row) => $row->mbpy_sdp_above_80_percent * 3500)
            ->addColumn('funds_mbpy_sdoap', fn($row) => $row->mbpy_sdoap * 3500)
            ->addColumn('funds_mbpy_clp', fn($row) => $row->mbpy_clp * 1000)
            ->addColumn('funds_mbpy_wp_aids', fn($row) => $row->mbpy_wp_aids * 1000)
            ->addColumn('funds_mbpy_dp_aids', fn($row) => $row->mbpy_dp_aids * 1000)
            ->addColumn('funds_mbpy_unmarried_women', fn($row) => $row->mbpy_unmarried_women * 1000)
            ->addColumn('funds_mbpy_orphan_due_to_covide', fn($row) => $row->mbpy_orphan_due_to_covide * 1000)
            ->addColumn('funds_mbpy_widow_due_to_covid', fn($row) => $row->mbpy_widow_due_to_covid * 1000)
            ->addColumn('funds_mbpy_divorce_or_destitute', fn($row) => $row->mbpy_divorce_or_destitute * 1000)
            ->addColumn('funds_mbpy_transgender', fn($row) => $row->mbpy_transgender * 1000)

            ->addColumn('total_beneficiaries', function ($row) {
                return
                $row->mbpy_oap_below_80_years +
                $row->mbpy_oap_above_80_years +
                $row->mbpy_wp +
                $row->mbpy_dp +
                $row->mbpy_sdp_below_80_percent +
                $row->mbpy_sdp_above_80_percent +
                $row->mbpy_sdoap +
                $row->mbpy_clp +
                $row->mbpy_wp_aids +
                $row->mbpy_dp_aids +
                $row->mbpy_unmarried_women +
                $row->mbpy_orphan_due_to_covide +
                $row->mbpy_widow_due_to_covid +
                $row->mbpy_divorce_or_destitute +
                $row->mbpy_transgender;
            })

            ->addColumn('total_fund', function ($row) {
                return
                ($row->mbpy_oap_below_80_years * 1000) +
                ($row->mbpy_oap_above_80_years * 3500) +
                ($row->mbpy_wp * 1000) +
                ($row->mbpy_dp * 1000) +
                ($row->mbpy_sdp_below_80_percent * 1200) +
                ($row->mbpy_sdp_above_80_percent * 3500) +
                ($row->mbpy_sdoap * 3500) +
                ($row->mbpy_clp * 1000) +
                ($row->mbpy_wp_aids * 1000) +
                ($row->mbpy_dp_aids * 1000) +
                ($row->mbpy_unmarried_women * 1000) +
                ($row->mbpy_orphan_due_to_covide * 1000) +
                ($row->mbpy_widow_due_to_covid * 1000) +
                ($row->mbpy_divorce_or_destitute * 1000) +
                ($row->mbpy_transgender * 1000);
            })

            ->with(['totals' => $totals])

            ->filter(function ($query) use ($request) {

                if ($request->has('search') && $request->search['value'] != '') {

                    $search = $request->search['value'];

                    $query->havingRaw("
                        districts.district_name LIKE ?
                        ", ["%{$search}%"]);
                }
            })

            ->make(true);
        }

        return view(
            'dashboard.pension.pension_fund_management.pension_fund_requirement_report_of_district',
            compact('dateConfig', 'month', 'approve_status')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function pension_fund_requirement_report_of_block_ulb(Request $request)
    {
        $dateConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('is_active', 'active')
        ->orderBy('id', 'desc')
        ->get();

        $month = $request->for_the_month ?? ($dateConfig->first()->for_the_month ?? now()->format('F-Y'));

        $activeConfig = PensionFundRequirementDates::where('for_which_page', 'pension_funds_requirements')
        ->where('for_the_month', $month)
        ->first();

        if (!$activeConfig) {
            return redirect()->back()->with('error', 'Submission dates are not configured for the selected month.');
        }

        $approve_status = $request->approve_status;

        if ($request->ajax() || $request->wantsJson()) {

            if ($approve_status == 3) {

                $blocksQuery = \DB::table('blocks as b')
                ->select(
                    'd.district_id',
                    'd.district_name',
                    'b.block_id',
                    'b.block_name',
                    \DB::raw('NULL as municipality_id'),
                    \DB::raw('NULL as municipality_name'),

                    \DB::raw('0 as mbpy_oap_below_80_years'),
                    \DB::raw('0 as mbpy_oap_above_80_years'),
                    \DB::raw('0 as mbpy_wp'),
                    \DB::raw('0 as mbpy_dp'),
                    \DB::raw('0 as mbpy_sdp_below_80_percent'),
                    \DB::raw('0 as mbpy_sdp_above_80_percent'),
                    \DB::raw('0 as mbpy_sdoap'),
                    \DB::raw('0 as mbpy_clp'),
                    \DB::raw('0 as mbpy_wp_aids'),
                    \DB::raw('0 as mbpy_dp_aids'),
                    \DB::raw('0 as mbpy_unmarried_women'),
                    \DB::raw('0 as mbpy_orphan_due_to_covide'),
                    \DB::raw('0 as mbpy_widow_due_to_covid'),
                    \DB::raw('0 as mbpy_divorce_or_destitute'),
                    \DB::raw('0 as mbpy_transgender')
                )
                ->leftJoin('districts as d', 'b.district_id', '=', 'd.district_id')
                ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month) {
                    $join->on('b.block_id', '=', 'pfr.block_id')
                    ->where('pfr.for_the_month', $month)
                    ->where('pfr.status', 1);
                })
                ->where('b.is_active', 'active')
                ->whereNull('pfr.id');

                $ulbQuery = \DB::table('municipalities as m')
                ->select(
                    'd.district_id',
                    'd.district_name',
                    \DB::raw('NULL as block_id'),
                    \DB::raw('NULL as block_name'),
                    'm.municipality_id',
                    'm.municipality_name',

                    \DB::raw('0 as mbpy_oap_below_80_years'),
                    \DB::raw('0 as mbpy_oap_above_80_years'),
                    \DB::raw('0 as mbpy_wp'),
                    \DB::raw('0 as mbpy_dp'),
                    \DB::raw('0 as mbpy_sdp_below_80_percent'),
                    \DB::raw('0 as mbpy_sdp_above_80_percent'),
                    \DB::raw('0 as mbpy_sdoap'),
                    \DB::raw('0 as mbpy_clp'),
                    \DB::raw('0 as mbpy_wp_aids'),
                    \DB::raw('0 as mbpy_dp_aids'),
                    \DB::raw('0 as mbpy_unmarried_women'),
                    \DB::raw('0 as mbpy_orphan_due_to_covide'),
                    \DB::raw('0 as mbpy_widow_due_to_covid'),
                    \DB::raw('0 as mbpy_divorce_or_destitute'),
                    \DB::raw('0 as mbpy_transgender')
                )
                ->leftJoin('districts as d', 'm.district_id', '=', 'd.district_id')
                ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month) {
                    $join->on('m.municipality_id', '=', 'pfr.municipality_id')
                    ->where('pfr.for_the_month', $month)
                    ->where('pfr.status', 1);
                })
                ->where('m.is_active', 'active')
                ->whereNull('pfr.id');

                $query = $blocksQuery->unionAll($ulbQuery);

                $totalsQuery = (object)[
                    'mbpy_oap_below_80_years' => 0,
                    'mbpy_oap_above_80_years' => 0,
                    'mbpy_wp' => 0,
                    'mbpy_dp' => 0,
                    'mbpy_sdp_below_80_percent' => 0,
                    'mbpy_sdp_above_80_percent' => 0,
                    'mbpy_sdoap' => 0,
                    'mbpy_clp' => 0,
                    'mbpy_wp_aids' => 0,
                    'mbpy_dp_aids' => 0,
                    'mbpy_unmarried_women' => 0,
                    'mbpy_orphan_due_to_covide' => 0,
                    'mbpy_widow_due_to_covid' => 0,
                    'mbpy_divorce_or_destitute' => 0,
                    'mbpy_transgender' => 0,
                ];

            } else {

                $query = \DB::table('pension_funds_requirements as pfr')
                ->select(
                    'd.district_id',
                    'd.district_name',
                    'b.block_id',
                    'b.block_name',
                    'm.municipality_id',
                    'm.municipality_name',

                    \DB::raw('COALESCE(SUM(pfr.mbpy_oap_below_80_years),0) as mbpy_oap_below_80_years'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_oap_above_80_years),0) as mbpy_oap_above_80_years'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_wp),0) as mbpy_wp'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_dp),0) as mbpy_dp'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_below_80_percent),0) as mbpy_sdp_below_80_percent'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_above_80_percent),0) as mbpy_sdp_above_80_percent'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_sdoap),0) as mbpy_sdoap'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_clp),0) as mbpy_clp'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_wp_aids),0) as mbpy_wp_aids'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_dp_aids),0) as mbpy_dp_aids'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_unmarried_women),0) as mbpy_unmarried_women'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_orphan_due_to_covide),0) as mbpy_orphan_due_to_covide'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_widow_due_to_covid),0) as mbpy_widow_due_to_covid'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_divorce_or_destitute),0) as mbpy_divorce_or_destitute'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_transgender),0) as mbpy_transgender')
                )
                ->leftJoin('districts as d', 'pfr.district_id', '=', 'd.district_id')
                ->leftJoin('blocks as b', 'pfr.block_id', '=', 'b.block_id')
                ->leftJoin('municipalities as m', 'pfr.municipality_id', '=', 'm.municipality_id')
                ->where('pfr.for_the_month', $month)
                ->where('pfr.approve_status', $approve_status)
                ->where('pfr.status', 1)
                ->groupBy(
                    'd.district_id',
                    'd.district_name',
                    'b.block_id',
                    'b.block_name',
                    'm.municipality_id',
                    'm.municipality_name'
                );

                $totalsQuery = \DB::table('pension_funds_requirements as pfr')
                ->select(
                    \DB::raw('COALESCE(SUM(pfr.mbpy_oap_below_80_years),0) as mbpy_oap_below_80_years'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_oap_above_80_years),0) as mbpy_oap_above_80_years'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_wp),0) as mbpy_wp'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_dp),0) as mbpy_dp'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_below_80_percent),0) as mbpy_sdp_below_80_percent'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_sdp_above_80_percent),0) as mbpy_sdp_above_80_percent'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_sdoap),0) as mbpy_sdoap'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_clp),0) as mbpy_clp'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_wp_aids),0) as mbpy_wp_aids'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_dp_aids),0) as mbpy_dp_aids'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_unmarried_women),0) as mbpy_unmarried_women'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_orphan_due_to_covide),0) as mbpy_orphan_due_to_covide'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_widow_due_to_covid),0) as mbpy_widow_due_to_covid'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_divorce_or_destitute),0) as mbpy_divorce_or_destitute'),
                    \DB::raw('COALESCE(SUM(pfr.mbpy_transgender),0) as mbpy_transgender')
                )
                ->where('pfr.for_the_month', $month)
                ->where('pfr.approve_status', $approve_status)
                ->where('pfr.status', 1);
            }

            $user = auth()->user();
            $userRole = $user->role_id;

            if ($approve_status == 3) {

                if (in_array($userRole, [4, 6])) {
                    $blocksQuery->where('b.block_id', $user->posted_block);
                }

                elseif ($userRole == 5) {
                    $ulbQuery->where('m.municipality_id', $user->posted_municipality);
                }

                elseif (in_array($userRole, [8, 10])) {

                    $blockIds = Block::where('subdivision_id', $user->posted_subdiv)
                    ->pluck('block_id');

                    $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)
                    ->pluck('municipality_id');

                    $blocksQuery->whereIn('b.block_id', $blockIds);
                    $ulbQuery->whereIn('m.municipality_id', $municipalityIds);
                }

                elseif (in_array($userRole, [9, 11])) {
                    $blocksQuery->where('d.district_id', $user->posted_district);
                    $ulbQuery->where('d.district_id', $user->posted_district);
                }

                $query = $blocksQuery->unionAll($ulbQuery);

            } else {

                if (in_array($userRole, [4, 6])) {

                    $query->where('pfr.block_id', $user->posted_block);
                    $totalsQuery->where('pfr.block_id', $user->posted_block);

                }

                elseif ($userRole == 5) {

                    $query->where('pfr.municipality_id', $user->posted_municipality);
                    $totalsQuery->where('pfr.municipality_id', $user->posted_municipality);

                }

                elseif (in_array($userRole, [8, 10])) {

                    $blockIds = Block::where('subdivision_id', $user->posted_subdiv)
                    ->pluck('block_id');

                    $municipalityIds = Municipality::where('subdivision_id', $user->posted_subdiv)
                    ->pluck('municipality_id');

                    $query->where(function ($q) use ($blockIds, $municipalityIds) {
                        $q->whereIn('pfr.block_id', $blockIds)
                        ->orWhereIn('pfr.municipality_id', $municipalityIds);
                    });

                    $totalsQuery->where(function ($q) use ($blockIds, $municipalityIds) {
                        $q->whereIn('pfr.block_id', $blockIds)
                        ->orWhereIn('pfr.municipality_id', $municipalityIds);
                    });
                }

                elseif (in_array($userRole, [9, 11])) {

                    $query->where('pfr.district_id', $user->posted_district);
                    $totalsQuery->where('pfr.district_id', $user->posted_district);
                }

            }

            $totalsQuery = ($approve_status == 3) ? $totalsQuery : $totalsQuery->first();

            $totals = [
                'mbpy_oap_below_80_years' => $totalsQuery->mbpy_oap_below_80_years,
                'funds_mbpy_oap_below_80_years' => $totalsQuery->mbpy_oap_below_80_years * 1000,

                'mbpy_oap_above_80_years' => $totalsQuery->mbpy_oap_above_80_years,
                'funds_mbpy_oap_above_80_years' => $totalsQuery->mbpy_oap_above_80_years * 3500,

                'mbpy_wp' => $totalsQuery->mbpy_wp,
                'funds_mbpy_wp' => $totalsQuery->mbpy_wp * 1000,

                'mbpy_dp' => $totalsQuery->mbpy_dp,
                'funds_mbpy_dp' => $totalsQuery->mbpy_dp * 1000,

                'mbpy_sdp_below_80_percent' => $totalsQuery->mbpy_sdp_below_80_percent,
                'funds_mbpy_sdp_below_80_percent' => $totalsQuery->mbpy_sdp_below_80_percent * 1200,

                'mbpy_sdp_above_80_percent' => $totalsQuery->mbpy_sdp_above_80_percent,
                'funds_mbpy_sdp_above_80_percent' => $totalsQuery->mbpy_sdp_above_80_percent * 3500,

                'mbpy_sdoap' => $totalsQuery->mbpy_sdoap,
                'funds_mbpy_sdoap' => $totalsQuery->mbpy_sdoap * 3500,

                'mbpy_clp' => $totalsQuery->mbpy_clp,
                'funds_mbpy_clp' => $totalsQuery->mbpy_clp * 1000,

                'mbpy_wp_aids' => $totalsQuery->mbpy_wp_aids,
                'funds_mbpy_wp_aids' => $totalsQuery->mbpy_wp_aids * 1000,

                'mbpy_dp_aids' => $totalsQuery->mbpy_dp_aids,
                'funds_mbpy_dp_aids' => $totalsQuery->mbpy_dp_aids * 1000,

                'mbpy_unmarried_women' => $totalsQuery->mbpy_unmarried_women,
                'funds_mbpy_unmarried_women' => $totalsQuery->mbpy_unmarried_women * 1000,

                'mbpy_orphan_due_to_covide' => $totalsQuery->mbpy_orphan_due_to_covide,
                'funds_mbpy_orphan_due_to_covide' => $totalsQuery->mbpy_orphan_due_to_covide * 1000,

                'mbpy_widow_due_to_covid' => $totalsQuery->mbpy_widow_due_to_covid,
                'funds_mbpy_widow_due_to_covid' => $totalsQuery->mbpy_widow_due_to_covid * 1000,

                'mbpy_divorce_or_destitute' => $totalsQuery->mbpy_divorce_or_destitute,
                'funds_mbpy_divorce_or_destitute' => $totalsQuery->mbpy_divorce_or_destitute * 1000,

                'mbpy_transgender' => $totalsQuery->mbpy_transgender,
                'funds_mbpy_transgender' => $totalsQuery->mbpy_transgender * 1000,
            ];

            $totals['total_beneficiaries'] = array_sum([
                $totalsQuery->mbpy_oap_below_80_years,
                $totalsQuery->mbpy_oap_above_80_years,
                $totalsQuery->mbpy_wp,
                $totalsQuery->mbpy_dp,
                $totalsQuery->mbpy_sdp_below_80_percent,
                $totalsQuery->mbpy_sdp_above_80_percent,
                $totalsQuery->mbpy_sdoap,
                $totalsQuery->mbpy_clp,
                $totalsQuery->mbpy_wp_aids,
                $totalsQuery->mbpy_dp_aids,
                $totalsQuery->mbpy_unmarried_women,
                $totalsQuery->mbpy_orphan_due_to_covide,
                $totalsQuery->mbpy_widow_due_to_covid,
                $totalsQuery->mbpy_divorce_or_destitute,
                $totalsQuery->mbpy_transgender,
            ]);

            $totals['total_fund'] = array_sum(array_filter($totals, fn($k) => str_starts_with($k, 'funds_'), ARRAY_FILTER_USE_KEY));

            return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('area_name', fn($row) => $row->block_name ?? $row->municipality_name)

            ->addColumn('funds_mbpy_oap_below_80_years', fn($row) => $row->mbpy_oap_below_80_years * 1000)
            ->addColumn('funds_mbpy_oap_above_80_years', fn($row) => $row->mbpy_oap_above_80_years * 3500)
            ->addColumn('funds_mbpy_wp', fn($row) => $row->mbpy_wp * 1000)
            ->addColumn('funds_mbpy_dp', fn($row) => $row->mbpy_dp * 1000)
            ->addColumn('funds_mbpy_sdp_below_80_percent', fn($row) => $row->mbpy_sdp_below_80_percent * 1200)
            ->addColumn('funds_mbpy_sdp_above_80_percent', fn($row) => $row->mbpy_sdp_above_80_percent * 3500)
            ->addColumn('funds_mbpy_sdoap', fn($row) => $row->mbpy_sdoap * 3500)
            ->addColumn('funds_mbpy_clp', fn($row) => $row->mbpy_clp * 1000)
            ->addColumn('funds_mbpy_wp_aids', fn($row) => $row->mbpy_wp_aids * 1000)
            ->addColumn('funds_mbpy_dp_aids', fn($row) => $row->mbpy_dp_aids * 1000)
            ->addColumn('funds_mbpy_unmarried_women', fn($row) => $row->mbpy_unmarried_women * 1000)
            ->addColumn('funds_mbpy_orphan_due_to_covide', fn($row) => $row->mbpy_orphan_due_to_covide * 1000)
            ->addColumn('funds_mbpy_widow_due_to_covid', fn($row) => $row->mbpy_widow_due_to_covid * 1000)
            ->addColumn('funds_mbpy_divorce_or_destitute', fn($row) => $row->mbpy_divorce_or_destitute * 1000)
            ->addColumn('funds_mbpy_transgender', fn($row) => $row->mbpy_transgender * 1000)

            ->addColumn('total_beneficiaries', fn($row) =>
                $row->mbpy_oap_below_80_years +
                $row->mbpy_oap_above_80_years +
                $row->mbpy_wp +
                $row->mbpy_dp +
                $row->mbpy_sdp_below_80_percent +
                $row->mbpy_sdp_above_80_percent +
                $row->mbpy_sdoap +
                $row->mbpy_clp +
                $row->mbpy_wp_aids +
                $row->mbpy_dp_aids +
                $row->mbpy_unmarried_women +
                $row->mbpy_orphan_due_to_covide +
                $row->mbpy_widow_due_to_covid +
                $row->mbpy_divorce_or_destitute +
                $row->mbpy_transgender
            )

            ->addColumn('total_fund', fn($row) =>
                ($row->mbpy_oap_below_80_years * 1000) +
                ($row->mbpy_oap_above_80_years * 3500) +
                ($row->mbpy_wp * 1000) +
                ($row->mbpy_dp * 1000) +
                ($row->mbpy_sdp_below_80_percent * 1200) +
                ($row->mbpy_sdp_above_80_percent * 3500) +
                ($row->mbpy_sdoap * 3500) +
                ($row->mbpy_clp * 1000) +
                ($row->mbpy_wp_aids * 1000) +
                ($row->mbpy_dp_aids * 1000) +
                ($row->mbpy_unmarried_women * 1000) +
                ($row->mbpy_orphan_due_to_covide * 1000) +
                ($row->mbpy_widow_due_to_covid * 1000) +
                ($row->mbpy_divorce_or_destitute * 1000) +
                ($row->mbpy_transgender * 1000)
            )

            ->with(['totals' => $totals])

            ->filter(function ($query) use ($request, $approve_status) {

                if ($request->filled('search.value')) {

                    $search = $request->search['value'];

                    if ($approve_status == 3) {
                        $query->havingRaw("
                            district_name LIKE ?
                            OR COALESCE(block_name,'') LIKE ?
                            OR COALESCE(municipality_name,'') LIKE ?
                            ", [
                                "%{$search}%",
                                "%{$search}%",
                                "%{$search}%"
                            ]);
                    } else {
                        $query->havingRaw("
                            d.district_name LIKE ?
                            OR COALESCE(b.block_name,'') LIKE ?
                            OR COALESCE(m.municipality_name,'') LIKE ?
                            ", [
                                "%{$search}%",
                                "%{$search}%",
                                "%{$search}%"
                            ]);
                    }
                }
            })

            ->make(true);
        }

        return view(
            'dashboard.pension.pension_fund_management.pension_fund_requirement_report_of_block_ulb', compact('dateConfig', 'month', 'approve_status')
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
