<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\StoreFeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeedbackController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $feedbacks = Feedback::with(['user', 'booking.room'])
            ->when(request('room_id'), fn ($q) => $q->whereHas('booking', fn ($q) => $q->where('room_id', request('room_id'))))
            ->latest()
            ->paginate(request('per_page', 15));

        return FeedbackResource::collection($feedbacks);
    }

    public function store(StoreFeedbackRequest $request, Booking $booking): JsonResponse
    {
        $feedback = Feedback::create([
            'booking_id' => $booking->id,
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return (new FeedbackResource($feedback->load(['user', 'booking'])))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Feedback $feedback): JsonResponse
    {
        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted']);
    }
}
