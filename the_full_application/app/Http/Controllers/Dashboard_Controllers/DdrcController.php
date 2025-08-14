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

/*Controller Requirements*/
use App\Models\DdrcDetails;

class DdrcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.ddrc.ddrc_details', compact('user'));
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
        $validationRules = [
            'ddrc_name' => 'required',
            'file_geo_tagged_image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'ddrc_latitude' => 'required|numeric|between:-90,90',
            'ddrc_longitude' => 'required|numeric|between:-180,180',
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

        $user = auth()->user();
        $alreadyExists = DdrcDetails::where('user_table_id', $user->id)->exists();
        if ($alreadyExists) {
            return back()->with('error', 'You have already provided the details.');
        }

        $previousId = DdrcDetails::latest()->value('id') ?? 0;
        $currentDate = now()->format('d/m/Y');
        $randomNumber = mt_rand(1000, 9999);
        $ddrcSystemGeneratedRegNo = "SSEPD/DDRC/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";
        $ddrcSystemGenRegNo = str_replace('/', '_', $ddrcSystemGeneratedRegNo);

        $folderPath = public_path("ddrc_files/{$ddrcSystemGenRegNo}");
        /*A folder i.e. storage/ddrc_files is created inside the root directory ssepd_ngo_working_portal/storage/ddrc_files*/
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/ddrc_files/{$ddrcSystemGenRegNo}";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        if (!file_exists($externalPath)) {
            mkdir($externalPath, 0755, true);
        }

        if ($request->hasFile('file_geo_tagged_image')) {
            $imageFile = $request->file('file_geo_tagged_image');
            $imageExtension = $imageFile->getClientOriginalExtension();
            $imageRandomName = 'DDRC_' . Str::random(40) . '.' . $imageExtension;

            $imageStoredPath = $imageFile->storeAs("ddrc_files/{$ddrcSystemGenRegNo}", $imageRandomName, 'public');
            copy(storage_path("app/public/{$imageStoredPath}"), "{$folderPath}/{$imageRandomName}");
            copy(storage_path("app/public/{$imageStoredPath}"), "{$externalPath}/{$imageRandomName}");
        }

        $ddrcDetails = new DdrcDetails();
        $ddrcDetails->user_table_id = $user->id;
        $ddrcDetails->ddrc_name = $validatedData['ddrc_name'];
        $ddrcDetails->file_geo_tagged_image = $imageStoredPath;
        $ddrcDetails->ddrc_latitude = $validatedData['ddrc_latitude'];
        $ddrcDetails->ddrc_longitude = $validatedData['ddrc_longitude'];
        $ddrcDetails->system_stored_latitude = $request->system_stored_latitude;
        $ddrcDetails->system_stored_longitude = $request->system_stored_longitude;
        $ddrcDetails->ddrc_address_type = $validatedData['ngo_address_type'];
        $ddrcDetails->state_id = $validatedData['state'];
        $ddrcDetails->district_id = $validatedData['district'];
        $ddrcDetails->block_id = $request->input('block', null);
        $ddrcDetails->gp_id = $request->input('grampanchayat', null);
        $ddrcDetails->village_id = $request->input('village', null);
        $ddrcDetails->municipality_id = $request->input('municipality', null);
        $ddrcDetails->pin = $validatedData['pin'];
        $ddrcDetails->ddrc_postal_address_at = $validatedData['ngo_postal_address_at'];
        $ddrcDetails->ddrc_postal_address_post = $validatedData['ngo_postal_address_post'];
        $ddrcDetails->ddrc_postal_address_via = $validatedData['ngo_postal_address_via'];
        $ddrcDetails->ddrc_postal_address_ps = $validatedData['ngo_postal_address_ps'];
        $ddrcDetails->ddrc_postal_address_district = $validatedData['ngo_postal_address_district'];
        $ddrcDetails->ddrc_postal_address_pin = $validatedData['ngo_postal_address_pin'];
        $ddrcDetails->is_active = 'active';
        $ddrcDetails->created_date = now()->setTimezone('Asia/Kolkata')->toDateString();
        $ddrcDetails->created_time = now()->setTimezone('Asia/Kolkata')->toTimeString();
        $ddrcDetails->created_by = Auth::id() ?? null;
        $ddrcDetails->status = 1;
        $ddrcDetails->save();
        return back()->with('success', 'You have successfully provided the details.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
