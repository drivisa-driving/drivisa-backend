<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Services\LessonService;
use Modules\Drivisa\Services\PaymentTransferService;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);


it('not send 100 dollar when 40 hours schedule added', function () {
    $instructorUser1 = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();

    $instructor1 = Instructor::factory()->approvedKycVerification()->create([
        'user_id' => $instructorUser1->id,
        'promotion_level' => 0
    ]);

    $instructorUser1->api_keys()->create([
        'access_token' => Uuid::uuid4()
    ]);

    $scheduleEndpoint = "/api/v1/drivisa/instructors/schedules";

    $point = \Modules\Drivisa\Entities\Point::factory()->create([
        'instructor_id' => $instructor1->id
    ]);

    for ($i = 0; $i <= 40; $i++) {
        $response = $this->postJson($scheduleEndpoint,
            [
                'date' => now()->addDays($i)->format('Y-m-d'),
                'working_hours' => [
                    [
                        'open_at' => '13:00',
                        'shift' => 60,
                        'point_id' => $point->id
                    ]
                ]
            ],
            [
                'Authorization' => 'Bearer ' . $instructorUser1->getFirstApiKey()->access_token
            ])->assertOk();
    }


});

it('can send $500 dollar when 100 hours lesson completed', function () {
    $this->withoutExceptionHandling();

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

    $lessons = Lesson::factory()
        ->drivingLesson()
        ->count(99)
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor1->id,
            'ended_at' => now(),
        ]);

    $this->assertDatabaseCount('drivisa__lessons', 99);

    $lastLesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor1->id
        ]);

    \Modules\Drivisa\Entities\StripeAccount::factory()
        ->create([
            'instructor_id' => $instructor1->id,
        ]);

    $lessonStartEndpoint = "/api/v1/drivisa/instructors/lessons/{$lastLesson->id}/update-ended-at";
    $response1 = $this->actingAs($instructorUser1)->post($lessonStartEndpoint, [], [
        'Authorization' => 'Bearer ' . $instructorUser1->getFirstApiKey()->access_token
    ]);

    $response1->assertOk();

});