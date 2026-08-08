<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini adalah tempat Anda meregistrasi route API untuk aplikasi Anda.
| Semua route ini akan dimuat oleh RouteServiceProvider dan otomatis
| diberikan awalan (prefix) "api".
|
*/

// ==========================================
// PUBLIC ROUTES
// ==========================================
Route::prefix('auth')->group(function () {
    Route::post('/login', function () {
        return response()->json([
            'success' => true,
            'message' => 'Contoh Endpoint Login.',
            'data' => null,
        ]);
    });
    
    Route::post('/register', function () {
        return response()->json([
            'success' => true,
            'message' => 'Contoh Endpoint Register.',
            'data' => null,
        ]);
    });
});

// ==========================================
// PROTECTED ROUTES (Require Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Data user saat ini.',
            'data' => $request->user()
        ]);
    });

    Route::post('/auth/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
            'data' => null
        ]);
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
    });
});
