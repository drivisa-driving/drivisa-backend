<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Traits\AvailabilityTrait;

class InstructorAvailabilityTransformer extends JsonResource
{
    use AvailabilityTrait;

    private $duration = null;
    private $point = null;

    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    public function setPoint($point)
    {
        $this->point = $point;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'no' => $this->no,
            'id' => $this->id,
            'avatar' => $this->resource->user->present()->gravatar(),
            'cover' => $this->resource->user->present()->cover(),
            'username' => $this->resource->user->username,
            'fullName' => $this->resource->user->present()->fullname(),
            'bio' => $this->resource->bio,
            'evaluation' => [
                'avg' => $this->resource->lessons()->avg('trainee_evaluation->value'),
                'count' => $this->resource->lessons()->whereNotNull('trainee_evaluation')->count(),
            ],
            'point' => new PointTransformer($this->point),
            'cars' => CarTransformer::collection($this->cars),
            'availability' => $this->availabilityData($this->point),
        ];
    }
}
