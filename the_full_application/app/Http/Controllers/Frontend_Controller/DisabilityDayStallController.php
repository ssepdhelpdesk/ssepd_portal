<?php

namespace App\Http\Controllers\Frontend_Controller;

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
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use App\Models\DisabilityDayStallRegistration;

class DisabilityDayStallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('frontend.disability_day_stall_registration.index');
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
        $validator = Validator::make($request->all(), [
            'name_of_the_organization' => 'required|string|max:255',
            'contact_person_name'      => 'required|string|max:255',
            'email'                    => 'required|email|max:255',
            'phone_number'             => ['required', 'regex:/^[0-9]{10}$/'],
            'purpose_of_requirement_of_stall' => 'required|string',
            'organization_address'     => 'required|string',
        ], [
            'name_of_the_organization.required' => 'Please enter the organization name.',
            'contact_person_name.required'      => 'Please enter the contact person name.',
            'email.required'                    => 'Please enter an email address.',
            'email.email'                       => 'Please enter a valid email address.',
            'phone_number.required'             => 'Please enter your mobile number.',
            'phone_number.regex'                => 'The mobile number must be exactly 10 digits.',
            'purpose_of_requirement_of_stall.required' => 'Please enter your purpose for stall requirement.',
            'organization_address.required'     => 'Please enter the organization address.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('error', 'Please correct the highlighted errors.');
        }

        try {

            $previousId = DisabilityDayStallRegistration::latest()->value('id') ?? 0;
            $currentDate = now()->format('d/m/Y');
            $randomNumber = mt_rand(1000, 9999);
            $stallSystemGenRegNo = "SSEPD/DISABILITYDAY/{$currentDate}/" . ($previousId + 1) . "{$randomNumber}";

            $data = $request->only([
                'name_of_the_organization',
                'contact_person_name',
                'email',
                'phone_number',
                'purpose_of_requirement_of_stall',
                'organization_address',
            ]);

            $data['registration_number'] = $stallSystemGenRegNo;
            $data['created_date'] = Carbon::now('Asia/Kolkata')->format('Y-m-d');
            $data['created_time'] = Carbon::now('Asia/Kolkata')->format('H:i:s');
            $data['created_by']   = Auth::check() ? Auth::user()->id : 0;
            $data['is_active']    = 'active';
            $data['status']       = 1;

            DisabilityDayStallRegistration::create($data);

            return redirect()->back()->with('success', 'Your stall application for the International Day of Persons with Disabilities 2025 has been successfully submitted. Please save your Registration Number for future reference: <span style="color:red; font-weight:bold;">' . $stallSystemGenRegNo . '</span>');

        } catch (\Exception $e) {
            return redirect()->back()
            ->with('error', 'Something went wrong while saving data: ' . $e->getMessage())
            ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function list()
    {
        $disabilityDayStallRegistrationData = DisabilityDayStallRegistration::orderBy('id','desc')->get();
        return view('frontend.disability_day_stall_registration.list', compact('disabilityDayStallRegistrationData'));
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
