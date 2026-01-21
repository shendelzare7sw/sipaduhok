<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule notifications (deadline reminders H-1, calendar announcements H-3)
Schedule::command('notifications:schedule')
    ->dailyAt('06:00')
    ->timezone('Asia/Jakarta')
    ->description('Send scheduled notifications for deadlines and announcements');
