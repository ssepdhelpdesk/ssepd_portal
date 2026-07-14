<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\New_Dashboard_Controllers\{
    NewDashboardController
};

Route::middleware([
    'auth',
    'prevent-back-history',
    'track.session',
    'verified',
    'force.password.change',
])
->prefix('new-dashboard')
->as('new_dashboard.')
->group(function () {

    Route::get('/', [NewDashboardController::class, 'index'])
        ->name('dashboard');

});