<?php

namespace App\Enums;

enum BookingStatus: string
{
    case APPROVED = 'approved';
    case CHECKED_IN = 'checked_in';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Approved',
            self::CHECKED_IN => 'Checked In',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
        };
    }
}
