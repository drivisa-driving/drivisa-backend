<?php

use Carbon\Carbon;

it('check if lesson can start before actual time', function ($now) {
    $startDate = "24-04-2022 06:00:00";
    $endDate = "24-04-2022 07:00:00";

    $startAt = Carbon::parse($startDate);
    $endAt = Carbon::parse($endDate);
    $currentDateTime = Carbon::parse($now);

    $this->assertFalse($startAt->greaterThan($currentDateTime->copy()->addMinutes(15)));
})->with([
    "24-04-2022 05:45:00",
    "24-04-2022 05:46:00",
    "24-04-2022 05:47:00",
    "24-04-2022 05:48:00",
    "24-04-2022 05:49:00",
    "24-04-2022 05:50:00",
    "24-04-2022 05:51:00",
    "24-04-2022 05:52:00",
    "24-04-2022 05:53:00",
    "24-04-2022 05:54:00",
    "24-04-2022 05:55:00",
    "24-04-2022 05:56:00",
    "24-04-2022 05:57:00",
    "24-04-2022 05:58:00",
    "24-04-2022 05:59:00",
    "24-04-2022 06:00:00",
]);
