<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function getUsersByRoleAndStatus(string $roleName, string $status, array $relations = [], int $perPage = 15)
    {
        return User::whereHas('role', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })
        ->where('user_status', $status)
        ->with($relations) // Eager load dinamis mengikuti permintaan Service
        ->paginate($perPage);
    }

    public function updateStatus(User $user, string $status): bool
    {
        $user->user_status = $status;
        return $user->save();
    }

    public function searchMemberByIdNumber(string $idNumber)
    {
        // Limit results to active users
        return User::where('user_status', 'active')
            ->where(function ($query) use ($idNumber) {
                $query->whereHas('student', function ($q) use ($idNumber) {
                    $q->where('student_id_number', 'like', "%{$idNumber}%");
                })->orWhereHas('lecturer', function ($q) use ($idNumber) {
                    $q->where('employee_id_number', 'like', "%{$idNumber}%");
                })->orWhereHas('staff', function ($q) use ($idNumber) {
                    $q->where('employee_id_number', 'like', "%{$idNumber}%");
                });
            })
            ->with(['student', 'lecturer', 'staff', 'role'])
            ->limit(10)
            ->get();
    }

    public function getUsersWithRolesByIds(array $userIds)
    {
        return User::whereIn('id', $userIds)->with('role')->get();
    }
}
