<?php

use Carbon\Carbon;

it('check if lesson under 30 min', function () {
    $now = Carbon::parse('2022-08-15 12:00:00');
    $lessonTime = Carbon::parse('2022-08-15 12:00:00')->subMinutes(30);

    $this->assertTrue($now->greaterThanOrEqualTo($lessonTime));


});
