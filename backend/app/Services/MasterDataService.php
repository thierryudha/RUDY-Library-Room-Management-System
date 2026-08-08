<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
use App\Repositories\RoleRepository;
use App\Repositories\StudyProgramRepository;
use App\Repositories\StaffUnitRepository;
use Illuminate\Database\Eloquent\Collection;

class MasterDataService
{
    public function __construct(
        protected DepartmentRepository $departmentRepository,
        protected StudyProgramRepository $studyProgramRepository,
        protected RoleRepository $roleRepository,
        protected StaffUnitRepository $staffUnitRepository
    ) {}

    public function getDepartments(): Collection
    {
        return $this->departmentRepository->getAll();
    }

    public function getStudyPrograms(?int $departmentId = null): Collection
    {
        return $this->studyProgramRepository->getByDepartmentId($departmentId);
    }

    public function getPublicRoles(): Collection
    {
        return $this->roleRepository->getByNames(['student', 'lecturer', 'staff']);
    }

    public function getAllRoles(): Collection
    {
        return $this->roleRepository->getAll();
    }

    public function getStaffUnits(): Collection
    {
        return $this->staffUnitRepository->getAll();
    }
}
