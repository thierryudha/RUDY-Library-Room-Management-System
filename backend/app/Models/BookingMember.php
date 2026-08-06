<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingMember extends Pivot
{
    protected $table = 'booking_members';
    public $timestamps = false;
}
