<?php

namespace App\Services;

use App\Helpers\OtpHelper;
use App\Repositories\OtpPasswordResetRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected OtpPasswordResetRepository $otpRepository,
        protected EmailService $emailService
    ) {}

    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Create Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->load(['role', 'student', 'lecturer', 'staff']),
            'token' => $token,
        ];
    }

    public function requestOtp(string $email): void
    {
        $otpCode = OtpHelper::generate6DigitCode();
        $expiresAt = now()->addMinutes(5);

        $this->otpRepository->createOtp($email, $otpCode, $expiresAt);
        $this->emailService->sendOtpEmail($email, $otpCode);
    }

    public function verifyOtp(string $email, string $otpCode): string
    {
        $record = $this->otpRepository->findByEmailAndOtp($email, $otpCode);

        if (! $record || now()->greaterThan($record->otp_expires_at)) {
            throw ValidationException::withMessages([
                'otp_code' => ['Invalid or expired OTP code.'],
            ]);
        }

        $resetToken = Str::random(60);
        $tokenExpiresAt = now()->addMinutes(15);

        $this->otpRepository->createResetToken($email, $resetToken, $tokenExpiresAt);

        return $resetToken;
    }

    public function resetPassword(string $email, string $resetToken, string $newPassword): void
    {
        $record = $this->otpRepository->findByEmailAndToken($email, $resetToken);

        if (! $record || now()->greaterThan($record->token_expires_at)) {
            throw ValidationException::withMessages([
                'reset_token' => ['Invalid or expired reset token.'],
            ]);
        }

        $user = $this->userRepository->findByEmail($email);
        $user->password = Hash::make($newPassword);
        $user->save();

        $this->otpRepository->deleteByEmail($email);
    }
}
