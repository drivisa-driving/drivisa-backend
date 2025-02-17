<?php

use Modules\Drivisa\Entities\Lesson;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Drivisa\Notifications\TraineeLessonRescheduleNotification;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('can send lesson reschedule notification to trainee', function () {
    Notification::fake();

    Notification::assertNothingSent();

    $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();

    $trainee = \Modules\Drivisa\Entities\Trainee::factory()
        ->verified()
        ->approvedKycVerification()
        ->user($traineeUser->id)
        ->create();

    $lesson = Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor1->id,
            'ended_at' => now(),
        ]);

    $data = [];

    $traineeUser->notify(new TraineeLessonRescheduleNotification($data, $lesson));
    Notification::assertSentTo([$traineeUser], TraineeLessonRescheduleNotification::class);
});
