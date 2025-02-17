<?php

use Tests\TestCase;

uses(TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

it('check all unread notifications', function () {
    $user = \Modules\User\Entities\Sentinel\User::create();

    $this->actingAs($user);


});