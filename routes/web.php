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
    Route::get('/profile', [AccountController::class, 'showProfile'])->name('profile');
    Route::get('/profile-edit', [AccountController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile-update', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile-delete', [AccountController::class, 'confirmDelete'])->name('profile-delete');

    // Password
    Route::put('/reset-password', [AccountController::class, 'updatePassword'])->name('password-update');

    // Projects - Routes
    Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.projects');
    Route::get('/project-creation', [ProjectsController::class, 'showCreate'])->name('projects.project-creation');
    Route::get('/project-edit/{id}', [ProjectsController::class, 'showEdit'])->name('projects.project-edit');
    Route::put('/project-update/{id}', [ProjectsController::class, 'update'])->name('projects.project-update');
    Route::get('/project-details/{id}', [ProjectsController::class, 'details'])->name('projects.project-details');
    Route::post('/project-store', [ProjectsController::class, 'store'])->name('projects.project-store');
    Route::delete('/project-destroy/{id}', [ProjectsController::class, 'destroy'])->name('projects.project-destroy');

    // Tickets - Routes
    Route::get('/tickets', [TicketsController::class, 'index'])->name('tickets.tickets');
    Route::get('/ticket-creation', [TicketsController::class, 'showCreate'])->name('tickets.ticket-creation');
    Route::get('/ticket-edit/{id}', [TicketsController::class, 'showEdit'])->name('tickets.ticket-edit');
    Route::put('/ticket-update/{id}', [TicketsController::class, 'update'])->name('tickets.ticket-update');
    Route::get('/ticket-details/{id}', [TicketsController::class, 'details'])->name('tickets.ticket-details');
    Route::post('/ticket-store', [TicketsController::class, 'store'])->name('tickets.ticket-store');
    Route::delete('/ticket-destroy/{id}', [TicketsController::class, 'destroy'])->name('tickets.ticket-destroy');
});
