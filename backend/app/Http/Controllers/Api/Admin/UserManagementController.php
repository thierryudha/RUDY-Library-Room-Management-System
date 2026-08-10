<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Services\Admin\UserManagementService;
use App\Traits\ApiResponse;

class UserManagementController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected UserManagementService $userManagementService
    ) {}

    public function getPendingStudents()
    {
        $students = $this->userManagementService->getPendingStudents();

        return $this->successResponse('Pending students retrieved successfully', $students);
    }

    public function updateStatus(UpdateUserStatusRequest $request, $id)
    {
        $validated = $request->validated();
        
        $result = $this->userManagementService->updateUserStatus(
            $id,
            $validated['status'],
            $validated['reason'] ?? null
        );

        if (!$result['success']) {
            return $this->errorResponse($result['message'], 400);
        }

        return $this->successResponse($result['message']);
    }
}
