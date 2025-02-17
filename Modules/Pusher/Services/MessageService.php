<?php

namespace Modules\Pusher\Services;

use Modules\Drivisa\Entities\RentalRequest;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Pusher\Entities\Message;
use Modules\Pusher\Notifications\ChatNotificationToUser;
use Modules\Pusher\Notifications\RentalRequestChatNotificationToUser;
use Modules\Pusher\Repositories\MessageRepository;

class MessageService
{
    public function __construct(
        public MessageRepository    $messageRepository,
        public LessonRepository     $lessonRepository,
        public TraineeRepository    $trainee,
        public InstructorRepository $instructor
    )
    {
    }

    private function parseData($data)
    {
        if (isset($data['messages']) && count($data['messages'])) {
            return json_decode($data['messages'][0]['data']);
        } else {
            return null;
        }
    }

    public function storeMessageOld($data)
    {
        if ($data = $this->parseData($data)) {

            $lesson = $this->lessonRepository->find($data->lesson_id);
            $instructor = $lesson->instructor;
            $trainee = $lesson->trainee;

            $this->messageRepository->create([
                'lesson_id' => $lesson->id,
                'trainee_id' => $trainee->id,
                'instructor_id' => $instructor->id,
                'message' => $data->text,
                'message_by' => Message::MESSAGE_BY[$data->message_by],
            ]);

            $instructorUser = $instructor->user;
            $traineeUser = $trainee->user;


            if ($data->message_by == 'trainee') {
                $instructorUser->notify(new ChatNotificationToUser($data, $lesson, $traineeUser));
            }

            if ($data->message_by == 'instructor') {
                $traineeUser->notify(new ChatNotificationToUser($data, $lesson, $instructorUser));
            }
        }

    }

    public function storeMessage($data)
    {
        if ($data = $this->parseData($data)) {

            $lesson = $this->getLesson($data);
            if($lesson) {
                $instructor = $lesson->instructor;
                $trainee = $lesson->trainee;

                $this->store($data, $instructor, $trainee);

                $instructorUser = $instructor->user;
                $traineeUser = $trainee->user;

                if ($data->message_by == 'trainee') {
                    if(isset($data->request_id) && $data->request_id) {
                        $instructorUser->notify(new RentalRequestChatNotificationToUser($data, $lesson, $traineeUser));
                    } else if(isset($data->lesson_id) && $data->lesson_id){
                        $instructorUser->notify(new ChatNotificationToUser($data, $lesson, $traineeUser));
                    }
                }

                if ($data->message_by == 'instructor') {
                    if(isset($data->request_id) && $data->request_id) {
                        $traineeUser->notify(new RentalRequestChatNotificationToUser($data, $lesson, $instructorUser));
                    } else if(isset($data->lesson_id) && $data->lesson_id){
                        $traineeUser->notify(new ChatNotificationToUser($data, $lesson, $instructorUser));
                    }
                }
            }
        }
    }

    private function getLesson($data)
    {
        if (isset($data->request_id) && $data->request_id) {
            return RentalRequest::find($data->request_id);
        } else if(isset($data->lesson_id) && $data->lesson_id) {
            return $this->lessonRepository->find($data->lesson_id);
        }

        return null;
    }

    private function store($data, $instructor, $trainee)
    {
        $messageData = [
            'trainee_id' => $trainee->id,
            'instructor_id' => $instructor->id,
            'message' => $data->text,
            'message_by' => Message::MESSAGE_BY[$data->message_by],
        ];

        if (isset($data->request_id) && $data->request_id) {
            $messageData['request_id'] = $data->request_id;
        } else if(isset($data->lesson_id) && $data->lesson_id){
            $messageData['lesson_id'] = $data->lesson_id;
        }

        $this->messageRepository->create($messageData);
    }
}