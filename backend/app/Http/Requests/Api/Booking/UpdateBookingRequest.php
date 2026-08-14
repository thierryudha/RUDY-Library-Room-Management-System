<?php

namespace App\Http\Requests\Api\Booking;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'start_at' => ['required', 'date', 'date_format:Y-m-d H:i'],
            'end_at' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:start_at'],
            'members' => ['nullable', 'array'],
            'members.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (empty($this->start_at) || empty($this->end_at) || empty($this->room_id)) {
                    return; // Skip advanced validation if basic fails
                }

                try {
                    $startAt = Carbon::parse($this->start_at);
                    $endAt = Carbon::parse($this->end_at);
                } catch (\Exception $e) {
                    return;
                }

                // 1. Validasi H-7 Max Advance Booking
                if ($startAt->copy()->startOfDay()->gt(now()->addDays(7)->startOfDay())) {
                    $validator->errors()->add('start_at', 'Booking can only be made a maximum of 7 days in advance.');
                }

                // 2. Validasi Hari (Senin-Jumat)
                if ($startAt->isWeekend()) {
                    $validator->errors()->add('start_at', 'Booking is only allowed on weekdays (Monday-Friday).');
                }

                // 3. Validasi Jam Operasional 09:00 - 15:00
                $startTime = Carbon::createFromTime(9, 0, 0);
                $endTime = Carbon::createFromTime(15, 0, 0);
                
                $startAtTime = $startAt->copy()->setDate($startTime->year, $startTime->month, $startTime->day);
                $endAtTime = $endAt->copy()->setDate($startTime->year, $startTime->month, $startTime->day);

                if ($startAtTime->lt($startTime) || $endAtTime->gt($endTime)) {
                    $validator->errors()->add('start_at', 'Booking must be within operational hours (09:00 - 15:00).');
                }

                // 4. Validasi Kelipatan 30 Menit
                if ($startAt->minute % 30 !== 0 || $endAt->minute % 30 !== 0 || $startAt->second !== 0 || $endAt->second !== 0) {
                    $validator->errors()->add('start_at', 'Booking times must be in 30-minute intervals (e.g., 09:00, 09:30).');
                }

                // 5. Validasi Maksimal Durasi 2 Jam
                if ($startAt->diffInMinutes($endAt) > 120) {
                    $validator->errors()->add('end_at', 'Maximum booking duration is 2 hours.');
                }

                // 6. Validasi Kapasitas Ruangan
                $room = Room::find($this->room_id);
                if ($room) {
                    $totalParticipants = 1 + count($this->members ?? []); // 1 is the PIC
                    if ($totalParticipants < $room->min_capacity) {
                        $validator->errors()->add('members', "Total participants must be at least {$room->min_capacity} for this room.");
                    }
                    if ($totalParticipants > $room->max_capacity) {
                        $validator->errors()->add('members', "Total participants cannot exceed {$room->max_capacity} for this room.");
                    }
                }
            }
        ];
    }
}
