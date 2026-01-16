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

    public function active_ineligible_with_scheme(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role_id;

        $from_date = $request->get('from_date');
        $to_date   = $request->get('to_date');

        $oldBase  = OldAge3500Pensioner::query();
        $oldDeath = OldAge3500Pensioner::query();
        $oldInel  = OldAge3500Pensioner::query();

        $disBase  = Disability3500Pensioner::query();
        $disDeath = Disability3500Pensioner::query();
        $disInel  = Disability3500Pensioner::query();

        if (!in_array($userRole, [1,2,12,13,14,15])) {

            if (in_array($userRole, [4, 6])) {
                $filters = ['block_id' => $user->posted_block];

            } elseif ($userRole == 5) {
                $filters = ['municipality_id' => $user->posted_municipality];

            } elseif (in_array($userRole, [8, 10])) {
                $blockIds = Blocks3500::where('subdivision_id', $user->posted_subdiv)
                ->pluck('block_id');
                $municipalityIds = Municipality3500::where('subdivision_id', $user->posted_subdiv)
                ->pluck('municipality_id');

                $oldBase->where(function($q) use($blockIds,$municipalityIds){
                    $q->whereIn('block_id',$blockIds)->orWhereIn('municipality_id',$municipalityIds);
                });
                $oldDeath->where(function($q) use($blockIds,$municipalityIds){
                    $q->whereIn('block_id',$blockIds)->orWhereIn('municipality_id',$municipalityIds);
                });
                $oldInel->where(function($q) use($blockIds,$municipalityIds){
                    $q->whereIn('block_id',$blockIds)->orWhereIn('municipality_id',$municipalityIds);
                });

                $disBase->where(function($q) use($blockIds,$municipalityIds){
                    $q->whereIn('block_id',$blockIds)->orWhereIn('municipality_id',$municipalityIds);
                });
                $disDeath->where(function($q) use($blockIds,$municipalityIds){
                    $q->whereIn('block_id',$blockIds)->orWhereIn('municipality_id',$municipalityIds);
                });
                $disInel->where(function($q) use($blockIds,$municipalityIds){
                    $q->whereIn('block_id',$blockIds)->orWhereIn('municipality_id',$municipalityIds);
                });

            } elseif (in_array($userRole, [9, 11])) {
                $filters = ['district_id' => $user->posted_district];

            } elseif ($userRole == 22) {
                $filters = ['special_school_id' => $user->special_school_id];
            }

            if (isset($filters)) {
                $oldBase->where($filters);
                $oldDeath->where($filters);
                $oldInel->where($filters);
                $disBase->where($filters);
                $disDeath->where($filters);
                $disInel->where($filters);
            }
        }

        if ($from_date && $to_date) {
            $oldDeath->whereBetween('discontinued_date', [$from_date,$to_date]);
            $oldInel ->whereBetween('discontinued_date', [$from_date,$to_date]);
            $disDeath->whereBetween('discontinued_date', [$from_date,$to_date]);
            $disInel ->whereBetween('discontinued_date', [$from_date,$to_date]);
        }

        $oldBaseData = $oldBase->selectRaw('district, COUNT(*) AS total_oldage')
        ->groupBy('district')->pluck('total_oldage','district')->toArray();

        $oldDeathData = $oldDeath->where('discontinued_reason','Death')
        ->selectRaw('district, COUNT(*) AS oldage_death')
        ->groupBy('district')->pluck('oldage_death','district')->toArray();

        $oldInelData = $oldInel->where('discontinued_reason','Ineligible')
        ->selectRaw('district, COUNT(*) AS oldage_ineligible')
        ->groupBy('district')->pluck('oldage_ineligible','district')->toArray();

        $oldMbpoap = (clone $oldBase)->where('updated_scheme_name','MBPOAP')
        ->selectRaw('district, COUNT(*) AS oldage_total_mbpoap')
        ->groupBy('district')->pluck('oldage_total_mbpoap','district')->toArray();

        $oldIgnoap = (clone $oldBase)->where('updated_scheme_name','IGNOAP')
        ->selectRaw('district, COUNT(*) AS oldage_total_ignoap')
        ->groupBy('district')->pluck('oldage_total_ignoap','district')->toArray();

        $oldDeathMbpoap = (clone $oldDeath)->where('updated_scheme_name','MBPOAP')
        ->selectRaw('district, COUNT(*) AS oldage_death_mbpoap')
        ->groupBy('district')->pluck('oldage_death_mbpoap','district')->toArray();

        $oldDeathIgnoap = (clone $oldDeath)->where('updated_scheme_name','IGNOAP')
        ->selectRaw('district, COUNT(*) AS oldage_death_ignoap')
        ->groupBy('district')->pluck('oldage_death_ignoap','district')->toArray();

        $oldInelMbpoap = (clone $oldInel)->where('updated_scheme_name','MBPOAP')
        ->selectRaw('district, COUNT(*) AS oldage_ineligible_mbpoap')
        ->groupBy('district')->pluck('oldage_ineligible_mbpoap','district')->toArray();

        $oldInelIgnoap = (clone $oldInel)->where('updated_scheme_name','IGNOAP')
        ->selectRaw('district, COUNT(*) AS oldage_ineligible_ignoap')
        ->groupBy('district')->pluck('oldage_ineligible_ignoap','district')->toArray();

        $disBaseData = $disBase->selectRaw('district, COUNT(*) AS total_disability')
        ->groupBy('district')->pluck('total_disability','district')->toArray();

        $disDeathData = $disDeath->where('discontinued_reason','Death')
        ->selectRaw('district, COUNT(*) AS disability_death')
        ->groupBy('district')->pluck('disability_death','district')->toArray();

        $disInelData = $disInel->where('discontinued_reason','Ineligible')
        ->selectRaw('district, COUNT(*) AS disability_ineligible')
        ->groupBy('district')->pluck('disability_ineligible','district')->toArray();

        $disMbpsdp = (clone $disBase)->where('updated_scheme_name','MBPSDP')
        ->selectRaw('district, COUNT(*) AS disability_total_mbpsdp')
        ->groupBy('district')->pluck('disability_total_mbpsdp','district')->toArray();

        $disIgndp = (clone $disBase)->where('updated_scheme_name','IGNDP')
        ->selectRaw('district, COUNT(*) AS disability_total_igndp')
        ->groupBy('district')->pluck('disability_total_igndp','district')->toArray();

        $disDeathMbpsdp = (clone $disDeath)->where('updated_scheme_name','MBPSDP')
        ->selectRaw('district, COUNT(*) AS disability_death_mbpsdp')
        ->groupBy('district')->pluck('disability_death_mbpsdp','district')->toArray();

        $disDeathIgndp = (clone $disDeath)->where('updated_scheme_name','IGNDP')
        ->selectRaw('district, COUNT(*) AS disability_death_igndp')
        ->groupBy('district')->pluck('disability_death_igndp','district')->toArray();

        $disInelMbpsdp = (clone $disInel)->where('updated_scheme_name','MBPSDP')
        ->selectRaw('district, COUNT(*) AS disability_ineligible_mbpsdp')
        ->groupBy('district')->pluck('disability_ineligible_mbpsdp','district')->toArray();

        $disInelIgndp = (clone $disInel)->where('updated_scheme_name','IGNDP')
        ->selectRaw('district, COUNT(*) AS disability_ineligible_igndp')
        ->groupBy('district')->pluck('disability_ineligible_igndp','district')->toArray();

        $allDistricts = collect(array_merge(
            array_keys($oldBaseData), array_keys($oldMbpoap), array_keys($oldIgnoap),
            array_keys($oldDeathData), array_keys($oldDeathMbpoap), array_keys($oldDeathIgnoap),
            array_keys($oldInelData), array_keys($oldInelMbpoap), array_keys($oldInelIgnoap),
            array_keys($disBaseData), array_keys($disMbpsdp), array_keys($disIgndp),
            array_keys($disDeathData), array_keys($disDeathMbpsdp), array_keys($disDeathIgndp),
            array_keys($disInelData), array_keys($disInelMbpsdp), array_keys($disInelIgndp)
        ))->unique()->sort()->values();

        $final_data = [];
        $sl = 1;

        foreach ($allDistricts as $district) {

            $totalOld = $oldBaseData[$district] ?? 0;
            $oldDeathCount = $oldDeathData[$district] ?? 0;
            $oldInelCount  = $oldInelData[$district] ?? 0;

            $totalDis = $disBaseData[$district] ?? 0;
            $disDeathCount = $disDeathData[$district] ?? 0;
            $disInelCount  = $disInelData[$district] ?? 0;

            $final_data[] = [
                'SlNo' => $sl++,
                'District' => $district,

                'TotalOldage' => $totalOld,
                'TotalOldageMbpoap' => $oldMbpoap[$district] ?? 0,
                'TotalOldageIgnoap' => $oldIgnoap[$district] ?? 0,

                'OldageDeath' => $oldDeathCount,
                'OldageDeathMbpoap' => $oldDeathMbpoap[$district] ?? 0,
                'OldageDeathIgnoap' => $oldDeathIgnoap[$district] ?? 0,

                'OldageIneligible' => $oldInelCount,
                'OldageIneligibleMbpoap' => $oldInelMbpoap[$district] ?? 0,
                'OldageIneligibleIgnoap' => $oldInelIgnoap[$district] ?? 0,

                'TotalOldageDiscontinued' => $oldDeathCount + $oldInelCount,
                'OldageActive' => $totalOld - ($oldDeathCount + $oldInelCount),

                'TotalDisability' => $totalDis,
                'TotalDisabilityMbpsdp' => $disMbpsdp[$district] ?? 0,
                'TotalDisabilityIgndp' => $disIgndp[$district] ?? 0,

                'DisabilityDeath' => $disDeathCount,
                'DisabilityDeathMbpsdp' => $disDeathMbpsdp[$district] ?? 0,
                'DisabilityDeathIgndp' => $disDeathIgndp[$district] ?? 0,

                'DisabilityIneligible' => $disInelCount,
                'DisabilityIneligibleMbpsdp' => $disInelMbpsdp[$district] ?? 0,
                'DisabilityIneligibleIgndp' => $disInelIgndp[$district] ?? 0,

                'TotalDisabilityDiscontinued' => $disDeathCount + $disInelCount,
                'DisabilityActive' => $totalDis - ($disDeathCount + $disInelCount),

                'TotalSanction' => $totalOld + $totalDis,
                'TotalDiscontinued' => ($oldDeathCount+$oldInelCount)+($disDeathCount+$disInelCount),
                'TotalActive' => ($totalOld + $totalDis) - (($oldDeathCount+$oldInelCount)+($disDeathCount+$disInelCount)),
            ];
        }

        return view('dashboard.benf_3500_files.report.active_ineligible_with_scheme',
            compact('final_data','from_date','to_date'));
    }

    public function bulk_aadhaar_verification_report()
    {
        $oldAgeTotal = OldAge3500Pensioner::count();

        $oldAgePending = OldAge3500Pensioner::whereNull('verified_aadhar')
        ->whereNull('verified_aadhar_remarks')
        ->count();

        $oldAgeVerified = OldAge3500Pensioner::query()
        ->whereNotNull('verified_aadhar_remarks')
        ->select('verified_aadhar_remarks')
        ->selectRaw('COUNT(*) as total')
        ->groupBy('verified_aadhar_remarks')
        ->get()
        ->map(fn ($row) => [
            'scheme' => 'OldAge',
            'verified_aadhar_remarks' => $row->verified_aadhar_remarks,
            'total' => $row->total,
        ]);

        $disabilityTotal = Disability3500Pensioner::count();

        $disabilityPending = Disability3500Pensioner::whereNull('verified_aadhar')
        ->whereNull('verified_aadhar_remarks')
        ->count();

        $disabilityVerified = Disability3500Pensioner::query()
        ->whereNotNull('verified_aadhar_remarks')
        ->select('verified_aadhar_remarks')
        ->selectRaw('COUNT(*) as total')
        ->groupBy('verified_aadhar_remarks')
        ->get()
        ->map(fn ($row) => [
            'scheme' => 'Disability',
            'verified_aadhar_remarks' => $row->verified_aadhar_remarks,
            'total' => $row->total,
        ]);

        $schemeWise = collect([
            [
                'scheme' => 'OldAge',
                'verified_aadhar_remarks' => 'Total No of Application',
                'total' => $oldAgeTotal,
            ],
            ...$oldAgeVerified,
            [
                'scheme' => 'OldAge',
                'verified_aadhar_remarks' => 'Pending Application for Verification',
                'total' => $oldAgePending,
            ],

            [
                'scheme' => 'Disability',
                'verified_aadhar_remarks' => 'Total No of Application',
                'total' => $disabilityTotal,
            ],
            ...$disabilityVerified,
            [
                'scheme' => 'Disability',
                'verified_aadhar_remarks' => 'Pending Application for Verification',
                'total' => $disabilityPending,
            ],
        ]);

        $combined = $schemeWise
        ->filter(fn ($row) =>
            !in_array($row['verified_aadhar_remarks'], [
                'Total No of Application',
                'Pending Application for Verification'
            ])
        )
        ->groupBy('verified_aadhar_remarks')
        ->map(fn ($items) => $items->sum('total'));

        $totalApplications = OldAge3500Pensioner::count() + Disability3500Pensioner::count();

        $totalPending = OldAge3500Pensioner::whereNull('verified_aadhar')->whereNull('verified_aadhar_remarks')->count() + Disability3500Pensioner::whereNull('verified_aadhar')->whereNull('verified_aadhar_remarks')->count();

        return view('dashboard.benf_3500_files.report.bulk_aadhaar_verification_report', compact('schemeWise', 'combined', 'totalApplications', 'totalPending'));
    }

    /*public function scheme_wise_list(Request $request)
    {
        ini_set('memory_limit', '1024M');

        $district   = $request->district;
        $category   = $request->category;
        $scheme     = $request->scheme;
        $status     = $request->status;
        $fromDate   = $request->from_date;
        $toDate     = $request->to_date;

        $data = collect();

        if (in_array($category, ['oldage', 'all'])) {

            $oldAge = OldAge3500Pensioner::query()
            ->where('district', $district)
            ->where('db_status', 1);

            if ($scheme === 'MBPOAP') {
                $oldAge->where('updated_scheme_name', 'MBPOAP');
            } elseif ($scheme === 'IGNOAP') {
                $oldAge->where('updated_scheme_name', 'IGNOAP');
            } elseif ($scheme === 'MBPY') {
                $oldAge->whereIn('updated_scheme_name', ['MBPOAP']);
            } elseif ($scheme === 'NSAP') {
                $oldAge->whereIn('updated_scheme_name', ['IGNOAP']);
            }

            switch ($status) {

                case 'death':
                $oldAge->where('status', 'Inactive')
                ->where('discontinued_reason', 'Death');
                break;

                case 'ineligible':
                $oldAge->where('status', 'Inactive')
                ->where('discontinued_reason', 'Ineligible');
                break;

                case 'discontinued':
                $oldAge->where('status', 'Inactive');
                break;

                case 'active':
                $oldAge->where('status', 'Active');
                break;

                case 'total':
                default:
                
                break;
            }

            if (in_array($status, ['death','ineligible','discontinued']) && $fromDate && $toDate) {
                $oldAge->whereBetween('discontinued_date', [$fromDate, $toDate]);
            }

            $data = $data->merge($oldAge->get());
        }

        if (in_array($category, ['disability', 'all'])) {

            $disability = Disability3500Pensioner::query()
            ->where('district', $district)
            ->where('db_status', 1);

            if ($scheme === 'MBPSDP') {
                $disability->where('updated_scheme_name', 'MBPSDP');
            } elseif ($scheme === 'IGNDP') {
                $disability->where('updated_scheme_name', 'IGNDP');
            } elseif ($scheme === 'MBPY') {
                $disability->whereIn('updated_scheme_name', ['MBPSDP']);
            } elseif ($scheme === 'NSAP') {
                $disability->whereIn('updated_scheme_name', ['IGNDP']);
            }

            switch ($status) {

                case 'death':
                $disability->where('status', 'Inactive')
                ->where('discontinued_reason', 'Death');
                break;

                case 'ineligible':
                $disability->where('status', 'Inactive')
                ->where('discontinued_reason', 'Ineligible');
                break;

                case 'discontinued':
                $disability->where('status', 'Inactive');
                break;

                case 'active':
                $disability->where('status', 'Active');
                break;

                case 'total':
                default:
                
                break;
            }

            if (in_array($status, ['death','ineligible','discontinued']) && $fromDate && $toDate) {
                $disability->whereBetween('discontinued_date', [$fromDate, $toDate]);
            }

            $data = $data->merge($disability->get());
        }

        return view('dashboard.benf_3500_files.report.scheme_wise_list', [
            'records'   => $data,
            'district'  => $district,
            'category'  => $category,
            'scheme'    => $scheme,
            'status'    => $status,
            'from_date' => $fromDate,
            'to_date'   => $toDate,
        ]);
    }*/

    public function scheme_wise_list(Request $request)
    {
        ini_set('memory_limit', '1024M');

        $user       = Auth::user();
        $userRole   = $user->role_id;

        $district   = $request->district;
        $category   = $request->category;
        $scheme     = $request->scheme;
        $status     = $request->status;
        $fromDate   = $request->from_date;
        $toDate     = $request->to_date;

        $applyRoleFilter = function ($query) use ($user, $userRole) {

            if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {

            } elseif (in_array($userRole, [4, 6])) {
                $query->where('block_id', $user->posted_block);

            } elseif ($userRole == 5) {
                $query->where('municipality_id', $user->posted_municipality);

            } elseif (in_array($userRole, [8, 10])) {

                $blockIds = Blocks3500::where('subdivision_id', $user->posted_subdiv)
                ->where('is_active', 'active')
                ->pluck('block_id');

                $municipalityIds = Municipality3500::where('subdivision_id', $user->posted_subdiv)
                ->where('is_active', 'active')
                ->pluck('municipality_id');

                $query->where(function ($q) use ($blockIds, $municipalityIds) {
                    $q->whereIn('block_id', $blockIds)
                    ->orWhereIn('municipality_id', $municipalityIds);
                });

            } elseif (in_array($userRole, [9, 11])) {
                $query->where('district_id', $user->posted_district);
            }
        };

        $data = collect();

        if (in_array($category, ['oldage', 'all'])) {

            $oldAge = OldAge3500Pensioner::query()
            ->where('district', $district)
            ->where('db_status', 1);

            $applyRoleFilter($oldAge);

            if ($scheme === 'MBPOAP') {
                $oldAge->where('updated_scheme_name', 'MBPOAP');
            } elseif ($scheme === 'IGNOAP') {
                $oldAge->where('updated_scheme_name', 'IGNOAP');
            } elseif ($scheme === 'MBPY') {
                $oldAge->whereIn('updated_scheme_name', ['MBPOAP']);
            } elseif ($scheme === 'NSAP') {
                $oldAge->whereIn('updated_scheme_name', ['IGNOAP']);
            }

            switch ($status) {

                case 'death':
                $oldAge->where('status', 'Inactive')
                ->where('discontinued_reason', 'Death');
                break;

                case 'ineligible':
                $oldAge->where('status', 'Inactive')
                ->where('discontinued_reason', 'Ineligible');
                break;

                case 'discontinued':
                $oldAge->where('status', 'Inactive');
                break;

                case 'active':
                $oldAge->where('status', 'Active');
                break;

                case 'total':
                default:
                break;
            }

            if (in_array($status, ['death', 'ineligible', 'discontinued']) && $fromDate && $toDate) {
                $oldAge->whereBetween('discontinued_date', [$fromDate, $toDate]);
            }

            $data = $data->merge($oldAge->get());
        }

        if (in_array($category, ['disability', 'all'])) {

            $disability = Disability3500Pensioner::query()
            ->where('district', $district)
            ->where('db_status', 1);

            $applyRoleFilter($disability);

            if ($scheme === 'MBPSDP') {
                $disability->where('updated_scheme_name', 'MBPSDP');
            } elseif ($scheme === 'IGNDP') {
                $disability->where('updated_scheme_name', 'IGNDP');
            } elseif ($scheme === 'MBPY') {
                $disability->whereIn('updated_scheme_name', ['MBPSDP']);
            } elseif ($scheme === 'NSAP') {
                $disability->whereIn('updated_scheme_name', ['IGNDP']);
            }

            switch ($status) {

                case 'death':
                $disability->where('status', 'Inactive')
                ->where('discontinued_reason', 'Death');
                break;

                case 'ineligible':
                $disability->where('status', 'Inactive')
                ->where('discontinued_reason', 'Ineligible');
                break;

                case 'discontinued':
                $disability->where('status', 'Inactive');
                break;

                case 'active':
                $disability->where('status', 'Active');
                break;

                case 'total':
                default:
                break;
            }

            if (in_array($status, ['death', 'ineligible', 'discontinued']) && $fromDate && $toDate) {
                $disability->whereBetween('discontinued_date', [$fromDate, $toDate]);
            }

            $data = $data->merge($disability->get());
        }

        return view('dashboard.benf_3500_files.report.scheme_wise_list', [
            'records'   => $data,
            'district'  => $district,
            'category'  => $category,
            'scheme'    => $scheme,
            'status'    => $status,
            'from_date' => $fromDate,
            'to_date'   => $toDate,
        ]);
    }

}