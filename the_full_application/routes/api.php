<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ThirtyFiveHundredPensionerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group assigned
| the "api" middleware group. Enjoy building your API!
|
*/

/*Example route to check authentication (default Laravel example)*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*Pensioner APIs*/
Route::get('/disability-pensioners', [ThirtyFiveHundredPensionerController::class, 'getDisabilityPensioners']);
Route::get('/oldage-pensioners', [ThirtyFiveHundredPensionerController::class, 'getOldAgePensioners']);

Route::get('/disability-pensioner/{aadhar_no}', [ThirtyFiveHundredPensionerController::class, 'getDisabilityPensionerByAadhar']);
Route::get('/oldage-pensioner/{aadhar_no}', [ThirtyFiveHundredPensionerController::class, 'getOldAgePensionerByAadhar']);

Route::get('/disability-pensioner-data/{aadhar_no}', [ThirtyFiveHundredPensionerController::class, 'getDisabilityPensionerByAadharRequiredData']);
Route::get('/oldage-pensioner-data/{aadhar_no}', [ThirtyFiveHundredPensionerController::class, 'getOldAgePensionerByAadharRequiredData']);