<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\AutoRejectExpiredPendingBookings;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('booking:auto-reject-expired', function () {
    $this->call(AutoRejectExpiredPendingBookings::class);
})->purpose('Auto-reject pending bookings that have passed start time');