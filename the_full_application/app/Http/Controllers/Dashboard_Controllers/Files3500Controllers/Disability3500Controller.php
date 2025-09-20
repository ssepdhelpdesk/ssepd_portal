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
use App\Models\Disability3500Pensioner;
use App\Models\District3500;
use App\Models\Blocks3500;
use App\Models\Municipality3500;
use App\Models\Grampanchyat3500;
use App\Models\Village3500;
use App\Models\WardMaster3500;
use App\Models\User3500;
use Yajra\DataTables\Facades\DataTables;

class Disability3500Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request)
{
    ini_set('memory_limit', '512M');
    $user = auth()->user();
    $userRole = $user->role_id;

    $oldAgeData = Disability3500Pensioner::query();

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
        ->addColumn('action', function ($row) {
            $buttons = '<div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action
            </button>
            <div class="dropdown-menu animated flipInX">';

            if (
                auth()->user()->can('pension-3500-edit') && is_null($row->discontinued_date) && is_null($row->discontinued_system_gen_date) && is_null($row->discontinued_system_gen_time) && is_null($row->discontinued_reason) && is_null($row->discontinued_by)
            ) {
                $buttons .= '<a class="dropdown-item" href="javascript:void(0)" 
                data-bs-toggle="modal" 
                data-bs-target="#actionModal" 
                data-id="'.$row->id.'"> Discontinue </a>';
            }

            $buttons .= '</div></div>';

            return $buttons;
        })

        ->rawColumns(['action'])
        ->make(true);
    }

    return view('dashboard.benf_3500_files.disability3500dataView');
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

        $record = Disability3500Pensioner::find($request->id);
        $record->status = $request->status;
        $record->discontinued_date = $request->discontinue_date;
        $record->discontinued_reason = $request->discontinued_reason;
        $record->discontinued_by = $user->user_id ?? null;
        $record->discontinued_system_gen_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $record->discontinued_system_gen_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $record->save();


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

            $query = Disability3500Pensioner::select('*')->where('district_id', $postedDistrict)->where('status', 'Active')
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
                    $editUrl = route('admin.disability3500data.edit', $row->id);
                    $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Address</a> ';
                }

                /*if (auth()->user()->can('pension-3500-delete')) {
                    $deleteUrl = route('admin.disability3500data.delete', $row->id);
                    $buttons .= '<a href="'.$deleteUrl.'" class="btn btn-sm btn-danger">Delete</a>';
                }*/

                return $buttons;
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('dashboard.benf_3500_files.disability3500data');
    }

    public function disability_index_district_block_ulb(Request $request)
    {        
        if ($request->ajax()) {
            $user = auth()->user();
            $userRole = $user->role_id;

            $query = Disability3500Pensioner::query();

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
                    $editUrl = route('admin.disability3500data.edit', $row->id);
                    $buttons .= '<a href="'.$editUrl.'" class="btn btn-sm btn-primary">Update Address</a> ';
                }

                return $buttons;
            })
            ->rawColumns(['action'])
            ->make(true);

        }
        return view('dashboard.benf_3500_files.disability3500dataDistBlockUlb');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.benf_3500_files.disabilityDataEntry');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'scheme_name' => 'required',
            'name_of_the_beneficiary' => 'required',
            'father_or_husband_name' => 'required',
            'date_of_birth' => 'required|date',
            'age' => 'required',
            'gender' => 'required',
            'udid_no' => 'required',
            'disability_category' => 'required',
            'disability_percentage' => 'required',
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
                'ngo_postal_address_at' => 'required|string',
                'ngo_postal_address_post' => 'required|string',
                'ngo_postal_address_via' => 'required|string',
                'ngo_postal_address_ps' => 'required|string',
                'ngo_postal_address_district' => 'required|string',
                'ngo_postal_address_pin' => 'required|digits:6',
            ]);
        } elseif ($request->ngo_address_type === "2") {
            $validationRules = array_merge($validationRules, [
                'state' => 'required',
                'district' => 'required',
                'municipality' => 'required',
                'ward' => 'required',
                'pin' => 'required',
                'ngo_postal_address_at' => 'required|string',
                'ngo_postal_address_post' => 'required|string',
                'ngo_postal_address_via' => 'required|string',
                'ngo_postal_address_ps' => 'required|string',
                'ngo_postal_address_district' => 'required|string',
                'ngo_postal_address_pin' => 'required|digits:6',
            ]);
        }
        $validatedData = $request->validate($validationRules);

        DB::beginTransaction();
        try {
            $user = auth()->user();

            if ($request->ngo_address_type === "1") {
                $district = District3500::where('district_id', $request->district)->value('district_name');
                $district_id = $validatedData['district'];
                $block_or_ulb = Blocks3500::where('block_id', $request->block)->value('block_name');
                $block_id = $validatedData['block'];
                $municipality_id = NULL;
                $block_or_ulb_id = $validatedData['block'];
                $gp_or_ward = Grampanchyat3500::where('gp_id', $request->grampanchayat)->value('gp_name');
                $gp_id = $validatedData['grampanchayat'];
                $ward_id = NULL;
                $gp_or_ward_id = $validatedData['grampanchayat'];
                $village = Village3500::where('village_id', $request->village)->value('village_name');
                $village_id = $validatedData['village'];
            } elseif ($request->ngo_address_type === "2") {
                $district = District3500::where('district_id', $request->district)->value('district_name');
                $district_id = $validatedData['district'];
                $block_or_ulb = Municipality3500::where('municipality_id', $request->municipality)->value('municipality_name');
                $block_id = NULL;
                $municipality_id = $validatedData['municipality'];
                $block_or_ulb_id = $validatedData['municipality'];
                $ward_master_name = WardMaster3500::where('ward_code', $request->ward)->value('ward_name');
                $gp_or_ward = $ward_master_name;
                $gp_id = NULL;
                $ward_id = $validatedData['ward'];
                $gp_or_ward_id = $validatedData['ward'];
                $village = NULL;
                $village_id = NULL;
            }

            $disability_pensioner = new Disability3500Pensioner;
            $disability_pensioner->scheme_name = $validatedData['scheme_name'];
            $disability_pensioner->updated_scheme_name = $validatedData['scheme_name'] === 'MBPDP' ? 'MBPSDP' : $validatedData['scheme_name'];          
            $disability_pensioner->name_of_the_beneficiary = $validatedData['name_of_the_beneficiary'];
            $disability_pensioner->father_or_husband_name = $validatedData['father_or_husband_name'];
            $disability_pensioner->date_of_birth = $validatedData['date_of_birth'];
            $disability_pensioner->age = $validatedData['age'];
            $disability_pensioner->gender = $validatedData['gender'];
            $disability_pensioner->udid_no = $validatedData['udid_no'];
            $disability_pensioner->disability_category = $validatedData['disability_category'];
            $disability_pensioner->disability_percentage = $validatedData['disability_percentage'];
            $disability_pensioner->district = $district;
            $disability_pensioner->district_id = $district_id;
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
            $disability_pensioner->aadhaar_no = $validatedData['aadhaar_no'];
            $disability_pensioner->nsap_sanction_order_no = $validatedData['nsap_sanction_order_no'];
            $disability_pensioner->sub_collector_sanction_order_no = $validatedData['sub_collector_sanction_order_no'];
            $disability_pensioner->pension_month = $validatedData['pension_month'];
            $disability_pensioner->created_by = $user->user_id ?? null;
            $disability_pensioner->created_by_date = now()->setTimezone('Asia/Kolkata')->toDateString();
            $disability_pensioner->create_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
            $disability_pensioner->save();
        DB::commit();
        return redirect()->back()->with('success', 'EP Disability Beneficiary data has been successfully added.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("🏫 EP Disability Beneficiary data Form Submission failed.", [
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
        $disability3500Pensioner = Disability3500Pensioner::whereId($id)->firstOrFail();
        return view('dashboard.benf_3500_files.disability3500dataEdit', compact('disability3500Pensioner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validationRules = [
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
                $block_or_ulb = Blocks3500::where('block_id', $request->block)->value('block_name');
                $block_id = $validatedData['block'];
                $municipality_id = NULL;
                $block_or_ulb_id = $validatedData['block'];
                $gp_or_ward = Grampanchyat3500::where('gp_id', $request->grampanchayat)->value('gp_name');
                $gp_id = $validatedData['grampanchayat'];
                $ward_id = NULL;
                $gp_or_ward_id = $validatedData['grampanchayat'];
                $village = Village3500::where('village_id', $request->village)->value('village_name');
                $village_id = $validatedData['village'];
            } elseif ($request->ngo_address_type === "2") {
                $block_or_ulb = Municipality3500::where('municipality_id', $request->municipality)->value('municipality_name');
                $block_id = NULL;
                $municipality_id = $validatedData['municipality'];
                $block_or_ulb_id = $validatedData['municipality'];
                $ward_master_name = WardMaster3500::where('ward_code', $request->ward)->value('ward_name');
                $gp_or_ward = $ward_master_name;
                $gp_id = NULL;
                $ward_id = $validatedData['ward'];
                $gp_or_ward_id = $validatedData['ward'];
                $village = NULL;
                $village_id = NULL;
            }

            Disability3500Pensioner::where('id', $id)->update([
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

            /*\DB::connection('rupees_3500')
            ->table('disability_Pensioner_Bene_With_percent_80AndAbove_24_03_2025')
            ->where('id', $id)
            ->update([
                'block_or_ulb'     => $block_or_ulb,
                'block_id'         => $block_id,
                'municipality_id'  => $municipality_id,
                'block_or_ulb_id'  => $block_or_ulb_id,
                'gp_or_ward'       => $gp_or_ward,
                'gp_id'            => $gp_id,
                'ward_id'          => $ward_id,
                'gp_or_ward_id'    => $gp_or_ward_id,
                'village'          => $village,
                'village_id'       => $village_id,
            ]);*/

            DB::commit();
            return redirect()->route('admin.disability3500data.index')->with('info', 'Address Updated successfully.');
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


    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        return $id;
    }
}
