<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->otp_for_forgot_password === '123456') {
                // Exclude the password change GET and POST routes, and logout route to prevent redirect loops.
                if (!$request->routeIs('admin.myprofile.changePassword') && 
                    !$request->routeIs('admin.myprofile.changePasswordStore') && 
                    !$request->routeIs('logout') && 
                    !$request->is('logout')) {
                    
                    return redirect()->route('admin.myprofile.changePassword')
                        ->with('warning', 'You must change your password to proceed.');
                }
            }
        }

        return $next($request);
    }
}
