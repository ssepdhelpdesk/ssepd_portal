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
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $this->app->booted(function () {
            DB::listen(function ($query) {
                try {
                    /*Skip session/cache queries to avoid recursion*/
                    if (str_contains($query->sql, 'sessions') || str_contains($query->sql, 'cache')) {
                        return;
                    }

                    $sql      = $query->sql;
                    $bindings = $query->bindings;
                    $time     = $query->time;

                    /*Controller & Method extraction*/
                    $controller = $method = 'N/A';
                    if (Route::current()) {
                        $action = Route::currentRouteAction();
                        if ($action && strpos($action, '@') !== false) {
                            [$controller, $method] = explode('@', class_basename($action));
                        } elseif ($action) {
                            $controller = class_basename($action);
                        }
                    }

                    /*Timestamp*/
                    $timestamp = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s A');

                    /*Interpolated SQL for readability*/
try {

    $pdo = DB::getPdo();

    $formattedBindings = array_map(function ($binding) use ($pdo) {

        if (is_null($binding)) {
            return 'NULL';
        }

        if (is_bool($binding)) {
            return $binding ? '1' : '0';
        }

        if (is_array($binding) || is_object($binding)) {
            return "'" . json_encode($binding) . "'";
        }

        return $pdo->quote($binding);

    }, $bindings);

    $segments = explode('?', $sql);

    $interpolatedSql = '';

    foreach ($segments as $index => $segment) {

        $interpolatedSql .= $segment;

        if (isset($formattedBindings[$index])) {
            $interpolatedSql .= $formattedBindings[$index];
        }
    }

} catch (\Throwable $e) {

    $interpolatedSql = $sql;
}

                    /*Beautify bindings*/
                    $bindingsPreview = json_encode($bindings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    if ($bindingsPreview === false) {
                        $bindingsPreview = json_encode(['message' => 'Bindings could not be encoded']);
                    }

                    /*Safe IP Address retrieval*/
                    $ipAddress = null;
                    if (!app()->runningInConsole()) {
                        try {
                            $ipAddress = request()->ip();
                        } catch (\Throwable $e) {
                            $ipAddress = null;
                        }
                    }

                    /*Function to beautify SQL*/
                    $beautifySql = function($sql) {
                        $keywords = ['SELECT', 'FROM', 'WHERE', 'JOIN', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'ON', 'AND', 'OR', 'ORDER BY', 'GROUP BY', 'LIMIT', 'INSERT', 'UPDATE', 'DELETE', 'VALUES', 'SET'];
                        $sql = preg_replace_callback('/\b(' . implode('|', $keywords) . ')\b/i', function($matches) {
                            return "\n" . strtoupper($matches[0]);
                        }, $sql);
                        $sql = preg_replace('/\s+/', ' ', $sql);
                        return trim($sql);
                    };

                    $rawSqlPretty         = $beautifySql($sql);
                    $interpolatedSqlPretty = $beautifySql($interpolatedSql);

                    /*Log payload*/
                    $logPayload = [
                        'Timestamp'        => $timestamp,
                        'Controller'       => $controller,
                        'Method'           => $method,
                        'Raw SQL'          => $rawSqlPretty,
                        'Interpolated SQL' => $interpolatedSqlPretty,
                        'Bindings'         => json_decode($bindingsPreview, true),
                        'Time (ms)'        => $time,
                        'IP Address'       => $ipAddress,
                    ];

                    /*Beautified JSON log*/
                    $beautifiedLog = "\n" .
                        "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
                        "📝 SQL Query Log @ {$timestamp}\n" .
                        "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
                        json_encode($logPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) .
                        "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

                    /*Write to daily log*/
                    Log::channel('daily')->info($beautifiedLog);
                    Log::channel('query')->info($beautifiedLog);
                    Log::channel('single')->info($beautifiedLog);

                } catch (\Throwable $e) {
                    Log::error("Query Listener Failed", [
                        'message' => $e->getMessage(),
                        'file'    => $e->getFile(),
                        'line'    => $e->getLine(),
                    ]);
                }
            });
        });
    }
}
