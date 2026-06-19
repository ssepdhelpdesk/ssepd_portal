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
        
        if ($user) {
            $userRoles = $user->roles->pluck('id')->toArray();
            $instituteRoles = [16, 17, 22, 23, 24];
            $hasInstituteRole = !empty(array_intersect($userRoles, $instituteRoles));
            
            if ($hasInstituteRole) {
                $redirectUrl = '/pia_institute_login';
            }
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect($redirectUrl);
    }
}
