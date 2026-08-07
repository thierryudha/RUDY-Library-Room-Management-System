<?php

namespace App\Http\Requests\Api\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'start_at' => 'required|date|after:now',
            'end_at' => 'required|date|after:start_at',
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id',
        ];
    }
}
