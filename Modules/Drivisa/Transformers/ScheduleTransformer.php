<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $hours = WorkingHourTransformer::collection($this->allWorkingHours()->get())->toResponse(app('request'));
        $res = $hours->getData()->data;
        $result =  collect($res)->filter(function ($schedule) {
            if($schedule->status > 1){
                return true;
            }
            if(isset($schedule->point)) {
                return $schedule?->point->deletedAt === null || $schedule?->point->deletedAt ==='';
            }
            return false;
        })->values();
        return [
            'id' => $this->id,
            'instructorId' => $this->instructor_id,
            'date' => $this->date,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'workingHours' => $result,
            'lessons' => LessonInstructorTransformer::collection($this->instructor->lessons()->whereDate('start_at', $this->date)->get()),
        ];
    }
}
