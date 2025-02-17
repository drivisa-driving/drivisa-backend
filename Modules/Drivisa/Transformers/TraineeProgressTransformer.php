<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TraineeProgressTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'instructor' => new InstructorProfileTransformer($this['instructor']),
            'progress' => $this->when($this['progress'], function () {
                return TraineeProgressStatsTransformer::collection($this['progress']);
            })
        ];
    }


}
