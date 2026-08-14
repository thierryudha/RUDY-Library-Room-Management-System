<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('bookings:check-no-show')->everyMinute();
