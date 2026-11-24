<?php

use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Projects
    Route::apiResource('projects', ProjectController::class);

    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);

    // Teams
    Route::get('/projects/{project}/teams', [TeamController::class, 'index']);
    Route::post('/projects/{project}/teams', [TeamController::class, 'store']);
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember']);
    Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember']);

    // Files
    Route::get('/projects/{project}/files', [FileController::class, 'index']);
    Route::post('/projects/{project}/files', [FileController::class, 'store']);
    Route::get('/files/{file}/download', [FileController::class, 'download']);

    // Meetings
    Route::get('/meetings/upcoming', [MeetingController::class, 'upcoming']);
    Route::get('/meetings', [MeetingController::class, 'index']);
    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::get('/meetings/{meeting}', [MeetingController::class, 'show']);
    Route::post('/meetings/{meeting}/summary', [MeetingController::class, 'summary']);
    Route::delete('/meetings/{meeting}', [MeetingController::class, 'destroy']);

    // Chats
    Route::get('/conversations', [ChatController::class, 'index']);
    Route::post('/conversations/direct', [ChatController::class, 'startDirect']);
    Route::post('/conversations/project', [ChatController::class, 'startProject']);
    Route::get('/conversations/{conversation}/messages', [ChatController::class, 'messages']);
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'send']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);

    // Reports
    Route::get('/reports/stats', [ReportController::class, 'stats']);
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);

    // Ask AI
    Route::get('/ai/messages', [AiController::class, 'index']);
    Route::post('/ai/chat', [AiController::class, 'chat']);
    Route::delete('/ai/messages', [AiController::class, 'clear']);
});
