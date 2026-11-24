<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AiController;


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
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats/direct', [ChatController::class, 'startDirect']);
    Route::post('/chats/project', [ChatController::class, 'startProject']);
    Route::post('/chats/{conversation}/messages', [ChatController::class, 'send']);
    Route::get('/messages/{message}/attachment', [ChatController::class, 'downloadAttachment']);
    Route::get('/meetings', [MeetingController::class, 'index']);
    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::get('/meetings/{meeting}', [MeetingController::class, 'show']);
    Route::post('/meetings/{meeting}/summary', [MeetingController::class, 'summary']);
    Route::delete('/meetings/{meeting}', [MeetingController::class, 'destroy']);
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/ai', [AiController::class, 'index']);
    Route::post('/ai', [AiController::class, 'ask']);
    Route::delete('/ai', [AiController::class, 'clear']);
    Route::get('/me', [AuthController::class, 'me']);




});
