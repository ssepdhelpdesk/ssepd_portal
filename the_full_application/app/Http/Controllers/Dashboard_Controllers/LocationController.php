<?php

namespace App\Http\Controllers\Dashboard_Controllers;

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
use App\Models\State;
use App\Models\District;
use App\Models\Subdivision;
use App\Models\Municipality;
use App\Models\Block;
use App\Models\Grampanchayat;
use App\Models\Village;
use App\Models\WardMaster;

class LocationController extends Controller
{
    function __construct()
    {
     $this->middleware('permission:location-list|location-create|location-edit|location-delete', ['only' => ['index','show', 'blockIndex', 'municipalityIndex']]);
     $this->middleware('permission:location-create', ['only' => ['create','store']]);
     $this->middleware('permission:location-edit', ['only' => ['edit','update']]);
     $this->middleware('permission:location-delete', ['only' => ['destroy']]);
 }

 public function blockIndex(): View
 {
    $data['states'] = State::orderBy("state_id", "asc")->get(["state_name", "state_id"]);
    return view('dashboard.locations.villages.index', $data);        
}

public function municipalityIndex(): View
{
    $data['states'] = State::orderBy("state_id", "asc")->get(["state_name", "state_id"]);
    return view('dashboard.locations.municipalities.index', $data);        
}

public function fetchDistrict(Request $request): JsonResponse
{
    $user = auth()->user();
    $userRole = $user->role_id;

    $query = District::query()
    ->where("state_id", $request->state_id)
    ->orderBy("district_name", "asc");

    if (in_array($userRole, [1, 2, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
        
    } elseif (in_array($userRole, [4, 6])) {
        $district_id = Block::where('block_id', $user->posted_block)->value('district_id');
        $query->where('district_id', $district_id);

    } elseif ($userRole == 5) {
        $district_id = Municipality::where('municipality_id', $user->posted_municipality)->value('district_id');
        $query->where('district_id', $district_id);

    } elseif (in_array($userRole, [8, 10])) {
        $district_id = Subdivision::where('subdivision_id', $user->posted_subdiv)->value('district_id');
        $query->where('district_id', $district_id);

    } elseif (in_array($userRole, [9, 11])) {
        $query->where('district_id', $user->posted_district);
    }

    $data['districts'] = $query->get(["district_name", "district_id"]);

    return response()->json($data);
}


public function fetchMunicipality(Request $request): JsonResponse
{
    $user = auth()->user();
    $userRole = $user->role_id;

    $query = Municipality::query()->orderBy("municipality_name", "asc");

    if (in_array($userRole, [1, 2, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
        $query->where("district_id", $request->district_id);

    } elseif (in_array($userRole, [4, 6])) {
        $query->where("district_id", $request->district_id);

    } elseif ($userRole == 5) {
        $query->where("district_id", $request->district_id)
        ->where('municipality_id', $user->posted_municipality);

    } elseif (in_array($userRole, [8, 10])) {
        $query->where("district_id", $request->district_id)
        ->where('subdivision_id', $user->posted_subdiv);

    } elseif (in_array($userRole, [9, 11])) {
        $query->where('district_id', $user->posted_district);
    }

    $data['municipalities'] = $query->get(["municipality_name", "municipality_id"]);
    return response()->json($data);
}


public function fetchBlock(Request $request): JsonResponse
{
    $user = auth()->user();
    $userRole = $user->role_id;

    $query = Block::query()->orderBy("block_name", "asc");

    if (in_array($userRole, [1, 2, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
        $query->where("district_id", $request->district_id);

    } elseif (in_array($userRole, [4, 6])) {
        $query->where("district_id", $request->district_id)
        ->where('block_id', $user->posted_block);

    } elseif ($userRole == 5) {
        $query->where("district_id", $request->district_id);

    } elseif (in_array($userRole, [8, 10])) {
        $query->where("district_id", $request->district_id)
        ->where('subdivision_id', $user->posted_subdiv);

    } elseif (in_array($userRole, [9, 11])) {
        $query->where('district_id', $user->posted_district);
    }

    $data['blocks'] = $query->get(["block_name", "block_id"]);
    return response()->json($data);
}

public function fetchGrampanchayat(Request $request): JsonResponse
{
    $data['grampanchayats'] = Grampanchayat::where("block_id", $request->block_id)->orderBy("gp_name", "asc")
    ->get(["gp_name", "gp_id"]);

    return response()->json($data);
}

public function fetchVillage(Request $request): JsonResponse
{
    $data['villages'] = Village::where("gp_id", $request->gp_id)->orderBy("village_name", "asc")
    ->get(["village_name", "village_id"]);

    return response()->json($data);
}

public function fetchWard(Request $request): JsonResponse
{
    $data['wards'] = WardMaster::where("municipal_area_code", $request->municipality_id)->orderBy("ward_code", "asc")
    ->get(["ward_name", "ward_code"]);

    return response()->json($data);
}
}
