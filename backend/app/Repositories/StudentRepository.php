<?php

namespace App\Repositories;

use App\Models\Student;

class StudentRepository
{
    public function create(array $data): Student
    {
        return Student::create($data);
    }
}
