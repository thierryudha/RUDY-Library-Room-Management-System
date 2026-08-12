<?php

namespace App\Http\Requests\Api\Facility;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $roleName = $this->user()?->role?->name;
        return in_array($roleName, ['admin', 'super admin']);
    }

    public function rules(): array
    {
        $facilityId = $this->route('facility');
        return [
            'name' => 'sometimes|string|max:50|unique:facilities,name,' . $facilityId,
        ];
    }
}
