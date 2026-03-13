<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TicketsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AccountController::class, 'showLogin'])->name('login');
Route::post('/login', [AccountController::class, 'login']);

Route::get('/register', [AccountController::class, 'showRegister'])->name('register');
Route::post('/register', [AccountController::class, 'register'])->name('registering');

Route::get('/reset-password', [AccountController::class, 'password'])->name('reset-password');

/*
|--------------------------------------------------------------------------
| Authentified routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Log out
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile-update');

    // Password
    Route::put('/reset-password', [AccountController::class, 'updatePassword'])->name('password-update');

    // Projects - Routes
    Route::get('/project', [ProjectsController::class, 'index'])->name('projects.projects');
    Route::get('/project-creation', [ProjectsController::class, 'creation'])->name('projects.project-creation');
    Route::get('/project-details/{id}', [ProjectsController::class, 'details'])->name('projects.project-details');

    // Projects - Routes resources
    Route::resource('projects', ProjectsController::class);

    // Tickets - Routes
    Route::get('/ticket', [TicketsController::class, 'index'])->name('tickets.tickets');
    Route::get('/ticket-creation', [TicketsController::class, 'creation'])->name('tickets.ticket-creation');
    Route::get('/ticket-details/{id}', [TicketsController::class, 'details'])->name('tickets.ticket-details');

    // Tickets - Routes resources
    Route::resource('tickets', TicketsController::class);
    Route::delete('/attachments/{attachment}', [TicketsController::class, 'deleteAttachment'])
        ->name('attachments.destroy');
});
