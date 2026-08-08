<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_photo_path' => $this->profile_photo_path,
            'role' => new RoleResource($this->whenLoaded('role')),
            // Depending on what data you need to expose publicly
            'student' => $this->whenLoaded('student'),
            'lecturer' => $this->whenLoaded('lecturer'),
            'staff' => $this->whenLoaded('staff'),
        ];
    }
}
