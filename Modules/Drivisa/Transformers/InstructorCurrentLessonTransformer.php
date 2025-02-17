<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class InstructorCurrentLessonTransformer extends JsonResource
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
            'trainee_username' => $this->trainee->user->username
        ];
    }
}
