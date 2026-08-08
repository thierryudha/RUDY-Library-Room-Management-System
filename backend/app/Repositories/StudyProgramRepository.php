<?php

namespace App\Repositories;

use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Collection;

class StudyProgramRepository
{
    public function getByDepartmentId(?int $departmentId): Collection
    {
        return StudyProgram::when($departmentId, function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->get();
    }
}
