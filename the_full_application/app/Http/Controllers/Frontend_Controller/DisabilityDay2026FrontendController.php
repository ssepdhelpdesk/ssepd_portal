<?php

namespace App\Http\Controllers\Frontend_Controller;

use App\Http\Controllers\Controller;
use App\Mail\StallRegistrationConfirmationMail;
use App\Mail\TrackingOtpMail;
use App\Models\DisabilityDayOtp;
use App\Models\DisabilityDayRegistration;
use App\Models\District;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DisabilityDay2026FrontendController extends Controller
{
    /**
     * Show the public stall registration form.
     */
    public function create()
    {
        $states = State::where('status', 1)->orderBy('state_name')->get();
        $districts = District::where('status', 1)->orderBy('district_name')->get();

        return view('main_website.disability_day_registration', compact('states', 'districts'));
    }

    /**
     * Handle public stall registration form submission.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string|max:255',
            'contact_person'    => 'required|string|max:255',
            'email'             => 'required|string|email|max:150',
            'phone'             => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'purpose'           => 'required|string|max:2000',
            'at'                => 'required|string|max:255',
            'po'                => 'required|string|max:255',
            'ps'                => 'required|string|max:255',
            'district_id'       => 'required|exists:districts,id',
            'state_id'          => 'required|exists:states,id',
            'pin'               => ['required', 'regex:/^[1-9][0-9]{5}$/'],
        ], [
            'organization_name.required' => 'Please enter the organization name.',
            'contact_person.required'    => 'Please enter the contact person name.',
            'email.required'             => 'Please enter your email address.',
            'email.email'                => 'Please enter a valid email address.',
            'phone.required'             => 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.',
            'phone.regex'                => 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.',
            'purpose.required'           => 'Please describe the purpose of requirement of stall.',
            'at.required'                => 'Please enter AT (Village / Street / Location).',
            'po.required'                => 'Please enter Post Office (PO).',
            'ps.required'                => 'Please enter Police Station (PS).',
            'district_id.required'       => 'Please select a District.',
            'district_id.exists'         => 'The selected District is invalid.',
            'state_id.required'          => 'Please select a State.',
            'state_id.exists'            => 'The selected State is invalid.',
            'pin.required'               => 'Please enter your 6-digit PIN code.',
            'pin.regex'                  => 'Please enter a valid 6-digit Indian PIN code (e.g. 751001).',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = strtolower(trim($request->input('email')));
        $phone = trim($request->input('phone'));
        $appliedAt = Carbon::now('Asia/Kolkata');

        $district = District::find($request->input('district_id'));
        $state = State::find($request->input('state_id'));

        $registration = null;
        $maxRetries = 5;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $registrationNo = DisabilityDayRegistration::generateUniqueRegistrationNo($appliedAt);

                $registration = DB::transaction(function () use ($request, $email, $phone, $appliedAt, $district, $state, $registrationNo) {
                    return DisabilityDayRegistration::create([
                        'registration_no'   => $registrationNo,
                        'organization_name' => trim($request->input('organization_name')),
                        'contact_person'    => trim($request->input('contact_person')),
                        'email'             => $email,
                        'phone'             => $phone,
                        'purpose'           => trim($request->input('purpose')),
                        'at'                => trim($request->input('at')),
                        'po'                => trim($request->input('po')),
                        'ps'                => trim($request->input('ps')),
                        'district_id'       => $district ? $district->id : null,
                        'district_name'     => $district ? $district->district_name : null,
                        'state_id'          => $state ? $state->id : null,
                        'state_name'        => $state ? $state->state_name : null,
                        'pin'               => trim($request->input('pin')),
                        'applied_at'        => $appliedAt,
                        'allotted_stall'    => null, // Initially NULL per requirements
                        'created_by'        => Auth::check() ? Auth::id() : null,
                    ]);
                });

                break; // Creation succeeded
            } catch (\Illuminate\Database\QueryException $qe) {
                // If unique constraint collision occurs on registration_no, retry
                if ($qe->getCode() == 23000 && $attempt < $maxRetries) {
                    continue;
                }
                Log::error('Disability Day Registration DB Query Exception: ' . $qe->getMessage());
                return redirect()->back()
                    ->with('error', 'A database error occurred while processing your registration. Please try again.')
                    ->withInput();
            } catch (\Throwable $e) {
                Log::error('Disability Day Registration Exception: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'An unexpected error occurred while saving your application. Please try again.')
                    ->withInput();
            }
        }

        if (!$registration) {
            return redirect()->back()
                ->with('error', 'Unable to generate a unique registration number. Please try submitting again.')
                ->withInput();
        }

        // Attempt email confirmation delivery outside transaction
        $mailSent = false;
        try {
            Mail::to($registration->email)->send(new StallRegistrationConfirmationMail($registration));
            $mailSent = true;
        } catch (\Throwable $mailException) {
            // CRITICAL: Registration is safely committed. Email failure must never rollback record.
            Log::error('Disability Day Registration confirmation email delivery failed: ' . $mailException->getMessage(), [
                'registration_no' => $registration->registration_no,
                'email'           => $registration->email,
            ]);
            $mailSent = false;
        }

        return redirect()->route('disability_day.registration')->with([
            'registration_success' => true,
            'registration_no'      => $registration->registration_no,
            'organization_name'    => $registration->organization_name,
            'contact_person'       => $registration->contact_person,
            'email'                => $registration->email,
            'applied_at'           => $registration->applied_at->format('d M Y, h:i A'),
            'mail_sent'            => $mailSent,
        ]);
    }

    /**
     * Show the public application tracking initial form.
     */
    public function trackForm()
    {
        // If applicant already has an active verified session, route directly to details
        if (session()->has('disability_day_verified_reg_id')) {
            $verifiedUntil = session('disability_day_verified_until', 0);
            if (time() < $verifiedUntil) {
                return redirect()->route('disability_day.track.details');
            }
            session()->forget(['disability_day_verified_reg_id', 'disability_day_verified_until']);
        }

        return view('main_website.disability_day.track');
    }

    /**
     * Process tracking request: validate credentials and dispatch OTP.
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_no' => 'required|string|max:64',
            'email'           => 'required|string|email|max:150',
        ], [
            'registration_no.required' => 'Please enter your Registration Number.',
            'email.required'           => 'Please enter your registered Email Address.',
            'email.email'              => 'Please enter a valid email address format.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $regNo = trim($request->input('registration_no'));
        $email = strtolower(trim($request->input('email')));

        $registration = DisabilityDayRegistration::where('registration_no', $regNo)
            ->where('email', $email)
            ->first();

        // Enforce enumeration prevention: treat registration_no + email as an atomic pair
        if (!$registration) {
            return redirect()->back()
                ->with('error', 'No registration found matching both the provided Registration Number and Email Address. Please check your details and try again.')
                ->withInput(['registration_no' => $regNo, 'email' => $email]);
        }

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Invalidate any previous pending OTPs for this registration
        DisabilityDayOtp::where('registration_id', $registration->id)
            ->whereNull('verified_at')
            ->delete();

        // Save new OTP record (hashed)
        DisabilityDayOtp::create([
            'registration_id' => $registration->id,
            'email'           => $email,
            'otp_hash'        => Hash::make($otp),
            'expires_at'      => Carbon::now()->addMinutes(5),
            'attempts'        => 0,
            'ip_address'      => $request->ip(),
        ]);

        // Send OTP email
        $mailSent = false;
        try {
            Mail::to($email)->send(new TrackingOtpMail($otp, $registration));
            $mailSent = true;
        } catch (\Throwable $e) {
            Log::error('Disability Day tracking OTP email failed: ' . $e->getMessage(), [
                'registration_id' => $registration->id,
            ]);
            $mailSent = false;
        }

        // Mask email for user display: e.g. j***@domain.com
        $emailParts = explode('@', $email);
        $namePart = $emailParts[0];
        $domainPart = $emailParts[1] ?? '';
        $maskedName = strlen($namePart) > 2 ? substr($namePart, 0, 2) . str_repeat('*', strlen($namePart) - 2) : $namePart . '**';
        $maskedEmail = $maskedName . '@' . $domainPart;

        session([
            'disability_day_tracking' => [
                'registration_id' => $registration->id,
                'registration_no' => $registration->registration_no,
                'email'           => $email,
                'masked_email'    => $maskedEmail,
                'otp_sent_at'     => time(),
                'expires_at'      => time() + 300,
            ]
        ]);

        $notice = $mailSent
            ? "A 6-digit verification code has been dispatched to {$maskedEmail}. Please enter it below."
            : "Verification code generated for {$maskedEmail}. If email is delayed, you may resend after the cooldown.";

        return redirect()->route('disability_day.track.verify_otp')->with('info', $notice);
    }

    /**
     * Show the OTP verification form.
     */
    public function verifyOtpForm()
    {
        $tracking = session('disability_day_tracking');
        if (!$tracking || !isset($tracking['registration_id'])) {
            return redirect()->route('disability_day.track')
                ->with('error', 'Please enter your Registration Number and Email to initiate tracking.');
        }

        return view('main_website.disability_day.verify-otp', compact('tracking'));
    }

    /**
     * Verify the applicant's entered OTP.
     */
    public function verifyOtp(Request $request)
    {
        $tracking = session('disability_day_tracking');
        if (!$tracking || !isset($tracking['registration_id'])) {
            return redirect()->route('disability_day.track')
                ->with('error', 'Tracking session expired. Please start again.');
        }

        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Please enter the 6-digit OTP code.',
            'otp.digits'   => 'The OTP must be exactly 6 digits.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $otpRecord = DisabilityDayOtp::where('registration_id', $tracking['registration_id'])
            ->whereNull('verified_at')
            ->latest('id')
            ->first();

        if (!$otpRecord) {
            return redirect()->route('disability_day.track')
                ->with('error', 'No active verification code found. Please request a new OTP.');
        }

        if ($otpRecord->isExpired()) {
            return redirect()->back()
                ->withErrors(['otp' => 'The verification code has expired (5-minute limit). Please request a new OTP.']);
        }

        if ($otpRecord->isMaxAttemptsExceeded(5)) {
            return redirect()->back()
                ->withErrors(['otp' => 'Maximum verification attempts exceeded (5 attempts). Please request a fresh OTP.']);
        }

        if (!$otpRecord->verify($request->input('otp'))) {
            $otpRecord->increment('attempts');
            $remaining = 5 - $otpRecord->attempts;
            return redirect()->back()
                ->withErrors(['otp' => "Incorrect verification code. You have {$remaining} attempt(s) remaining."]);
        }

        // OTP is valid!
        $otpRecord->update(['verified_at' => Carbon::now()]);

        // Promote session to verified application view
        session()->forget('disability_day_tracking');
        session([
            'disability_day_verified_reg_id' => $tracking['registration_id'],
            'disability_day_verified_until'  => time() + 900, // 15-minute access window
        ]);

        return redirect()->route('disability_day.track.details')
            ->with('success', 'Verification successful! Here are your application details.');
    }

    /**
     * Resend a fresh OTP to the applicant.
     */
    public function resendOtp(Request $request)
    {
        $tracking = session('disability_day_tracking');
        if (!$tracking || !isset($tracking['registration_id'])) {
            return redirect()->route('disability_day.track')
                ->with('error', 'Tracking session expired. Please start again.');
        }

        // Enforce 60-second cooldown between resends
        $lastSent = $tracking['otp_sent_at'] ?? 0;
        $secondsSince = time() - $lastSent;
        if ($secondsSince < 60) {
            $wait = 60 - $secondsSince;
            return redirect()->back()->with('error', "Please wait {$wait} seconds before requesting a new OTP.");
        }

        $registration = DisabilityDayRegistration::find($tracking['registration_id']);
        if (!$registration) {
            session()->forget('disability_day_tracking');
            return redirect()->route('disability_day.track')
                ->with('error', 'Registration record not found.');
        }

        // Invalidate previous OTPs
        DisabilityDayOtp::where('registration_id', $registration->id)
            ->whereNull('verified_at')
            ->delete();

        // Generate fresh OTP
        $otp = (string) random_int(100000, 999999);

        DisabilityDayOtp::create([
            'registration_id' => $registration->id,
            'email'           => $registration->email,
            'otp_hash'        => Hash::make($otp),
            'expires_at'      => Carbon::now()->addMinutes(5),
            'attempts'        => 0,
            'ip_address'      => $request->ip(),
        ]);

        try {
            Mail::to($registration->email)->send(new TrackingOtpMail($otp, $registration));
        } catch (\Throwable $e) {
            Log::error('Disability Day tracking OTP resend email failed: ' . $e->getMessage());
        }

        $tracking['otp_sent_at'] = time();
        $tracking['expires_at']  = time() + 300;
        session(['disability_day_tracking' => $tracking]);

        return redirect()->back()->with('success', 'A new 6-digit verification code has been dispatched to your email.');
    }

    /**
     * Display verified applicant details.
     */
    public function applicationDetails()
    {
        $regId = session('disability_day_verified_reg_id');
        $validUntil = session('disability_day_verified_until', 0);

        if (!$regId || time() > $validUntil) {
            session()->forget(['disability_day_verified_reg_id', 'disability_day_verified_until']);
            return redirect()->route('disability_day.track')
                ->with('error', 'Your tracking verification session has expired. Please verify with OTP again.');
        }

        $registration = DisabilityDayRegistration::with(['district', 'state'])->findOrFail($regId);

        return view('main_website.disability_day.application-details', compact('registration'));
    }

    /**
     * End applicant tracking session.
     */
    public function endTrackingSession()
    {
        session()->forget([
            'disability_day_tracking',
            'disability_day_verified_reg_id',
            'disability_day_verified_until',
        ]);

        return redirect()->route('disability_day.track')
            ->with('info', 'Your tracking session has been safely closed.');
    }
}

