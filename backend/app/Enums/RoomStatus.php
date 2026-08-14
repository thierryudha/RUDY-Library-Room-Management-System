<?php

namespace App\Enums;

enum RoomStatus: string
{
    case AVAILABLE = 'available';
    case MAINTENANCE = 'unavailable';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::MAINTENANCE => 'Maintenance',
        };
    }
}
