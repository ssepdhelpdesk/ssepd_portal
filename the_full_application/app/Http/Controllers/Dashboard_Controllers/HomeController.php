<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply 'auth' middleware except for the 'refreshCaptcha' method
        $this->middleware('auth')->except('refreshCaptcha');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
        //return view('home');
    }

    /**
     * Refresh captcha image.
     *
     * @return JsonResponse
     */
    public function refreshCaptcha(): JsonResponse
    {
        return response()->json(['captcha' => captcha_img('math_blue')]);
    }
}
