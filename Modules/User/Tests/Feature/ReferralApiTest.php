<?php

use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Entities\ReferralCode;
use Modules\User\Services\ReferralCodeGenerator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

it('cant sign up with non numeric referral code', function () {

    $signUpEndpoint = '/api/v1/auth/register';

    $res = $this->postJson($signUpEndpoint, [
        'first_name' => 'Deep',
        'last_name' => 'Instructor',
        'email' => 'deep@instructor.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'user_type' => 1,
        'player_id' => 'XXXXX',
        'refer_code' => 'dfsddfasdfsd', // 123456
    ]);

    $res->assertStatus(422);
});

it('can generate new code once a code is copied or sent to user', function () {


    $this->withoutExceptionHandling();
    $this->withoutMiddleware();

    $generator = new ReferralCodeGenerator();
    $code = $generator->generate();

    $user = \Modules\User\Entities\Sentinel\User::factory()->create();
    Sentinel::activate($user);

    $signUpEndpoint = "/api/v1/user/referral-code/code-sent";


    $res = $this->actingAs($user)->post($signUpEndpoint, [
        'code' => $code,
    ])
        ->assertOk()
        ->assertJson([
            'message' => 'code update and new code generated'
        ]);

    $this->assertNotEquals($code, $res->json('new_code'));
    $this->assertNotEquals($code, $user->activeReferralCode->code);
});

/**
 * @return int
 * @throws Exception
 */
function getReferralCode(): int
{
    $generator = new ReferralCodeGenerator();
    $code = $generator->generate();


    ReferralCode::factory()->create([
        'code' => $code
    ]);
    return $code;
}

it('can sign up with referral code and generate a unique code', function () {

    \Spatie\PestPluginTestTime\testTime()->freeze();

    $this->withoutExceptionHandling();

    $code = getReferralCode();

    $signUpEndpoint = '/api/v1/auth/register';
    $res = $this->post($signUpEndpoint, [
        'first_name' => 'Deep',
        'last_name' => 'Instructor',
        'email' => 'deep@instructor.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'user_type' => 1,
        'player_id' => 'XXXXX',
        'refer_code' => $code,
    ]);

    $res->assertOk();

    $this->assertDatabaseHas('referral_codes', [
        'code' => $code,
        'used_at' => now()
    ]);

    //3. new user has referral id of advocate instructor user id

    $this->assertDatabaseHas('users', [
        'refer_id' => 1
    ]);
});

it('can sign up without refer code', function () {

    $this->withoutExceptionHandling();

    $code = getReferralCode();

    $signUpEndpoint = '/api/v1/auth/register';

    $res = $this->post($signUpEndpoint, [
        'first_name' => 'Deep',
        'last_name' => 'Instructor',
        'email' => 'deep@instructor.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'user_type' => 1,
        'player_id' => 'XXXXX',
        'refer_code' => $code,
    ]);

    $res->assertOk();

    $signUpEndpoint = '/api/v1/auth/register';

    $res = $this->post($signUpEndpoint, [
        'first_name' => 'Ravi',
        'last_name' => 'Instructor',
        'email' => 'ravi@instructor.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'user_type' => 1,
        'player_id' => 'XXXXX',
        'refer_code' => $code,
    ]);

    $res->assertStatus(422);
});


it('cant sign up without unique refer code', function () {

    //1. check if user enter a valid refer code
    $signUpEndpoint = '/api/v1/auth/register';

    $res = $this->post($signUpEndpoint, [
        'first_name' => 'Deep',
        'last_name' => 'Instructor',
        'email' => 'deep@instructor.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'user_type' => 1,
        'player_id' => 'XXXXX',
    ]);

    $res->assertOk();
});

it('a unique code will generated for the referer instructor after sign up', function () {

    $code = getReferralCode();

    $signUpEndpoint = '/api/v1/auth/register';

    $res = $this->post($signUpEndpoint, [
        'first_name' => 'Deep',
        'last_name' => 'Instructor',
        'email' => 'deep@instructor.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'user_type' => 1,
        'player_id' => 'XXXXX',
        'refer_code' => $code,
    ]);

    $res->assertOk();

    $this->assertDatabaseHas('users', [
        'refer_id' => 1
    ]);
});

it('has a referral code in login and user response', function () {

    $this->withoutExceptionHandling();

    $generator = new ReferralCodeGenerator();
    $code = $generator->generate();

    $user = \Modules\User\Entities\Sentinel\User::factory()->create();
    Sentinel::activate($user);


    ReferralCode::factory()->create([
        'user_id' => $user->id,
        'code' => $code
    ]);

    $loginEndpoint = '/api/v1/auth/login';

    $res = $this->post($loginEndpoint, [
        'email' => $user->email,
        'password' => 'password'
    ]);


    $res->assertOk()
        ->assertJson([
            "user" => [
                'referral_code' => $code
            ]
        ]);
});


it('once a user sign up with refer code advocate should auto generate new code', function () {

    $this->withoutExceptionHandling();

    $generator = new ReferralCodeGenerator();
    $code = $generator->generate();

    $user = \Modules\User\Entities\Sentinel\User::factory()->create();
    Sentinel::activate($user);


    ReferralCode::factory()->create([
        'user_id' => $user->id,
        'code' => $code
    ]);


    $signUpEndpoint = '/api/v1/auth/register';

    $res = $this->post($signUpEndpoint, [
        'first_name' => 'Deep',
        'last_name' => 'Instructor',
        'email' => 'deep@instructor.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'user_type' => 1,
        'player_id' => 'XXXXX',
        'refer_code' => $code,
    ]);

    $this->assertNotEquals($code, $user->activeReferralCode->code);

});

it('it can sign up empty refer_code', function () {

    $this->withoutExceptionHandling();

    $generator = new ReferralCodeGenerator();
    $code = $generator->generate();

    $user = \Modules\User\Entities\Sentinel\User::factory()->create();
    Sentinel::activate($user);


    ReferralCode::factory()->create([
        'user_id' => $user->id,
        'code' => $code
    ]);


    $signUpEndpoint = '/api/v1/auth/register';

    $res = $this->post($signUpEndpoint, [
        'first_name' => 'Deep',
        'last_name' => 'Instructor',
        'email' => 'deep@instructor.com',
        'password' => '123456',
        'password_confirmation' => '123456',
        'user_type' => 1,
        'player_id' => 'XXXXX',
        'refer_code' => '',
    ])->assertOk();

    $this->assertEquals($code, $user->activeReferralCode->code);

});