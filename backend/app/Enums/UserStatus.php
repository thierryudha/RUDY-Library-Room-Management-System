<?php

namespace App\Enums;

enum UserStatus: string
{
    case APPROVED = 'approved';
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case BLOCKED = 'blocked';
}
