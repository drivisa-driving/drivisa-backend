<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);

it('can hit webhook with data', function () {
    $data = [
        'lesson_id' => '101',
        'message' => 'this is testing from pest',
        'message_by' => 'instructor',
    ];

    $url = "/api/v1/drivisa/webhook/pusher";

    $res = $this->post($url, $data);

    $res->assertOK();
});