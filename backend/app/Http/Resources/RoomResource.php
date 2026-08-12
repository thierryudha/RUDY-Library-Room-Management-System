<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_image_url' => $this->room_image_path ? asset('storage/' . $this->room_image_path) : null,
            'name' => $this->name,
            'min_capacity' => $this->min_capacity,
            'max_capacity' => $this->max_capacity,
            'location' => $this->location,
            'facilities' => FacilityResource::collection($this->whenLoaded('facilities')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
