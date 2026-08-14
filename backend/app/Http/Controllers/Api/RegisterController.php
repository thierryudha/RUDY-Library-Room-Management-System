<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\RegisterService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    use ApiResponse;
    
    public function __construct(protected RegisterService $registerService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $file = $request->file('activation_proof');
        $result = $this->registerService->registerUser($request->validated(), $file);

        return $this->successResponse(
            message: 'Registration successful',
            data: [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            code: 201
        );
    }
}
