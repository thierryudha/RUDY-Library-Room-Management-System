<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::get('/rooms/{room}', [RoomController::class, 'show']);
    Route::put('/rooms/{room}', [RoomController::class, 'update']);
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);

    Route::post('/bookings/{booking}/feedback', [FeedbackController::class, 'store']);
    Route::get('/feedbacks', [FeedbackController::class, 'index']);
    Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus']);

    Route::prefix('master')->group(function () {
        Route::get('/roles', [MasterDataController::class, 'roles']);
        Route::post('/roles', [MasterDataController::class, 'storeRole']);
        Route::put('/roles/{role}', [MasterDataController::class, 'updateRole']);
        Route::delete('/roles/{role}', [MasterDataController::class, 'destroyRole']);

        Route::get('/departments', [MasterDataController::class, 'departments']);
        Route::post('/departments', [MasterDataController::class, 'storeDepartment']);
        Route::put('/departments/{department}', [MasterDataController::class, 'updateDepartment']);
        Route::delete('/departments/{department}', [MasterDataController::class, 'destroyDepartment']);

        Route::get('/study-programs', [MasterDataController::class, 'studyPrograms']);
        Route::post('/study-programs', [MasterDataController::class, 'storeStudyProgram']);
        Route::put('/study-programs/{studyProgram}', [MasterDataController::class, 'updateStudyProgram']);
        Route::delete('/study-programs/{studyProgram}', [MasterDataController::class, 'destroyStudyProgram']);

        Route::get('/staff-units', [MasterDataController::class, 'staffUnits']);
        Route::post('/staff-units', [MasterDataController::class, 'storeStaffUnit']);
        Route::put('/staff-units/{staffUnit}', [MasterDataController::class, 'updateStaffUnit']);
        Route::delete('/staff-units/{staffUnit}', [MasterDataController::class, 'destroyStaffUnit']);

        Route::get('/facilities', [MasterDataController::class, 'facilities']);
        Route::post('/facilities', [MasterDataController::class, 'storeFacility']);
        Route::put('/facilities/{facility}', [MasterDataController::class, 'updateFacility']);
        Route::delete('/facilities/{facility}', [MasterDataController::class, 'destroyFacility']);
    });
});
