<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Facility extends Model
{
    public $timestamps = false;

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_facilities')->withPivot('description');
    }
}
