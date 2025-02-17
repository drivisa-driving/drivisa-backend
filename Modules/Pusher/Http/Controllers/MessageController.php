<?php

namespace Modules\Pusher\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\RentalRequest;
use Modules\Pusher\Entities\Message;
use Modules\Pusher\Repositories\MessageRepository;
use Modules\Pusher\Services\MessageService;
use Modules\Pusher\Transformers\MessageTransformer;

class MessageController extends ApiBaseController
{
    public function __construct(
        public MessageRepository $messageRepository,
        public MessageService    $messageService
    )
    {

    }

    public function store(Request $request)
    {
        try {
            $this->messageService->storeMessage($request->all());

            return $this->successMessage("Message Stored");
        } catch (\Exception $e) {
            dd($e);
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getLessonById(Lesson $lesson)
    {
        try {
            $messages = $this->messageRepository
                ->where('lesson_id', $lesson->id)
                ->latest()
                ->get();

            return MessageTransformer::collection($messages);
        } catch (\Exception $e) {
            dd($e);
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getRequestById(RentalRequest $rentalRequest)
    {
        try {
            $messages = $this->messageRepository
                ->where('request_id', $rentalRequest->id)
                ->latest()
                ->get();

            return MessageTransformer::collection($messages);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
