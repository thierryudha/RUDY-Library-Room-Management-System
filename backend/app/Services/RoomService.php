<?php

namespace App\Services;

use App\Repositories\RoomRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RoomService
{
    protected RoomRepository $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function getAllRooms(): Collection
    {
        return $this->roomRepository->getWithFacilities();
    }

    public function getRoomById(int $id): Model
    {
        return $this->roomRepository->findOrFailWithFacilities($id);
    }

    public function createRoom(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Handle image upload if present
            if (isset($data['room_image']) && $data['room_image'] instanceof UploadedFile) {
                $path = $data['room_image']->store('rooms', 'public');
                $data['room_image_path'] = $path;
            }

            // we don't need the $data['room_image] from the form submission, 
            // because we already store it in the server, and the DB only need its path
            // not the raw image file name
            $facilities = $data['facilities'] ?? [];
            unset($data['facilities'], $data['room_image']);

            // Create room
            $room = $this->roomRepository->create($data);

            // Sync facilities
            if (!empty($facilities)) {
                $syncData = [];
                foreach ($facilities as $facility) {
                    $syncData[$facility['id']] = ['description' => $facility['description'] ?? null];
                }
                $room->facilities()->sync($syncData);
            }

            return $this->getRoomById($room->id);
        });
    }

    public function updateRoom(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $room = $this->roomRepository->findOrFail($id);

            // Handle image upload if present
            if (isset($data['room_image']) && $data['room_image'] instanceof UploadedFile) {
                // Delete old image
                if ($room->room_image_path) {
                    Storage::disk('public')->delete($room->room_image_path);
                }
                $path = $data['room_image']->store('rooms', 'public');
                $data['room_image_path'] = $path;
            }

            // Extract facilities if present
            $syncFacilities = isset($data['facilities']);
            $facilities = $data['facilities'] ?? [];
            unset($data['facilities'], $data['room_image']);

            // Update room
            $this->roomRepository->update($id, $data);

            // Sync facilities if provided
            if ($syncFacilities) {
                $syncData = [];
                foreach ($facilities as $facility) {
                    $syncData[$facility['id']] = ['description' => $facility['description'] ?? null];
                }
                $room->facilities()->sync($syncData);
            }

            return $this->getRoomById($id);
        });
    }

    public function deleteRoom(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            // We use soft deletes, so we don't delete the image or detach facilities 
            // as they might be needed if the room is restored.
            return $this->roomRepository->delete($id);
        });
    }
}
