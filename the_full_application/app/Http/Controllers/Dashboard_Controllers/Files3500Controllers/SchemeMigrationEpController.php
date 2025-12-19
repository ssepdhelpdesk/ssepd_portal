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
use Illuminate\Validation\Rule;
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
    public function nsap_sanction_order_no_check(Request $request)
    {
        $beneficiaries = collect();
        $sanctionNo = null;

        return view(
            'dashboard.benf_3500_files.scheme_migration.scheme_migration_list',
            compact('beneficiaries', 'sanctionNo')
        );
    }

    public function check_benf_nsap_sanction_or_no(Request $request)
    {
        $sanctionNo = strtoupper(trim($request->nsap_sanction_order_no));

        if (empty($sanctionNo)) {
            return response()->json(['status' => 2]);
        }

        $oldAge = OldAge3500Pensioner::where('db_status', 1)
        ->whereRaw(
            'TRIM(UPPER(nsap_sanction_order_no)) LIKE ?',
            ['%' . $sanctionNo . '%']
        )
        ->first();

        $disability = Disability3500Pensioner::where('db_status', 1)
        ->whereRaw(
            'TRIM(UPPER(nsap_sanction_order_no)) LIKE ?',
            ['%' . $sanctionNo . '%']
        )
        ->first();

        if ($oldAge && $disability) {
            return response()->json([
                'status'     => 1,
                'oldage'     => $oldAge,
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

        return response()->json(['status' => 0]);
    }

    public function nsap_sanction_order_no_check_list(Request $request)
    {
        $request->validate([
            'nsap_sanction_order_no' => 'required'
        ]);

        $sanctionNo = strtoupper(trim($request->nsap_sanction_order_no));

        $oldage = OldAge3500Pensioner::whereRaw(
            'TRIM(UPPER(nsap_sanction_order_no)) LIKE ?',
            ['%' . $sanctionNo . '%']
        )
        ->where('db_status', 1)
        ->get()
        ->map(function ($row) {
            return [
                'id'        => $row->id,
                'type'      => 'OAP',
                'scheme'    => $row->updated_scheme_name,
                'name'      => $row->name_of_the_beneficiary,
                'father'    => $row->father_or_husband_name,
                'age'       => $row->age,
                'gender'    => $row->gender,
                'district'  => $row->district,
                'block_ulb' => $row->block_or_ulb,
                'gp_ward'   => $row->gp_or_ward,
                'aadhaar'   => $row->aadhaar_no,
                'nsap_no'   => $row->nsap_sanction_order_no,
                'status'    => $row->status,
                'migration_link' => 'admin.schememigrationep.oap_to_dp_migration'
            ];
        });

        $disability = Disability3500Pensioner::whereRaw(
            'TRIM(UPPER(nsap_sanction_order_no)) LIKE ?',
            ['%' . $sanctionNo . '%']
        )
        ->where('db_status', 1)
        ->get()
        ->map(function ($row) {
            return [
                'id'        => $row->id,
                'type'      => 'DP',
                'scheme'    => $row->updated_scheme_name,
                'name'      => $row->name_of_the_beneficiary,
                'father'    => $row->father_or_husband_name,
                'age'       => $row->age,
                'gender'    => $row->gender,
                'district'  => $row->district,
                'block_ulb' => $row->block_or_ulb,
                'gp_ward'   => $row->gp_or_ward,
                'aadhaar'   => $row->aadhaar_no,
                'nsap_no'   => $row->nsap_sanction_order_no,
                'status'    => $row->status,
                'migration_link' => 'admin.schememigrationep.dp_to_oap_migration'
            ];
        });


        $beneficiaries = $oldage->concat($disability)->values();

        return view(
            'dashboard.benf_3500_files.scheme_migration.scheme_migration_list',
            compact('beneficiaries', 'sanctionNo')
        );
    }

    public function oap_to_dp_migration(string $id)
    {
        $oldAge3500Pensioner = OldAge3500Pensioner::whereId($id)->firstOrFail();
        return view('dashboard.benf_3500_files.scheme_migration.oap_to_dp_scheme_migration_form', compact('oldAge3500Pensioner'));
    }

    public function oap_to_dp_migration_update(Request $request, string $id)
    {
        $validationRules = [
            'scheme_name' => 'required|in:MBPDP,IGNDP',
            'name_of_the_beneficiary' => 'required',
            'father_or_husband_name' => 'required',
            'date_of_birth' => 'required|date',
            'age' => 'required',
            'gender' => 'required',
            'udid_no' => 'required',
            'aadhaar_no' => 'required',
            'nsap_sanction_order_no' => 'required',
            'disability_category' => 'required',
            'disability_percentage' => 'required|integer|between:80,100',
            'sub_collector_sanction_order_no' => 'required',
            'pension_month' => 'required',
            'ngo_address_type' => 'required|in:1,2',
        ];

        if ($request->ngo_address_type === "1") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'block' => 'required',
                'grampanchayat' => 'required',
                'village' => 'required',
                'pin' => 'required',
            ]);
        } elseif ($request->ngo_address_type === "2") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'municipality' => 'required',
                'ward' => 'required',
                'pin' => 'required',
            ]);
        }

        $validatedData = $request->validate($validationRules);

        $user = auth()->user();
        if ($request->ngo_address_type === "1") {
            $address_type = 1;
            $district = District3500::where('district_id', $request->district)->value('district_name');
            $district_id = $validatedData['district'];
            $block_or_ulb = Blocks3500::where('block_id', $request->block)->value('block_name');
            $block_id = $validatedData['block'];
            $municipality_id = 'NULL';
            $block_or_ulb_id = $validatedData['block'];
            $gp_or_ward = Grampanchyat3500::where('gp_id', $request->grampanchayat)->value('gp_name');
            $gp_id = $validatedData['grampanchayat'];
            $ward_id = 'NULL';
            $gp_or_ward_id = $validatedData['grampanchayat'];
            $village = Village3500::where('village_id', $request->village)->value('village_name');
            $village_id = $validatedData['village'];
        } elseif ($request->ngo_address_type === "2") {
            $address_type = 2;
            $district = District3500::where('district_id', $request->district)->value('district_name');
            $district_id = $validatedData['district'];
            $block_or_ulb = Municipality3500::where('municipality_id', $request->municipality)->value('municipality_name');
            $block_id = 'NULL';
            $municipality_id = $validatedData['municipality'];
            $block_or_ulb_id = $validatedData['municipality'];
            $ward_master_name = WardMaster3500::where('ward_code', $request->ward)->value('ward_name');
            $gp_or_ward = $ward_master_name;
            $gp_id = 'NULL';
            $ward_id = $validatedData['ward'];
            $gp_or_ward_id = $validatedData['ward'];
            $village = 'NULL';
            $village_id = 'NULL';
        }

        if ($validatedData['scheme_name'] == 'MBPDP') {
            $updated_scheme_name = 'MBPSDP';
        } elseif ($validatedData['scheme_name'] == 'IGNDP') {
            $updated_scheme_name = 'IGNDP';
        } elseif (empty($validatedData['scheme_name'])) {
            return redirect()->back()->withErrors(['scheme_name' => 'Please select an appropriate Scheme Name']);
        } else {
            return redirect()->back()->withErrors(['scheme_name' => 'Invalid Scheme Name selected']);
        }

        $aadhaarNo = trim($validatedData['aadhaar_no']);
        $disability_aadhaar_no_check = Disability3500Pensioner::where('aadhaar_no', $aadhaarNo)->first();
        if($disability_aadhaar_no_check) {
            return redirect()->back()
            ->with('error', 
                'This Aadhaar is already registered with ' .
                $disability_aadhaar_no_check->name_of_the_beneficiary .
                ' (District: ' . $disability_aadhaar_no_check->district .
                ', Block/ULB: ' . $disability_aadhaar_no_check->block_or_ulb .
                ', GP/Ward: ' . $disability_aadhaar_no_check->gp_or_ward .
                ', Village: ' . ($disability_aadhaar_no_check->village ?? 'N/A') . ')'
            )
            ->withInput();
        }

        $udidNo = trim($validatedData['udid_no']);
        $disability_udid_no_check = Disability3500Pensioner::where('udid_no', $udidNo)->first();
        if($disability_udid_no_check) {
            return redirect()->back()
            ->with('error', 
                'This UDID is already registered with ' .
                $disability_udid_no_check->name_of_the_beneficiary .
                ' (District: ' . $disability_udid_no_check->district .
                ', Block/ULB: ' . $disability_udid_no_check->block_or_ulb .
                ', GP/Ward: ' . $disability_udid_no_check->gp_or_ward .
                ', Village: ' . ($disability_udid_no_check->village ?? 'N/A') . ')'
            )
            ->withInput();
        }
        DB::beginTransaction();
        try {
            $oldage_pensioner = OldAge3500Pensioner::where('id', $id)->first();            
            $sanctionNo = strtoupper(trim($validatedData['nsap_sanction_order_no']));
            $disability_pensioner_check = Disability3500Pensioner::whereRaw('TRIM(UPPER(nsap_sanction_order_no)) = ?', [$sanctionNo])->first();
            if(!$disability_pensioner_check) {
                $disability_pensioner = new Disability3500Pensioner;
                $disability_pensioner->scheme_name = $validatedData['scheme_name'];
                $disability_pensioner->updated_scheme_name = $validatedData['scheme_name'] === 'MBPDP' ? 'MBPSDP' : $validatedData['scheme_name'];          
                $disability_pensioner->name_of_the_beneficiary = strtoupper(trim($validatedData['name_of_the_beneficiary']));
                $disability_pensioner->father_or_husband_name = $validatedData['father_or_husband_name'];
                $disability_pensioner->date_of_birth = $validatedData['date_of_birth'];
                $disability_pensioner->age = $validatedData['age'];
                $disability_pensioner->gender = $validatedData['gender'];
                $disability_pensioner->udid_no = $validatedData['udid_no'];
                $disability_pensioner->disability_category = strtoupper(trim($validatedData['disability_category']));
                $disability_pensioner->disability_percentage = $validatedData['disability_percentage'];
                $disability_pensioner->district = $district;
                $disability_pensioner->district_id = $district_id;
                $disability_pensioner->address_type = $address_type;
                $disability_pensioner->block_or_ulb = $block_or_ulb;
                $disability_pensioner->block_id = $block_id;
                $disability_pensioner->municipality_id = $municipality_id;
                $disability_pensioner->block_or_ulb_id = $block_or_ulb_id;
                $disability_pensioner->gp_or_ward = $gp_or_ward;
                $disability_pensioner->gp_id = $gp_id;
                $disability_pensioner->ward_id = $ward_id;
                $disability_pensioner->gp_or_ward_id = $gp_or_ward_id;
                $disability_pensioner->village = $village;
                $disability_pensioner->village_id = $village_id;
                $disability_pensioner->aadhaar_no = trim($validatedData['aadhaar_no']);
                $disability_pensioner->nsap_sanction_order_no = strtoupper(trim($validatedData['nsap_sanction_order_no']));
                $disability_pensioner->sub_collector_sanction_order_no = $validatedData['sub_collector_sanction_order_no'];
                $disability_pensioner->pension_month = $validatedData['pension_month'];
                $disability_pensioner->created_by = $user->user_id ?? null;
                $disability_pensioner->created_by_date = now()->setTimezone('Asia/Kolkata')->toDateString();
                $disability_pensioner->create_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
                $disability_pensioner->scheme_migration_id = $id;
                $disability_pensioner->scheme_migration_remarks = 'Migrated from Old Age Pensioner (OAP) record ID: ' . (int) $id .' on ' . now()->setTimezone('Asia/Kolkata')->format('d-m-Y H:i');
                $disability_pensioner->save();

                if($oldage_pensioner) {
                    $oldage_pensioner->scheme_migration_id = $disability_pensioner->id;
                    $oldage_pensioner->scheme_migration_remarks = 'Migrated to Disability Pensioner (DP) record ID: ' . (int) $disability_pensioner->id .' on ' . now()->setTimezone('Asia/Kolkata')->format('d-m-Y H:i') . ' hence disabled and db_status = 0';
                    $oldage_pensioner->db_status = 0;
                    $oldage_pensioner->save();
                }

                $disability_pensioner_verification_app = PensionVerificationAppBeneficiary::where('excel_data_type', 'OAPEP')->where('ssepd_id', $id)->where('sanction_number', $sanctionNo)->first();
                if ($disability_pensioner_verification_app) {
                    if ($request->ngo_address_type === "1") {
                        $user_level_of_verification_app = 'block';
                        $district_id_of_verification_app = PensionVerificationAppDistrict::where('id', $request->district)->value('id');
                        $district_name_of_verification_app = PensionVerificationAppDistrict::where('id', $request->district)->value('name');            
                        $block_id_of_verification_app = PensionVerificationAppBlock::where('type', 'block')->where('block_code', $request->block)->value('id');
                        $block_name_of_verification_app = PensionVerificationAppBlock::where('type', 'block')->where('block_code', $request->block)->value('name');            
                        $gp_id_of_verification_app = PensionVerificationAppGramaPanchayat::where('gp_code', $request->grampanchayat)->value('id');
                        $gp_name_of_verification_app = PensionVerificationAppGramaPanchayat::where('gp_code', $request->grampanchayat)->value('name');
                        $village_id_of_verification_app = PensionVerificationAppVillage::where('village_code', $request->village)->value('id');
                        $village_name_of_verification_app = PensionVerificationAppVillage::where('village_code', $request->village)->value('name'); 
                    } elseif ($request->ngo_address_type === "2") {
                        $user_level_of_verification_app = 'municipality';
                        $district_id_of_verification_app = PensionVerificationAppDistrict::where('id', $request->district)->value('id');
                        $district_name_of_verification_app = PensionVerificationAppDistrict::where('id', $request->district)->value('name');            
                        $block_id_of_verification_app = PensionVerificationAppBlock::where('type', 'municipality')->where('municipality_code', $request->municipality)->value('id');
                        $block_name_of_verification_app = PensionVerificationAppBlock::where('type', 'municipality')->where('municipality_code', $request->municipality)->value('name'); 
                        $ward_id_of_verification_app = PensionVerificationAppWard::where('ward_code', $request->ward)->value('id');
                        $ward_name_of_verification_app = PensionVerificationAppWard::where('ward_code', $request->ward)->value('name');
                    }

                    $disability_pensioner_verification_app->sanction_number = strtoupper(trim($validatedData['nsap_sanction_order_no']));
                    $disability_pensioner_verification_app->name = strtoupper(trim($validatedData['name_of_the_beneficiary']));
                    $disability_pensioner_verification_app->gender = $validatedData['gender'];
                    $disability_pensioner_verification_app->dob = $validatedData['date_of_birth'];
                    $disability_pensioner_verification_app->father_name = $validatedData['father_or_husband_name'];
                    $disability_pensioner_verification_app->state_id = 21;
                    $disability_pensioner_verification_app->district_id = $district_id_of_verification_app;
                    $disability_pensioner_verification_app->district_name = $district_name_of_verification_app;
                    $disability_pensioner_verification_app->block_id = $block_id_of_verification_app;
                    $disability_pensioner_verification_app->block_name = $block_name_of_verification_app;
                    if ($request->ngo_address_type === "1") {
                        $disability_pensioner_verification_app->gp_id = $gp_id_of_verification_app;
                        $disability_pensioner_verification_app->gp_name = $gp_name_of_verification_app;
                        $disability_pensioner_verification_app->village_id = $village_id_of_verification_app;
                        $disability_pensioner_verification_app->village_name = $village_name_of_verification_app;
                    } elseif ($request->ngo_address_type === "2") {
                        $disability_pensioner_verification_app->ward_id = $ward_id_of_verification_app;
                        $disability_pensioner_verification_app->ward_name = $ward_name_of_verification_app;
                    }

                    if ($validatedData['scheme_name'] === 'MBPDP') {
                        $disability_pensioner_verification_app->scheme = 'MBPDP';
                        $disability_pensioner_verification_app->scheme_type = 'MBPY';
                        $disability_pensioner_verification_app->updated_scheme_name = 'MBPSDP';
                    } else {
                        $disability_pensioner_verification_app->scheme = 'IGNDP';
                        $disability_pensioner_verification_app->scheme_type = 'NSAP';
                        $disability_pensioner_verification_app->updated_scheme_name = 'IGNDP';
                    }
                    $disability_pensioner_verification_app->age = $validatedData['age'];
                    $disability_pensioner_verification_app->aadhar_no = hash('sha256', $validatedData['aadhaar_no']);
                    $disability_pensioner_verification_app->user_level = $user_level_of_verification_app;        
                    $disability_pensioner_verification_app->disability_percentage = $validatedData['disability_percentage'];
                    $disability_pensioner_verification_app->disability_category = strtoupper(trim($validatedData['disability_category']));
                    $disability_pensioner_verification_app->udid_no = $validatedData['udid_no'];
                    $disability_pensioner_verification_app->excel_data_type = 'DPEP';
                    $disability_pensioner_verification_app->ssepd_id = (string) $id;
                    $disability_pensioner_verification_app->status = '1';
                    $disability_pensioner_verification_app->is_new = '1';
                    $disability_pensioner_verification_app->save();
                }
            }
            DB::commit();
            return redirect()->route('admin.schememigrationep.nsap_sanction_order_no_check')->with('info', 'Migrated Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🏫 EP OAP to DP Migration data Form Submission failed.", [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'time'    => now()->toDateTimeString(),
                'user_id' => auth()->id(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
        }
    }

    public function dp_to_oap_migration(string $id)
    {
        $disability3500Pensioner = Disability3500Pensioner::whereId($id)->firstOrFail();
        return view('dashboard.benf_3500_files.scheme_migration.dp_to_oap_scheme_migration_form', compact('disability3500Pensioner'));
    }

    public function dp_to_oap_migration_update(Request $request, string $id)
    {
        $validationRules = [
            'scheme_name' => 'required|in:MBPOAP,IGNOAP',
            'name_of_the_beneficiary' => 'required',
            'father_or_husband_name' => 'required',
            'date_of_birth' => 'required|date',
            'age' => 'required',
            'gender' => 'required',
            'aadhaar_no' => 'required',
            'nsap_sanction_order_no' =>'required',
            'sub_collector_sanction_order_no' => 'required',
            'pension_month' => 'required',
            'ngo_address_type' => 'required|in:1,2',
        ];

        if ($request->ngo_address_type === "1") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'block' => 'required',
                'grampanchayat' => 'required',
                'village' => 'required',
                'pin' => 'required',
            ]);
        } elseif ($request->ngo_address_type === "2") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'municipality' => 'required',
                'ward' => 'required',
                'pin' => 'required',
            ]);
        }

        $validatedData = $request->validate($validationRules);

        $user = auth()->user();
        if ($request->ngo_address_type === "1") {
            $address_type = 1;
            $district = District3500::where('district_id', $request->district)->value('district_name');
            $district_id = $validatedData['district'];
            $block_or_ulb = Blocks3500::where('block_id', $request->block)->value('block_name');
            $block_id = $validatedData['block'];
            $municipality_id = 'NULL';
            $block_or_ulb_id = $validatedData['block'];
            $gp_or_ward = Grampanchyat3500::where('gp_id', $request->grampanchayat)->value('gp_name');
            $gp_id = $validatedData['grampanchayat'];
            $ward_id = 'NULL';
            $gp_or_ward_id = $validatedData['grampanchayat'];
            $village = Village3500::where('village_id', $request->village)->value('village_name');
            $village_id = $validatedData['village'];
        } elseif ($request->ngo_address_type === "2") {
            $address_type = 2;
            $district = District3500::where('district_id', $request->district)->value('district_name');
            $district_id = $validatedData['district'];
            $block_or_ulb = Municipality3500::where('municipality_id', $request->municipality)->value('municipality_name');
            $block_id = 'NULL';
            $municipality_id = $validatedData['municipality'];
            $block_or_ulb_id = $validatedData['municipality'];
            $ward_master_name = WardMaster3500::where('ward_code', $request->ward)->value('ward_name');
            $gp_or_ward = $ward_master_name;
            $gp_id = 'NULL';
            $ward_id = $validatedData['ward'];
            $gp_or_ward_id = $validatedData['ward'];
            $village = 'NULL';
            $village_id = 'NULL';
        }

        if ($validatedData['scheme_name'] == 'MBPOAP') {
            $updated_scheme_name = 'MBPOAP';
        } elseif ($validatedData['scheme_name'] == 'IGNOAP') {
            $updated_scheme_name = 'IGNOAP';
        } elseif (empty($validatedData['scheme_name'])) {
            return redirect()->back()->withErrors(['scheme_name' => 'Please select an appropriate Scheme Name']);
        } else {
            return redirect()->back()->withErrors(['scheme_name' => 'Invalid Scheme Name selected']);
        }

        $aadhaarNo = trim($validatedData['aadhaar_no']);
        $oldage_aadhaar_no_check = OldAge3500Pensioner::where('aadhaar_no', $aadhaarNo)->first();
        if($oldage_aadhaar_no_check) {
            return redirect()->back()
            ->with('error', 
                'This Aadhaar is already registered with ' .
                $oldage_aadhaar_no_check->name_of_the_beneficiary .
                ' (District: ' . $oldage_aadhaar_no_check->district .
                ', Block/ULB: ' . $oldage_aadhaar_no_check->block_or_ulb .
                ', GP/Ward: ' . $oldage_aadhaar_no_check->gp_or_ward .
                ', Village: ' . ($oldage_aadhaar_no_check->village ?? 'N/A') . ')'
            )
            ->withInput();
        }

        DB::beginTransaction();
        try {
            $disability_pensioner = Disability3500Pensioner::where('id', $id)->first();            
            $sanctionNo = strtoupper(trim($validatedData['nsap_sanction_order_no']));
            $oldage_pensioner_check = OldAge3500Pensioner::whereRaw('TRIM(UPPER(nsap_sanction_order_no)) = ?', [$sanctionNo])->first();
            if(!$oldage_pensioner_check) {
                $oldage_pensioner = new OldAge3500Pensioner;
                $oldage_pensioner->scheme_name = $validatedData['scheme_name'];
                $oldage_pensioner->updated_scheme_name = $validatedData['scheme_name'];          
                $oldage_pensioner->name_of_the_beneficiary = strtoupper(trim($validatedData['name_of_the_beneficiary']));
                $oldage_pensioner->father_or_husband_name = $validatedData['father_or_husband_name'];
                $oldage_pensioner->date_of_birth = $validatedData['date_of_birth'];
                $oldage_pensioner->age = $validatedData['age'];
                $oldage_pensioner->gender = $validatedData['gender'];
                $oldage_pensioner->district = $district;
                $oldage_pensioner->district_id = $district_id;
                $oldage_pensioner->address_type = $address_type;
                $oldage_pensioner->block_or_ulb = $block_or_ulb;
                $oldage_pensioner->block_id = $block_id;
                $oldage_pensioner->municipality_id = $municipality_id;
                $oldage_pensioner->block_or_ulb_id = $block_or_ulb_id;
                $oldage_pensioner->gp_or_ward = $gp_or_ward;
                $oldage_pensioner->gp_id = $gp_id;
                $oldage_pensioner->ward_id = $ward_id;
                $oldage_pensioner->gp_or_ward_id = $gp_or_ward_id;
                $oldage_pensioner->village = $village;
                $oldage_pensioner->village_id = $village_id;
                $oldage_pensioner->aadhaar_no = trim($validatedData['aadhaar_no']);
                $oldage_pensioner->nsap_sanction_order_no = strtoupper(trim($validatedData['nsap_sanction_order_no']));
                $oldage_pensioner->sub_collector_sanction_order_no = $validatedData['sub_collector_sanction_order_no'];
                $oldage_pensioner->pension_month = $validatedData['pension_month'];
                $oldage_pensioner->created_by = $user->user_id ?? null;
                $oldage_pensioner->created_by_date = now()->setTimezone('Asia/Kolkata')->toDateString();
                $oldage_pensioner->create_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
                $oldage_pensioner->scheme_migration_id = $id;
                $oldage_pensioner->scheme_migration_remarks = 'Migrated from Disability Pensioner (DP) record ID: ' . (int) $id .' on ' . now()->setTimezone('Asia/Kolkata')->format('d-m-Y H:i');
                $oldage_pensioner->save();

                if($disability_pensioner) {
                    $disability_pensioner->scheme_migration_id = $oldage_pensioner->id;
                    $disability_pensioner->scheme_migration_remarks = 'Migrated to Old Age Pensioner (OAP) record ID: ' . (int) $disability_pensioner->id .' on ' . now()->setTimezone('Asia/Kolkata')->format('d-m-Y H:i') . ' hence disabled and db_status = 0';
                    $disability_pensioner->db_status = 0;
                    $disability_pensioner->save();
                }

                $old_age_pensioner_verification_app = PensionVerificationAppBeneficiary::where('excel_data_type', 'DPEP')->where('ssepd_id', $id)->where('sanction_number', $sanctionNo)->first();
                if ($old_age_pensioner_verification_app) {
                    if ($request->ngo_address_type === "1") {
                        $user_level_of_verification_app = 'block';
                        $district_id_of_verification_app = PensionVerificationAppDistrict::where('id', $request->district)->value('id');
                        $district_name_of_verification_app = PensionVerificationAppDistrict::where('id', $request->district)->value('name');            
                        $block_id_of_verification_app = PensionVerificationAppBlock::where('type', 'block')->where('block_code', $request->block)->value('id');
                        $block_name_of_verification_app = PensionVerificationAppBlock::where('type', 'block')->where('block_code', $request->block)->value('name');            
                        $gp_id_of_verification_app = PensionVerificationAppGramaPanchayat::where('gp_code', $request->grampanchayat)->value('id');
                        $gp_name_of_verification_app = PensionVerificationAppGramaPanchayat::where('gp_code', $request->grampanchayat)->value('name');
                        $village_id_of_verification_app = PensionVerificationAppVillage::where('village_code', $request->village)->value('id');
                        $village_name_of_verification_app = PensionVerificationAppVillage::where('village_code', $request->village)->value('name'); 
                    } elseif ($request->ngo_address_type === "2") {
                        $user_level_of_verification_app = 'municipality';
                        $district_id_of_verification_app = PensionVerificationAppDistrict::where('id', $request->district)->value('id');
                        $district_name_of_verification_app = PensionVerificationAppDistrict::where('id', $request->district)->value('name');            
                        $block_id_of_verification_app = PensionVerificationAppBlock::where('type', 'municipality')->where('municipality_code', $request->municipality)->value('id');
                        $block_name_of_verification_app = PensionVerificationAppBlock::where('type', 'municipality')->where('municipality_code', $request->municipality)->value('name'); 
                        $ward_id_of_verification_app = PensionVerificationAppWard::where('ward_code', $request->ward)->value('id');
                        $ward_name_of_verification_app = PensionVerificationAppWard::where('ward_code', $request->ward)->value('name');
                    }
                    $old_age_pensioner_verification_app->sanction_number = $validatedData['nsap_sanction_order_no'];
                    $old_age_pensioner_verification_app->name = $validatedData['name_of_the_beneficiary'];
                    $old_age_pensioner_verification_app->gender = $validatedData['gender'];
                    $old_age_pensioner_verification_app->dob = $validatedData['date_of_birth'];
                    $old_age_pensioner_verification_app->father_name = $validatedData['father_or_husband_name'];
                    $old_age_pensioner_verification_app->state_id = 21;
                    $old_age_pensioner_verification_app->district_id = $district_id_of_verification_app;
                    $old_age_pensioner_verification_app->district_name = $district_name_of_verification_app;
                    $old_age_pensioner_verification_app->block_id = $block_id_of_verification_app;
                    $old_age_pensioner_verification_app->block_name = $block_name_of_verification_app;
                    if ($request->ngo_address_type === "1") {
                        $old_age_pensioner_verification_app->gp_id = $gp_id_of_verification_app;
                        $old_age_pensioner_verification_app->gp_name = $gp_name_of_verification_app;
                        $old_age_pensioner_verification_app->village_id = $village_id_of_verification_app;
                        $old_age_pensioner_verification_app->village_name = $village_name_of_verification_app;
                    } elseif ($request->ngo_address_type === "2") {
                        $old_age_pensioner_verification_app->ward_id = $ward_id_of_verification_app;
                        $old_age_pensioner_verification_app->ward_name = $ward_name_of_verification_app;
                    }        

                    if ($validatedData['scheme_name'] === 'MBPOAP') {
                        $old_age_pensioner_verification_app->scheme = 'MBPOAP';
                        $old_age_pensioner_verification_app->scheme_type = 'MBPY';
                    } else {
                        $old_age_pensioner_verification_app->scheme = 'IGNOAP';
                        $old_age_pensioner_verification_app->scheme_type = 'NSAP';
                    }
                    $old_age_pensioner_verification_app->age = $validatedData['age'];
                    $old_age_pensioner_verification_app->aadhar_no = hash('sha256', $validatedData['aadhaar_no']);
                    /*$old_age_pensioner_verification_app->month = $validatedData['pension_month'];*/
                    $old_age_pensioner_verification_app->user_level = $user_level_of_verification_app;        
                    $old_age_pensioner_verification_app->disability_percentage = null;
                    $old_age_pensioner_verification_app->disability_category = null;
                    $old_age_pensioner_verification_app->udid_no = null;
                    $old_age_pensioner_verification_app->updated_scheme_name = $validatedData['scheme_name'];
                    $old_age_pensioner_verification_app->excel_data_type = 'OAPEP';
                    $old_age_pensioner_verification_app->ssepd_id = (string) $id;
                    $old_age_pensioner_verification_app->status = '1';
                    $old_age_pensioner_verification_app->is_new = '1';
                    $old_age_pensioner_verification_app->save();
                }
            }
            DB::commit();
            return redirect()->route('admin.schememigrationep.nsap_sanction_order_no_check')->with('info', 'Migrated Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🏫 EP DP to OAP Migration data Form Submission failed.", [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'time'    => now()->toDateTimeString(),
                'user_id' => auth()->id(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.'])->withInput();
        }
    }
}