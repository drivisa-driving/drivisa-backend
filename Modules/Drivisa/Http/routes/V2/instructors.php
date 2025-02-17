<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'instructors'], function (Router $router) {

    $router->group(['prefix' => 'schedules', 'middleware' => ['verified']], function (Router $router) {
        $router->get('', ['uses' => 'V2\ScheduleController@index']);
    });
        $router->get('{username}', ['uses' => 'V2\InstructorController@findByUsername'])->withoutMiddleware('api.token');
});
$router->get('search-instructors', ['uses' => 'V2\InstructorController@searchInstructors'])->withoutMiddleware('api.token');
