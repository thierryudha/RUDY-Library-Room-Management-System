<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Role;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // First get the role to determine required fields
        $roleId = $this->input('role_id');
        $role = $roleId ? Role::find($roleId) : null;
        $roleName = $role ? strtolower($role->name) : '';

        return [
            // Universal fields
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],

            // Student specific fields
            'student_id_number' => [
                Rule::requiredIf($roleName === 'student'),
                'nullable', 'string', 'max:20', 'unique:students,student_id_number'
            ],
            'study_program_id' => [
                Rule::requiredIf($roleName === 'student'),
                'nullable', 'exists:study_programs,id'
            ],
            'class_of' => [
                Rule::requiredIf($roleName === 'student'),
                'nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 5)
            ],
            'activation_proof' => [
                Rule::requiredIf($roleName === 'student'),
                'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'
            ],

            // Lecturer specific fields
            'employee_id_number' => [
                Rule::requiredIf($roleName === 'lecturer' || $roleName === 'staff'),
                'nullable', 'string', 'max:30', 
                function ($attribute, $value, $fail) use ($roleName) {
                    if ($roleName === 'lecturer' && \App\Models\Lecturer::where('employee_id_number', $value)->exists()) {
                        $fail('The employee id number has already been taken.');
                    }
                    if ($roleName === 'staff' && \App\Models\Staff::where('employee_id_number', $value)->exists()) {
                        $fail('The employee id number has already been taken.');
                    }
                }
            ],
            'department_id' => [
                Rule::requiredIf($roleName === 'lecturer'),
                'nullable', 'exists:departments,id'
            ],

            // Staff specific fields
            'unit_id' => [
                Rule::requiredIf($roleName === 'staff'),
                'nullable', 'exists:staff_units,id'
            ],
        ];
    }
}
