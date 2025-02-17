<?php

use Illuminate\Routing\Router;
use Modules\Drivisa\Http\Controllers\NotificationController;
use Modules\User\Http\Controllers\Api\ReferralCodeController;

/** @var Router $router */
$router->group(['prefix' => 'v1/user', 'middleware' => ['api.token', 'blocked', 'deleted']], function (Router $router) {


    $router->group(['prefix' => '/referral-code'], function (Router $router) {
        $router->post('/code-sent', [ReferralCodeController::class, 'codeSent']);
    });


    $router->group(['prefix' => 'notifications'], function (Router $router) {
        $router->get('all', [NotificationController::class, 'getAllNotifications']);
        $router->get('unread-notifications', [NotificationController::class, 'getUnreadNotifications']);
        $router->get('checked-notifications', [NotificationController::class, 'getReadNotifications']);
        $router->post('read-single/{id}', [NotificationController::class, 'readNotification']);
        $router->post('read-all', [NotificationController::class, 'readAllNotification']);
    });

    $router->group(['prefix' => 'roles'], function (Router $router) {
        $router->get('/', [
            'uses' => 'RoleController@index',
            'middleware' => 'token-can:user.roles.list',
        ]);
        $router->post('/', [
            'uses' => 'RoleController@store',
            'middleware' => 'token-can:user.roles.create',
        ]);
        $router->post('find/{role}', [
            'uses' => 'RoleController@find',
            'middleware' => 'token-can:user.roles.edit',
        ]);
        $router->post('{role}/edit', [
            'uses' => 'RoleController@update',
            'middleware' => 'token-can:user.roles.edit',
        ]);
        $router->delete('{role}', [
            'as' => 'api.user.role.destroy',
            'uses' => 'RoleController@destroy',
            'middleware' => 'token-can:user.roles.destroy',
        ]);
    });

    $router->group(['prefix' => 'users'], function (Router $router) {
        $router->get('/', [
            'uses' => 'UserController@index',
            'middleware' => 'token-can:user.users.list',
        ]);
        $router->post('/', [
            'uses' => 'UserController@store',
            'middleware' => 'token-can:user.users.create',
        ]);
        $router->post('find/{user}', [
            'uses' => 'UserController@find',
            'middleware' => 'token-can:user.users.edit',
        ]);
        $router->post('{user}/edit', [
            'uses' => 'UserController@update',
            'middleware' => 'token-can:user.users.edit',
        ]);
        $router->get('{user}/sendResetPassword', [
            'uses' => 'UserController@sendResetPassword',
            'middleware' => 'token-can:user.users.edit',
        ]);
        $router->delete('{user}', [
            'as' => 'api.user.user.destroy',
            'uses' => 'UserController@destroy',
            'middleware' => 'token-can:user.users.destroy',
        ]);
    });

    $router->get('permissions', [
        'uses' => 'PermissionsController@index',
        'middleware' => 'token-can:user.roles.list',
    ]);

    $router->post('change-password', [
        'uses' => 'UserController@changePassword'
    ]);
    $router->post('update-user-profile-picture', [
        'uses' => 'UserController@updateUserProfilePicture'
    ]);
    $router->post('delete-user-profile-picture', [
        'uses' => 'UserController@deleteUserProfilePicture'
    ]);

    $router->delete('delete-account/{username}', [
        'uses' => 'UserController@deleteAccount'
    ]);

    // Admin can change password of the users (trainee/instructor).
    $router->post('change-user-password/{user}', [
        'uses' => 'UserController@changeUserPassword'
    ]);
});
$router->group(['prefix' => 'v1/auth'], function (Router $router) {
    $router->group(['middleware' => ['api.token', 'blocked']], function (Router $router) {
        $router->get('login-history', ['uses' => 'AuthController@loginHistory']);
    });
    $router->get('logout', ['uses' => 'AuthController@logout']);
    $router->get('me', ['uses' => 'AuthController@me']);
    # Login
    $router->post('login', ['uses' => 'AuthController@postLogin']);
    $router->put('addDeviceToken', ['uses' => 'AuthController@addDeviceToken']);
    # Register
    $router->post('register', ['uses' => 'AuthController@postRegister']);

    $router->post('activate', ['uses' => 'AuthController@completeActivation']);
    # Reset password
    $router->post('reset-password', 'AuthController@resetPassword');

    $router->post('complete-reset-password', 'AuthController@completeResetPassword');

    # Resend activation code
    $router->post("resend-activation-code", ['uses' => 'AuthController@resendActivationCode']);
    $router->post("refresh-token", ['uses' => 'AuthController@refreshToken']);

    #verify password
    $router->post('verify-password', ['uses' => 'AuthController@verifyPassword']);
});
