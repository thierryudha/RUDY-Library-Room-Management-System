<?php

namespace App\Http\Requests\Api\Room;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_name' => 'sometimes|string|max:30',
            'room_image_path' => 'nullable|string|max:255',
            'min_capacity' => 'sometimes|integer|min:1',
            'max_capacity' => 'sometimes|integer|min:1|gte:min_capacity',
            'location' => 'sometimes|string',
            'facilities' => 'nullable|array',
            'facilities.*' => 'exists:facilities,id',
        ];
    }
}
