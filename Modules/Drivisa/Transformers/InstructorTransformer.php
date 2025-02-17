<?php

namespace Modules\Drivisa\Transformers;

use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\WorkingHour;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorTransformer extends JsonResource
{
    public $trainee;

    public function __construct($resource, $trainee = null)
    {
        parent::__construct($resource);
        $this->trainee = $trainee;
    }



    public function toArray($request)
    {
        return [
            'no' => $this->resource->no,
            'id' => $this->resource->id,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'bio' => $this->resource->bio,
            'kycVerification' => array_search($this->resource->kyc_verification, Instructor::KYC),
            'cars' => CarTransformer::collection($this->resource->cars),
            'point' => new PointTransformer($this->getPoint($this)),
            $this->mergeWhen($this->resource->user->exists(), function () {
                return [
                    'username' => $this->resource->user->username,
                    'fullName' => $this->resource->user->present()->fullname(),
                    'avatar' => $this->resource->user->present()->gravatar(),
                    'cover' => $this->resource->user->present()->cover(),
                    'email' => $this->resource->user->email,
                ];
            })
        ];
    }

    private function getPoint($instructor)
    {
        $lesson = $this->trainee?->lessons()->whereNotNull('ended_at')->where('instructor_id', $instructor->id)->latest('ended_at')->first();

        if ($lesson) {
            $workingHour = WorkingHour::find($lesson->working_hour_id);
            if ($workingHour) {
                $point= Point::find($workingHour->point_id);
                if($point) {
                    return $point;
                }
            }
        }

        return $instructor->points()->where('is_active', true)->first();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(null), $options);
    }
}
