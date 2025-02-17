<?php

namespace Modules\Drivisa\Transformers\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\WorkingHour;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\LessonCancellation;
use Modules\Drivisa\Transformers\PointTransformer;
use Modules\Drivisa\Transformers\TraineeTransformer;

class WorkingHourTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $lesson = $this->getLesson($this->id);

        return [
            'id' => $this->id,
            'status' => $this->status,
            'status_text' => array_search($this->status, WorkingHour::STATUS),
            'openAt' => $this->open_at,
            'closeAt' => $this->close_at,
            'openAt_formatted' => Carbon::parse($this->open_at)->format('h:i a'),
            'closeAt_formatted' => Carbon::parse($this->close_at)->format('h:i a'),
            'duration' => $this->getDuration(),
            'createdAt' => $this->created_at,
            'point' => new PointTransformer($this->point),
            'trainee' => $lesson ? new TraineeTransformer($lesson->trainee) : '',
            'lesson_type' => $lesson ? ucwords(
                str_replace('_', " ", array_search($lesson->lesson_type, Lesson::TYPE))
            ) : '',
            'lesson_status' => $lesson ? $lesson->status : '',
            'lesson_status_text' => $lesson ? array_search($lesson->status, Lesson::STATUS) : '',
            'lesson_cost' => $lesson ? $lesson->cost : '',
            'cancel_by' => $lesson && $lesson->lessonCancellation ? $lesson->lessonCancellation->cancel_by : ''
        ];
    }

    private function getLesson($id)
    {
        $lessons = Lesson::where('working_hour_id', $id)->get();

        foreach ($lessons as $lesson) {

            if ($lesson) {

                if ($lesson->lessonCancellation && $lesson->lessonCancellation->cancel_by == 'instructor') return $lesson;

                if (in_array($lesson->status, [Lesson::STATUS['canceled'], Lesson::STATUS['rescheduled']])) continue;

                return $lesson;
            }
        }
    }

    private function getDuration()
    {
        $date = $this->workingDay->date;
        $start_at = Carbon::parse($date . $this->open_at);
        $end_at = Carbon::parse($date . $this->close_at);
        return $start_at->diffInHours($end_at);
    }
}
