<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Lesson;
use Modules\Pusher\Notifications\ChatNotificationToUser;
use Ramsey\Uuid\Uuid;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('can send notification to user', function () {
    Notification::fake();

    Notification::assertNothingSent();

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

    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor1->id,
            'ended_at' => now(),
        ]);

    $data = [];


    $traineeUser->notify(new ChatNotificationToUser($data, $lesson, $instructorUser1));

    Notification::assertSentTo([$traineeUser], ChatNotificationToUser::class);
});