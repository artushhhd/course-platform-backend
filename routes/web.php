<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Course Platform API is running.',
        'health' => url('/up'),
    ]);
})->withoutMiddleware([
    StartSession::class,
    ShareErrorsFromSession::class,
]);
