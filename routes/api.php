<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profile/update-password', [AccountController::class, 'updatePassword'])->name('api.profile.update-password');
});
