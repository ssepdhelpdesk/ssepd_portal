<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
