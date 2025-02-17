<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'v1/drivisa/admin'], function (Router $router) {
    $router->group(['middleware' => ['is-admin']], function (Router $router) {


        // dashboard report

        $router->group(['prefix' => 'stats'], function (Router $router) {
            $router->get('/dashboard', ['uses' => 'admin\DashboardController@index']);
        });

        // Sales report

        $router->group(['prefix' => 'sales-report'], function (Router $router) {
            $router->get('/all', ['uses' => 'admin\SalesReportController@index']);
            $router->get('/yearly', ['uses' => 'admin\SalesReportController@getYearlySalesReport']);
            $router->get('/revenue', ['uses' => 'admin\SalesReportController@getRevenue']);
        });

        $router->group(['prefix' => 'report'], function (Router $router) {
            $router->get('/', ['uses' => 'admin\SalesReportController@report']);
        });

        // BDE course Data

        $router->group(['prefix' => 'bde'], function (Router $router) {
            $router->get('/list', ['uses' => 'admin\CourseAdminController@bdeList']);
            $router->get('/bde-log/{id}', ['uses' => 'BDELogController@details']);
            $router->post('/{trainee}/licence-issued', ['uses' => 'admin\CourseAdminController@licenceIssued']);
            $router->get('get-marking-keys', ['uses' => 'admin\CourseAdminController@getMarkingKeys']);
            $router->post('add-marking-keys', ['uses' => 'admin\CourseAdminController@addMarkingKeys']);
            $router->post('add-final-test-keys/{username}', ['uses' => 'admin\CourseAdminController@addOrUpdateFinalTestLog']);
            $router->post('add-trainee-sign/{username}', ['uses' => 'admin\TraineeAdminController@addTraineeSignature']);
            $router->get('empty-sign-logs/{username}', ['uses' => 'admin\TraineeAdminController@getEmptySignBdeLogs']);
        });


        // history admin endpoints
        $router->group(['prefix' => 'history'], function (Router $router) {
            $router->get('/transactions', ['uses' => 'admin\TransactionAdminController@index']);
            $router->get('/refund', ['uses' => 'admin\TransactionAdminController@refund']);
        });

        // admin packages api endpoints
        require_once 'apiPackageRoutes.php';

        // block user

        $router->post('/block-user', ['uses' => 'admin\AdminController@blockUser']);
        $router->post('/unblock-user', ['uses' => 'admin\AdminController@unblockUser']);


        // admin profile endpoint

        $router->get("/get-profile-info", ['uses' => 'admin\AdminController@getProfile']);
        $router->post("/update-profile", ['uses' => 'admin\AdminController@updateAdminProfile']);


        //admin curd endpoints

        $router->get("/admins", ['uses' => 'admin\AdminController@allAdmin']);
        $router->post('/create-admin', ['uses' => 'admin\AdminController@createAdmin']);
        $router->put('/update-admin', ['uses' => 'admin\AdminController@updateAdmin']);

        $router->group(['middleware' => ['is-super-admin']], function (Router $router) {
            $router->delete('/{id}/delete', ['uses' => 'admin\AdminController@deleteAdmin']);
        });


        // instructor admin endpoint

        $router->group(['prefix' => 'instructors'], function (Router $router) {
            $router->group(['prefix' => '{instructor}/cars'], function (Router $router) {
                $router->get('', ['uses' => 'admin\InstructorCarAdminController@index']);
            });
            $router->group(['prefix' => '{instructor}/documents'], function (Router $router) {
                $router->get('', ['uses' => 'admin\InstructorDocumentAdminController@index']);
            });
            $router->group(['prefix' => '{instructor}/points'], function (Router $router) {
                $router->get('', ['uses' => 'admin\InstructorPointAdminController@index']);
            });

            $router->post('{instructor}/{id}/change-status', [
                'uses' => 'admin\InstructorDocumentAdminController@changeDocumentStatus'
            ]);

            $router->get('{instructor}/transactions', [
                'uses' => 'admin\TransactionAdminController@getInstructorTransactions'
            ]);

            $router->group(['prefix' => 'finance'], function (Router $router) {
                $router->group(['prefix' => 'earnings'], function (Router $router) {
                    $router->get('breakdown-weekly/{id}', ['uses' => 'admin\InstructorAdminEarningController@getEarningBreakDown']);
                    $router->get('weekly/{id}', ['uses' => 'admin\InstructorAdminEarningController@getWeeklyEarnings']);
                    $router->get('{id}', ['uses' => 'admin\InstructorAdminEarningController@getEarnings']);
                });
            });

            $router->get('', ['uses' => 'admin\InstructorAdminController@index']);
            $router->get('all', ['uses' => 'admin\InstructorAdminController@all']);
            $router->get('details/{id}', ['uses' => 'admin\InstructorAdminController@details']);
            $router->get('{instructor}', ['uses' => 'admin\InstructorAdminController@show']);
            $router->post('{instructor}/verify', ['uses' => 'admin\InstructorAdminController@verifyOrRejectInstructor',]);
            $router->get('instructor-schedules/{instructor_id}', ['uses' => 'admin\InstructorAdminController@instructorSchedules']);
            $router->get('instructor-schedules-new/{instructor_id}', ['uses' => 'admin\InstructorAdminController@instructorSchedulesNew']);
            $router->get('instructor-points/{instructor_id}', ['uses' => 'admin\InstructorAdminController@getPoints']);
            $router->get('completed-lessons/{id}', ['uses' => 'admin\InstructorAdminController@completedLessons']);


            $router->group(['prefix' => 'lessons'], function (Router $router) {
                $router->get('today-upcoming/{instructor}', ['uses' => 'admin\InstructorAdminController@getInstructorLessonsList']);
                $router->get('history/{instructor}', ['uses' => 'admin\InstructorAdminController@getInstructorLessonsHistory']);
                $router->get('completed/{instructor}', ['uses' => 'admin\InstructorAdminController@getInstructorCompletedLessons']);
                $router->post('{id}/end-lesson', ['uses' => 'admin\InstructorAdminController@endLesson']);
                $router->post('{lesson}/initiate-refund', ['uses' => 'admin\TraineeAdminController@initiateRefundWhenLessonExpired']);
            });

            $router->group(['prefix' => 'bde'], function (Router $router) {
                $router->get('{lesson}/marking-keys', ['uses' => 'admin\InstructorAdminController@getMarkingKeys']);
                $router->post('{lesson}/end-bde-lesson', ['uses' => 'admin\InstructorAdminController@endBdeLessonByAdmin']);
            });

            $router->group(['prefix' => 'car-rentals/{instructor}'], function (Router $router) {
                $router->get('available-requests', ['uses' => 'admin\InstructorAdminController@getAvailableRequestForInstructor']);
                $router->get('accepted-requests', ['uses' => 'admin\InstructorAdminController@getAcceptedRequestForInstructor']);
            });
        });

        // trainee admin endpoint

        $router->group(['prefix' => 'trainees'], function (Router $router) {
            $router->group(['prefix' => '{trainee}/documents'], function (Router $router) {
                $router->get('', ['uses' => 'admin\TraineeDocumentAdminController@index']);
            });

            $router->group(['prefix' => 'credit'], function (Router $router) {
                $router->post('add', ['uses' => 'admin\TraineeAdminController@addCredit']);
                $router->post('reduce', ['uses' => 'admin\TraineeAdminController@reduceCredit']);
                $router->post('bonus', ['uses' => 'admin\TraineeAdminController@addBonusCredit']);
            });

            $router->get('all', ['uses' => 'admin\TraineeAdminController@all']);
            $router->get('{trainee}', ['uses' => 'admin\TraineeAdminController@show']);
            $router->get('', ['uses' => 'admin\TraineeAdminController@index']);
            $router->post('{trainee}/{id}/change-status', [
                'uses' => 'admin\TraineeAdminController@changeDocumentStatus'
            ]);

            $router->get('{trainee}/transactions', [
                'uses' => 'admin\TransactionAdminController@getTraineeTransactions'
            ]);

            $router->get('details/{id}', ['uses' => 'admin\TraineeAdminController@details']);
            $router->post('{trainee}/verify', ['uses' => 'admin\TraineeAdminController@verifyOrRejectTrainee',]);
            $router->get('purchases/{trainee}', [
                'uses' => 'admin\TraineeAdminController@getTraineePurchases'
            ]);
            $router->get('courses/{trainee}', [
                'uses' => 'admin\TraineeAdminController@getTraineeCourses'
            ]);
            $router->get('refunds/{trainee}', [
                'uses' => 'admin\TraineeAdminController@getTraineeRefunds'
            ]);


            $router->group(['prefix' => 'lessons'], function (Router $router) {
                $router->get('today-upcoming/{trainee}', ['uses' => 'admin\TraineeAdminController@getTraineeLessonsList']);
                $router->get('history/{trainee}', ['uses' => 'admin\TraineeAdminController@getTraineeLessonsHistory']);
                $router->get('completed/{trainee}', ['uses' => 'admin\TraineeAdminController@getTraineeCompletedLessons']);
            });

            $router->group(['prefix' => 'car-rental/{trainee}'], function (Router $router) {
                $router->get('all-requests', ['uses' => 'admin\TraineeAdminController@getAllRentalRequestsForTrainee']);
            });

            $router->group(['prefix' => 'unpurchased-trainees'], function (Router $router) {
                $router->get('get', ['uses' => 'admin\TraineeAdminController@getUnpurchasedTrainees']);
                $router->post('notify', ['uses' => 'admin\TraineeAdminController@notifyUnpurchasedTrainees']);
            });
        });

        // lessons endpoints
        $router->group(['prefix' => 'lessons'], function (Router $router) {
            $router->get('today-lessons', ['uses' => 'LessonController@todayLessonsList']);
            $router->post('create-lesson', ['uses' => 'LessonController@createLessonByAdmin']);
            $router->post('create-transfer/{lesson}/{instructor}', ['uses' => 'LessonController@transferToInstructor']);
            $router->get('created-by-admin', ['uses' => 'LessonController@getLessonsListCreatedByAdmin']);
        });

        //course admin endpoints

        $router->group(['prefix' => 'courses'], function (Router $router) {
            $router->get('', ['uses' => 'admin\CourseAdminController@index']);
            $router->get('{course}', ['uses' => 'admin\CourseAdminController@show']);
            $router->post('', ['uses' => 'admin\CourseAdminController@store']);
            $router->post('{course}', ['uses' => 'admin\CourseAdminController@update']);
            $router->delete('{course}', ['uses' => 'admin\CourseAdminController@delete']);
        });

        $router->group(['prefix' => 'notification'], function (Router $router) {
            $router->get('instructors', ['uses' => 'admin\NotificationController@getInstructors']);
            $router->get('trainees', ['uses' => 'admin\NotificationController@getTrainees']);
            $router->post('', ['uses' => 'admin\NotificationController@sendNotification']);
        });

        $router->group(['prefix' => 'discounts'], function (Router $router) {
            $router->get('', ['uses' => 'DiscountController@getDiscounts']);
            $router->get('discountUsers', ['uses' => 'DiscountController@discountUsers']);
            $router->put('storeDiscount', ['uses' => 'DiscountController@storeDiscount']);
            $router->delete('{id}', ['uses' => 'DiscountController@deleteDiscount']);
        });

        // Road Test Training Location crud
        $router->group(['prefix' => 'training-location'], function (Router $router) {
            $router->post('', ['uses' => 'admin\TrainingLocationController@addLocation']);
            $router->put('/{id}', ['uses' => 'admin\TrainingLocationController@updateLocation']);
            $router->delete('/{id}', ['uses' => 'admin\TrainingLocationController@deleteLocation']);
        });

        $router->get('export-database', ['uses' => 'admin\DatabaseDumpController@exportDatabase']);
        $router->post('clear-telescope-entries', ['uses' => 'admin\TelescopeController@clearTelescopeEntries']);

        $router->group(['prefix' => 'logs'], function (Router $router) {
            $router->get('notifications', ['uses' => 'LogController@getNotificationLogs']);
        });
    });
});
