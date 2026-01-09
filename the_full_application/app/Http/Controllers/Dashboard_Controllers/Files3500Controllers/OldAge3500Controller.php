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
use Yajra\DataTables\Facades\DataTables;
use DB;

/*Controller Requirements*/
use App\Models\{
    OldAge3500Pensioner,
    District3500,
    Blocks3500,
    Municipality3500,
    Grampanchyat3500,
    Village3500,
    WardMaster3500,
    User3500
};

use App\Models\PensionVerificationAppModels\{
    PensionVerificationAppBeneficiary,
    PensionVerificationAppDistrict,
    PensionVerificationAppBlock,
    PensionVerificationAppGramaPanchayat,
    PensionVerificationAppVillage,
    PensionVerificationAppWard
};

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;

class OldAge3500Controller extends Controller
{
/**
* Display a listing of the resource.
*/

public function index(Request $request)
{
    ini_set('memory_limit', '512M');    
    $user = auth()->user();
    $userRole = $user->role_id;

    $oldAgeData = OldAge3500Pensioner::query();
    $oldAgeData->where('db_status', 1);

    if (in_array($userRole, [1, 2, 12, 13, 14, 15])) {

    } elseif (in_array($userRole, [4, 6])) {
        $oldAgeData->where('block_id', $user->posted_block);
    } elseif ($userRole == 5) {
        $oldAgeData->where('municipality_id', $user->posted_municipality);
    } elseif (in_array($userRole, [8, 10])) {
        $blockIds = Blocks3500::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')
        ->pluck('block_id');
        $municipalityIds = Municipality3500::where('subdivision_id', $user->posted_subdiv)
        ->where('is_active', 'active')
        ->pluck('municipality_id');

        $oldAgeData->where(function ($query) use ($blockIds, $municipalityIds) {
            $query->whereIn('block_id', $blockIds)
            ->orWhereIn('municipality_id', $municipalityIds);
        });
    } elseif (in_array($userRole, [9, 11])) {
        $oldAgeData->where('district_id', $user->posted_district);
    }

    if ($request->ajax()) {
        return DataTables::eloquent($oldAgeData)
        ->addIndexColumn()
        ->addColumn('aadhaar_verification_status', function ($row) {
            if ($row->verified_aadhar === 1) {
            return '<span class="badge bg-success">Verified Aadhaar</span>';
        }

        if (is_null($row->verified_aadhar)) {
            return '<span class="badge bg-warning text-dark">Pending to Verify</span>';
        }

        if ($row->verified_aadhar === 0) {
            if ($this->isJson($row->verified_aadhar_remarks)) {
                $data = json_decode($row->verified_aadhar_remarks, true);
                return '<span class="badge bg-danger">'
                    . ($data[0]['failureMessage'] ?? 'Verification Failed')
                    . '</span>';
            }
            return '<span class="badge bg-danger">'
                . e($row->verified_aadhar_remarks)
                . '</span>';
        }

        return '-';
    })
        ->addColumn('action', function ($row) {
            $buttons = '<div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action
            </button>
            <div class="dropdown-menu animated flipInX">';

            if (auth()->user()->can('pension-3500-edit') && is_null($row->discontinued_date) && is_null($row->discontinued_system_gen_date) && is_null($row->discontinued_system_gen_time) && is_null($row->discontinued_reason) && is_null($row->discontinued_by) && ($row->status == 'Active')) 
            {
                $buttons .= '<a class="dropdown-item" href="javascript:void(0)" 
                data-bs-toggle="modal" 
                data-bs-target="#actionModal" 
                data-id="'.$row->id.'"> Discontinue </a>';
            }
            if (is_null($row->discontinued_date) && is_null($row->discontinued_system_gen_date) && is_null($row->discontinued_system_gen_time) && is_null($row->discontinued_reason) && is_null($row->discontinued_by) && ($row->status == 'Active')) 
            {
                $editUrl = route('admin.oldage3500data.edit', $row->id);
                $buttons .= '<a href="'.$editUrl.'"  class="dropdown-item">Migration/Update Address</a> ';                
            }

            $buttons .= '</div></div>';

            return $buttons;
        })

        ->rawColumns(['action', 'aadhaar_verification_status'])
        ->make(true);
    }

    return view('dashboard.benf_3500_files.oldage3500dataView');
}

private function isJson($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

public function update_status(Request $request)
{
    $request->validate([
        'id' => 'required',
        'status' => 'required|string',
        'discontinue_date' => 'required|date',
        'discontinued_reason' => 'required|string'
    ]);

    try {
        $user = auth()->user();

        $record = OldAge3500Pensioner::find($request->id);
        $record->status = $request->status;
        $record->discontinued_date = $request->discontinue_date;
        $record->discontinued_reason = $request->discontinued_reason;
        $record->discontinued_by = $user->user_id ?? null;
        $record->discontinued_system_gen_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $record->discontinued_system_gen_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $record->save();

        $record_pensoin_verification_app = PensionVerificationAppBeneficiary::where('excel_data_type', 'OAPEP')->where('ssepd_id', $request->id)->first();
        if ($record_pensoin_verification_app) {
            $record_pensoin_verification_app->is_active = '0';
            $record_pensoin_verification_app->save();
        }


        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'EP Old Age Beneficiary Discontinued Successfully.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🏫 EP OldAge Pension Discontinued Modal Submission Failed.", [
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


public function index_district(Request $request)
{
    if ($request->ajax()) {

        $postedDistrict = auth()->user()->posted_district;

        $activeBlocks = Blocks3500::where('district_id', $postedDistrict)
        ->where('is_active', 'active')
        ->pluck('block_name')
        ->toArray();

        $activeMunicipalities = Municipality3500::where('district_id', $postedDistrict)
        ->where('is_active', 'active')
        ->pluck('municipality_name')
        ->toArray();

        $excludeList = array_merge($activeBlocks, $activeMunicipalities);

        $query = OldAge3500Pensioner::select('*')->where('district_id', $postedDistrict)->where('db_status', 1)->where('status', 'Active')
        ->whereNotIn('block_or_ulb', $excludeList);

        return DataTables::eloquent($query)
        ->addIndexColumn()
        ->addColumn('complete_address', function ($row) {
            $parts = array_filter([
                $row->block_or_ulb !== 'Not Provided By District' ? $row->block_or_ulb : '',
                $row->gp_or_ward !== 'Not Provided By District' ? $row->gp_or_ward : '',
                $row->village !== 'Not Provided By District' ? $row->village : ''
            ]);

            return implode(', ', $parts);
        })
        ->addColumn('action', function ($row) {
            $buttons = '';

            if (auth()->user()->can('pension-3500-edit')) {
                $editUrl = route('admin.oldage3500data.edit', $row->id);
                $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Address</a> ';
            }

            return $buttons;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    return view('dashboard.benf_3500_files.oldage3500data');
}

/*public function oldage_index_district_block_ulb(Request $request)
{        
if ($request->ajax()) {
$user = auth()->user();
$userRole = $user->role_id;

$query = OldAge3500Pensioner::query();

if ($userRole == 9) {
$postedDistrict = $user->posted_district;

$activeDistrictIds = District3500::where('district_id', $postedDistrict)
->where('is_iap', 'a')
->pluck('district_id')
->toArray();

$query->where('district_id', $postedDistrict)
->where('status', 'Active');

if (!empty($activeDistrictIds)) {
$query->where(function($q) {
$q->where(function($sub) {
$sub->whereNotNull('block_id')
->whereNull('gp_id');
})
->orWhere(function($sub) {
$sub->whereNotNull('municipality_id')
->whereNull('ward_id');
});
});
}
}

if ($userRole == 4) {
$postedBlock = $user->posted_block;

$activeGPIds = Grampanchyat3500::where('block_id', $postedBlock)
->where('is_active', 'active')
->pluck('gp_id')
->toArray();

$query->where('block_id', $postedBlock)
->where('status', 'Active');

if (!empty($activeGPIds)) {
$query->whereNull('gp_id');
}

}

if ($userRole == 5) {
$postedMunicipality = $user->posted_municipality;

$activeWardIds = WardMaster3500::where('municipal_area_code', $postedMunicipality)
->where('is_active', '1')
->pluck('ward_code')
->toArray();

$query->where('municipality_id', $postedMunicipality)
->where('status', 'Active');

if (!empty($activeWardIds)) {
$query->whereNull('ward_id');
}
}

return DataTables::eloquent($query)
->addIndexColumn()
->addColumn('complete_address', function ($row) {
$parts = array_filter([
$row->block_or_ulb !== 'Not Provided By District' ? $row->block_or_ulb : '',
$row->gp_or_ward !== 'Not Provided By District' ? $row->gp_or_ward : '',
$row->village !== 'Not Provided By District' ? $row->village : ''
]);

return implode(', ', $parts);
})
->addColumn('action', function ($row) {
$buttons = '';

if (auth()->user()->can('pension-3500-edit')) {
$editUrl = route('admin.oldage3500data.edit', $row->id);
$buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Address</a> ';
}

return $buttons;
})
->rawColumns(['action'])
->make(true);

}
return view('dashboard.benf_3500_files.oldage3500dataDistBlockUlb');
}*/

/*public function oldage_index_district_block_ulb(Request $request)
{        
if ($request->ajax()) {
$user = auth()->user();
$userRole = $user->role_id;

$query = OldAge3500Pensioner::query();

if ($userRole == 9) {
$postedDistrict = $user->posted_district;

$activeDistrictIds = District3500::where('district_id', $postedDistrict)
->where('is_iap', 'a')
->pluck('district_id')
->toArray();

$query->where('district_id', $postedDistrict)
->where('status', 'Active');

if (!empty($activeDistrictIds)) {
$query->where(function ($q) {
$q->where(function ($sub) {
$sub->where(function ($q2) {
$q2->whereNotNull('block_id')
->where('block_id', '!=', '');
})
->where(function ($q2) {
$q2->whereNull('gp_id')
->orWhere('gp_id', '');
});
})
->orWhere(function ($sub) {
$sub->where(function ($q2) {
$q2->whereNotNull('municipality_id')
->where('municipality_id', '!=', '');
})
->where(function ($q2) {
$q2->whereNull('ward_id')
->orWhere('ward_id', '');
});
});
});
}
}

if ($userRole == 4) {
$postedBlock = $user->posted_block;

$activeGPIds = Grampanchyat3500::where('block_id', $postedBlock)
->where('is_active', 'active')
->pluck('gp_id')
->toArray();

$query->where('block_id', $postedBlock)
->where('status', 'Active');

if (!empty($activeGPIds)) {
$query->whereNull('gp_id');
}

}

if ($userRole == 5) {
$postedMunicipality = $user->posted_municipality;

$activeWardIds = WardMaster3500::where('municipal_area_code', $postedMunicipality)
->where('is_active', '1')
->pluck('ward_code')
->toArray();

$query->where('municipality_id', $postedMunicipality)
->where('status', 'Active');

if (!empty($activeWardIds)) {
$query->whereNull('ward_id');
}
}

return DataTables::eloquent($query)
->addIndexColumn()
->addColumn('complete_address', function ($row) {
$parts = array_filter([
$row->block_or_ulb !== 'Not Provided By District' ? $row->block_or_ulb : '',
$row->gp_or_ward !== 'Not Provided By District' ? $row->gp_or_ward : '',
$row->village !== 'Not Provided By District' ? $row->village : ''
]);

return implode(', ', $parts);
})
->addColumn('action', function ($row) {
$buttons = '';

if (auth()->user()->can('pension-3500-edit')) {
$editUrl = route('admin.oldage3500data.edit', $row->id);
$buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Address</a> ';
}

return $buttons;
})
->rawColumns(['action'])
->make(true);

}
return view('dashboard.benf_3500_files.oldage3500dataDistBlockUlb');
}*/

public function oldage_index_district_block_ulb(Request $request)
{
    if ($request->ajax()) {
        $user = auth()->user();
        $userRole = $user->role_id;

        $query = OldAge3500Pensioner::query();
        $query->where('db_status', 1);

        if ($userRole == 9) {
            $postedDistrict = $user->posted_district;

            $activeGPNames = Grampanchyat3500::where('district_id', $postedDistrict)
            ->where('is_active', 'active')
            ->pluck('gp_name')
            ->toArray();

            $activeMunicipality = Municipality3500::where('district_id', $postedDistrict)
            ->where('is_active', 'active')
            ->pluck('municipality_id')
            ->toArray();

            $activeWardNames = WardMaster3500::where('district_code', $postedDistrict)
            ->whereIn('municipal_area_code', $activeMunicipality)
            ->where('is_active', '1')
            ->pluck('ward_name')
            ->toArray();

            $activeGPNames = array_values(array_filter(array_map('trim', $activeGPNames)));
            $activeMunicipality = array_values(array_filter(array_map('trim', $activeMunicipality)));
            $activeWardNames = array_values(array_filter(array_map('trim', $activeWardNames)));

            $activeNames = array_values(array_filter(array_merge($activeGPNames, $activeWardNames)));

            $pensionerGpOrWard = OldAge3500Pensioner::where('district_id', $postedDistrict)
            ->whereNotNull('gp_or_ward')
            ->pluck('gp_or_ward')
            ->toArray();

            $pensionerGpOrWard = array_values(array_filter(array_map('trim', $pensionerGpOrWard)));

            $notMatchingGpOrWard = array_values(array_diff($pensionerGpOrWard, $activeNames));

            $query->where('district_id', $postedDistrict)
            ->where('status', 'Active');

            if (!empty($notMatchingGpOrWard)) {
                $query->whereIn('gp_or_ward', $notMatchingGpOrWard);
            } else {
                $activeDistrictIds = District3500::where('district_id', $postedDistrict)
                ->where('is_iap', 'a')
                ->pluck('district_id')
                ->toArray();

                if (!empty($activeDistrictIds)) {
                    $query->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where(function ($q2) {
                                $q2->whereNotNull('block_id')
                                ->where('block_id', '!=', '');
                            })
                            ->where(function ($q2) {
                                $q2->whereNull('gp_id')
                                ->orWhere('gp_id', '');
                            });
                        })
                        ->orWhere(function ($sub) {
                            $sub->where(function ($q2) {
                                $q2->whereNotNull('municipality_id')
                                ->where('municipality_id', '!=', '');
                            })
                            ->where(function ($q2) {
                                $q2->whereNull('ward_id')
                                ->orWhere('ward_id', '');
                            });
                        });
                    });
                }
            }
        }

        if ($userRole == 4) {

            $postedBlock = $user->posted_block;

            $activeGPNames = Grampanchyat3500::where('block_id', $postedBlock)
            ->where('is_active', 'active')
            ->pluck('gp_name')
            ->map(fn($v) => strtolower(trim($v)))
            ->toArray();

            $pensionerGpOrWard = OldAge3500Pensioner::where('block_id', $postedBlock)
            ->pluck('gp_or_ward')
            ->map(fn($v) => strtolower(trim($v)))
            ->toArray();

            $notMatchingGpOrWard = array_values(array_filter(
                $pensionerGpOrWard,
                fn($v) => !in_array($v, $activeGPNames)
            ));

            $query->where('block_id', $postedBlock)
            ->where('status', 'Active');

            if (!empty($notMatchingGpOrWard)) {
                $query->whereIn('gp_or_ward', $notMatchingGpOrWard);
            }
        }

        if ($userRole == 5) {

            $postedMunicipality = $user->posted_municipality;

            $activeWardNames = WardMaster3500::where('municipal_area_code', $postedMunicipality)
            ->where('is_active', '1')
            ->pluck('ward_name')
            ->toArray();

            $activeWardNames = array_values(array_filter(array_map('trim', $activeWardNames)));

            $pensionerGpOrWard = OldAge3500Pensioner::where('municipality_id', $postedMunicipality)
            ->whereNotNull('gp_or_ward')
            ->pluck('gp_or_ward')
            ->toArray();

            $pensionerGpOrWard = array_values(array_filter(array_map('trim', $pensionerGpOrWard)));

            $notMatchingGpOrWard = array_values(array_diff($pensionerGpOrWard, $activeWardNames));

            $query->where('municipality_id', $postedMunicipality)
            ->where('status', 'Active');

            if (!empty($notMatchingGpOrWard)) {
                $query->whereIn('gp_or_ward', $notMatchingGpOrWard);
            }
        }

        return DataTables::eloquent($query)
        ->addIndexColumn()
        ->addColumn('complete_address', function ($row) {
            $parts = array_filter([
                $row->block_or_ulb !== 'Not Provided By District' ? $row->block_or_ulb : '',
                $row->gp_or_ward !== 'Not Provided By District' ? $row->gp_or_ward : '',
                $row->village !== 'Not Provided By District' ? $row->village : ''
            ]);

            return implode(', ', $parts);
        })
        ->addColumn('action', function ($row) {
            $buttons = '';

            if (auth()->user()->can('pension-3500-edit')) {
                $editUrl = route('admin.oldage3500data.edit', $row->id);
                $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Address</a> ';
            }

            return $buttons;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    return view('dashboard.benf_3500_files.oldage3500dataDistBlockUlb');
}

public function oldage_index_district_block_ulb_gp_update(Request $request)
{
    if ($request->ajax()) {

        $user = auth()->user();
        $role = $user->role_id;

        if ($role == 5) {
            return redirect()->back()->with('error', "You don't have specific permission to access the block.");
        }

        $activeGPQuery = Grampanchyat3500::where('is_active', 'active');

        if ($role == 9) {
            $activeGPQuery->where('district_id', $user->posted_district);
        }

        if ($role == 4) {
            $activeGPQuery->where('block_id', $user->posted_block);
        }

        $activeGPNames = $activeGPQuery
        ->pluck('gp_name')
        ->map(fn($gp) => strtolower(trim($gp)))
        ->toArray();

        $query = OldAge3500Pensioner::query()
        ->where('address_type', 1)->where('db_status', 1);

        if (!empty($activeGPNames)) {
            $query->whereNotIn(DB::raw("LOWER(TRIM(gp_or_ward))"), $activeGPNames);
        }

        if ($role == 9) {
            $query->where('district_id', $user->posted_district);
        }

        if ($role == 4) {
            $query->where('block_id', $user->posted_block);
        }

        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $buttons = '';

            if (auth()->user()->can('pension-3500-edit')) {
                $editUrl = route('admin.oldage3500data.edit', $row->id);
                $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Address</a> ';
            }

            return $buttons;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    return view('dashboard.benf_3500_files.oldage_index_district_block_ulb_gp_update');
}

public function oldage_index_district_block_ulb_ward_update(Request $request)
{
    if ($request->ajax()) {

        $user = auth()->user();
        $role = $user->role_id;

        if ($role == 4) {
            return redirect()->back()->with('error', "You don't have specific permission to access the ULB.");
        }

        $activeWardQuery = WardMaster3500::where('is_active', 1);

        if ($role == 9) {
            $activeWardQuery->where('district_code', $user->posted_district);
        }        

        if ($role == 5) {
            $activeWardQuery->where('municipal_area_code', $user->posted_municipality);
        }

        $activeWardNames = $activeWardQuery
        ->pluck('ward_name')
        ->map(fn($w) => strtolower(trim($w)))
        ->toArray();

        $query = OldAge3500Pensioner::query()
        ->where('address_type', 2)
        ->where('db_status', 1)
        ->where(function ($q) {
            $q->whereNull('ward_id')
            ->orWhere('ward_id', '=', 0)
            ->orWhere('ward_id', 'NOT LIKE', '24%');
        });

        if (!empty($activeWardNames)) {
            $query->whereNotIn(
                DB::raw('LOWER(TRIM(gp_or_ward))'),
                $activeWardNames
            );
        }

        if ($role == 9) {
            $query->where('district_id', $user->posted_district);
        }

        if ($role == 5) {
            $query->where('municipality_id', $user->posted_municipality);
        }

        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            if (auth()->user()->can('pension-3500-edit')) {
                return '<a href="'.route('admin.oldage3500data.edit', $row->id).'"
                class="btn btn-sm btn-primary">Update Address</a>';
            }
            return '';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    return view('dashboard.benf_3500_files.oldage_index_district_block_ulb_ward_update');
}


/**
* Show the form for creating a new resource.
*/
public function create()
{
    return view('dashboard.benf_3500_files.oldageDataEntry');
}

/**
* Store a newly created resource in storage.
*/
public function store(Request $request)
{
    $validationRules = [
        'scheme_name' => 'required|in:MBPOAP,IGNOAP',
        'name_of_the_beneficiary' => 'required',
        'father_or_husband_name' => 'required',
        'date_of_birth' => 'required|date',
        'age' => 'required|integer|between:80,140',
        'gender' => 'required',
        'aadhaar_no' => 'required',
        'nsap_sanction_order_no' => 'required',
        'sub_collector_sanction_order_no' => 'required',
        'pension_month' => 'required',
        'ngo_address_type' => 'required|in:1,2',
    ];

    $addressMessage = '';

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

        $old_age_pensioner = new OldAge3500Pensioner;
        $old_age_pensioner->scheme_name = $validatedData['scheme_name'];
        $old_age_pensioner->updated_scheme_name = $validatedData['scheme_name'];
        $old_age_pensioner->name_of_the_beneficiary = $validatedData['name_of_the_beneficiary'];
        $old_age_pensioner->father_or_husband_name = $validatedData['father_or_husband_name'];
        $old_age_pensioner->date_of_birth = $validatedData['date_of_birth'];
        $old_age_pensioner->age = $validatedData['age'];
        $old_age_pensioner->gender = $validatedData['gender'];
        $old_age_pensioner->district = $district;
        $old_age_pensioner->district_id = $district_id;
        $old_age_pensioner->address_type = $address_type;
        $old_age_pensioner->block_or_ulb = $block_or_ulb;
        $old_age_pensioner->block_id = $block_id;
        $old_age_pensioner->municipality_id = $municipality_id;
        $old_age_pensioner->block_or_ulb_id = $block_or_ulb_id;
        $old_age_pensioner->gp_or_ward = $gp_or_ward;
        $old_age_pensioner->gp_id = $gp_id;
        $old_age_pensioner->ward_id = $ward_id;
        $old_age_pensioner->gp_or_ward_id = $gp_or_ward_id;
        $old_age_pensioner->village = $village;
        $old_age_pensioner->village_id = $village_id;
        $old_age_pensioner->aadhaar_no = $validatedData['aadhaar_no'];
        $old_age_pensioner->nsap_sanction_order_no = $validatedData['nsap_sanction_order_no'];
        $old_age_pensioner->sub_collector_sanction_order_no = $validatedData['sub_collector_sanction_order_no'];
        $old_age_pensioner->pension_month = $validatedData['pension_month'];
        $old_age_pensioner->created_by = $user->user_id ?? null;
        $old_age_pensioner->created_by_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $old_age_pensioner->create_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $old_age_pensioner->save();

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

        $old_age_pensioner_verification_app = new PensionVerificationAppBeneficiary;
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
        $old_age_pensioner_verification_app->month = $validatedData['pension_month'];
        $old_age_pensioner_verification_app->user_level = $user_level_of_verification_app;        
        $old_age_pensioner_verification_app->disability_percentage = null;
        $old_age_pensioner_verification_app->disability_category = null;
        $old_age_pensioner_verification_app->import_type = 'Created By USER_ID_' . ($user->user_id ?? 'Unknown');
        $old_age_pensioner_verification_app->udid_no = null;
        $old_age_pensioner_verification_app->updated_scheme_name = $validatedData['scheme_name'];
        $old_age_pensioner_verification_app->excel_data_type = 'OAPEP';
        $old_age_pensioner_verification_app->ssepd_id = (string) $old_age_pensioner->id;
        $old_age_pensioner_verification_app->status = '1';
        $old_age_pensioner_verification_app->is_new = '1';
        $old_age_pensioner_verification_app->save();

        DB::commit();
        return redirect()->back()->with('success', 'EP Old Age Beneficiary data has been successfully added.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🏫 EP OldAge Beneficiary data Form Submission failed.", [
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
    $oldAge3500Pensioner = OldAge3500Pensioner::whereId($id)->firstOrFail();

    if (request()->get('from') === 'duplicate') {
        return view('dashboard.benf_3500_files.oldage_duplicate_sanction_order_no_update', compact('oldAge3500Pensioner'));
    }
    return view('dashboard.benf_3500_files.oldage3500dataEdit', compact('oldAge3500Pensioner'));
}

/**
* Update the specified resource in storage.
*/
public function update(Request $request, string $id)
{
    $validationRules = [
        'scheme_name' => 'required|in:MBPOAP,IGNOAP',
        'name_of_the_beneficiary' => 'required',
        'father_or_husband_name' => 'required',
        'date_of_birth' => 'required|date',
        'age' => 'required|integer|between:80,140',
        'gender' => 'required',
        'aadhaar_no' => 'required',
        'nsap_sanction_order_no' => 'required',
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

    DB::beginTransaction();
    try {
        if ($request->ngo_address_type === "1") {
            $address_type = 1;
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

        OldAge3500Pensioner::where('id', $id)->update([
            'scheme_name' => $validatedData['scheme_name'],
            'updated_scheme_name' => $updated_scheme_name,
            'name_of_the_beneficiary' => $validatedData['name_of_the_beneficiary'],
            'father_or_husband_name' => $validatedData['father_or_husband_name'],
            'date_of_birth' => $validatedData['date_of_birth'],
            'age' => $validatedData['age'],
            'gender' => $validatedData['gender'],
            'aadhaar_no' => $validatedData['aadhaar_no'],
            'nsap_sanction_order_no' => $validatedData['nsap_sanction_order_no'],
            'sub_collector_sanction_order_no' => $validatedData['sub_collector_sanction_order_no'],
            'address_type' => $address_type,
            'block_or_ulb' => $block_or_ulb,
            'block_id' => $block_id,
            'municipality_id' => $municipality_id,
            'block_or_ulb_id' => $block_or_ulb_id,
            'gp_or_ward' => $gp_or_ward,
            'gp_id' => $gp_id,
            'ward_id' => $ward_id,
            'gp_or_ward_id' => $gp_or_ward_id,
            'village' => $village,
            'village_id' => $village_id,
        ]);

        $old_age_pensioner_verification_app = PensionVerificationAppBeneficiary::where('excel_data_type', 'OAPEP')->where('ssepd_id', $id)->first();
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

        DB::commit();
        return redirect()->route('admin.oldage3500data.index')->with('info', 'Address Updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🏫 OldAge 3500 Benf Address Update form submission failed", [
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
    $aadhaar_no = $request->get('aadhaar_no');
    if (empty($aadhaar_no)) {
        return response()->json(2);
    }
    $aadharExistsInNgo = OldAge3500Pensioner::where('db_status', 1)->where('aadhaar_no', $aadhaar_no)->exists();
    return response()->json($aadharExistsInNgo ? 1 : 0);
}

public function check_benf_nsap_sanction_or_no(Request $request)
{
    $nsap_sanction_order_no = $request->nsap_sanction_order_no;

    if (!$nsap_sanction_order_no) {
        return response()->json(2);
    }
    $exists = OldAge3500Pensioner::where('db_status', 1)->where('nsap_sanction_order_no', $nsap_sanction_order_no)->exists();
    return response()->json($exists ? 1 : 0);
}

/**
* Remove the specified resource from storage.
*/
public function delete(string $id)
{
    return $id;
}

public function oldage_duplicate_sanction_order_no(Request $request)
{
    if ($request->ajax()) {
        $user = auth()->user();
        $userRole = $user->role_id;

        $query = OldAge3500Pensioner::where('db_status', 1)->whereNotNull('nsap_sanction_order_no')
        ->whereRaw("TRIM(nsap_sanction_order_no) != ''")
        ->whereRaw("TRIM(nsap_sanction_order_no) REGEXP 'OR-S-[0-9]+'");
        /*->whereRaw("TRIM(nsap_sanction_order_no) LIKE 'OR-S-%'");*/

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

        $sanctionNos = $query->select(DB::raw("TRIM(nsap_sanction_order_no) AS so"))
        ->pluck('so')
        ->toArray();

        $duplicateSanctionNos = array_values(
            array_unique(
                array_diff_assoc($sanctionNos, array_unique($sanctionNos))
            )
        );

        $finalQuery = OldAge3500Pensioner::where('db_status', 1)->whereIn(
            DB::raw("TRIM(nsap_sanction_order_no)"),
            $duplicateSanctionNos
        )
        ->orderBy('nsap_sanction_order_no');

        return DataTables::of($finalQuery)
        ->addIndexColumn()

        ->addColumn('district', function ($row) {
            return $row->district ?? '';
        })

        ->addColumn('block_or_ulb', function ($row) {
            if ($row->block_id) return $row->block_or_ulb ?? '';
            if ($row->municipality_id) return $row->block_or_ulb ?? '';
            return '';
        })

        ->addColumn('scheme_name', function ($row) {
            return $row->updated_scheme_name;
        })

        ->addColumn('age', function ($row) {
            return $row->age ?? '';
        })

        ->addColumn('status', function ($row) {

            if ($row->status === 'Active') {
                return 'Continue';
            } elseif ($row->status === 'Inactive') {
                return 'Discontinued';
            }

            return '';
        })

        ->addColumn('action', function ($row) {
            $buttons = '';

            if (auth()->user()->can('pension-3500-edit')) {
                $editUrl = route('admin.oldage3500data.edit', $row->id) . '?from=duplicate';
                $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Application</a>';
            }

            return $buttons;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    return view('dashboard.benf_3500_files.oldage_duplicate_sanction_order_no');
}

public function oldage_duplicate_sanction_order_no_update(Request $request, string $id) 
{
    $validationRules = [
        'scheme_name' => 'required',
        'name_of_the_beneficiary' => 'required',
        'father_or_husband_name' => 'required',
        'date_of_birth' => 'required|date',
        'age' => 'required|integer|between:80,140',
        'gender' => 'required',
        'aadhaar_no' => 'required',
        'nsap_sanction_order_no' => 'required',
        'sub_collector_sanction_order_no' => 'required',
        'pension_month' => 'required',
        'ngo_address_type' => 'required|in:1,2',
        'db_status' => 'required|in:1,0',
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
        if ($request->ngo_address_type === "1") {
            $address_type = 1;
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

        $nsapValue = $validatedData['nsap_sanction_order_no'];

        if ($validatedData['db_status'] == 0) {
            $nsapValue = "It is a Duplicate Record so Disabled by the user_id " 
            . auth()->user()->user_table_id . 
            " on " . now()->setTimezone('Asia/Kolkata')->toDateTimeString();
        }

        OldAge3500Pensioner::where('id', $id)->update([
            'scheme_name'                     => $validatedData['scheme_name'],
            'updated_scheme_name'             => $updated_scheme_name,
            'name_of_the_beneficiary'         => $validatedData['name_of_the_beneficiary'],
            'father_or_husband_name'          => $validatedData['father_or_husband_name'],
            'date_of_birth'                   => $validatedData['date_of_birth'],
            'age'                             => $validatedData['age'],
            'gender'                          => $validatedData['gender'],
            'aadhaar_no'                      => $validatedData['aadhaar_no'],
            'nsap_sanction_order_no'          => $nsapValue,
            'sub_collector_sanction_order_no' => $validatedData['sub_collector_sanction_order_no'],
            'address_type'                    => $address_type,
            'block_or_ulb'                    => $block_or_ulb,
            'block_id'                        => $block_id,
            'municipality_id'                 => $municipality_id,
            'block_or_ulb_id'                 => $block_or_ulb_id,
            'gp_or_ward'                      => $gp_or_ward,
            'gp_id'                           => $gp_id,
            'ward_id'                         => $ward_id,
            'gp_or_ward_id'                   => $gp_or_ward_id,
            'village'                         => $village,
            'village_id'                      => $village_id,
            'db_status'                       => $validatedData['db_status'],
        ]);

/*$old_age_pensioner_verification_app = PensionVerificationAppBeneficiary::where('excel_data_type', 'OAPEP')->where('ssepd_id', $id)->first();
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
}*/

DB::commit();
return redirect()->route('admin.oldage3500data.oldage_duplicate_sanction_order_no')->with('info', 'Sanction Order No/Address Updated successfully.');
} catch (\Exception $e) {
    DB::rollBack();
    \Log::error("🏫 OldAge 3500 Benf Address Update form submission failed", [
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

public function oldage_wrong_sanction_order_no(Request $request)
{
    if ($request->ajax()) {
        $user = auth()->user();
        $userRole = $user->role_id;

        $query = OldAge3500Pensioner::where('db_status', 1)
        ->whereRaw("TRIM(nsap_sanction_order_no) NOT REGEXP 'OR-S-[0-9]+'");

        if (in_array($userRole, [4, 6])) {
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

        $finalQuery = $query->orderBy('nsap_sanction_order_no');

        return DataTables::of($finalQuery)
        ->addIndexColumn()

        ->addColumn('district', function ($row) {
            return $row->district ?? '';
        })

        ->addColumn('block_or_ulb', function ($row) {
            if ($row->block_id) return $row->block_or_ulb ?? '';
            if ($row->municipality_id) return $row->block_or_ulb ?? '';
            return '';
        })

        ->addColumn('scheme_name', function ($row) {
            return $row->updated_scheme_name;
        })

        ->addColumn('age', function ($row) {
            return $row->age ?? '';
        })

        ->addColumn('status', function ($row) {

            if ($row->status === 'Active') {
                return 'Continue';
            } elseif ($row->status === 'Inactive') {
                return 'Discontinued';
            }

            return '';
        })

        ->addColumn('action', function ($row) {
            $buttons = '';
            if (auth()->user()->can('pension-3500-edit')) {
                $editUrl = route('admin.oldage3500data.edit', $row->id) . '?from=duplicate';
                $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Application</a>';
            }
            return $buttons;
        })

        ->rawColumns(['action'])
        ->make(true);
    }

    return view('dashboard.benf_3500_files.oldage_wrong_sanction_order_no');
}

public function oldage_bulk_aadhar_verification()
{
    $pendingCount = OldAge3500Pensioner::whereNull('verified_aadhar')->count();

    return view(
        'dashboard.benf_3500_files.aadhar_verification.oldage_bulk_aadhar_verification',
        compact('pendingCount')
    );
}

public function oldage_bulk_aadhar_verification_process(Request $request)
{
    $limit = $request->get('limit', 100);

    $records = OldAge3500Pensioner::whereNull('verified_aadhar')
    ->whereNull('verified_aadhar_remarks')
    ->whereNotNull('aadhaar_no')
    ->whereNotNull('name_of_the_beneficiary')
    ->limit($limit)
    ->get();

    if ($records->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No pending Aadhaar records found'
        ], 422);
    }

    $processedCount = 0;

    foreach ($records as $pensioner) {

        $verified = 0;
        $remarks  = null;

        try {
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 120,
                'connect_timeout' => 20,
                'curl' => [
                    CURLOPT_SSLVERSION   => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                ],
            ])
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'PostmanRuntime/7.36.0',
            ])
            ->asForm()
            ->post('https://ssepd.gov.in:8443/swp/api/nfbs/requestToUid', [
                'aadhaar_no' => trim($pensioner->aadhaar_no),
                'name'       => trim($pensioner->name_of_the_beneficiary),
            ]);

            $remarks = trim($response->body());

            if ($response->successful() && str_contains(strtolower($remarks), 'verify successfully')) {
                $verified = 1;
            } else {
                $verified = 0;
            }

        } catch (\Throwable $e) {
            $verified = 0;
            $remarks  = 'Exception: ' . $e->getMessage();
        }

        $pensioner->update([
            'verified_aadhar' => $verified,
            'verified_aadhar_remarks' => $remarks,
        ]);

        $processedCount++;
    }

    return response()->json([
        'status' => true,
        'message' => 'Bulk Aadhaar verification completed',
        'processed_records' => $processedCount
    ]);
}

}