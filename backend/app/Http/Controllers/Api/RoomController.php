<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $rooms = Room::with('facilities')
            ->when(request('search'), fn ($q) => $q->where('room_name', 'ilike', '%' . request('search') . '%'))
            ->when(request('min_capacity'), fn ($q) => $q->where('max_capacity', '>=', request('min_capacity')))
            ->paginate(request('per_page', 15));

        return RoomResource::collection($rooms);
    }

    public function show(Room $room): RoomResource
    {
        return new RoomResource($room->load('facilities'));
    }

    public function store(StoreRoomRequest $request): JsonResponse
    {
        $room = Room::create($request->validated());

        if ($request->has('facilities')) {
            $room->facilities()->sync($request->facilities);
        }

        return (new RoomResource($room->load('facilities')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateRoomRequest $request, Room $room): RoomResource
    {
        $room->update($request->validated());

        if ($request->has('facilities')) {
            $room->facilities()->sync($request->facilities);
        }

        return new RoomResource($room->load('facilities'));
    }

    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json(['message' => 'Room deleted']);
    }
}
