<?php

it('check if credit is low', function () {
    $credit = 1;
    $duration = 2;

    $this->assertTrue($credit < $duration);
});