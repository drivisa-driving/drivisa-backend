<?php

use Illuminate\Routing\Router;

/** @var Router $router */

use \Modules\Setting\Http\Controllers\SettingController;


$router->group([
    'prefix' => '/v1/admin',
    'middleware' => ['api.token', 'is-admin']
], function (Router $router) {

    $router->get('/settings', [SettingController::class, 'allSettings']);
    $router->post('/settings', [SettingController::class, 'store']);
    $router->get('/settings/{id}', [SettingController::class, 'getSingleSetting']);
    $router->put('/settings/{id}', [SettingController::class, 'update']);
    $router->delete('/settings/{id}', [SettingController::class, 'delete']);
});