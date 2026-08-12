<?php

namespace App\Http\Requests\Api\Room;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        $roleName = $this->user()?->role?->name;
        return in_array($roleName, ['admin', 'super admin']);
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:30',
            'room_image' => 'nullable|image|max:2048',
            'min_capacity' => 'sometimes|integer|min:1',
            'max_capacity' => 'sometimes|integer|min:1|gte:min_capacity',
            'location' => 'sometimes|string',
            'facilities' => 'nullable|array',
            'facilities.*.id' => 'required|exists:facilities,id',
            'facilities.*.description' => 'nullable|string',
        ];
    }
}
