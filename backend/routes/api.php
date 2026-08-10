<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\Admin\UserManagementController;

// ==========================================
// MASTER DATA FOR REGISTER ROUTES
// ==========================================
Route::prefix('master')->group(function () {
    Route::get('/departments', [MasterDataController::class, 'getDepartments']);
    Route::get('/study-programs', [MasterDataController::class, 'getStudyPrograms']);
    Route::get('/roles/public', [MasterDataController::class, 'getPublicRoles']);
    Route::get('/staff-units', [MasterDataController::class, 'getStaffUnits']);
});

// ==========================================
// PUBLIC ROUTES
// ==========================================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Forgot Password Flow
    Route::post('/request-otp', [AuthController::class, 'requestOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// ==========================================
// PROTECTED ROUTES (Require Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // ------------------------------------------
    // ROLE: ADMIN ONLY
    // ------------------------------------------
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json([
                'success' => true,
                'message' => 'Selamat datang di Admin Dashboard.',
                'data' => null
            ]);
        });
    });

    // ------------------------------------------
    // ROLE: ADMIN OR SUPER ADMIN
    // ------------------------------------------
    Route::middleware('role:admin,super admin')->group(function () {
        Route::get('/master-data', function () {
            return response()->json([
                'success' => true,
                'message' => 'Daftar Master Data.',
                'data' => []
            ]);
        });
        
        Route::get('/master/roles/admin', [MasterDataController::class, 'getAllRoles']);

        // User Management (Admin Panel)
        Route::prefix('admin/users')->controller(UserManagementController::class)->group(function () {
            Route::get('/pending-students', 'getPendingStudents');
            Route::patch('/{id}/status', 'updateStatus');
        });
    });
});
