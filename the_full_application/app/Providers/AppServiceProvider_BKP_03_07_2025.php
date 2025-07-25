<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log all executed queries
        DB::listen(function ($query) {
            $sql = $query->sql;
            $bindings = $query->bindings;
            $time = $query->time;

            // Get the controller and method name
            $action = Route::currentRouteAction();
            $controller = $method = 'N/A';
            
            if ($action) {
                $actionParts = explode('@', class_basename($action));
                $controller = $actionParts[0] ?? 'N/A';
                $method = $actionParts[1] ?? 'N/A';
            }

            // Get the current timestamp
            $dateTime = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s A');

            // Prepare the log data in JSON format
            $logData = [
                'Executed Query' => $sql,
                'Bindings' => $bindings,
                'Time (ms)' => $time,
                'Controller' => $controller,
                'Method' => $method,
                'Timestamp' => $dateTime,
            ];

            // Log the formatted JSON data
            Log::info(json_encode($logData, JSON_PRETTY_PRINT));

            // Add separator line for readability
            Log::info("*************************************************************");
        });
    }
}
