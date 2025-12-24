<?php

namespace App\Http\Controllers\Dashboard_Controllers;

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
use Illuminate\Support\Collection;

/*Controller Requirements*/
use App\Models\{
    PensionFundsRequirement,
    District,
    Block,
    Subdivision,
    Municipality,
    Grampanchayat,
    Village,
    WardMaster,
    PensionDisbursementAuthority,
    PensionFundRequirementDates,
    MonthlyPensionDisbursemenet,
    DailyPensionDisbursement,
    DisabilityPensionerConsent,
    OldAgePensionerConsent
};
use Yajra\DataTables\Facades\DataTables;

class PensionController extends Controller
{

    public function disability_pensioner_consents_create()
    {
        return view('dashboard.pension.consent.disability_pensioner_consents_create_form');
    }

    public function disability_pensioner_consents_store(Request $request)
    {
        $validationRules = [
            'scheme_name' => 'required|in:MBPDP,DisabilityPensionAidsHiv',
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
            'pension_amount' => 'required',
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
        DB::beginTransaction();
        try {
            $user = auth()->user();

            if ($request->ngo_address_type === "1") {
                $address_type = 1;
                $district = District::where('district_id', $request->district)->value('district_name');
                $district_id = $validatedData['district'];
                $block_or_ulb = Block::where('block_id', $request->block)->value('block_name');
                $block_id = $validatedData['block'];
                $municipality_id = 'NULL';
                $block_or_ulb_id = $validatedData['block'];
                $gp_or_ward = Grampanchayat::where('gp_id', $request->grampanchayat)->value('gp_name');
                $gp_id = $validatedData['grampanchayat'];
                $ward_id = 'NULL';
                $gp_or_ward_id = $validatedData['grampanchayat'];
                $village = Village::where('village_id', $request->village)->value('village_name');
                $village_id = $validatedData['village'];
            } elseif ($request->ngo_address_type === "2") {
                $address_type = 2;
                $district = District::where('district_id', $request->district)->value('district_name');
                $district_id = $validatedData['district'];
                $block_or_ulb = Municipality::where('municipality_id', $request->municipality)->value('municipality_name');
                $block_id = 'NULL';
                $municipality_id = $validatedData['municipality'];
                $block_or_ulb_id = $validatedData['municipality'];
                $ward_master_name = WardMaster::where('ward_code', $request->ward)->value('ward_name');
                $gp_or_ward = $ward_master_name;
                $gp_id = 'NULL';
                $ward_id = $validatedData['ward'];
                $gp_or_ward_id = $validatedData['ward'];
                $village = 'NULL';
                $village_id = 'NULL';
            }

            $disability_pensioner_consent = new DisabilityPensionerConsent;
            $disability_pensioner_consent->scheme_name = trim($validatedData['scheme_name']);
            $disability_pensioner_consent->updated_scheme_name = trim($validatedData['scheme_name']);
            $disability_pensioner_consent->name_of_the_beneficiary = trim($validatedData['name_of_the_beneficiary']);
            $disability_pensioner_consent->father_or_husband_name = trim($validatedData['father_or_husband_name']);
            $disability_pensioner_consent->date_of_birth = trim($validatedData['date_of_birth']);
            $disability_pensioner_consent->age = trim($validatedData['age']);
            $disability_pensioner_consent->gender = trim($validatedData['gender']);
            $disability_pensioner_consent->udid_no = trim($validatedData['udid_no']);
            $disability_pensioner_consent->disability_category = trim($validatedData['disability_category']);
            $disability_pensioner_consent->disability_percentage = trim($validatedData['disability_percentage']);
            $disability_pensioner_consent->state_id = trim($validatedData['state']);
            $disability_pensioner_consent->district = $district;
            $disability_pensioner_consent->district_id = $district_id;
            $disability_pensioner_consent->address_type = $address_type;
            $disability_pensioner_consent->block_or_ulb = $block_or_ulb;
            $disability_pensioner_consent->block_id = $block_id;
            $disability_pensioner_consent->municipality_id = $municipality_id;
            $disability_pensioner_consent->block_or_ulb_id = $block_or_ulb_id;
            $disability_pensioner_consent->gp_or_ward = $gp_or_ward;
            $disability_pensioner_consent->gp_id = $gp_id;
            $disability_pensioner_consent->ward_id = $ward_id;
            $disability_pensioner_consent->gp_or_ward_id = $gp_or_ward_id;
            $disability_pensioner_consent->village = $village;
            $disability_pensioner_consent->village_id = $village_id;
            $disability_pensioner_consent->pin = trim($validatedData['pin']);
            $disability_pensioner_consent->aadhaar_no = trim($validatedData['aadhaar_no']);
            $disability_pensioner_consent->nsap_sanction_order_no = trim($validatedData['nsap_sanction_order_no']);
            $disability_pensioner_consent->sub_collector_sanction_order_no = trim($validatedData['sub_collector_sanction_order_no']);
            $disability_pensioner_consent->pension_amount = trim($validatedData['pension_amount']);
            $disability_pensioner_consent->created_by = $user->user_id ?? null;
            $disability_pensioner_consent->created_by_user_table_id = $user->user_table_id ?? 0;
            $disability_pensioner_consent->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $disability_pensioner_consent->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $disability_pensioner_consent->save();
            DB::commit();
            return redirect()->route('admin.pensionforbeneficiaries.disability_pensioner_consents_create')->with('success', 'Disabled Beneficiary Consent data has been successfully added.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🏫 Disability Beneficiary Consent Form Submission failed.", [
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

    public function check_benf_aadhar(Request $request): JsonResponse
    {
        $aadhaar_no = trim($request->get('aadhaar_no'));
        if (empty($aadhaar_no)) {
            return response()->json(3);
        }
        $existsDisability = DisabilityPensionerConsent::where('db_status', 1)
        ->where('aadhaar_no', $aadhaar_no)
        ->exists();
        if ($existsDisability) {
            return response()->json(1);
        }

        $existsOldAge = OldAgePensionerConsent::where('db_status', 1)
        ->where('aadhaar_no', $aadhaar_no)
        ->exists();

        if ($existsOldAge) {
            return response()->json(2);
        }

        return response()->json(0);
    }

    public function check_benf_nsap_sanction_or_no(Request $request)
    {
        $nsap_sanction_order_no = trim($request->nsap_sanction_order_no);

        if (empty($nsap_sanction_order_no)) {
            return response()->json(3);
        }

        $existsDisability = DisabilityPensionerConsent::where('db_status', 1)
        ->where('nsap_sanction_order_no', $nsap_sanction_order_no)
        ->exists();

        if ($existsDisability) {
            return response()->json(1);
        }

        $existsOldAge = OldAgePensionerConsent::where('db_status', 1)
        ->where('nsap_sanction_order_no', $nsap_sanction_order_no)
        ->exists();

        if ($existsOldAge) {
            return response()->json(2);
        }

        return response()->json(0);
    }

    public function check_benf_udidno(Request $request)
    {
        $udid = $request->udid_no;

        if (!$udid || strlen($udid) < 18) {
            return response()->json(2);
        }
        $exists = DisabilityPensionerConsent::where('db_status', 1)->where('udid_no', $udid)->exists();
        return response()->json($exists ? 1 : 0);
    }

    public function oldage_pensioner_consents_create()
    {
        return view('dashboard.pension.consent.oldage_pensioner_consents_create_form');
    }

    public function oldage_pensioner_consents_store(Request $request)
    {
        $validationRules = [
            'scheme_name' => 'required|in:MBPOAP,MBPWP,CaseOfLeprosyPatient,WidowPensionAidsHiv,UnmarriedWoman,DivorceeWomen,Transgender,WidowDueToCovid,OrphanDueToCovid',
            'name_of_the_beneficiary' => 'required',
            'father_or_husband_name' => 'required',
            'date_of_birth' => 'required|date',
            'age' => 'required',
            'gender' => 'required',
            'aadhaar_no' => 'required',
            'nsap_sanction_order_no' => 'required',
            'sub_collector_sanction_order_no' => 'required',
            'pension_amount' => 'required',
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

        DB::beginTransaction();
        try {
            $user = auth()->user();

            if ($request->ngo_address_type === "1") {
                $address_type = 1;
                $district = District::where('district_id', $request->district)->value('district_name');
                $district_id = $validatedData['district'];
                $block_or_ulb = Block::where('block_id', $request->block)->value('block_name');
                $block_id = $validatedData['block'];
                $municipality_id = 'NULL';
                $block_or_ulb_id = $validatedData['block'];
                $gp_or_ward = Grampanchayat::where('gp_id', $request->grampanchayat)->value('gp_name');
                $gp_id = $validatedData['grampanchayat'];
                $ward_id = 'NULL';
                $gp_or_ward_id = $validatedData['grampanchayat'];
                $village = Village::where('village_id', $request->village)->value('village_name');
                $village_id = $validatedData['village'];
            } elseif ($request->ngo_address_type === "2") {
                $address_type = 2;
                $district = District::where('district_id', $request->district)->value('district_name');
                $district_id = $validatedData['district'];
                $block_or_ulb = Municipality::where('municipality_id', $request->municipality)->value('municipality_name');
                $block_id = 'NULL';
                $municipality_id = $validatedData['municipality'];
                $block_or_ulb_id = $validatedData['municipality'];
                $ward_master_name = WardMaster::where('ward_code', $request->ward)->value('ward_name');
                $gp_or_ward = $ward_master_name;
                $gp_id = 'NULL';
                $ward_id = $validatedData['ward'];
                $gp_or_ward_id = $validatedData['ward'];
                $village = 'NULL';
                $village_id = 'NULL';
            }

            $oldage_pensioner_consent = new OldAgePensionerConsent;
            $oldage_pensioner_consent->scheme_name = trim($validatedData['scheme_name']);
            $oldage_pensioner_consent->updated_scheme_name = trim($validatedData['scheme_name']);
            $oldage_pensioner_consent->name_of_the_beneficiary = trim($validatedData['name_of_the_beneficiary']);
            $oldage_pensioner_consent->father_or_husband_name = trim($validatedData['father_or_husband_name']);
            $oldage_pensioner_consent->date_of_birth = trim($validatedData['date_of_birth']);
            $oldage_pensioner_consent->age = trim($validatedData['age']);
            $oldage_pensioner_consent->gender = trim($validatedData['gender']);
            $oldage_pensioner_consent->state_id = trim($validatedData['state']);
            $oldage_pensioner_consent->district = $district;
            $oldage_pensioner_consent->district_id = $district_id;
            $oldage_pensioner_consent->address_type = $address_type;
            $oldage_pensioner_consent->block_or_ulb = $block_or_ulb;
            $oldage_pensioner_consent->block_id = $block_id;
            $oldage_pensioner_consent->municipality_id = $municipality_id;
            $oldage_pensioner_consent->block_or_ulb_id = $block_or_ulb_id;
            $oldage_pensioner_consent->gp_or_ward = $gp_or_ward;
            $oldage_pensioner_consent->gp_id = $gp_id;
            $oldage_pensioner_consent->ward_id = $ward_id;
            $oldage_pensioner_consent->gp_or_ward_id = $gp_or_ward_id;
            $oldage_pensioner_consent->village = $village;
            $oldage_pensioner_consent->village_id = $village_id;
            $oldage_pensioner_consent->pin = trim($validatedData['pin']);
            $oldage_pensioner_consent->aadhaar_no = trim($validatedData['aadhaar_no']);
            $oldage_pensioner_consent->nsap_sanction_order_no = trim($validatedData['nsap_sanction_order_no']);
            $oldage_pensioner_consent->sub_collector_sanction_order_no = trim($validatedData['sub_collector_sanction_order_no']);
            $oldage_pensioner_consent->pension_amount = trim($validatedData['pension_amount']);
            $oldage_pensioner_consent->created_by = $user->user_id ?? null;
            $oldage_pensioner_consent->created_by_user_table_id = $user->user_table_id ?? 0;
            $oldage_pensioner_consent->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $oldage_pensioner_consent->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $oldage_pensioner_consent->save();
            DB::commit();
            return redirect()->route('admin.pensionforbeneficiaries.oldage_pensioner_consents_create')->with('success', 'OldAge Beneficiary Consent data has been successfully added.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🏫 OldAge Beneficiary Consent Form Submission failed.", [
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
