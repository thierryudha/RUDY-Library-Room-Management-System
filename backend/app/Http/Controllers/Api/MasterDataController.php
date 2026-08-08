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
            data: $departments,
        );
    }

    public function getStudyPrograms(Request $request): JsonResponse
    {
        $departmentId = $request->query('department_id');
        $studyPrograms = $this->masterDataService->getStudyPrograms($departmentId ? (int)$departmentId : null);
        
        return $this->successResponse(
            data: StudyProgramResource::collection($studyPrograms),
            message: 'Study Programs retrieved successfully'
        );
    }

    public function getPublicRoles(): JsonResponse
    {
        $roles = $this->masterDataService->getPublicRoles();

        return $this->successResponse(
            data: RoleResource::collection($roles),
            message: 'Public roles retrieved successfully'
        );
    }

    public function getAllRoles(): JsonResponse
    {
        $roles = $this->masterDataService->getAllRoles();

        return $this->successResponse(
            data: RoleResource::collection($roles),
            message: 'All roles retrieved successfully'
        );
    }

    public function getStaffUnits(): JsonResponse
    {
        $units = $this->masterDataService->getStaffUnits();

        return $this->successResponse(
            data: StaffUnitResource::collection($units),
            message: 'Staff units retrieved successfully'
        );
    }
}
