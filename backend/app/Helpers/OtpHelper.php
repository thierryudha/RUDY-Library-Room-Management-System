<?php

namespace App\Helpers;

class OtpHelper
{
    /**
     * Generate a random 6-digit numeric OTP code.
     */
    public static function generate6DigitCode(): string
    {
        return (string) random_int(100000, 999999);
    }
}
