<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\District;
use App\Models\Subdivision;
use App\Models\Municipality;
use App\Models\Block;
use App\Models\Grampanchayat;
use App\Models\Village;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

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
        $data['districts'] = District::where("state_id", $request->state_id)->orderBy("district_name", "asc")
                                ->get(["district_name", "district_id"]);

        return response()->json($data);
    }

    public function fetchMunicipality(Request $request): JsonResponse
    {
        $data['municipalities'] = Municipality::where("district_id", $request->district_id)->orderBy("municipality_name", "asc")
                                ->get(["municipality_name", "municipality_id"]);
  
        return response()->json($data);
    }

    public function fetchBlock(Request $request): JsonResponse
    {
        $data['blocks'] = Block::where("district_id", $request->district_id)->orderBy("block_name", "asc")
                                ->get(["block_name", "block_id"]);
  
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
}
