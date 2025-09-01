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

            $controller = $method = 'N/A';

            /*Safely extract controller and method name if available*/
            if (Route::current() && $action = Route::currentRouteAction()) {
                if (strpos($action, '@') !== false) {
                    [$controller, $method] = explode('@', class_basename($action));
                } else {
                    $controller = class_basename($action);
                }
            }

            $timestamp = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s A');

            /*Try to interpolate bindings into SQL for readability*/
            try {
                $interpolatedSql = vsprintf(str_replace('?', "'%s'", $sql), $bindings);
            } catch (\Exception $e) {
                $interpolatedSql = 'Could not interpolate SQL: ' . $e->getMessage();
            }

            /*Optional: Limit very large bindings*/
            $bindingsPreview = strlen(json_encode($bindings)) > 2000
                ? ['message' => 'Bindings too large to log']
                : $bindings;

            /*Build log payload*/
            $logPayload = [
                'Timestamp'        => $timestamp,
                'Controller'       => $controller,
                'Method'           => $method,
                'Raw SQL'          => $sql,
                'Interpolated SQL' => $interpolatedSql,
                'Bindings'         => $bindingsPreview,
                'Time (ms)'        => $time,
                'User ID'          => Auth::check() ? Auth::id() : null,
            ];

            /*Log to daily and query-specific log channels*/
            Log::channel('daily')->info(json_encode($logPayload, JSON_PRETTY_PRINT));
            //Log::channel('query')->info(json_encode($logPayload, JSON_PRETTY_PRINT));
        });
    }
}
