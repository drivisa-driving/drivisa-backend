<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkingDayForTraineeTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'instructorId' => $this->instructor_id,
            'date' => $this->date,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'workingHours' => WorkingHourTransformer::collection($this->workingHoursWithActivePoint),
        ];
    }
}
