<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['room_image_path', 'room_name', 'min_capacity', 'max_capacity', 'location'])]
class Room extends Model
{
    use SoftDeletes;

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'room_facilities')
            ->withPivot('description');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
