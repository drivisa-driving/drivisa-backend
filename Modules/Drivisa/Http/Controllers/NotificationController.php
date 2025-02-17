<?php

namespace Modules\Drivisa\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Transformers\CarTransformer;
use Modules\User\Transformers\NotificationTransformer;

class NotificationController extends ApiBaseController
{
    public function getAllNotifications(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            return NotificationTransformer::collection($authUser->notifications);

        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getUnreadNotifications(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            return NotificationTransformer::collection($authUser->unreadNotifications);

        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getReadNotifications(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return NotificationTransformer::collection($authUser->readNotifications);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function readNotification(Request $request, $id)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            if ($authUser->unreadNotifications()->where('id', $id)->update(['read_at' => now()])) {
                return $this->successMessage('messages status to read', Response::HTTP_OK);
            } else {
                return $this->successMessage('not message to read', Response::HTTP_OK);
            }


        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function readAllNotification(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $authUser->unreadNotifications->markAsRead();

            return $this->errorMessage('All messages read.', Response::HTTP_OK);

        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
