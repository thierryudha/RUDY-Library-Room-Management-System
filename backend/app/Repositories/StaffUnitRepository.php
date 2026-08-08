<?php

namespace App\Repositories;

use App\Models\StaffUnit;
use Illuminate\Database\Eloquent\Collection;

class StaffUnitRepository
{
    public function getAll(): Collection
    {
        return StaffUnit::all();
    }
}
