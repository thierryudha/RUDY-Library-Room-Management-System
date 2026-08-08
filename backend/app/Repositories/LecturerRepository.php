<?php

namespace App\Repositories;

use App\Models\Lecturer;

class LecturerRepository
{
    public function create(array $data): Lecturer
    {
        return Lecturer::create($data);
    }
}
