<?php

namespace App\Http\Controllers\Dashboard_Controllers\PensionFundManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PensionFundsRequirement;
use App\Models\District;
use Illuminate\Support\Facades\DB;
class PensionFundRequirementDisbursementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $month = 'April-2026';

    $data = \App\Models\District::select(
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
        ->leftJoin('pension_funds_requirements as pfr', function ($join) use ($month) {
            $join->on('districts.district_id', '=', 'pfr.district_id')
                ->where('pfr.for_the_month', $month)
                ->where('pfr.approve_status', 1)
                ->where('pfr.status', 1);
        })
        ->groupBy('districts.district_id', 'districts.district_name')
        ->orderBy('districts.district_name')
        ->get()
        ->map(function ($row) {

            return [
                'district_id'   => $row->district_id,
                'district_name' => $row->district_name,

                // Pair 1
                'mbpy_oap_below_80_years' => $row->mbpy_oap_below_80_years,
                'funds_mbpy_oap_below_80_years' => $row->mbpy_oap_below_80_years * 1000,

                // Pair 2
                'mbpy_oap_above_80_years' => $row->mbpy_oap_above_80_years,
                'funds_mbpy_oap_above_80_years' => $row->mbpy_oap_above_80_years * 3500,

                // Pair 3
                'mbpy_wp' => $row->mbpy_wp,
                'funds_mbpy_wp' => $row->mbpy_wp * 1000,

                // Pair 4
                'mbpy_dp' => $row->mbpy_dp,
                'funds_mbpy_dp' => $row->mbpy_dp * 1000,

                // Pair 5
                'mbpy_sdp_below_80_percent' => $row->mbpy_sdp_below_80_percent,
                'funds_mbpy_sdp_below_80_percent' => $row->mbpy_sdp_below_80_percent * 1200,

                // Pair 6
                'mbpy_sdp_above_80_percent' => $row->mbpy_sdp_above_80_percent,
                'funds_mbpy_sdp_above_80_percent' => $row->mbpy_sdp_above_80_percent * 3500,

                // Pair 7
                'mbpy_sdoap' => $row->mbpy_sdoap,
                'funds_mbpy_sdoap' => $row->mbpy_sdoap * 3500,

                // Pair 8
                'mbpy_clp' => $row->mbpy_clp,
                'funds_mbpy_clp' => $row->mbpy_clp * 1000,

                // Pair 9
                'mbpy_wp_aids' => $row->mbpy_wp_aids,
                'funds_mbpy_wp_aids' => $row->mbpy_wp_aids * 1000,

                // Pair 10
                'mbpy_dp_aids' => $row->mbpy_dp_aids,
                'funds_mbpy_dp_aids' => $row->mbpy_dp_aids * 1000,

                // Pair 11
                'mbpy_unmarried_women' => $row->mbpy_unmarried_women,
                'funds_mbpy_unmarried_women' => $row->mbpy_unmarried_women * 1000,

                // Pair 12
                'mbpy_orphan_due_to_covide' => $row->mbpy_orphan_due_to_covide,
                'funds_mbpy_orphan_due_to_covide' => $row->mbpy_orphan_due_to_covide * 1000,

                // Pair 13
                'mbpy_widow_due_to_covid' => $row->mbpy_widow_due_to_covid,
                'funds_mbpy_widow_due_to_covid' => $row->mbpy_widow_due_to_covid * 1000,

                // Pair 14
                'mbpy_divorce_or_destitute' => $row->mbpy_divorce_or_destitute,
                'funds_mbpy_divorce_or_destitute' => $row->mbpy_divorce_or_destitute * 1000,

                // Pair 15
                'mbpy_transgender' => $row->mbpy_transgender,
                'funds_mbpy_transgender' => $row->mbpy_transgender * 1000,

                // Total
                'total_fund' =>
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
                    ($row->mbpy_transgender * 1000),
            ];
        });

    return response()->json($data);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
