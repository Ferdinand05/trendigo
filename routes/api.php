<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// midtrans notif callback / auto
Route::post('/midtrans/notification', [CheckoutController::class, 'notification']);
