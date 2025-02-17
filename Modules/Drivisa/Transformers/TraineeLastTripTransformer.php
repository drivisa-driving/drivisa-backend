<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TraineeLastTripTransformer extends JsonResource
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
            'lesson_id' => $this->id,
            'instructor_name' => $this->instructor->first_name . " " . $this->instructor->last_name,
            'avatar' => $this->instructor->user->present()->gravatar()
        ];
    }
}
