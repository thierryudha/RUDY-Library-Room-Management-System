<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\StudyProgramResource;
use App\Http\Resources\StaffUnitResource;
use App\Services\MasterDataService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    use ApiResponse;

    public function __construct(protected MasterDataService $masterDataService) {}

    public function getDepartments(): JsonResponse
    {
        $departments = $this->masterDataService->getDepartments();

        return $this->successResponse(
            message: 'Departments retrieved successfully',
            data: DepartmentResource::collection($departments),
        );
    }

    public function getStudyPrograms(Request $request): JsonResponse
    {
        $departmentId = $request->query('department_id');
        $studyPrograms = $this->masterDataService->getStudyPrograms($departmentId ? (int)$departmentId : null);
        
        return $this->successResponse(
            message: 'Study Programs retrieved successfully',
            data: StudyProgramResource::collection($studyPrograms),
        );
    }

    public function getPublicRoles(): JsonResponse
    {
        $roles = $this->masterDataService->getPublicRoles();

        return $this->successResponse(
            message: 'Public roles retrieved successfully',
            data: RoleResource::collection($roles),
        );
    }

    public function getAllRoles(): JsonResponse
    {
        $roles = $this->masterDataService->getAllRoles();

        return $this->successResponse(
            message: 'All roles retrieved successfully',
            data: RoleResource::collection($roles),
        );
    }

    public function getStaffUnits(): JsonResponse
    {
        $units = $this->masterDataService->getStaffUnits();

        return $this->successResponse(
            message: 'Staff units retrieved successfully',
            data: StaffUnitResource::collection($units),
        );
    }
}
