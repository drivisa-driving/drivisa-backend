<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\LessonCancellation;
use Modules\Drivisa\Entities\WorkingHour;

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

        $cancel_by = null;
        if ($lesson && $lesson->status == Lesson::STATUS['canceled']) {
            $cancel_by = LessonCancellation::where('lesson_id', $lesson->id)->pluck('cancel_by')->first();
        }

        return [
            'id' => $this->id,
            'status' => $this->status,
            'status_text' => array_search($this->status, WorkingHour::STATUS),
            'openAt' => $this->open_at,
            'closeAt' => $this->close_at,
            'duration' => $this->getDuration(),
            'openAt_formatted' => Carbon::parse($this->open_at)->format('h:i a'),
            'closeAt_formatted' => Carbon::parse($this->close_at)->format('h:i a'),
            'point' => new PointTransformer($this->point),
            'createdAt' => $this->created_at,
            'trainee' => $lesson ? new TraineeTransformer($lesson->trainee) : '',
            'lesson_cost' => $lesson ? $lesson->cost : '',
            'lesson_status' => $lesson ? $lesson->status : '',
            'lesson_status_text' => $lesson ? array_search($lesson->status, Lesson::STATUS) : '',
            'lesson_type' => $lesson ? ucwords(
                str_replace('_', " ", array_search($lesson->lesson_type, Lesson::TYPE))
            ) : '',
            'cancel_by' => $cancel_by ? $cancel_by : ''
        ];
    }

    public function getLesson($id)
    {
        return Lesson::where('working_hour_id', $id)->first();
    }

    private function getDuration()
    {
        $date = $this->workingDay->date;
        $start_at = Carbon::parse($date . $this->open_at);
        $end_at = Carbon::parse($date . $this->close_at);
        return $start_at->diffInHours($end_at);
    }
}
