<?php

namespace App\Repositories;

use App\Models\Staff;

class StaffRepository
{
    public function create(array $data): Staff
    {
        return Staff::create($data);
    }
}
