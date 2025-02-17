<?php


it('check trainee credit stats', function () {

    $refund_credit_total = 2;

    $total_hours = 10;

    $used_hours = 6;


    $used_hours -= $refund_credit_total;

    $remaining_hours = $total_hours - $used_hours;

    $this->assertEquals(10, $total_hours);
    $this->assertEquals(4, $used_hours);
    $this->assertEquals(6, $remaining_hours);
});