<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'instructors'], function (Router $router) {


    $router->group(['prefix' => 'stats'], function (Router $router) {
        $router->get('missing-info', ['uses' => 'InstructorStatsController@missingInfo']);
    });

    $router->group(['prefix' => 'car-rentals'], function (Router $router) {
        $router->get('check-conflict/{rentalRequest}', ['uses' => 'RentalRequestController@checkConflict']);
        $router->get('available-requests', ['uses' => 'RentalRequestController@getAvailableRequestForInstructor']);
        $router->get('accepted-requests', ['uses' => 'RentalRequestController@getAcceptedRequestForInstructor']);
        $router->put('/{rentalRequest}/{type}', ['uses' => 'RentalRequestController@acceptRentalRequestByInstructor']);
    });


    $router->group(['prefix' => 'finance'], function (Router $router) {
        $router->group(['prefix' => 'accounts'], function (Router $router) {
            $router->get('', ['uses' => 'StripeController@getStripeAccount']);
            $router->delete('', ['uses' => 'StripeController@deleteConnectedStripeAccount']);
            $router->post('', ['uses' => 'StripeController@connectStripeAccount']);
            $router->post('attach-bank-account', ['uses' => 'StripeController@connectBankAccount']);
            $router->post('update', ['uses' => 'StripeController@updateConnectedStripeAccount']);
        });
        $router->group(['prefix' => 'earnings'], function (Router $router) {
            $router->get('breakdown-weekly', ['uses' => 'InstructorEarningController@getEarningBreakDown']);
            $router->get('weekly', ['uses' => 'StripeController@getWeeklyEarnings']);
            $router->get('', ['uses' => 'StripeController@getEarnings']);
        });
    });

    $router->group(['prefix' => 'kyc'], function (Router $router) {
        $router->post('signed-agreement', ['uses' => 'KycController@signAgreement']);
        $router->post('', ['uses' => 'KycController@updateInstructorKYC']);
    });

    $router->group(['prefix' => 'schedules', 'middleware' => ['verified']], function (Router $router) {
        $router->get('', ['uses' => 'ScheduleController@index']);
        $router->post('', ['uses' => 'ScheduleController@saveSchedule']);
        $router->post('copy-schedule', ['uses' => 'ScheduleController@copySchedule']);
    });
    $router->group(['prefix' => 'workingDays/{workingDay}'], function (Router $router) {
        $router->group(['middleware' => ['verified']], function (Router $router) {
            $router->post('update-status', ['uses' => 'WorkingDayController@updateWorkingDayStatus']);
        });
    });
    $router->group(['prefix' => 'workingHours/{workingHour}', 'middleware' => ['verified']], function (Router $router) {
        $router->post('update', ['uses' => 'WorkingHourController@updateWorkingHour']);
        $router->delete('', ['uses' => 'WorkingHourController@deleteWorkingHour']);
        $router->post('update-status', ['uses' => 'WorkingHourController@updateWorkingHourStatus']);
        $router->post('make-available', ['uses' => 'WorkingHourController@makeWorkingHourAvailable']);
    });
    $router->group(['prefix' => 'points'], function (Router $router) {
        $router->get('{point}', ['uses' => 'InstructorPointController@getPoint']);
        $router->post('set-point', ['uses' => 'InstructorPointController@setPoint']);
        $router->delete('{point}', ['uses' => 'InstructorPointController@destroy']);
        $router->post('{point}', ['uses' => 'InstructorPointController@update']);
        $router->post('{point}/toggle-point', ['uses' => 'InstructorPointController@togglePoint']);
    });
    $router->group(['prefix' => '{instructor}/points'], function (Router $router) {
        $router->get('', ['uses' => 'InstructorPointController@index']);
    });
    $router->post(
        '{instructor}/verify',
        [
            'uses' => 'InstructorController@verifyInstructor',
            'middleware' => 'token-can:drivisa.instructor.verify'
        ]
    );
    $router->group(['prefix' => 'lessons'], function (Router $router) {
        $router->get('today-upcoming', ['uses' => 'LessonController@getLessonsListForInstructor']);
        $router->get('history', ['uses' => 'LessonController@getInstructorHistory']);
        $router->get('', ['uses' => 'LessonController@getInstructorLessons']);
        $router->get('{lesson}', ['uses' => 'LessonController@getInstructorLesson']);
        $router->post('{lesson}/update-started-at', ['uses' => 'LessonController@updateStartedAt']);
        $router->post('{lesson}/update-ended-at', ['uses' => 'LessonController@updateEndedAt']);
        $router->post('{lesson}/update-evaluation', ['uses' => 'LessonController@updateInstructorEvaluation']);
        $router->post('{lesson}/cancel-by-instructor', ['uses' => 'LessonController@cancelLessonByInstructor']); // cancelLessonOnTraineeNotAvailable
        $router->post('{lesson}/cancel-booking-when-trainee-not-available', ['uses' => 'LessonController@cancelLessonOnTraineeNotAvailable']);
        $router->post('{lesson}/notify-trainee', ['uses' => 'LessonController@notifyTrainee']);
    });
    $router->group(['prefix' => 'evaluations'], function (Router $router) {
        $router->get('', ['uses' => 'EvaluationIndicatorController@index']);
        $router->get('{trainee_id}', ['uses' => 'EvaluationIndicatorController@getEvaluations']);
    });
    $router->group(['prefix' => 'documents'], function (Router $router) {
        $router->post('single', ['uses' => 'InstructorController@uploadSingleDocuments']);
        $router->post('', ['uses' => 'InstructorController@uploadDocuments']);
        $router->get('', ['uses' => 'InstructorController@getDocuments']);
    });
    $router->group(['prefix' => 'cars'], function (Router $router) {
        $router->get('', ['uses' => 'CarController@index']);
        $router->get('{car}', ['uses' => 'CarController@getCar']);
        $router->post('', ['uses' => 'CarController@addCar']);
        $router->post('{car}', ['uses' => 'CarController@updateCar']);
        $router->delete('{car}', ['uses' => 'CarController@destroy']);
    });

    $router->get('me', ['uses' => 'InstructorController@me']);
    $router->get('get-profile-info', ['uses' => 'InstructorController@getProfileInfo']);
    $router->get('get-points', ['uses' => 'InstructorPointController@getPoints']);
    $router->get('get-active-points', ['uses' => 'InstructorPointController@getActivePoints']);
    $router->post('update-profile', ['uses' => 'InstructorController@updateInstructorProfile']);
    $router->get('current-ongoing-lesson', ['uses' => 'InstructorController@currentLesson']);
    $router->get('/', ['uses' => 'InstructorController@index']);

    $router->group(['prefix' => 'marking-keys'], function (Router $router) {
        $router->get('', ['uses' => 'MarkingKeyController@index']);
        $router->get('{trainee_id}', ['uses' => 'MarkingKeyController@getMarkingKeys']);
    });
    $router->group(['prefix' => 'bde'], function (Router $router) {
        $router->post('bde-log', ['uses' => 'BDELogController@addBde']);
        $router->get('bde-log/{lesson_id}', ['uses' => 'BDELogController@getLatestBdeLog']);

        // marking keys and log
        $router->post('marking-key-log', ['uses' => 'MarkingKeyLogController@addMarkingKeyLog']);
        $router->post('final-test-log', ['uses' => 'FinalTestLogController@finalTestLog']);
        $router->get('final-test-keys', ['uses' => 'FinalTestKeyController@finalTestKeys']);
    });
});
