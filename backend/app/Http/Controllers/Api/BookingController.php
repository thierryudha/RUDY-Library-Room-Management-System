<?php

namespace App\Http\Controllers\Api;

// Karena kita menggunakan Service & Repository pattern, tambahkan use service layer nanti
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Booking\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BookingMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // gunakan dependency injection: constructor injection untuk service dan method injection untuk form request 
}
