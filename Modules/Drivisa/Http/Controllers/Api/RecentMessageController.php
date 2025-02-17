<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Modules\Drivisa\Transformers\RecentMessageTransformer;
use Modules\Drivisa\Services\RecentMessageService;
use Modules\Drivisa\Entities\Lesson;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Exception;

class RecentMessageController extends ApiBaseController
{

    public function __construct(
        public RecentMessageService $recentMessageService
    ) {
        $this->recentMessageService = $recentMessageService;
    }

    public function getRecentMessages(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $messages = $this->recentMessageService->getRecentMessages($user);
            return RecentMessageTransformer::collection($messages);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function readMessages(Request $request, Lesson $lesson)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $this->recentMessageService->readMessages($user, $lesson);
            DB::commit();
            return $this->successMessage(trans('drivisa::drivisa.messages.message_seen'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getUnreadMessages(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found', [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $unreadMessages = $this->recentMessageService->getUnreadMessages($user);
            return response()->json(['unread_messages' => $unreadMessages], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
