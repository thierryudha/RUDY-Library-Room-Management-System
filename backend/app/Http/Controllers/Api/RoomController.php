<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Room\StoreRoomRequest;
use App\Http\Requests\Api\Room\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Services\RoomService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    use ApiResponse;

    protected RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function index(): JsonResponse
    {
        $rooms = $this->roomService->getAllRooms();
        return $this->successResponse('Rooms retrieved successfully.', RoomResource::collection($rooms));
    }

    public function store(StoreRoomRequest $request): JsonResponse
    {
        $room = $this->roomService->createRoom($request->validated());
        return $this->successResponse('Room created successfully.', new RoomResource($room), 201);
    }

    public function show($id): JsonResponse
    {
        $room = $this->roomService->getRoomById($id);
        return $this->successResponse('Room retrieved successfully.', new RoomResource($room));
    }

    public function update(UpdateRoomRequest $request, $id): JsonResponse
    {
        $room = $this->roomService->updateRoom($id, $request->validated());
        return $this->successResponse('Room updated successfully.', new RoomResource($room));
    }

    public function destroy($id): JsonResponse
    {
        $this->roomService->deleteRoom($id);
        return $this->successResponse('Room deleted successfully.');
    }
}
