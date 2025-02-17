<?php

use App\Jobs\SendNotificationToInstructorForEndLessonJob;
use App\Jobs\SendNotificationToInstructorForStartLessonJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Notifications\SendLessonNotEndNotification;
use Modules\Drivisa\Notifications\SendLessonNotStartedNotification;
use Ramsey\Uuid\Nonstandard\Uuid;


uses(RefreshDatabase::class)->in(__DIR__);

it('can send notification when a booking end time exceed 10 min', function () {

    Queue::fake();
    Notification::fake();

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

    Lesson::factory()
        ->drivingLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor1->id,
            'start_at' => '2022-01-01 10:00:00',
            'start_time' => '10:00:00',
            'end_at' => '2022-01-01 11:00:00',
            'additional_cost' => 200.0,
            'additional_tax' => 26.0
        ]);

    $this->travelTo(\Carbon\Carbon::parse('2022-01-01 10:00:00'));

    Queue::assertNothingPushed();


    Artisan::call('schedule:run');
    Queue::assertPushed(SendNotificationToInstructorForEndLessonJob::class);
});


it('check if reminder send on time for lesson end', function () {
    Queue::fake();
    Notification::fake();
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
            'start_at' => '2022-01-01 22:00:00',
            'started_at' => '2022-01-01 22:00:00',
            'start_time' => '22:00:00',
            'end_at' => '2022-01-01 23:00:00',
            'additional_cost' => 200.0,
            'additional_tax' => 26.0
        ]);

    $this->travelTo(\Carbon\Carbon::parse('2022-01-01 23:00:00'));
    Artisan::call('schedule:run');
    $this->travelTo(\Carbon\Carbon::parse('2022-01-01 23:10:00'));
    Queue::assertPushed(SendNotificationToInstructorForEndLessonJob::class);

    $jobClass = new SendNotificationToInstructorForEndLessonJob();
    $this->assertCount(1, $jobClass->getAllLessonForToday());
    $jobClass->handle();

    Notification::assertSentTo($instructorUser1, SendLessonNotEndNotification::class);
});
