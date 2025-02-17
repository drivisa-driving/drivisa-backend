<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class InstructorCommentTransformer extends JsonResource
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
            'name' => $this->trainee->first_name . " " . $this->trainee->last_name,
            'rating' => $this->trainee_evaluation ? json_decode($this->trainee_evaluation)->value : null,
            'note' => $this->trainee_note,
            'profilePhoto' => $this->trainee->user->present()->gravatar(),
            'date' => $this->updated_at,
            'formattedDate' => $this->updated_at->format('M d, Y'),
            'trainee_id' => $this->trainee->id
        ];
    }
}
