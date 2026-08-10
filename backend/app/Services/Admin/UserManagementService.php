<?php

namespace App\Services\Admin;

use App\Enums\UserStatus;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserManagementService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected EmailService $emailService
    ) {}

    public function getPendingStudents()
    {
        // Beri tahu Repository relasi spesifik apa yang mau di-load untuk fungsi ini
        $relations = ['student.studyProgram', 'role'];
        
        return $this->userRepository->getUsersByRoleAndStatus('student', UserStatus::PENDING->value, $relations, 15);
    }

    public function updateUserStatus(int $userId, string $status, ?string $reason = null)
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->findById($userId);

            if (!$user) {
                throw new Exception("User not found.");
            }

            $this->userRepository->updateStatus($user, $status);

            // Handle rejection logic
            if ($status === UserStatus::REJECTED->value) {
                if ($user->role->name === 'student' && $user->student) {
                    $user->student->reject_reason = $reason;
                    $user->student->save();
                }

                $this->emailService->sendRejectionEmail($user->email, $user->name, $reason ?? 'No reason provided.');
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'User status updated successfully.',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update User Status Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
