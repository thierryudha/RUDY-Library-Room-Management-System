<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class StaffUnit extends Model
{
    protected $table = 'staff_units';
    public $timestamps = false;

    public function staffs(): HasMany
    {
        return $this->hasMany(Staff::class, 'unit_id');
    }
}
