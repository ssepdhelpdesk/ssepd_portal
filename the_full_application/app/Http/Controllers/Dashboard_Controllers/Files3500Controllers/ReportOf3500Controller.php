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
    public function active_ineligible(Request $request)
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

            $totalDis = $disability_total[$district] ?? 0;
            $disDeath = $disability_death[$district] ?? 0;
            $disIneligible = $disability_ineligible[$district] ?? 0;
            $disActive = $totalDis - ($disDeath + $disIneligible);

            $final_data[] = [
                'SlNo' => $slno++,
                'District' => $district,
                'TotalOldage' => $totalOld,
                'OldageDeath' => $oldDeath,
                'OldageIneligible' => $oldIneligible,
                'OldageActive' => $oldActive,
                'TotalDisability' => $totalDis,
                'DisabilityDeath' => $disDeath,
                'DisabilityIneligible' => $disIneligible,
                'DisabilityActive' => $disActive,
                'TotalDiscontinued' => $oldDeath + $oldIneligible + $disDeath + $disIneligible
            ];
        }

        return view('dashboard.benf_3500_files.report.active_ineligible', compact('final_data', 'from_date', 'to_date'));
    }
}
