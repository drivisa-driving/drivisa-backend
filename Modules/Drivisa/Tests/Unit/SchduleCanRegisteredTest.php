<?php


use Carbon\Carbon;

it('check if schedule can not register before 45 min of lesson booking time', function ($now) {

    $bookingTime = Carbon::parse("29-06-2022 11:00:00"); // lesson time
    $currentDateTime = Carbon::parse($now); // trainee booking current time

    // schedule should not book after "29-06-2022 10:15:00"

    $this->assertFalse($bookingTime->copy()->subMinutes(45)->gt($currentDateTime));
})->with([
    "29-06-2022 10:15:00",
    "29-06-2022 10:25:00",
    "29-06-2022 10:35:00",
    "29-06-2022 10:45:00",
    "29-06-2022 10:55:00",
    "29-06-2022 11:00:00",
]);
