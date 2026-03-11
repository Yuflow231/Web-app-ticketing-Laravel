<?php

use App\Http\Controllers\DashController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashController::class, 'index'])->name('dashboard');

Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.projects');
Route::get('/project-details', [ProjectsController::class, 'details'])->name('projects.project-details');
Route::get('/project-creation', [ProjectsController::class, 'creation'])->name('projects.project-creation');


Route::get('/tickets', [TicketsController::class, 'index'])->name('tickets.tickets');
Route::get('/ticket-details', [TicketsController::class, 'details'])->name('tickets.ticket-details');
Route::get('/ticket-creation', [TicketsController::class, 'creation'])->name('tickets.ticket-creation');

Route::get('/create-account', [AccountController::class, 'create'])->name('create-account');
Route::get('/reset-password', [AccountController::class, 'password'])->name('reset-password');
Route::get('/login', [AccountController::class, 'login'])->name('login');
Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
