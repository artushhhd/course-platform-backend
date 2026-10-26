<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Course Platform API is running.',
        'health' => url('/up'),
    ]);
});
