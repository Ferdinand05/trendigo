<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// jalankan setiap menit
Schedule::command('app:expired-order')->dailyAt('12:00');

// jalankan per hari
// Schedule::command('app:expired-order')->daily();


// chart reminder by mail
Schedule::command('app:cart-reminder')->dailyAt('12:00');
