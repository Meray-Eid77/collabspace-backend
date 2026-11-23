<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/projects/{project}/teams', [ProjectController::class, 'storeTeam']);
    Route::post('/teams/{team}/members', [ProjectController::class, 'addMember']);
    Route::delete('/teams/{team}/members/{user}', [ProjectController::class, 'removeMember']);
    Route::post('/projects/{project}/files', [ProjectController::class, 'uploadFile']);
    Route::get('/files/{file}/download', [ProjectController::class, 'downloadFile']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/profile/settings', [ProfileController::class, 'settings']);
    Route::put('/profile', [ProfileController::class, 'update']);
    });
