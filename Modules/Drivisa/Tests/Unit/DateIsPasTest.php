<?php

use Carbon\Carbon;

it('check if date is past', function () {
    $dateOf = Carbon::parse('2022-06-13 11:30')->subMinutes(30);
    $now = Carbon::now();

    $this->assertTrue($dateOf->gt($now));
});