<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    public function __construct(
        protected BookingRepository $bookingRepository,
        protected UserRepository $userRepository
    ) {}

    public function createBooking(array $data, User $user): Booking
    {
        $startAt = Carbon::parse($data['start_at']);
        $endAt = Carbon::parse($data['end_at']);
        $members = $data['members'] ?? [];
        $roomId = $data['room_id'];

        // 1. Check room availability
        if (!$this->bookingRepository->isRoomAvailable($roomId, $startAt, $endAt)) {
            throw new Exception("The room is already booked for the selected time period.", 422);
        }

        // 2. Prepare user IDs to check quota (creator + members)
        $userIdsToCheck = array_merge([$user->id], $members);
        
        // Let's filter only users who are students or alumni to check daily quota
        $users = $this->userRepository->getUsersWithRolesByIds($userIdsToCheck);
        
        $restrictedUserIds = [];
        foreach ($users as $u) {
            if (in_array(strtolower($u->role->name), ['student', 'alumni'])) {
                $restrictedUserIds[] = $u->id;
            }
        }

        if (!empty($restrictedUserIds)) {
            if ($this->bookingRepository->hasUsersExceededDailyQuota($restrictedUserIds, $startAt)) {
                throw new Exception("One or more students/alumni in the booking have already reached their daily quota of 1 booking.", 422);
            }
        }

        // 3. Insert data with DB Transaction
        return DB::transaction(function () use ($data, $user, $members, $roomId, $startAt, $endAt) {
            $booking = $this->bookingRepository->create([
                'room_id' => $roomId,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'created_by_user_id' => $user->id,
                'booking_status' => BookingStatus::APPROVED, // Default status
            ]);

            if (!empty($members)) {
                $this->bookingRepository->attachMembers($booking, $members);
            }

            return $booking->load(['room', 'createdBy', 'bookingMembers']);
        });
    }

    public function updateBooking(Booking $booking, array $data, User $user): Booking
    {
        if ($booking->booking_status !== BookingStatus::APPROVED) {
            throw new Exception("Only approved bookings can be updated.", 422);
        }

        $oldStartAt = Carbon::parse($booking->start_at);
        if (now()->addMinutes(30)->gte($oldStartAt)) {
            throw new Exception("Bookings can only be updated at least 30 minutes before the original start time.", 422);
        }

        $startAt = Carbon::parse($data['start_at']);
        $endAt = Carbon::parse($data['end_at']);
        $members = $data['members'] ?? [];
        $roomId = $data['room_id'];

        // 1. Check room availability (ignore this booking's ID)
        if (!$this->bookingRepository->isRoomAvailable($roomId, $startAt, $endAt, $booking->id)) {
            throw new Exception("The room is already booked for the selected time period.", 422);
        }

        // 2. Prepare user IDs to check quota (creator + members)
        $userIdsToCheck = array_merge([$user->id], $members);
        
        $users = $this->userRepository->getUsersWithRolesByIds($userIdsToCheck);
        
        $restrictedUserIds = [];
        foreach ($users as $u) {
            if (in_array(strtolower($u->role->name), ['student', 'alumni'])) {
                $restrictedUserIds[] = $u->id;
            }
        }

        if (!empty($restrictedUserIds)) {
            // Check quota, ignoring this booking's ID
            if ($this->bookingRepository->hasUsersExceededDailyQuota($restrictedUserIds, $startAt, $booking->id)) {
                throw new Exception("One or more students/alumni in the booking have already reached their daily quota of 1 booking.", 422);
            }
        }

        return DB::transaction(function () use ($booking, $data, $members, $roomId, $startAt, $endAt) {
            $this->bookingRepository->update($booking->id, [
                'room_id' => $roomId,
                'start_at' => $startAt,
                'end_at' => $endAt,
            ]);

            // Sync members
            $this->bookingRepository->syncMembers($booking, $members);

            return $booking->load(['room', 'createdBy', 'bookingMembers']);
        });
    }

    public function checkInBooking(Booking $booking): Booking
    {
        if ($booking->booking_status !== BookingStatus::APPROVED) {
            throw new Exception("Only approved bookings can be checked in.", 422);
        }

        $this->bookingRepository->update($booking->id, ['booking_status' => BookingStatus::CHECKED_IN]);
        $booking->booking_status = BookingStatus::CHECKED_IN;

        return $booking;
    }

    public function cancelBooking(Booking $booking, User $user, bool $isAdmin = false): Booking
    {
        if ($booking->booking_status !== BookingStatus::APPROVED) {
            throw new Exception("Only approved bookings can be cancelled.", 422);
        }

        $startAt = Carbon::parse($booking->start_at);

        if (!$isAdmin) {
            // Check cancellation time limit (max 30 mins before)
            if (now()->addMinutes(30)->gte($startAt)) {
                throw new Exception("Bookings can only be cancelled at least 30 minutes before the start time.", 422);
            }
            
            // Allow user to cancel only their own booking
            if ($booking->created_by_user_id !== $user->id) {
                throw new Exception("You are not authorized to cancel this booking.", 403);
            }
        }

        return DB::transaction(function () use ($booking, $user, $startAt) {
            $this->bookingRepository->update($booking->id, ['booking_status' => BookingStatus::CANCELLED]);
            $booking->booking_status = BookingStatus::CANCELLED;

            // Check penalties for the creator of the booking
            $this->applyCancellationPenalty($booking->created_by_user_id, $startAt);

            return $booking;
        });
    }

    public function markAsNoShow(Booking $booking): Booking
    {
        if ($booking->booking_status !== BookingStatus::APPROVED) {
            return $booking; // Already processed
        }

        return DB::transaction(function () use ($booking) {
            $this->bookingRepository->update($booking->id, ['booking_status' => BookingStatus::NO_SHOW]);
            $booking->booking_status = BookingStatus::NO_SHOW;

            $startAt = Carbon::parse($booking->start_at);
            $this->applyCancellationPenalty($booking->created_by_user_id, $startAt);

            return $booking;
        });
    }

    protected function applyCancellationPenalty(int $userId, Carbon $date): void
    {
        $dailyCancellations = $this->bookingRepository->countUserDailyCancellations($userId, $date);

        // If >= 4, block the user
        if ($dailyCancellations >= 4) {
            $creator = $this->userRepository->findById($userId);
            if ($creator) {
                $this->userRepository->updateStatus($creator, 'blocked');
            }
        }
    }

    public function processExpiredNoShows(): void
    {
        $thresholdTime = now()->subMinutes(15);
        $expiredBookings = $this->bookingRepository->getExpiredBookings($thresholdTime);

        foreach ($expiredBookings as $booking) {
            $this->markAsNoShow($booking);
        }
    }
}
