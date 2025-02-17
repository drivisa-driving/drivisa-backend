<?php

namespace Modules\Drivisa\Services;

use Modules\Pusher\Repositories\MessageRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Entities\Lesson;

class RecentMessageService
{
    public function __construct(
        public InstructorRepository $instructor,
        public TraineeRepository $trainee,
        public MessageRepository $messageRepository,
        public LessonRepository $lessonRepository
    ) {
        $this->instructor = $instructor;
        $this->trainee = $trainee;
        $this->messageRepository = $messageRepository;
        $this->lessonRepository = $lessonRepository;
    }


    public function getRecentMessages($user)
    {
        $lessons = $this->getUserLessons($user);
        $messages = [];

        foreach ($lessons as $lesson) {

            // find last message
            $lastMessage = $this->messageRepository
                ->where('lesson_id', $lesson->id)
                ->latest('created_at')
                ->first();

            if ($lastMessage) {
                $messages[] = [
                    'last_message' => $lastMessage,
                    'unread_message_count' => $this->getUnreadMessagesCount($lesson, $user->user_type),
                    'user_type' => $user->user_type,
                ];
            }
        }

        return $messages;
    }


    public function readMessages($user, $lesson)
    {
        $messages = $this->messageRepository
            ->where('lesson_id', $lesson->id)
            ->where('message_by', $user->user_type)
            ->whereNull('read_at')
            ->get();

        foreach ($messages as $message) {
            $message->read_at = now();
            $message->save();
        }
    }

    public function getUnreadMessages($user)
    {
        $totalUnreadMessages = 0;
        $lessons = $this->getUserLessons($user);

        foreach ($lessons as $lesson) {
            $unreadMessageCount = $this->getUnreadMessagesCount($lesson, $user->user_type);
            $totalUnreadMessages += $unreadMessageCount;
        }

        return $totalUnreadMessages;
    }

    private function getUserLessons($user)
    {
        if ($user->user_type == 1) {
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);

            return $this->lessonRepository->where('status', Lesson::STATUS['reserved'])->where('instructor_id', $instructor->id)->whereDate('start_at', '>=', today())->get();
        } else if ($user->user_type == 2) {
            $trainee = $this->trainee->findByAttributes(['user_id' => $user->id]);

            return $this->lessonRepository->where('status', Lesson::STATUS['reserved'])->where('trainee_id', $trainee->id)->whereDate('start_at', '>=', today())->get();
        }
    }

    private function getUnreadMessagesCount($lesson, $user_type)
    {
        return $this->messageRepository
            ->where('lesson_id', $lesson->id)
            ->where('message_by', $user_type)
            ->whereNull('read_at')
            ->count();
    }
}
