<?php

use Illuminate\Routing\Router;

/** @var Router $router */


$router->group(['prefix' => 'instructors'], function (Router $router) {
    $router->get('{username}', ['uses' => 'InstructorController@findByUsername']);
    $router->get('{username}/get-schedule', ['uses' => 'ScheduleController@getInstructorSchedule']);
    $router->get('workingDays/{workingDay}', ['uses' => 'WorkingDayController@index']);
});
$router->group(['prefix' => 'courses'], function (Router $router) {
    $router->get('', ['uses' => 'CourseController@index']);
    $router->get('{course}', ['uses' => 'CourseController@show']);
});


$router->get('get-nearest-instructors', ['uses' => 'InstructorPointController@getNearestInstructorsPoints']);
$router->get('get-top-instructors', ['uses' => 'InstructorController@getTopInstructors']);
$router->get('search-instructors', ['uses' => 'InstructorController@searchInstructors']);
$router->get('get-car-maker', ['uses' => 'CarController@getCarMaker']);
$router->get('get-packages/{type}', ['uses' => 'PackageController@getPackagesByType']);