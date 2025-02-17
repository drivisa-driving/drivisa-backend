<?php

use Illuminate\Routing\Router;

/** @var Router $router */


$router->group(['prefix' => 'v1/drivisa'], function (Router $router) {
    $router->group(['middleware' => ['api.token', 'blocked', 'deleted']], function (Router $router) {
        //instructor routes

        require_once 'instructors.php';

        //End Of instructor routes

        // trainee routes

        require_once 'trainee.php';


        $router->group(['prefix' => 'workingHours'], function (Router $router) {
            $router->get('{workingHour}', ['uses' => 'WorkingHourController@getWorkingHour']);
        });
        $router->group(['prefix' => 'courses'], function (Router $router) {
            $router->post('{course}/subscription', ['uses' => 'CourseController@subscription']);
        });

        // Complaint Routes
        $router->group(['prefix' => 'complaint'], function (Router $router) {
            $router->post('', ['uses' => 'ComplaintController@addComplaint']);
            $router->get('', ['uses' => 'ComplaintController@getComplaints']);
            $router->get('/user', ['uses' => 'ComplaintController@getComplaintByUser']);
        });

        $router->post('complaint-reply/{id}', ['uses' => 'ComplaintReplyController@addComplaintReply']);

        // BDE Log
        $router->get('bde/trainee/{username}', ['uses' => 'BDELogController@traineeBDEDetails']);

        // Get All Road Test Training Locations Created By Admin 
        $router->group(['prefix' => 'training-location'], function (Router $router) {
            $router->get("", ['uses' => 'admin\TrainingLocationController@allTrainingLocations']);
        });

        // Get Recent Chat Messages of Trainee and Instructor
        $router->group(['prefix' => 'messages'], function (Router $router) {
            $router->get('recent', ['uses' => 'RecentMessageController@getRecentMessages']);
            $router->get('read/{lesson}', ['uses' => 'RecentMessageController@readMessages']);
            $router->get('unread', ['uses' => 'RecentMessageController@getUnreadMessages']);
        });
    });


    // non auth routes
    require_once 'non-auth-apiRoutes.php';
});

// admin routes

require_once 'admin.php';


//======= v2 routes ======//

$router->group(['prefix' => 'v2/drivisa'], function (Router $router) {
    $router->group(['middleware' => ['api.token', 'blocked', 'deleted']], function (Router $router) {

        //v2 instructor routes
        require_once 'routes/V2/instructors.php';
    });
});
