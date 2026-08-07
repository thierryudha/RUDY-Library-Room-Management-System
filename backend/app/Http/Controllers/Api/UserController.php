<?php

namespace App\Http\Controllers\Api;

// Karena kita menggunakan Service & Repository Layer, tambahkan use service layer nanti
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    // gunakan dependency injection: constructor injection untuk service dan method injection untuk form request 

}
