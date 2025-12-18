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
use App\Models\{
    OldAge3500Pensioner,
    Disability3500Pensioner,
    District3500,
    Blocks3500,
    Municipality3500,
    Grampanchyat3500,
    Village3500,
    WardMaster3500,
    User3500
};
use Yajra\DataTables\Facades\DataTables;

use App\Models\PensionVerificationAppModels\{
    PensionVerificationAppBeneficiary,
    PensionVerificationAppDistrict,
    PensionVerificationAppBlock,
    PensionVerificationAppGramaPanchayat,
    PensionVerificationAppVillage,
    PensionVerificationAppWard
};

class SchemeMigrationEpController extends Controller
{
    public function oap_to_dp_check(Request $request)
    {
        return view('dashboard.benf_3500_files.scheme_migration.oap_to_dp_scheme_migration');
    }

    public function check_oldage_benf_nsap_sanction_or_no(Request $request)
{
    $sanctionNo = strtoupper(trim($request->nsap_sanction_order_no));

    if (!$sanctionNo) {
        return response()->json(['status' => 2]);
    }

    $oldAge = OldAge3500Pensioner::where('db_status', 1)
        ->whereRaw('TRIM(UPPER(nsap_sanction_order_no)) = ?', [$sanctionNo])
        ->first();

    $disability = Disability3500Pensioner::where('db_status', 1)
        ->whereRaw('TRIM(UPPER(nsap_sanction_order_no)) = ?', [$sanctionNo])
        ->first();

    if ($oldAge && $disability) {
        return response()->json([
            'status' => 1,
            'oldage' => $oldAge,
            'disability' => $disability
        ]);
    }

    if ($oldAge) {
        return response()->json([
            'status' => 3,
            'oldage' => $oldAge
        ]);
    }

    if ($disability) {
        return response()->json([
            'status' => 4,
            'disability' => $disability
        ]);
    }

    // ❗ Not found anywhere → treat as invalid
    return response()->json(['status' => 2]);
}



    public function oap_to_dp(Request $request)
    {
        $validationRules = [
            'nsap_sanction_order_no' => 'required',
        ];
        return $request->nsap_sanction_order_no;
        return view('dashboard.benf_3500_files.scheme_migration.oap_to_dp_scheme_migration');
    }

}
