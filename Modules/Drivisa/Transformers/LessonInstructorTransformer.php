<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'trainee' => new TraineeTransformer($this->trainee),

        ];
    }
}
