<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->post('/register', [UserController::class, 'register']);
