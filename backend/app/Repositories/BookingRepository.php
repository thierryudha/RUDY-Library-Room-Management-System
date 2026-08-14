<?php

namespace App\Repositories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingRepository extends BaseRepository
{
    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    /**
     * Get booking history for a specific user (either as creator or member)
     */
    public function getUserBookings(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Booking::with(['room', 'createdBy', 'bookingMembers'])
            ->where('created_by_user_id', $userId)
            ->orWhereHas('bookingMembers', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('start_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Check if a room is already booked during a specific time period.
     */
    public function isRoomAvailable(int $roomId, Carbon $startAt, Carbon $endAt, ?int $ignoreBookingId = null): bool
    {
        $query = Booking::where('room_id', $roomId)
            ->whereIn('booking_status', [BookingStatus::APPROVED, BookingStatus::CHECKED_IN])
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt);

        if ($ignoreBookingId) {
            $query->where('id', '!=', $ignoreBookingId);
        }

        return !$query->exists();
    }

    /**
     * Check if users (creator or members) have already booked a room on a specific date.
     * Returns true if any of the given users has reached their daily quota.
     */
    public function hasUsersExceededDailyQuota(array $userIds, Carbon $date, ?int $ignoreBookingId = null): bool
    {
        // For students and alumni, they can only have 1 active/completed booking per day
        $query = Booking::whereIn('booking_status', [BookingStatus::APPROVED, BookingStatus::CHECKED_IN])
            ->whereDate('start_at', $date->toDateString())
            ->where(function (Builder $q) use ($userIds) {
                $q->whereIn('created_by_user_id', $userIds)
                  ->orWhereHas('bookingMembers', function (Builder $subQ) use ($userIds) {
                      $subQ->whereIn('user_id', $userIds);
                  });
            });

        if ($ignoreBookingId) {
            $query->where('id', '!=', $ignoreBookingId);
        }

        return $query->exists();
    }

    /**
     * Count the number of cancellations/no-shows for a user on a specific date.
     */
    public function countUserDailyCancellations(int $userId, Carbon $date): int
    {
        return Booking::where('created_by_user_id', $userId)
            ->whereIn('booking_status', [BookingStatus::CANCELLED, BookingStatus::NO_SHOW])
            ->whereDate('start_at', $date->toDateString())
            ->count();
    }

    public function attachMembers(Booking $booking, array $members): void
    {
        $booking->bookingMembers()->attach($members);
    }

    public function syncMembers(Booking $booking, array $members): void
    {
        $booking->bookingMembers()->sync($members);
    }

    public function getExpiredBookings(Carbon $thresholdTime): Collection
    {
        return Booking::where('booking_status', BookingStatus::APPROVED)
            ->where('start_at', '<=', $thresholdTime)
            ->get();
    }
}
