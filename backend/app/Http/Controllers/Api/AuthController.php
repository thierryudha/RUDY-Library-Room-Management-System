<?php

namespace App\Http\Controllers\Api;

// Karena kita menggunakan Service & Repository pattern, tambahkan use service layer nanti
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // gunakan dependency injection: constructor injection untuk service dan method injection untuk form request 
}
