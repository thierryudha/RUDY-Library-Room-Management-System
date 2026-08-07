<?php

namespace App\Http\Controllers\Api;

// Karena kita menggunakan Service & Repository Layer, tambahkan use service layer nanti
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Feedback\StoreFeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeedbackController extends Controller
{
    // gunakan dependency injection: constructor injection untuk service dan method injection untuk form request 

}
