<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Repositories\LecturerRepository;
use App\Repositories\StaffRepository;
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RegisterService
{
    public function __construct(
        protected StudentRepository $studentRepository,
        protected LecturerRepository $lecturerRepository,
        protected StaffRepository $staffRepository
    ) {}

    public function registerUser(array $data, ?UploadedFile $activationProof = null): array
    {
        return DB::transaction(function () use ($data, $activationProof) {
            $role = Role::find($data['role_id']);
            $roleName = strtolower($role->name);
            
            $userStatus = ($roleName === 'student') ? 'pending' : 'approved';

            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'role_id' => $data['role_id'],
                'user_status' => $userStatus,
            ]);

            // Handle specific profile creation
            if ($roleName === 'student') {
                $proofPath = '';
                if ($activationProof) {
                    $proofPath = $activationProof->store('activation_proofs', 'public');
                }

                $this->studentRepository->create([
                    'user_id' => $user->id,
                    'student_id_number' => $data['student_id_number'],
                    'study_program_id' => $data['study_program_id'],
                    'class_of' => $data['class_of'],
                    'activation_proof_path' => $proofPath,
                ]);
            } elseif ($roleName === 'lecturer') {
                $this->lecturerRepository->create([
                    'user_id' => $user->id,
                    'employee_id_number' => $data['employee_id_number'],
                    'department_id' => $data['department_id'],
                ]);
            } elseif ($roleName === 'staff') {
                $this->staffRepository->create([
                    'user_id' => $user->id,
                    'employee_id_number' => $data['employee_id_number'],
                    'unit_id' => $data['unit_id'],
                ]);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user->load(['role', 'student', 'lecturer', 'staff']),
                'token' => $token,
            ];
        });
    }
}
