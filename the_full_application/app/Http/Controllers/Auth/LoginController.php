<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     * Overrides trait to use 'username' input field.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Determine the database field to check for username (email or user_id).
     *
     * @param  string  $usernameInput
     * @return string
     */
    protected function usernameField($usernameInput)
    {
        return filter_var($usernameInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_id';
    }

    /**
     * Get the needed authorization credentials from the request.
     * Dynamically maps to 'email' or 'user_id'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $usernameField = $this->usernameField($request->input($this->username()));

        return [
            $usernameField => $request->input($this->username()),
            'password'      => $request->input('password'),
        ];
    }

    /**
     * Get the rate limiting throttle key for CAPTCHA failures.
     * Keyed by client IP address and session ID to prevent credential-independent brute-forcing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function captchaThrottleKey(Request $request): string
    {
        $ip = $request->ip() ?? '127.0.0.1';
        $sessionId = ($request->hasSession() && $request->session()->getId())
            ? $request->session()->getId()
            : 'nosession';

        return 'captcha_fail|' . $ip . '|' . $sessionId;
    }

    /**
     * Validate the user login request, enforcing mandatory server-side CAPTCHA verification
     * with dedicated rate limiting for failed CAPTCHA attempts.
     * Fails closed: CAPTCHA is strictly mandatory in production and cannot be bypassed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        // Fail-closed enforcement: rely strictly on config('captcha.disable', false)
        // which evaluates to false unless APP_ENV is explicitly 'local' or 'testing'
        // and CAPTCHA_DISABLE=true. Unset APP_ENV or production always enforces CAPTCHA.
        $allowBypass = (bool) config('captcha.disable', false);

        if (!$allowBypass) {
            $captchaThrottleKey = $this->captchaThrottleKey($request);

            if ($this->limiter()->tooManyAttempts($captchaThrottleKey, 5)) {
                $seconds = $this->limiter()->availableIn($captchaThrottleKey);

                throw ValidationException::withMessages([
                    'captcha' => ["Too many failed CAPTCHA attempts. Please try again in {$seconds} seconds."],
                ])->status(Response::HTTP_TOO_MANY_REQUESTS);
            }
        }

        $rules = [
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ];

        if ($allowBypass) {
            $rules['captcha'] = 'nullable';
        } else {
            $rules['captcha'] = [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($captchaThrottleKey) {
                    if (!captcha_check(trim((string) $value))) {
                        $this->limiter()->hit($captchaThrottleKey, 60);
                        $fail('Invalid captcha code. Please try again.');
                    } else {
                        $this->limiter()->clear($captchaThrottleKey);
                    }
                },
            ];
        }

        $request->validate($rules, [
            'captcha.required' => 'Please enter the security verification answer.',
        ]);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        $userRoles = $user->roles->pluck('id')->toArray();
        $instituteRoles = [16, 17, 22, 23, 24];
        
        $hasInstituteRole = !empty(array_intersect($userRoles, $instituteRoles));
        $loginType = $request->input('login_type');

        if ($loginType === 'institute') {
            if (!$hasInstituteRole) {
                auth()->logout();
                return redirect()->back()
                    ->withInput($request->only('username', 'remember'))
                    ->withErrors(['username' => 'Standard users are not allowed to login from the Institute login page. Please use the standard login page.']);
            }
        } else {
            // Default/Standard login
            if ($hasInstituteRole) {
                auth()->logout();
                return redirect()->back()
                    ->withInput($request->only('username', 'remember'))
                    ->withErrors(['username' => 'Institute users are not allowed to login from here. Please use the Institute Login page.']);
            }
        }
        
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $redirectUrl = '/'; // Default redirect
        
        \Log::info('Logout method called', [
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ] : null,
        ]);

        if ($user) {
            $userRoles = $user->roles->pluck('id')->toArray();
            $instituteRoles = [16, 17, 22, 23, 24];
            $hasInstituteRole = !empty(array_intersect($userRoles, $instituteRoles));
            
            \Log::info('Checking user roles for logout redirect', [
                'user_roles' => $userRoles,
                'institute_roles' => $instituteRoles,
                'has_institute_role' => $hasInstituteRole,
            ]);

            if ($hasInstituteRole) {
                $redirectUrl = '/pia_institute_login';
            }
        }

        // Handle login history logout update
        $loginHistoryId = $request->session()->get('login_history_id');
        if ($loginHistoryId) {
            $loginHistory = \App\Models\LoginHistory::find($loginHistoryId);
            if ($loginHistory && $loginHistory->login_time) {
                $loginHistory->logout_date_time = \Carbon\Carbon::now('Asia/Kolkata');
                $loginHistory->logout_time = \Carbon\Carbon::now('Asia/Kolkata')->format('H:i:s');
                $loginTime = \Carbon\Carbon::createFromFormat('H:i:s', $loginHistory->login_time);
                $logoutTime = \Carbon\Carbon::createFromFormat('H:i:s', $loginHistory->logout_time);
                $sessionDuration = $logoutTime->diff($loginTime)->format('%H:%I:%S');
                $loginHistory->session_duration = $sessionDuration;                                
                $loginHistory->save();
                $request->session()->forget('login_history_id');
            } else {
                \Log::warning("Login history record with ID $loginHistoryId not found during LoginController logout.");
            }
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        \Log::info('Logout completed', [
            'redirectUrl' => $redirectUrl,
        ]);

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect($redirectUrl);
    }
}
