<?php

use Illuminate\Routing\Router;
use Modules\Setting\Http\Controllers\SettingController;

/** @var Router $router */

$router->group(['prefix' => 'trainees'], function (Router $router) {


    $router->get('get-extra-distance', ['uses' => 'TraineeController@getExtraDistance']);
    $router->post('reset-pick-drop', ['uses' => 'TraineeController@resetPickDrop']);
    $router->get('/core-settings', [SettingController::class, 'allSettings']);

    $router->group(['prefix' => 'packages'], function (Router $router) {
        $router->post('{package}/buy', ['uses' => 'PackageController@buyPackage']);
        $router->get('{package}', ['uses' => 'PackageController@getSinglePackage']);
        $router->post('buy-extra-hours', ['uses' => 'PackageController@buyExtraHours']);
    });

    $router->group(['prefix' => 'car-rental'], function (Router $router) {

        $router->post('register', ['uses' => 'RentalRequestController@register']);
        $router->post('reschedule', ['uses' => 'RentalRequestController@reschedule']);
        $router->get('all-requests', ['uses' => 'RentalRequestController@allRequests']);
        $router->post('{rentalRequest}/paid', ['uses' => 'RentalRequestController@paid']);
    });


    $router->group(['prefix' => 'saved-location'], function (Router $router) {

        $router->get('', ['uses' => 'SavedLocationController@getLocation']);
        $router->post('', ['uses' => 'SavedLocationController@saveLocation']);
        $router->post('combined', ['uses' => 'SavedLocationController@combined']);
        $router->delete('{savedLocation}', ['uses' => 'SavedLocationController@deleteLocation']);
    });


    $router->group(['prefix' => 'lessons'], function (Router $router) {
        $router->get('today-upcoming', ['uses' => 'LessonController@getLessonsListForTrainee']);
        $router->get('past', ['uses' => 'LessonController@getPastLessonsListForTrainee']);
        $router->get('', ['uses' => 'LessonController@getTraineeLessons']);
        $router->get('available-for-reschedule', ['uses' => 'LessonController@availableForReschedule']);
        $router->get('get-progress', ['uses' => 'LessonController@getProgress']);
        $router->get('{lesson}/get-instructor-availability', ['uses' => 'LessonController@getInstructorAvailability']);
        $router->post('reschedule', ['uses' => 'LessonController@reschedule']);
        $router->get('last-trip', ['uses' => 'LessonController@getLastTrip']);
        $router->get('{lesson}', ['uses' => 'LessonController@getTraineeLesson']);
        $router->post('{lesson}/cancel-by-trainee', ['uses' => 'LessonController@cancelLessonByTrainee']);
        $router->post('{lesson}/update-evaluation', ['uses' => 'LessonController@updateTraineeEvaluation']);
        $router->post('{lesson}/refund-choice', ['uses' => 'LessonController@refundChoice']);
    });

    $router->group(['prefix' => 'kyc'], function (Router $router) {
        $router->post('', ['uses' => 'KycController@updateTraineeKYC']);
    });

    $router->group(['prefix' => 'documents'], function (Router $router) {
        $router->post('single', ['uses' => 'TraineeController@uploadSingleDocuments']);
        $router->post('', ['uses' => 'TraineeController@uploadDocuments']);
        $router->get('', ['uses' => 'TraineeController@getDocuments']);
    });

    $router->group(['prefix' => 'history'], function (Router $router) {
        $router->get('purchases', ['uses' => 'PurchaseController@index']);
        $router->get('transactions', ['uses' => 'TransactionController@index']);
    });

    $router->group(['prefix' => 'courses'], function (Router $router) {
        $router->get('', ['uses' => 'CourseController@getTraineeCourses']);
        $router->post('{course}/cancel-by-trainee', ['uses' => 'CourseController@cancelCourse']);
    });

    $router->group(['prefix' => 'recent'], function (Router $router) {
        $router->get('instructors', ['uses' => 'TraineeController@getRecentInstructors']);
    });

    $router->group(['prefix' => 'card'], function (Router $router) {
        $router->post('add', ['uses' => 'StripeCardController@add']);
        $router->get('get-cards', ['uses' => 'StripeCardController@getAll']);
        $router->post('update', ['uses' => 'StripeCardController@updateCard']);
        $router->delete('{card_id}/delete', ['uses' => 'StripeCardController@deleteCard']);
    });

    $router->group(['prefix' => 'stats'], function (Router $router) {
        $router->get('missing-info', ['uses' => 'StatsController@missingInfo']);
        $router->get('{type?}', ['uses' => 'StatsController@getCourseStatsByType']);
    });

    $router->get('me', ['uses' => 'TraineeController@me']);
    $router->post('update-profile', ['uses' => 'TraineeController@updateTraineeProfile']);
    $router->post('booking-lesson', ['uses' => 'TraineeController@BookingLesson']);
    $router->post('booking-lesson/by-credit', ['uses' => 'TraineeController@bookingLessonByCredit']);
    $router->post('get-additional-price', ['uses' => 'TraineeController@getAdditionalPrice']);
    $router->get('get-profile-info', ['uses' => 'TraineeController@getProfileInfo']);
    $router->get('{username}', ['uses' => 'TraineeController@findByUsername']);
    $router->get('/evaluation/{username}', ['uses' => 'TraineeController@getTraineeEvaluation']);
    $router->get('/evaluation-by-trainee/{username}', ['uses' => 'TraineeController@getTraineeEvaluationByTrainee']);

    $router->group(['prefix' => 'discounts'], function (Router $router) {
        $router->put('getCodeDetails', ['uses' => 'DiscountController@getCodeDetails']);
        $router->put('storeUserCode', ['uses' => 'DiscountController@storeUserCode']);
    });
});
