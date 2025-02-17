<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\RentalRequest;
use Ramsey\Uuid\Uuid;

uses(RefreshDatabase::class)->in(__DIR__);

/**
 * @return mixed
 */
function setRoadTest($subDay = null)
{
    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();

    $trainee = \Modules\Drivisa\Entities\Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();


    $instructorUser1 = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();
    $instructor1 = Instructor::factory()->create([
        'user_id' => $instructorUser1->id,
        'promotion_level' => 1
    ]);

    $instructorUser1->api_keys()->create([
        'access_token' => Uuid::uuid4()
    ]);

    $package = \Modules\Drivisa\Entities\Package::factory()->create();

    $request = RentalRequest::factory()->create([
        'instructor_id' => $instructor1->id,
        'trainee_id' => $trainee->id,
        'package_id' => $package->id,
        'created_at' => $subDay ? now()->subDays($subDay) : now()
    ]);

    \Illuminate\Support\Facades\DB::table('drivisa__instructor_rental_request')
        ->insert([
            'instructor_id' => $instructor1->id,
            'rental_request_id' => $request->id
        ]);
    return $instructorUser1;
}

it('can have list of available request', function () {
    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();

    $trainee = \Modules\Drivisa\Entities\Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();


    $instructorUser1 = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();
    $instructor1 = Instructor::factory()->create([
        'user_id' => $instructorUser1->id,
        'promotion_level' => 1
    ]);

    $instructorUser1->api_keys()->create([
        'access_token' => Uuid::uuid4()
    ]);

    $package = \Modules\Drivisa\Entities\Package::factory()->create();

    $request = RentalRequest::factory()->create([
        'instructor_id' => $instructor1->id,
        'trainee_id' => $trainee->id,
        'package_id' => $package->id,
        'created_at' => now()
    ]);

    \Illuminate\Support\Facades\DB::table('drivisa__instructor_rental_request')
        ->insert([
            'instructor_id' => $instructor1->id,
            'rental_request_id' => $request->id
        ]);

    $this->assertDatabaseCount('drivisa__rental_requests', 1);

    $endpoint = "/api/v1/drivisa/instructors/car-rentals/available-requests";

    $response = $this->getJson($endpoint,
        [
            'Authorization' => 'Bearer ' . $instructorUser1->getFirstApiKey()->access_token
        ])->assertOk();

    $response->assertJsonCount(1, 'data');

});