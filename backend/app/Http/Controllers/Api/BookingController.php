<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Booking\StoreBookingRequest;
use App\Http\Requests\Api\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use App\Services\BookingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class BookingController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepository $bookingRepository,
        protected UserRepository $userRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->query('per_page', 15);

        $bookings = $this->bookingRepository->getUserBookings($user->id, $perPage);

        return $this->successResponse(
            'Bookings retrieved successfully.',
            [
                'bookings' => BookingResource::collection($bookings),
                'meta' => [
                    'current_page' => $bookings->currentPage(),
                    'last_page' => $bookings->lastPage(),
                    'per_page' => $bookings->perPage(),
                    'total' => $bookings->total(),
                ]
            ]
        );
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $booking = $this->bookingService->createBooking($request->validated(), $user);

            return $this->successResponse(
                'Booking created successfully.',
                new BookingResource($booking),
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function update(UpdateBookingRequest $request, Booking $booking): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Allow user to update only their own booking
            if ($booking->created_by_user_id !== $user->id) {
                return $this->errorResponse("You are not authorized to update this booking.", 403);
            }

            $updatedBooking = $this->bookingService->updateBooking($booking, $request->validated(), $user);

            return $this->successResponse(
                'Booking updated successfully.',
                new BookingResource($updatedBooking)
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    

    public function cancel(Booking $booking, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $isAdmin = in_array(strtolower($user->role->name), ['admin', 'super admin']);

            $cancelledBooking = $this->bookingService->cancelBooking($booking, $user, $isAdmin);

            return $this->successResponse(
                'Booking cancelled successfully.',
                new BookingResource($cancelledBooking)
            );
        } catch (Exception $e) {
            $code = $e->getCode() ?: 400;
            return $this->errorResponse($e->getMessage(), $code === 0 ? 400 : $code);
        }
    }

    public function checkIn(Booking $booking, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!in_array(strtolower($user->role->name), ['admin', 'super admin'])) {
                return $this->errorResponse('Only administrators can perform check-in.', 403);
            }

            $checkedInBooking = $this->bookingService->checkInBooking($booking);

            return $this->successResponse(
                'Booking checked in successfully.',
                new BookingResource($checkedInBooking)
            );
        } catch (Exception $e) {
            $code = $e->getCode() ?: 400;
            return $this->errorResponse($e->getMessage(), $code === 0 ? 400 : $code);
        }
    }

    public function searchMember(Request $request): JsonResponse
    {
        $q = $request->query('q');

        if (empty($q) || strlen($q) < 3) {
            return $this->errorResponse('Search query must be at least 3 characters long.', 400);
        }

        $users = $this->userRepository->searchMemberByIdNumber($q);

        return $this->successResponse(
            'Members retrieved successfully.',
            $users->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'role' => $u->role->name,
                    'identity_number' => $u->student->student_id_number ?? $u->lecturer->employee_id_number ?? $u->staff->employee_id_number ?? null,
                ];
            })
        );
    }
}
