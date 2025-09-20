<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HandleNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// midtrans notif callback / auto
Route::post('/midtrans/notification', [HandleNotificationController::class, 'notification']);
