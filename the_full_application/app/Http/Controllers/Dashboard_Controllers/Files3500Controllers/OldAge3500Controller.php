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
use App\Models\District3500;
use App\Models\Blocks3500;
use App\Models\Municipality3500;
use App\Models\Grampanchyat3500;
use App\Models\Village3500;
use App\Models\WardMaster3500;
use Yajra\DataTables\Facades\DataTables;

class OldAge3500Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /*public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(
                OldAge3500Pensioner::select('*')
            )
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $editUrl = route('admin.oldage3500data.edit', $row->id);
                $deleteUrl = route('admin.oldage3500data.delete', $row->id);

                return '
                <a href="'.$editUrl.'" class="btn btn-sm btn-primary">Edit</a>
                <button type="button" class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('dashboard.benf_3500_files.oldage3500data');
    }*/

    public function index(Request $request)
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

            $query = OldAge3500Pensioner::select('*')->where('district_id', $postedDistrict)->where('status', 'Active')
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

                /*if (auth()->user()->can('pension-3500-delete')) {
                    $deleteUrl = route('admin.oldage3500data.delete', $row->id);
                    $buttons .= '<a href="'.$deleteUrl.'" class="btn btn-sm btn-danger">Delete</a>';
                }*/

                return $buttons;
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('dashboard.benf_3500_files.oldage3500data');
    }

    public function index_district_block_ulb(Request $request)
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
        $oldAge3500Pensioner = OldAge3500Pensioner::whereId($id)->firstOrFail();
        return view('dashboard.benf_3500_files.oldage3500dataEdit', compact('oldAge3500Pensioner'));
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

            OldAge3500Pensioner::where('id', $id)->update([
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
            ->table('oldAge_Pensioner_Beneficiaries_WithAge_80AndAbove_04_03_2025')
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


    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        return $id;
    }
}