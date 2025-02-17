<?php

namespace Modules\Drivisa\Transformers\WebSchedules;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\LessonCancellation;
use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Transformers\PointTransformer;

class WorkingHourNewTransformer extends JsonResource
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
            'status' => $this->status,
            'point' => new PointTransformer(Point::withTrashed()->find($this->point_id)),
        ];
    }


}