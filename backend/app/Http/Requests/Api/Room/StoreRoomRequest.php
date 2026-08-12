<?php

namespace App\Http\Requests\Api\Room;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        $roleName = $this->user()?->role?->name;
        return in_array($roleName, ['admin', 'super admin']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30',
            'room_image' => 'nullable|image|max:2048',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1|gte:min_capacity',
            'location' => 'required|string',
            'facilities' => 'nullable|array',
            'facilities.*.id' => 'required|exists:facilities,id',
            'facilities.*.description' => 'nullable|string',
        ];
    }
}
