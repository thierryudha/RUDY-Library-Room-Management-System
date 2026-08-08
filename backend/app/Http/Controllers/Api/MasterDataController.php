<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\StudyProgramResource;
use App\Http\Resources\StaffUnitResource;
use App\Services\MasterDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function __construct(protected MasterDataService $masterDataService) {}

    public function getDepartments(): JsonResponse
    {
        $departments = $this->masterDataService->getDepartments();
        return response()->json([
            'status' => 'success',
            'message' => 'Departments retrieved successfully',
            'data' => DepartmentResource::collection($departments),
        ]);
    }

    public function getStudyPrograms(Request $request): JsonResponse
    {
        $departmentId = $request->query('department_id');
        $studyPrograms = $this->masterDataService->getStudyPrograms($departmentId ? (int)$departmentId : null);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Study Programs retrieved successfully',
            'data' => StudyProgramResource::collection($studyPrograms),
        ]);
    }

    public function getPublicRoles(): JsonResponse
    {
        $roles = $this->masterDataService->getPublicRoles();
        return response()->json([
            'status' => 'success',
            'message' => 'Public roles retrieved successfully',
            'data' => RoleResource::collection($roles),
        ]);
    }

    public function getAllRoles(): JsonResponse
    {
        $roles = $this->masterDataService->getAllRoles();
        return response()->json([
            'status' => 'success',
            'message' => 'All roles retrieved successfully',
            'data' => RoleResource::collection($roles),
        ]);
    }

    public function getStaffUnits(): JsonResponse
    {
        $units = $this->masterDataService->getStaffUnits();
        return response()->json([
            'status' => 'success',
            'message' => 'Staff units retrieved successfully',
            'data' => StaffUnitResource::collection($units),
        ]);
    }
}
