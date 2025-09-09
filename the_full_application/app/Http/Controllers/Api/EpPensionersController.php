<?php

namespace App\Http\Controllers\Api;

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
use App\Models\Disability3500Pensioner;
use App\Models\OldAge3500Pensioner;
use App\Models\Blocks3500;
use App\Models\Municipality3500;
use App\Models\Grampanchyat3500;
use App\Models\Village3500;
use App\Models\WardMaster3500;
use Yajra\DataTables\Facades\DataTables;

class EpPensionersController extends Controller
{
    /* Fetch Old Age Pensioner by Aadhaar */
    public function getOldAgePensionerByAadharRequiredDatas($aadhaar_no)
    {
        $record = OldAge3500Pensioner::where('aadhaar_no', $aadhaar_no)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'No old age pensioner found with this Aadhaar number'
            ], 404, [], JSON_PRETTY_PRINT);
        }

        /*Mask Aadhaar (keep last 4 digits only)*/
        $maskedAadhaar = str_repeat('X', 8) . substr($record->aadhaar_no, -4);

        $filteredData = [
            'scheme_name'            => $record->scheme_name,
            'name_of_the_beneficiary'=> $record->name_of_the_beneficiary,
            'father_or_husband_name' => $record->father_or_husband_name,
            'date_of_birth'          => $record->date_of_birth,
            'age'                    => $record->age,
            'gender'                 => $record->gender,
            'district'               => $record->district,
            'block_or_ulb'           => $record->block_or_ulb,
            'gp_or_ward'             => $record->gp_or_ward,
            'village'                => $record->village,
            'status'                 => $record->status,
            'aadhaar_no'             => $maskedAadhaar,
        ];

        return response()->json([
            'success' => true,
            'data'    => $filteredData
        ], 200, [], JSON_PRETTY_PRINT);
    }

    /* Fetch Disability Pensioner by Aadhaar */
    public function getDisabilityPensionerByAadharRequiredDatas($aadhaar_no)
    {
        $record = Disability3500Pensioner::where('aadhaar_no', $aadhaar_no)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'No disability pensioner found with this Aadhaar number'
            ], 404, [], JSON_PRETTY_PRINT);
        }

        /*Mask Aadhaar (keep last 4 digits only)*/
        $maskedAadhaar = str_repeat('X', 8) . substr($record->aadhaar_no, -4);

        $filteredData = [
            'scheme_name'            => $record->scheme_name,
            'name_of_the_beneficiary'=> $record->name_of_the_beneficiary,
            'father_or_husband_name' => $record->father_or_husband_name,
            'udid_no'                => $record->udid_no,
            'disability_category'    => $record->disability_category,
            'disability_percentage'  => $record->disability_percentage,
            'date_of_birth'          => $record->date_of_birth,
            'age'                    => $record->age,
            'gender'                 => $record->gender,
            'district'               => $record->district,
            'block_or_ulb'           => $record->block_or_ulb,
            'gp_or_ward'             => $record->gp_or_ward,
            'village'                => $record->village,
            'status'                 => $record->status,
            'aadhaar_no'             => $maskedAadhaar,
        ];

        return response()->json([
            'success' => true,
            'data'    => $filteredData
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
