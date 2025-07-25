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
        /*Skip logging during Artisan CLI (like migrations, seeding, etc.)*/
        if (app()->runningInConsole()) {
            return;
        }

        /*Optional: Only enable query logging in local/dev environments*/
        if (!config('app.debug') || !env('LOG_DB_QUERIES', false)) {
            return;
        }

        /*Listen to DB queries*/
        DB::listen(function ($query) {
            $sql = $query->sql;
            $bindings = $query->bindings;
            $time = $query->time;

            /*Default controller/method*/
            $controller = $method = 'N/A';

            /*Extract controller and method name*/
            if ($action = Route::currentRouteAction()) {
                if (strpos($action, '@') !== false) {
                    [$controller, $method] = explode('@', class_basename($action));
                } else {
                    $controller = class_basename($action);
                }
            }

            $timestamp = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s A');

            $logPayload = [
                'Timestamp'      => $timestamp,
                'Controller'     => $controller,
                'Method'         => $method,
                'Executed Query' => $sql,
                'Bindings'       => $bindings,
                'Time (ms)'      => $time,
            ];

            /*Log to multiple channels*/
            Log::channel('daily')->info(json_encode($logPayload, JSON_PRETTY_PRINT));
            Log::channel('query')->info(json_encode($logPayload, JSON_PRETTY_PRINT));
        });
    }
}
