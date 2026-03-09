<?php

use App\Http\Controllers\TripInviteController;
use App\Http\Controllers\TripShareController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::get('/login', fn () => redirect('/admin/login'))
	->name('login');

Route::middleware('auth')->get('/invites/{token}', [TripInviteController::class, 'accept'])
	->name('trip-invites.accept');

Route::middleware('auth')->get('/admin/invites/{token}', [TripInviteController::class, 'accept']);

Route::get('/share/trips/{token}', [TripShareController::class, 'show'])
	->name('trip-share.show');
