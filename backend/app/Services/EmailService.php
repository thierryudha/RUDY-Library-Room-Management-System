<?php

namespace App\Services;

use Resend\Laravel\Facades\Resend;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send OTP email using Resend.
     */
    public function sendOtpEmail(string $toEmail, string $otpCode): void
    {
        try {
            Resend::emails()->send([
                'from' => env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev'),
                'to' => $toEmail,
                'subject' => 'Password Reset OTP - Library Room System',
                'html' => "<p>Hello,</p><p>Your 6-digit OTP code to reset your password is: <strong>{$otpCode}</strong>.</p><p>This code is valid for 5 minutes.</p>",
            ]);
        } catch (\Exception $e) {
            Log::error('Resend Email Error: ' . $e->getMessage());
        }
    }

    /**
     * Send account rejection email using Resend.
     */
    public function sendRejectionEmail(string $toEmail, string $userName, string $reason): void
    {
        try {
            Resend::emails()->send([
                'from' => env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev'),
                'to' => $toEmail,
                'subject' => 'Account Registration Rejected - Library Room System',
                'html' => "<p>Hello {$userName},</p><p>We regret to inform you that your account registration has been rejected.</p><p><strong>Reason:</strong> {$reason}</p><p>If you have any questions, please contact the administrator.</p>",
            ]);
        } catch (\Exception $e) {
            Log::error('Resend Rejection Email Error: ' . $e->getMessage());
        }
    }
}
