<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckNoShowBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:check-no-show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for bookings that have not checked in within 15 minutes of start time and mark them as no-show.';

    /**
     * Execute the console command.
     */
    public function handle(BookingService $bookingService)
    {
        $this->info('Checking for no-show bookings...');
        
        $bookingService->processExpiredNoShows();

        $this->info('Finished processing no-show bookings.');
    }
}
