<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BookingMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $user = request()->user();
        $bookings = Booking::with(['room', 'createdBy', 'bookingMembers.user'])
            ->when($user->role?->role !== 'admin', fn ($q) => $q->where('created_by_user_id', $user->id)
                ->orWhereHas('bookingMembers', fn ($q) => $q->where('user_id', $user->id)))
            ->when(request('status'), fn ($q) => $q->where('booking_status', request('status')))
            ->when(request('room_id'), fn ($q) => $q->where('room_id', request('room_id')))
            ->latest()
            ->paginate(request('per_page', 15));

        return BookingResource::collection($bookings);
    }

    public function show(Booking $booking): BookingResource
    {
        return new BookingResource($booking->load(['room', 'createdBy', 'bookingMembers.user', 'feedbacks']));
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = DB::transaction(function () use ($request) {
            $booking = Booking::create([
                'room_id' => $request->room_id,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'created_by_user_id' => $request->user()->id,
                'booking_status' => 'pending',
            ]);

            foreach ($request->members as $userId) {
                BookingMember::create([
                    'booking_id' => $booking->id,
                    'user_id' => $userId,
                    'created_at' => now(),
                ]);
            }

            return $booking;
        });

        return (new BookingResource($booking->load(['room', 'createdBy', 'bookingMembers.user'])))
            ->response()
            ->setStatusCode(201);
    }

    public function updateStatus(Request $request, Booking $booking): BookingResource
    {
        $request->validate(['booking_status' => 'required|in:approved,rejected,cancelled,completed']);

        $booking->update(['booking_status' => $request->booking_status]);

        return new BookingResource($booking->load(['room', 'createdBy', 'bookingMembers.user']));
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();

        return response()->json(['message' => 'Booking deleted']);
    }
}
