<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonTraineeTransformer extends JsonResource
{


    public function toArray($request)
    {
        return [
            $this->merge(new LessonTransformer($this->resource)),'instructor' => new InstructorProfileTransformer($this->instructor),
        ];
    }
}
