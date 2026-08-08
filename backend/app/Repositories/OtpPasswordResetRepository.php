<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class OtpPasswordResetRepository
{
    public function createOtp(string $email, string $otpCode, string $expiresAt): void
    {
        // Delete previous OTPs for this email to avoid duplicates
        DB::table('otp_password_resets')->where('email', $email)->delete();

        DB::table('otp_password_resets')->insert([
            'email' => $email,
            'otp_code' => $otpCode,
            'otp_expires_at' => $expiresAt,
            'created_at' => now(),
        ]);
    }

    public function findByEmailAndOtp(string $email, string $otpCode): ?object
    {
        return DB::table('otp_password_resets')
            ->where('email', $email)
            ->where('otp_code', $otpCode)
            ->first();
    }

    public function createResetToken(string $email, string $resetToken, string $expiresAt): void
    {
        DB::table('otp_password_resets')
            ->where('email', $email)
            ->update([
                'reset_token' => $resetToken,
                'token_expires_at' => $expiresAt,
                'otp_code' => '', // Clear OTP as it's been used
            ]);
    }

    public function findByEmailAndToken(string $email, string $resetToken): ?object
    {
        return DB::table('otp_password_resets')
            ->where('email', $email)
            ->where('reset_token', $resetToken)
            ->first();
    }

    public function deleteByEmail(string $email): void
    {
        DB::table('otp_password_resets')->where('email', $email)->delete();
    }
}
