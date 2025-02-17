<?php

namespace Modules\Drivisa\Tests\Feature;

use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Entities\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Drivisa\Entities\CreditUseHistory;
use Modules\Drivisa\Services\ResetPickDropService;

uses(RefreshDatabase::class)->in(__DIR__);

it('can calculate extra distance in positive figure', function () {
    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();
    $instructorUser = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();

    $trainee = Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();

    $instructor = Instructor::factory()->create([
        'user_id' => $instructorUser->id,
        'promotion_level' => 1
    ]);

    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'instructor_id' => $instructor->id,
            'working_hour_id' => \Modules\Drivisa\Entities\WorkingHour::factory()->create()->id,
            'additional_km' => 10,
            'created_by' => $traineeUser->id,
            'additional_cost' => 5223.02,
            'additional_km' => 5223.02,
            'additional_tax' => 912.20,
            'pickup_point' => '{"latitude":"44.2311717","longitude":"-76.4859544","address":"Kingston, ON, Canada"}',
            'dropoff_point' => '{"latitude":"53.562677","longitude":"-113.505546","address":"Kingsway Mall, Kingsway Northwest, Edmonton, AB, Canada"}'
        ]);

    $pick_drop = [
        'type' => "pick-drop",
        'drop_lat' => 49.2827223,
        'drop_long' => -123.1207324,
        'pick_lat' => 43.8253305,
        'pick_long' => -79.5381769,
        'pick_address' => "Vaughan Mills, Bass Pro Mills Drive, Vaughan, ON, Canada",
        'drop_address' => "Vancouver, BC, Canada",
    ];

    $data = [
        'lesson_id' => $lesson->id,
        'pick_drop' => $pick_drop
    ];

    $resetPickDropService = app()->make(ResetPickDropService::class);
    $response = $resetPickDropService->getExtraDistance($data);

    $this->assertGreaterThan('1.00', $response['difference']);
    $this->assertGreaterThan($response['new_additional_km'], $lesson->additional_km);
});

it('can calculate extra distance equal to zero', function () {
    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();
    $instructorUser = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();

    $trainee = Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();

    $instructor = Instructor::factory()->create([
        'user_id' => $instructorUser->id,
        'promotion_level' => 1
    ]);

    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'instructor_id' => $instructor->id,
            'working_hour_id' => \Modules\Drivisa\Entities\WorkingHour::factory()->create()->id,
            'additional_km' => 0,
            'created_by' => $traineeUser->id,
            'additional_cost' => 0,
            'additional_km' => 0,
            'additional_tax' => 0,
            'pickup_point' => '{"latitude":"44.2612684","longitude":"-76.9752762","address":"Kingston, ON, Canada"}',
            'dropoff_point' => '{"latitude":"44.2612684","longitude":"-76.9752762","address":"Kingston, ON, Canada"}'
        ]);

    $pick_drop = [
        'type' => "pick-drop",
        'drop_lat' => 43.8253305,
        'drop_long' => -79.5381769,
        'pick_lat' => 43.8253305,
        'pick_long' => -79.5381769,
        'pick_address' => "Vaughan Mills, Bass Pro Mills Drive, Vaughan, ON, Canada",
        'drop_address' => "Vaughan Mills, Bass Pro Mills Drive, Vaughan, ON, Canada",
    ];

    $data = [
        'lesson_id' => $lesson->id,
        'pick_drop' => $pick_drop
    ];


    $resetPickDropService = app()->make(ResetPickDropService::class);
    $response = $resetPickDropService->getExtraDistance($data);
    $this->assertEquals('0.0', $response['difference']);
});

it('can calculate extra distance in negative figure', function () {
    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();
    $instructorUser = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();

    $trainee = Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();

    $instructor = Instructor::factory()->create([
        'user_id' => $instructorUser->id,
        'promotion_level' => 1
    ]);

    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'instructor_id' => $instructor->id,
            'working_hour_id' => \Modules\Drivisa\Entities\WorkingHour::factory()->create()->id,
            'additional_km' => 0,
            'created_by' => $traineeUser->id,
            'additional_cost' => 0,
            'additional_km' => 0,
            'additional_tax' => 0,
            'pickup_point' => '{"latitude":"44.2612684","longitude":"-76.9752762","address":"Kingston, ON, Canada"}',
            'dropoff_point' => '{"latitude":"44.2612684","longitude":"-76.9752762","address":"Kingston, ON, Canada"}'
        ]);

    $pick_drop = [
        'type' => "pick-drop",
        'drop_lat' => 44.2612684,
        'drop_long' => -76.9752762,
        'pick_lat' => 44.2612684,
        'pick_long' => -76.9752762,
        'pick_address' => "Kingston, ON, Canada",
        'drop_address' => "Kingston, ON, Canada",
    ];

    $data = [
        'lesson_id' => $lesson->id,
        'pick_drop' => $pick_drop
    ];


    $resetPickDropService = app()->make(ResetPickDropService::class);
    $response = $resetPickDropService->getExtraDistance($data);

    $this->assertLessThan('1.00', $response['difference']);
    $this->assertLessThan($response['new_additional_km'], $lesson->additional_km);
});


it('can can reset pick drop if difference is in positive figure and lesson booked by payment', function () {

    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();
    $instructorUser = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();

    $trainee = Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();

    $instructor = Instructor::factory()->create([
        'user_id' => $instructorUser->id,
        'promotion_level' => 1
    ]);


    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'status' => Lesson::STATUS['reserved'],
            'trainee_id' => $trainee->id,
            'instructor_id' => $instructor->id,
            'working_hour_id' => \Modules\Drivisa\Entities\WorkingHour::factory()->create()->id,
            'additional_km' => 10,
            'created_by' => $traineeUser->id,
            'additional_cost' => 5223.02,
            'additional_km' => 5223.02,
            'additional_tax' => 912.20,
            'pickup_point' => '{"latitude":"44.2311717","longitude":"-76.4859544","address":"Kingston, ON, Canada"}',
            'dropoff_point' => '{"latitude":"53.562677","longitude":"-113.505546","address":"Kingsway Mall, Kingsway Northwest, Edmonton, AB, Canada"}',
            'payment_intent_id' => "pi_3MXWusBt0VLpt9dF20AwwgGr",
        ]);

    $data = [
        'lesson_id' => $lesson->id,
        'new_pickup_point' => '{\"latitude\":\"43.8253305\",\"longitude\":\"-79.5381769\",\"address\":\"Vaughan Mills, Bass Pro Mills Drive, Vaughan, ON, Canada\"}',
        'new_dropoff_point' => '{\"latitude\":\"49.2827291\",\"longitude\":\"-123.1207375\",\"address\":\"Vancouver, BC, Canada\"}',
        'new_additional_cost' => 4403.356,
        'new_additional_km' => 4403.356,
        'new_additional_tax' => 572.43628,
        'difference' => 4403.356,
        'amount' => 4975.79228
    ];

    $resetPickDropService = app()->make(ResetPickDropService::class);
    $response = $resetPickDropService->resetPickDrop($data);

    expect(null)->toBeEmpty($response);
});

it('can can reset pick drop if difference is in positive figure and lesson booked by credit', function () {

    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();
    $instructorUser = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();

    $trainee = Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();

    $instructor = Instructor::factory()->create([
        'user_id' => $instructorUser->id,
        'promotion_level' => 1
    ]);

    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'instructor_id' => $instructor->id,
            'status' => Lesson::STATUS['reserved'],
            'working_hour_id' => \Modules\Drivisa\Entities\WorkingHour::factory()->create()->id,
            'additional_km' => 10,
            'created_by' => $traineeUser->id,
            'additional_cost' => 0,
            'additional_km' => 0,
            'additional_tax' => 0,
            'pickup_point' => '{"latitude":"44.2311717","longitude":"-76.4859544","address":"Kingston, ON, Canada"}',
            'dropoff_point' => '{"latitude":"53.562677","longitude":"-113.505546","address":"Kingsway Mall, Kingsway Northwest, Edmonton, AB, Canada"}',
            "cost" => 0,
            "commission" => 0,
            "net_amount" => 0,
            "tax" => 0,
            "payment_by" => Lesson::PAYMENT_BY['credit'],
            'credit_use_histories_id' => CreditUseHistory::factory()->create()->id
        ]);

    $data = [
        'lesson_id' => $lesson->id,
        'new_pickup_point' => '{\"latitude\":\"43.8253305\",\"longitude\":\"-79.5381769\",\"address\":\"Vaughan Mills, Bass Pro Mills Drive, Vaughan, ON, Canada\"}',
        'new_dropoff_point' => '{\"latitude\":\"49.2827291\",\"longitude\":\"-123.1207375\",\"address\":\"Vancouver, BC, Canada\"}',
        'new_additional_cost' => 14,
        'new_additional_km' => 14,
        'new_additional_tax' => 56.76,
        'difference' => 3,
        'amount' => 3
    ];

    $resetPickDropService = app()->make(ResetPickDropService::class);
    $response = $resetPickDropService->resetPickDrop($data);

    expect(null)->toBeEmpty($response);
});
