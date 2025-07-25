<?php

namespace App\Http;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel

{
	.....

	.....

    /**

     * The application's route middleware.

     *

     * These middleware may be assigned to groups or used individually.

     *

     * @var array

     */

    protected $routeMiddleware = [

        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --memory=1024')->everyMinute();
    }

}