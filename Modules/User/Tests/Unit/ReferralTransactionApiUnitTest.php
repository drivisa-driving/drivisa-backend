<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Lesson;
use Modules\User\Services\ReferralCodeGenerator;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

it('can store the transaction when a lesson booking completed', function () {
    $this->withoutExceptionHandling();

    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();

    $trainee = \Modules\Drivisa\Entities\Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();


    $instructorUser1 = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();
    $instructor1 = Instructor::factory()->create([
        'user_id' => $instructorUser1->id
    ]);

    $instructorUser2 = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create([
        'refer_id' => $instructorUser1->id
    ]);

    $instructorUser2->api_keys()->create([
        'access_token' => Uuid::uuid4()
    ]);

    $instructor2 = Instructor::factory()->create([
        'user_id' => $instructorUser2->id
    ]);

    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor2->id
        ]);


    $lessonStartEndpoint = "/api/v1/drivisa/instructors/lessons/{$lesson->id}/update-started-at";
    $response1 = $this->actingAs($instructorUser2)->post($lessonStartEndpoint, [], [
        'Authorization' => 'Bearer ' . $instructorUser2->getFirstApiKey()->access_token
    ])->assertOk();

    $lessonEndAt = "/api/v1/drivisa/instructors/lessons/{$lesson->id}/update-ended-at";
    $this->actingAs($instructorUser2)->post($lessonEndAt, [], [
        'Authorization' => 'Bearer ' . $instructorUser2->getFirstApiKey()->access_token
    ])->assertOk();

    $this->assertDatabaseCount('referral_transactions', 1);


});

it('not send referral when lesson is road test', function () {
    $this->withoutExceptionHandling();

    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();

    $trainee = \Modules\Drivisa\Entities\Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();


    $instructorUser1 = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();
    $instructor1 = Instructor::factory()->create([
        'user_id' => $instructorUser1->id
    ]);

    $instructorUser2 = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create([
        'refer_id' => $instructorUser1->id
    ]);

    $instructorUser2->api_keys()->create([
        'access_token' => Uuid::uuid4()
    ]);

    $instructor2 = Instructor::factory()->create([
        'user_id' => $instructorUser2->id
    ]);

    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor2->id
        ]);


    $lessonStartEndpoint = "/api/v1/drivisa/instructors/lessons/{$lesson->id}/update-started-at";
    $response1 = $this->actingAs($instructorUser2)->post($lessonStartEndpoint, [], [
        'Authorization' => 'Bearer ' . $instructorUser2->getFirstApiKey()->access_token
    ])->assertOk();

    $lessonEndAt = "/api/v1/drivisa/instructors/lessons/{$lesson->id}/update-ended-at";
    $this->actingAs($instructorUser2)->post($lessonEndAt, [], [
        'Authorization' => 'Bearer ' . $instructorUser2->getFirstApiKey()->access_token
    ])->assertOk();

    $this->assertDatabaseCount('referral_transactions', 0);


});