<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in(__DIR__);

it('a user can update his or her player id when login', function () {
    $user = getActivatedUser();
    $endpoint = "/api/v1/auth/login";


    $res = $this->withoutExceptionHandling()->postJson($endpoint, [
        'email' => $user->email,
        'password' => 'password',
        'player_id' => 'test-player_id'
    ])->assertOk();


    $this->assertDatabaseHas('user_devices', [
        'player_id' => 'test-player_id'
    ]);
});

it('a user cant have duplicate player id', function () {
    $user = getActivatedUser();

    $user2 = getActivatedUser();
    $endpoint = "/api/v1/auth/login";


    $this->withoutExceptionHandling()->postJson($endpoint, [
        'email' => $user->email,
        'password' => 'password',
        'player_id' => 'test-player_id'
    ])->assertOk();


    $this->withoutExceptionHandling()->postJson($endpoint, [
        'email' => $user2->email,
        'password' => 'password',
        'player_id' => 'test-player_id'
    ])->assertOk();

    $this->assertSoftDeleted('user_devices', [
        'player_id' => 'test-player_id',
        'user_id' => $user->id
    ]);

    $this->assertDatabaseHas('user_devices', [
        'player_id' => 'test-player_id',
        'user_id' => $user2->id
    ]);

});