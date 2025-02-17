<?php

namespace Modules\Drivisa\Transformers\WebSchedules;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Transformers\WebSchedules\LessonTransformer;

class LessonInstructorTransformer extends JsonResource
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
            $this->merge(new LessonTransformer($this->resource)),

        ];
    }
}
