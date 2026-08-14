<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['room_id', 'start_at', 'end_at', 'created_by_user_id', 'booking_status'])]
class Booking extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'booking_status' => \App\Enums\BookingStatus::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function bookingMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'booking_members')->withPivot('created_at');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}
