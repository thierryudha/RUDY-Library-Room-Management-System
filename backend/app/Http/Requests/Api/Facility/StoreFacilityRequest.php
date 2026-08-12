<?php

namespace App\Http\Requests\Api\Facility;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $roleName = $this->user()?->role?->name;
        return in_array($roleName, ['admin', 'super admin']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:facilities,name',
        ];
    }
}
