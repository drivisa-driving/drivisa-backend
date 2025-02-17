<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\RentalRequest;
use Modules\Drivisa\Services\EarningService;
use Ramsey\Uuid\Uuid;

uses(RefreshDatabase::class)->in(__DIR__);


it('have no earning when no lesson ended', function () {
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
            'additional_cost' => 200.0,
            'additional_tax' => 26.0
        ]);

    $earningService = new EarningService;
    $earningBreakDown = $earningService->getEarningBreakDown($instructor1, [
        'from' => '2022-06-08',
        'to' => now()
    ]);


    $this->assertEquals('0.00', $earningBreakDown['lesson_earning']);
    $this->assertEquals('0.00', $earningBreakDown['additional_amount']);
    $this->assertEquals('0.00', $earningBreakDown['compensation_earning']);
    $this->assertEquals('0.00', $earningBreakDown['referral_amount']);
    $this->assertEquals('0.00', $earningBreakDown['hst']);
});

it('have 1 driving lesson earning breakdown', function () {
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
            'ended_at' => now(),
            'additional_cost' => 200.0,
            'additional_tax' => 26.0
        ]);

//    Lesson::factory()
//        ->g2TestLesson()
//        ->create([
//            'trainee_id' => $trainee->id,
//            'created_by' => $trainee->user_id,
//            'instructor_id' => $instructor1->id,
//            'ended_at' => now()
//        ]);
//
//
//    Lesson::factory()
//        ->g2TestLesson()
//        ->create([
//            'trainee_id' => $trainee->id,
//            'created_by' => $trainee->user_id,
//            'instructor_id' => $instructor1->id,
//            'ended_at' => now(),
//            'is_rescheduled' => 1,
//            'rescheduled_payment_id' => \Illuminate\Support\Str::random()
//        ]);
//
//    Lesson::factory()
//        ->g2TestLesson()
//        ->create([
//            'trainee_id' => $trainee->id,
//            'created_by' => $trainee->user_id,
//            'instructor_id' => $instructor1->id,
//            'is_rescheduled' => 1,
//            'rescheduled_payment_id' => \Illuminate\Support\Str::random()
//        ]);
//
//    $g2Test = Lesson::factory()
//        ->g2TestLesson()
//        ->create([
//            'trainee_id' => $trainee->id,
//            'created_by' => $trainee->user_id,
//            'instructor_id' => $instructor1->id,
//        ]);
//
//    \Modules\Drivisa\Entities\LessonCancellation::factory()->create([
//        'lesson_id' => $g2Test->id,
//        'cancel_at' => now(),
//        'cancel_by' => 'trainee',
//        'reason' => 'testing',
//        'refund_id' => 101,
//        'time_left' => 1
//    ]);


    $earningService = new EarningService;
    $earningBreakDown = $earningService->getEarningBreakDown($instructor1, [
        'from' => '2022-06-08',
        'to' => now()
    ]);


    $this->assertEquals('40.00', $earningBreakDown['lesson_earning']);
    $this->assertEquals('200.00', $earningBreakDown['additional_amount']);
    $this->assertEquals('0.00', $earningBreakDown['compensation_earning']);
    $this->assertEquals('0.00', $earningBreakDown['referral_amount']);
    $this->assertEquals('31.20', $earningBreakDown['hst']);

});

it('have 1 driving lesson single reschedule earning breakdown', function () {
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
            'additional_cost' => 200.0,
            'additional_tax' => 26.0
        ]);

    $lesson->update([
        'is_rescheduled' => 1,
        'times_rescheduled' => 1,
        'rescheduled_payment_id' => \Illuminate\Support\Str::random()
    ]);

    $earningService = new EarningService;
    $earningBreakDown = $earningService->getEarningBreakDown($instructor1, [
        'from' => '2022-06-08',
        'to' => now()
    ]);


    $this->assertEquals('40.00', $earningBreakDown['lesson_earning']);
    $this->assertEquals('200.00', $earningBreakDown['additional_amount']);
    $this->assertEquals('20.00', $earningBreakDown['compensation_earning']);
    $this->assertEquals('0.00', $earningBreakDown['referral_amount']);
    $this->assertEquals('33.80', $earningBreakDown['hst']); // 26.0 + 5.2 + 2.6
});


it('have 1 driving lesson multiple reschedule earning with lesson end', function () {
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
            'additional_cost' => 200.0,
            'additional_tax' => 26.0
        ]);

    $lesson->update([
        'is_rescheduled' => 1,
        'times_rescheduled' => 1,
        'rescheduled_payment_id' => \Illuminate\Support\Str::random()
    ]);

    $lesson->update([
        'is_rescheduled' => 1,
        'times_rescheduled' => 2,
        'rescheduled_payment_id' => \Illuminate\Support\Str::random()
    ]);

    $lesson->update([
        'is_rescheduled' => 1,
        'times_rescheduled' => 3,
        'rescheduled_payment_id' => \Illuminate\Support\Str::random()
    ]);

    $earningService = new EarningService;
    $earningBreakDown = $earningService->getEarningBreakDown($instructor1, [
        'from' => '2022-06-08',
        'to' => now()
    ]);


    $this->assertEquals('40.00', $earningBreakDown['lesson_earning']);
    $this->assertEquals('200.00', $earningBreakDown['additional_amount']);
    $this->assertEquals('60.00', $earningBreakDown['compensation_earning']); // 3 * 20
    $this->assertEquals('0.00', $earningBreakDown['referral_amount']);
    $this->assertEquals('39.00', $earningBreakDown['hst']); // 26.0 + 5.2 + 2.6 + 2.6 + 2.6
});

it('have 1 g2test lesson without ended', function () {
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
        ->g2TestLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor1->id,
        ]);


    $earningService = new EarningService;
    $earningBreakDown = $earningService->getEarningBreakDown($instructor1, [
        'from' => '2022-06-08',
        'to' => now()
    ]);


    $this->assertEquals('0.00', $earningBreakDown['lesson_earning']);
    $this->assertEquals('0.00', $earningBreakDown['additional_amount']);
    $this->assertEquals('0.00', $earningBreakDown['compensation_earning']);
    $this->assertEquals('0.00', $earningBreakDown['referral_amount']);
    $this->assertEquals('0.00', $earningBreakDown['hst']);
});

it('have 1 g2test lesson ended but reschedule 1 time', function () {
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
        ->g2TestLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor1->id,
            'is_rescheduled' => 1,
            'rescheduled_payment_id' => \Illuminate\Support\Str::random()
        ]);


    $earningService = new EarningService;
    $earningBreakDown = $earningService->getEarningBreakDown($instructor1, [
        'from' => '2022-06-08',
        'to' => now()
    ]);


    $this->assertEquals('0.00', $earningBreakDown['lesson_earning']);
    $this->assertEquals('0.00', $earningBreakDown['additional_amount']);
    $this->assertEquals('0.00', $earningBreakDown['compensation_earning']);
    $this->assertEquals('0.00', $earningBreakDown['referral_amount']);
    $this->assertEquals('0.00', $earningBreakDown['hst']);
});

it('have 1 g2test lesson without ended but reschedule 1 time', function () {
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
        ->g2TestLesson()
        ->create([
            'trainee_id' => $trainee->id,
            'created_by' => $trainee->user_id,
            'instructor_id' => $instructor1->id,
            'ended_at' => now(),
            'is_rescheduled' => 1,
            'rescheduled_payment_id' => \Illuminate\Support\Str::random()
        ]);


    $earningService = new EarningService;
    $earningBreakDown = $earningService->getEarningBreakDown($instructor1, [
        'from' => '2022-06-08',
        'to' => now()
    ]);


    $this->assertEquals('115.00', $earningBreakDown['lesson_earning']);
    $this->assertEquals('0.00', $earningBreakDown['additional_amount']);
    $this->assertEquals('50.00', $earningBreakDown['compensation_earning']);
    $this->assertEquals('0.00', $earningBreakDown['referral_amount']);
    $this->assertEquals('21.45', $earningBreakDown['hst']);
});