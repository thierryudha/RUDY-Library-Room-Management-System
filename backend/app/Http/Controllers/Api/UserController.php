<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::with('role')
            ->when(request('role_id'), fn ($q) => $q->where('role_id', request('role_id')))
            ->when(request('status'), fn ($q) => $q->where('user_status', request('status')))
            ->when(request('search'), fn ($q) => $q->where(function ($q) {
                $q->where('name', 'ilike', '%' . request('search') . '%')
                    ->orWhere('email', 'ilike', '%' . request('search') . '%');
            }))
            ->latest()
            ->paginate(request('per_page', 15));

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('role', 'student.studyProgram', 'lecturer.department', 'staff.unit'));
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate(['user_status' => 'required|in:active,inactive,suspended']);

        $user->update(['user_status' => $request->user_status]);

        return response()->json(['message' => 'User status updated', 'user' => new UserResource($user)]);
    }
}
