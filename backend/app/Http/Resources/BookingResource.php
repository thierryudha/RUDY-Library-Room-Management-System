<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->room_id,
            'room' => [
                'id' => $this->whenLoaded('room', function () { return $this->room->id; }),
                'name' => $this->whenLoaded('room', function () { return $this->room->name; }),
            ],
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'status' => $this->booking_status,
            'created_by' => [
                'id' => $this->whenLoaded('createdBy', function () { return $this->createdBy->id; }),
                'name' => $this->whenLoaded('createdBy', function () { return $this->createdBy->name; }),
            ],
            'members' => $this->whenLoaded('bookingMembers', function () {
                return $this->bookingMembers->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                        'email' => $member->email,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
