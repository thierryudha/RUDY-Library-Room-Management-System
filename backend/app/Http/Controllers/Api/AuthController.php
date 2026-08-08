<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RequestOtpRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(Protected AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
            'data' => null,
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'student', 'lecturer', 'staff']);
        return response()->json([
            'status' => 'success',
            'message' => 'Profile retrieved successfully',
            'data' => new UserResource($user),
        ]);
    }

    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {
        $this->authService->requestOtp($request->validated('email'));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully to your email',
            'data' => null,
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $resetToken = $this->authService->verifyOtp($validated['email'], $validated['otp_code']);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully',
            'data' => [
                'reset_token' => $resetToken,
            ],
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->authService->resetPassword(
            $validated['email'], 
            $validated['reset_token'], 
            $validated['password']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully',
            'data' => null,
        ]);
    }
}
