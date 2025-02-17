<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Sentinel\User;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Notifications\SendMessageToUserNotification;

class NotificationController extends ApiBaseController
{

    private $traineeRepository;
    private $instructorRepository;

    /**
     * @param TraineeRepository $traineeRepository
     * @param InstructorRepository $instructorRepository
     */

    public function __construct(TraineeRepository $traineeRepository, InstructorRepository $instructorRepository)
    {
        $this->traineeRepository = $traineeRepository;
        $this->instructorRepository = $instructorRepository;
    }

    public function getInstructors(Request $request)
    {
        $authUser = $this->getUserFromRequest($request);
        if (!$authUser) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        try {
            $instructors = $this->instructorRepository->all();
            return response()->json(['data' => $instructors], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getTrainees(Request $request)
    {
        $authUser = $this->getUserFromRequest($request);
        if (!$authUser) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }
        try {
            $trainees = $this->traineeRepository->all();
            return response()->json(['data' => $trainees], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }


    public function sendNotification(Request $request)
    {
        $request->validate([
            'message' => 'required',
        ]);

        DB::beginTransaction();
        $authUser = $this->getUserFromRequest($request);
        if (!$authUser) {
            $message = trans('user::users.messages.user_not_found');
            return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
        }

        try {
            $id = array_merge($request->selectedInstructors, $request->selectedTrainees);

            $message = $request->message;
            $users = User::whereIn('id', $id)->get();

            foreach ($users as $user) {
                $user->notify(new SendMessageToUserNotification($message));
            }
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.notification_sent_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorMessage(trans('drivisa::drivisa.messages.can\'t_send_notification'));
        }
    }
}
