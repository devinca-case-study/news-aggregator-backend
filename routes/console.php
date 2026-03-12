<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

foreach (config('news.providers') as $provider => $config) {
    $minutes = $config['rotation_minutes'];
    
    Schedule::command("news:fetch {$provider}")
        ->cron("*/{$minutes} * * * *")
        ->withoutOverlapping();
}