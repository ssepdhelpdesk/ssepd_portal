<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('oldage:aadhar-verify 200')
    ->everyFourMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();

Schedule::command('disability:aadhar-verify 200')
    ->everySixMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();