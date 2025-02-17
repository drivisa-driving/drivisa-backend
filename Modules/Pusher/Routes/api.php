<?php

use Illuminate\Routing\Router;
use Modules\Pusher\Http\Controllers\MessageController;

/** @var Router $router */


$router->group(['prefix' => 'v1/drivisa/webhook/'], function (Router $router) {
    $router->post('ably', [MessageController::class, 'store']);
    $router->get('messages/{lesson}', [MessageController::class, 'getLessonById']);
    $router->get('messages/request/{rentalRequest}', [MessageController::class, 'getRequestById']);
});