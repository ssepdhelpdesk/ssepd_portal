<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use App\Models\LoginHistory;
use Carbon\Carbon;


class TrackUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $agent = new Agent();
            $deviceType = $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop');

            if (!$user->currentLoginHistory()) {
                $loginHistory = new LoginHistory();
                $loginHistory->user_id = $user->id;
                $loginHistory->ipv4_address = $request->ip();
                $loginHistory->ipv6_address = $_SERVER['REMOTE_ADDR'];
                $ip = $request->ip();
                $ipApiUrl = "http://ip-api.com/json/{117.211.75.216}";
                $locationData = @json_decode(file_get_contents($ipApiUrl), true);
                if($locationData && $locationData['status'] === 'success')
                {
                    $location = [
                        'city' => $locationData['city'] ?? null,
                        'region' => $locationData['regionName'] ?? null,
                        'country' => $locationData['country'] ?? null,
                        'latitude' => $locationData['lat'] ?? null,
                        'longitude' => $locationData['lon'] ?? null,
                    ];
                    $loginHistory->login_location = json_encode($location);
                }
                $loginHistory->device_type = $deviceType;
                $loginHistory->login_date_time = Carbon::now('Asia/Kolkata');
                $loginHistory->login_time = Carbon::now('Asia/Kolkata')->format('H:i:s');
                $loginHistory->save();

                // Store the login history ID in session for easy access on logout
                $request->session()->put('login_history_id', $loginHistory->id);
            }
        }

        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (Auth::check()) {
            // Check if the request is for logging out
            if ($request->is('logout') || $request->is('*/logout')) { // Adjust this based on your logout route
                $loginHistoryId = $request->session()->get('login_history_id');

                if ($loginHistoryId) {
                    $loginHistory = LoginHistory::find($loginHistoryId);
                    $loginHistory->logout_time = Carbon::now('Asia/Kolkata'); // Set timezone for logout time
                    $loginHistory->session_duration = $loginHistory->logout_time->diffInSeconds($loginHistory->login_time);
                    $loginHistory->save();

                    // Remove the session value
                    $request->session()->forget('login_history_id');
                }
            }
        }
    }
}
