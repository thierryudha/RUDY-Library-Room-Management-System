<?php

namespace App\Http\Controllers\Api;

// Karena kita menggunakan Service & Repository pattern, tambahkan use service layer nanti
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Room\StoreRoomRequest;
use App\Http\Requests\Api\Room\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomController extends Controller
{
    // gunakan dependency injection: constructor injection untuk service dan method injection untuk form request 

}
