<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Modules\Drivisa\Entities\Instructor;
use Tests\TestCase;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

uses(Tests\TestCase::class)->in('Feature');


function getActivatedUser()
{
    $user = \Modules\User\Entities\Sentinel\User::factory()->create();
    Sentinel::activate($user);
    return $user;
}


function createTrainee()
{
    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();

    return \Modules\Drivisa\Entities\Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();
}

function createInstructor()
{
    $instructorUser1 = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();
    return Instructor::factory()->create([
        'user_id' => $instructorUser1->id,
        'promotion_level' => 1
    ]);
}