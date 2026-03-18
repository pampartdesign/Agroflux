<?php

use App\Console\Commands\EvaluateSensorRules;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*
|--------------------------------------------------------------------------
| Sensor Rules Evaluation
|--------------------------------------------------------------------------
| Runs every minute. The command itself deduplicates — a time-based rule
| only fires once per day even if the scheduler ticks multiple times within
| the trigger window.
|
| To test manually:
|   php artisan sensor-rules:evaluate
*/
Schedule::command(EvaluateSensorRules::class)
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
