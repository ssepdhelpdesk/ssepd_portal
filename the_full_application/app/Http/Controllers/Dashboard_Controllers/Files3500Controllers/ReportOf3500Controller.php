<?php

namespace App\Http\Controllers\Dashboard_Controllers\Files3500Controllers;

/*Basic Requirements*/
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use App\Models\ApplicationStageHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Mail\NgoRegistrationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AadhaarVerifier;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use DB;

/*Controller Requirements*/
use App\Models\OldAge3500Pensioner;
use App\Models\Disability3500Pensioner;
use App\Models\District3500;
use App\Models\Blocks3500;
use App\Models\Municipality3500;
use App\Models\Grampanchyat3500;
use App\Models\Village3500;
use App\Models\WardMaster3500;
use Yajra\DataTables\Facades\DataTables;

class ReportOf3500Controller extends Controller
{
    /*public function active_ineligible(Request $request)
    {
        $from_date = $request->get('from_date');
        $to_date   = $request->get('to_date');

        $oldAgeQuery = OldAge3500Pensioner::selectRaw('district, COUNT(*) as total_oldage')
        ->groupBy('district');

        $oldAgeDeathQuery = OldAge3500Pensioner::selectRaw('district, COUNT(*) as oldage_death')
        ->where('discontinued_reason', 'Death')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('discontinued_date', [$from_date, $to_date]))
        ->groupBy('district');

        $oldAgeIneligibleQuery = OldAge3500Pensioner::selectRaw('district, COUNT(*) as oldage_ineligible')
        ->where('discontinued_reason', 'Ineligible')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('discontinued_date', [$from_date, $to_date]))
        ->groupBy('district');

        $disabilityQuery = Disability3500Pensioner::selectRaw('district, COUNT(*) as total_disability')
        ->groupBy('district');

        $disabilityDeathQuery = Disability3500Pensioner::selectRaw('district, COUNT(*) as disability_death')
        ->where('discontinued_reason', 'Death')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('discontinued_date', [$from_date, $to_date]))
        ->groupBy('district');

        $disabilityIneligibleQuery = Disability3500Pensioner::selectRaw('district, COUNT(*) as disability_ineligible')
        ->where('discontinued_reason', 'Ineligible')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('discontinued_date', [$from_date, $to_date]))
        ->groupBy('district');

        $oldage_total = $oldAgeQuery->pluck('total_oldage', 'district')->toArray();
        $oldage_death = $oldAgeDeathQuery->pluck('oldage_death', 'district')->toArray();
        $oldage_ineligible = $oldAgeIneligibleQuery->pluck('oldage_ineligible', 'district')->toArray();

        $disability_total = $disabilityQuery->pluck('total_disability', 'district')->toArray();
        $disability_death = $disabilityDeathQuery->pluck('disability_death', 'district')->toArray();
        $disability_ineligible = $disabilityIneligibleQuery->pluck('disability_ineligible', 'district')->toArray();

        $all_districts = collect(array_merge(
            array_keys($oldage_total),
            array_keys($oldage_death),
            array_keys($oldage_ineligible),
            array_keys($disability_total),
            array_keys($disability_death),
            array_keys($disability_ineligible),
        ))->unique()->sort()->values();

        $final_data = [];
        $slno = 1;

        foreach ($all_districts as $district) {
            $totalOld = $oldage_total[$district] ?? 0;
            $oldDeath = $oldage_death[$district] ?? 0;
            $oldIneligible = $oldage_ineligible[$district] ?? 0;
            $oldActive = $totalOld - ($oldDeath + $oldIneligible);
            $oldDiscontinued = $oldDeath + $oldIneligible;

            $totalDis = $disability_total[$district] ?? 0;
            $disDeath = $disability_death[$district] ?? 0;
            $disIneligible = $disability_ineligible[$district] ?? 0;
            $disActive = $totalDis - ($disDeath + $disIneligible);
            $disDiscontinued = $disDeath + $disIneligible;

            $totalDiscontinued = $oldDiscontinued + $disDiscontinued;
            $totalSanction = $totalOld + $totalDis;
            $totalActive = $totalSanction - $totalDiscontinued;

            $final_data[] = [
                'SlNo' => $slno++,
                'District' => $district,
                'TotalOldage' => $totalOld,
                'OldageDeath' => $oldDeath,
                'OldageIneligible' => $oldIneligible,
                'TotalOldageDiscontinued' => $oldDiscontinued,
                'OldageActive' => $oldActive,
                'TotalDisability' => $totalDis,
                'DisabilityDeath' => $disDeath,
                'DisabilityIneligible' => $disIneligible,
                'TotalDisabilityDiscontinued' => $disDiscontinued,
                'DisabilityActive' => $disActive,
                'TotalSanction' => $totalSanction,
                'TotalDiscontinued' => $totalDiscontinued,
                'TotalActive' => $totalActive,
            ];
        }

        return view('dashboard.benf_3500_files.report.active_ineligible', compact('final_data', 'from_date', 'to_date'));
    }*/

    public function active_ineligible(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role_id;

        $from_date = $request->get('from_date');
        $to_date   = $request->get('to_date');

        $oldAgeQuery = OldAge3500Pensioner::query();
        $oldAgeDeathQuery = OldAge3500Pensioner::query();
        $oldAgeIneligibleQuery = OldAge3500Pensioner::query();

        $disabilityQuery = Disability3500Pensioner::query();
        $disabilityDeathQuery = Disability3500Pensioner::query();
        $disabilityIneligibleQuery = Disability3500Pensioner::query();

        if (!in_array($userRole, [1, 2, 12, 13, 14, 15])) {
            if (in_array($userRole, [4, 6])) {
                $oldAgeQuery->where('block_id', $user->posted_block);
                $oldAgeDeathQuery->where('block_id', $user->posted_block);
                $oldAgeIneligibleQuery->where('block_id', $user->posted_block);
                $disabilityQuery->where('block_id', $user->posted_block);
                $disabilityDeathQuery->where('block_id', $user->posted_block);
                $disabilityIneligibleQuery->where('block_id', $user->posted_block);

            } elseif ($userRole == 5) {
                $oldAgeQuery->where('municipality_id', $user->posted_municipality);
                $oldAgeDeathQuery->where('municipality_id', $user->posted_municipality);
                $oldAgeIneligibleQuery->where('municipality_id', $user->posted_municipality);
                $disabilityQuery->where('municipality_id', $user->posted_municipality);
                $disabilityDeathQuery->where('municipality_id', $user->posted_municipality);
                $disabilityIneligibleQuery->where('municipality_id', $user->posted_municipality);

            } elseif (in_array($userRole, [8, 10])) {
                $blockIds = Blocks3500::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
                $municipalityIds = Municipality3500::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');

                $oldAgeQuery->where(function($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });
                $oldAgeDeathQuery->where(function($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });
                $oldAgeIneligibleQuery->where(function($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });
                $disabilityQuery->where(function($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });
                $disabilityDeathQuery->where(function($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });
                $disabilityIneligibleQuery->where(function($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });

            } elseif (in_array($userRole, [9, 11])) {
                $oldAgeQuery->where('district_id', $user->posted_district);
                $oldAgeDeathQuery->where('district_id', $user->posted_district);
                $oldAgeIneligibleQuery->where('district_id', $user->posted_district);
                $disabilityQuery->where('district_id', $user->posted_district);
                $disabilityDeathQuery->where('district_id', $user->posted_district);
                $disabilityIneligibleQuery->where('district_id', $user->posted_district);

            } elseif ($userRole == 22) {
                $oldAgeQuery->where('special_school_id', $user->special_school_id);
                $oldAgeDeathQuery->where('special_school_id', $user->special_school_id);
                $oldAgeIneligibleQuery->where('special_school_id', $user->special_school_id);
                $disabilityQuery->where('special_school_id', $user->special_school_id);
                $disabilityDeathQuery->where('special_school_id', $user->special_school_id);
                $disabilityIneligibleQuery->where('special_school_id', $user->special_school_id);
            }
        }

        $oldAgeQuery = $oldAgeQuery->selectRaw('district, COUNT(*) as total_oldage')->groupBy('district');
        $oldAgeDeathQuery = $oldAgeDeathQuery->selectRaw('district, COUNT(*) as oldage_death')
        ->where('discontinued_reason', 'Death')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('discontinued_date', [$from_date, $to_date]))
        ->groupBy('district');
        $oldAgeIneligibleQuery = $oldAgeIneligibleQuery->selectRaw('district, COUNT(*) as oldage_ineligible')
        ->where('discontinued_reason', 'Ineligible')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('discontinued_date', [$from_date, $to_date]))
        ->groupBy('district');

        $disabilityQuery = $disabilityQuery->selectRaw('district, COUNT(*) as total_disability')->groupBy('district');
        $disabilityDeathQuery = $disabilityDeathQuery->selectRaw('district, COUNT(*) as disability_death')
        ->where('discontinued_reason', 'Death')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('discontinued_date', [$from_date, $to_date]))
        ->groupBy('district');
        $disabilityIneligibleQuery = $disabilityIneligibleQuery->selectRaw('district, COUNT(*) as disability_ineligible')
        ->where('discontinued_reason', 'Ineligible')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('discontinued_date', [$from_date, $to_date]))
        ->groupBy('district');

        $oldage_total = $oldAgeQuery->pluck('total_oldage', 'district')->toArray();
        $oldage_death = $oldAgeDeathQuery->pluck('oldage_death', 'district')->toArray();
        $oldage_ineligible = $oldAgeIneligibleQuery->pluck('oldage_ineligible', 'district')->toArray();

        $disability_total = $disabilityQuery->pluck('total_disability', 'district')->toArray();
        $disability_death = $disabilityDeathQuery->pluck('disability_death', 'district')->toArray();
        $disability_ineligible = $disabilityIneligibleQuery->pluck('disability_ineligible', 'district')->toArray();

        $all_districts = collect(array_merge(
            array_keys($oldage_total),
            array_keys($oldage_death),
            array_keys($oldage_ineligible),
            array_keys($disability_total),
            array_keys($disability_death),
            array_keys($disability_ineligible),
        ))->unique()->sort()->values();

        $final_data = [];
        $slno = 1;

        foreach ($all_districts as $district) {
            $totalOld = $oldage_total[$district] ?? 0;
            $oldDeath = $oldage_death[$district] ?? 0;
            $oldIneligible = $oldage_ineligible[$district] ?? 0;
            $oldActive = $totalOld - ($oldDeath + $oldIneligible);
            $oldDiscontinued = $oldDeath + $oldIneligible;

            $totalDis = $disability_total[$district] ?? 0;
            $disDeath = $disability_death[$district] ?? 0;
            $disIneligible = $disability_ineligible[$district] ?? 0;
            $disActive = $totalDis - ($disDeath + $disIneligible);
            $disDiscontinued = $disDeath + $disIneligible;

            $totalDiscontinued = $oldDiscontinued + $disDiscontinued;
            $totalSanction = $totalOld + $totalDis;
            $totalActive = $totalSanction - $totalDiscontinued;

            $final_data[] = [
                'SlNo' => $slno++,
                'District' => $district,
                'TotalOldage' => $totalOld,
                'OldageDeath' => $oldDeath,
                'OldageIneligible' => $oldIneligible,
                'TotalOldageDiscontinued' => $oldDiscontinued,
                'OldageActive' => $oldActive,
                'TotalDisability' => $totalDis,
                'DisabilityDeath' => $disDeath,
                'DisabilityIneligible' => $disIneligible,
                'TotalDisabilityDiscontinued' => $disDiscontinued,
                'DisabilityActive' => $disActive,
                'TotalSanction' => $totalSanction,
                'TotalDiscontinued' => $totalDiscontinued,
                'TotalActive' => $totalActive,
            ];
        }

        return view('dashboard.benf_3500_files.report.active_ineligible', compact('final_data', 'from_date', 'to_date'));
    }


    /*public function sanction_report(Request $request)
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $from_date = $request->get('from_date');
        $to_date   = $request->get('to_date');

        $oldAgeQuery = OldAge3500Pensioner::selectRaw('district, COUNT(*) as total_oldage')
        ->groupBy('district');

        $oldAgeSanctionedQuery = OldAge3500Pensioner::selectRaw('district, COUNT(*) as sanctioned_oldage')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('created_by_date', [$from_date, $to_date]))
        ->groupBy('district');

        $disabilityQuery = Disability3500Pensioner::selectRaw('district, COUNT(*) as total_disability')
        ->groupBy('district');

        $disabilitySanctionedQuery = Disability3500Pensioner::selectRaw('district, COUNT(*) as sanctioned_disability')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('created_by_date', [$from_date, $to_date]))
        ->groupBy('district');

        $oldage_total = $oldAgeQuery->pluck('total_oldage', 'district')->toArray();
        $sanctioned_oldage = $oldAgeSanctionedQuery->pluck('sanctioned_oldage', 'district')->toArray();

        $disability_total = $disabilityQuery->pluck('total_disability', 'district')->toArray();
        $sanctioned_disability = $disabilitySanctionedQuery->pluck('sanctioned_disability', 'district')->toArray();

        $all_districts = collect(array_merge(
            array_keys($oldage_total),
            array_keys($sanctioned_oldage),
            array_keys($disability_total),
            array_keys($sanctioned_disability),
        ))->unique()->sort()->values();

        $final_data = [];
        $slno = 1;

        foreach ($all_districts as $district) {
            $totalOld = $oldage_total[$district] ?? 0;
            $oldSanctioned = $sanctioned_oldage[$district] ?? 0;

            $totalDis = $disability_total[$district] ?? 0;
            $disSanctioned = $sanctioned_disability[$district] ?? 0;

            $totalBenf = $totalOld + $totalDis;
            $totalSanction = $oldSanctioned + $disSanctioned;

            $final_data[] = [
                'SlNo' => $slno++,
                'District' => $district,
                'TotalOldage' => $totalOld,
                'OldageSanctioned' => $oldSanctioned,
                'TotalDisability' => $totalDis,
                'DisabilitySanctioned' => $disSanctioned,
                'TotalBenf' => $totalBenf,
                'TotalSanction' => $totalSanction,
            ];
        }

        return view('dashboard.benf_3500_files.report.sanction_report', compact('final_data', 'from_date', 'to_date'));
    }*/

    public function sanction_report(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role_id;

        $request->validate([
            'from_date' => 'nullable|date',
            'to_date'   => 'nullable|date|after_or_equal:from_date',
        ]);

        $from_date = $request->get('from_date');
        $to_date   = $request->get('to_date');

        $oldAgeQuery = OldAge3500Pensioner::query();
        $oldAgeSanctionedQuery = OldAge3500Pensioner::query();

        $disabilityQuery = Disability3500Pensioner::query();
        $disabilitySanctionedQuery = Disability3500Pensioner::query();

        if (!in_array($userRole, [1, 2, 12, 13, 14, 15])) {
            if (in_array($userRole, [4, 6])) {
                $oldAgeQuery->where('block_id', $user->posted_block);
                $oldAgeSanctionedQuery->where('block_id', $user->posted_block);
                $disabilityQuery->where('block_id', $user->posted_block);
                $disabilitySanctionedQuery->where('block_id', $user->posted_block);

            } elseif ($userRole == 5) {
                $oldAgeQuery->where('municipality_id', $user->posted_municipality);
                $oldAgeSanctionedQuery->where('municipality_id', $user->posted_municipality);
                $disabilityQuery->where('municipality_id', $user->posted_municipality);
                $disabilitySanctionedQuery->where('municipality_id', $user->posted_municipality);

            } elseif (in_array($userRole, [8, 10])) {
                $blockIds = Blocks3500::where('subdivision_id', $user->posted_subdiv)->pluck('block_id');
                $municipalityIds = Municipality3500::where('subdivision_id', $user->posted_subdiv)->pluck('municipality_id');

                $oldAgeQuery->where(function ($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });
                $oldAgeSanctionedQuery->where(function ($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });
                $disabilityQuery->where(function ($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });
                $disabilitySanctionedQuery->where(function ($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)->orWhereIn('municipality_id', $municipalityIds);
                });

            } elseif (in_array($userRole, [9, 11])) {
                $oldAgeQuery->where('district_id', $user->posted_district);
                $oldAgeSanctionedQuery->where('district_id', $user->posted_district);
                $disabilityQuery->where('district_id', $user->posted_district);
                $disabilitySanctionedQuery->where('district_id', $user->posted_district);

            } elseif ($userRole == 22) {
                $oldAgeQuery->where('special_school_id', $user->special_school_id);
                $oldAgeSanctionedQuery->where('special_school_id', $user->special_school_id);
                $disabilityQuery->where('special_school_id', $user->special_school_id);
                $disabilitySanctionedQuery->where('special_school_id', $user->special_school_id);
            }
        }

        $oldAgeQuery = $oldAgeQuery
        ->selectRaw('district, COUNT(*) as total_oldage')
        ->groupBy('district');

        $oldAgeSanctionedQuery = $oldAgeSanctionedQuery
        ->selectRaw('district, COUNT(*) as sanctioned_oldage')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('created_by_date', [$from_date, $to_date]))
        ->groupBy('district');

        $disabilityQuery = $disabilityQuery
        ->selectRaw('district, COUNT(*) as total_disability')
        ->groupBy('district');

        $disabilitySanctionedQuery = $disabilitySanctionedQuery
        ->selectRaw('district, COUNT(*) as sanctioned_disability')
        ->when($from_date && $to_date, fn($q) => $q->whereBetween('created_by_date', [$from_date, $to_date]))
        ->groupBy('district');

        $oldage_total = $oldAgeQuery->pluck('total_oldage', 'district')->toArray();
        $sanctioned_oldage = $oldAgeSanctionedQuery->pluck('sanctioned_oldage', 'district')->toArray();

        $disability_total = $disabilityQuery->pluck('total_disability', 'district')->toArray();
        $sanctioned_disability = $disabilitySanctionedQuery->pluck('sanctioned_disability', 'district')->toArray();

        $all_districts = collect(array_merge(
            array_keys($oldage_total),
            array_keys($sanctioned_oldage),
            array_keys($disability_total),
            array_keys($sanctioned_disability),
        ))->unique()->sort()->values();

        $final_data = [];
        $slno = 1;

        foreach ($all_districts as $district) {
            $totalOld = $oldage_total[$district] ?? 0;
            $oldSanctioned = $sanctioned_oldage[$district] ?? 0;

            $totalDis = $disability_total[$district] ?? 0;
            $disSanctioned = $sanctioned_disability[$district] ?? 0;

            $totalBenf = $totalOld + $totalDis;
            $totalSanction = $oldSanctioned + $disSanctioned;

            $final_data[] = [
                'SlNo' => $slno++,
                'District' => $district,
                'TotalOldage' => $totalOld,
                'OldageSanctioned' => $oldSanctioned,
                'TotalDisability' => $totalDis,
                'DisabilitySanctioned' => $disSanctioned,
                'TotalBenf' => $totalBenf,
                'TotalSanction' => $totalSanction,
            ];
        }

        return view('dashboard.benf_3500_files.report.sanction_report', compact('final_data', 'from_date', 'to_date'));
    }

    public function duplicate_sanction_order_no()
    {
        $pattern = "OR-S-%";

        $oldage = OldAge3500Pensioner::select(DB::raw("TRIM(nsap_sanction_order_no) AS so"))
        ->whereNotNull('nsap_sanction_order_no')
        ->whereRaw("TRIM(nsap_sanction_order_no) != ''")
        ->whereRaw("TRIM(nsap_sanction_order_no) LIKE '{$pattern}'")
        ->pluck('so')
        ->toArray();

        $disability = Disability3500Pensioner::select(DB::raw("TRIM(nsap_sanction_order_no) AS so"))
        ->whereNotNull('nsap_sanction_order_no')
        ->whereRaw("TRIM(nsap_sanction_order_no) != ''")
        ->whereRaw("TRIM(nsap_sanction_order_no) LIKE '{$pattern}'")
        ->pluck('so')
        ->toArray();

        $duplicateSanctionNos = array_intersect($oldage, $disability);

        $result = [
            'oldage' => OldAge3500Pensioner::whereIn(
                DB::raw("TRIM(nsap_sanction_order_no)"), 
                $duplicateSanctionNos
            )->get(),

            'disability' => Disability3500Pensioner::whereIn(
                DB::raw("TRIM(nsap_sanction_order_no)"), 
                $duplicateSanctionNos
            )->get(),
        ];

        return response()->json([
            'duplicate_sanction_order_nos' => array_values($duplicateSanctionNos),
            'records' => $result
        ]);
    }

}