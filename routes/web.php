<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TicketsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AccountController::class, 'showLogin'])->name('login');
Route::post('/login', [AccountController::class, 'login']);

Route::get('/register', [AccountController::class, 'showRegister'])->name('register');
Route::post('/register', [AccountController::class, 'register']);

Route::get('/create-account', [AccountController::class, 'create'])->name('create-account');
Route::post('/create-account', [AccountController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Routes authentifiées
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Déconnexion
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile-update');

    // Mot de passe
    Route::get('/reset-password', [AccountController::class, 'password'])->name('reset-password');
    Route::put('/reset-password', [AccountController::class, 'updatePassword'])->name('password-update');

    // Projets - Routes personnalisées
    Route::get('/project', [ProjectsController::class, 'index'])->name('projects.projects');
    Route::get('/project-creation', [ProjectsController::class, 'creation'])->name('projects.project-creation');
    Route::get('/project-details', [ProjectsController::class, 'details'])->name('projects.project-details');

    // Projets - Routes ressources
    Route::resource('projects', ProjectsController::class);

    // Tickets - Routes personnalisées
    Route::get('/ticket', [TicketsController::class, 'index'])->name('tickets.tickets');
    Route::get('/ticket-creation', [TicketsController::class, 'creation'])->name('tickets.ticket-creation');
    Route::get('/ticket-details', [TicketsController::class, 'details'])->name('tickets.ticket-details');

    // Tickets - Routes ressources
    Route::resource('tickets', TicketsController::class);
    Route::delete('/attachments/{attachment}', [TicketsController::class, 'deleteAttachment'])
        ->name('attachments.destroy');
});
