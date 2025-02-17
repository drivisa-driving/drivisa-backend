<?php

namespace Modules\Drivisa\Transformers;

use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Instructor;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\Pusher\Entities\Message;

class RecentMessageTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {

        $lesson = Lesson::find($this['last_message']->lesson_id);
        $lesson_type = str_replace('_', " ", array_search($lesson->lesson_type, Lesson::TYPE));
        $date = Carbon::parse($lesson->start_at)->format('D, F d');
        $time = Carbon::parse($lesson->start_at)->format('h:i A') . " to " . Carbon::parse($lesson->end_at)->format('h:i A');
        $message_by = $this['last_message']->message_by;
        $userType = $this['user_type'];

        return [
            'message_by' => array_search($message_by, Message::MESSAGE_BY),
            'message' => $this['last_message']->message,
            'created_at' => Carbon::parse($this['last_message']->created_at)->format('F d, Y h:i A'),
            'unread_message_count' => $this['unread_message_count'],
            'lesson' => [
                'lesson_id' => $lesson->id,
                'lesson_no' => $lesson->no,
                'lesson_type' => ucwords($lesson_type),
                'date' => $date,
                'time' => $time
            ],
            'user' => $this->getUserDetails($userType, $lesson, $message_by)
        ];
    }

    private function getUserDetails($userType, $lesson, $message_by)
    {
        $trainee = Trainee::find($lesson->trainee_id);
        $instructor = Instructor::find($lesson->instructor_id);

        $details = [];

        if ($userType == 1) {
            $details['fullname'] = $trainee->user->present()->fullname();
            $details['avatar'] = $trainee->user->present()->gravatar();
        } else if ($userType == 2) {
            $details['fullname'] = $instructor->user->present()->fullname();
            $details['avatar'] = $instructor->user->present()->gravatar();
        }

        if ($userType == 1 && $message_by == 1) {
            $details['last_sent_by'] = $trainee->user->present()->fullname();
        } else if ($userType == 2 && $message_by == 2) {
            $details['last_sent_by'] = $instructor->user->present()->fullname();
        } else {
            $details['last_sent_by'] = "You";
        }

        return $details;
    }
}
