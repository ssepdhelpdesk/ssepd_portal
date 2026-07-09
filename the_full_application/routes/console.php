<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*Schedule::command('oldage:aadhar-verify 200')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();

Schedule::command('disability:aadhar-verify 100')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();*/

    Schedule::command('ssepdpension:aadhar-verify 200')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();

    Schedule::command('app:update-the-aadhar-n-bank-mobile-data-using-nsap-api')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();