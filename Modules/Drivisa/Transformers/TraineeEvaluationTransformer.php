<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;

class TraineeEvaluationTransformer extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->evaluationIndicator = app(EvaluationIndicatorRepository::class);
    }

    public function toArray($request)
    {
        $lessons = $this->resource->lessons()->whereNotNull('ended_at')->get();
        return [
            'no' => $this->resource->no,
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'bio' => $this->resource->bio,
            'fullName' => $this->resource->user->present()->fullname(),
            'avatar' => $this->resource->user->present()->gravatar(),
            'cover' => $this->resource->user->present()->cover(),
            'evaluations' => $this->getEvaluations($lessons),
        ];
    }

    private function getEvaluations($lessons)
    {
        $evaluation = [];
        foreach ($lessons as $lesson) {
            if ($lesson->instructor_note == null) continue;

            $evaluation[] = [
                'lesson_date' => $lesson->end_at,
                'lesson_date_formatted' => Carbon::parse($lesson->end_at)->format('d-m-Y H:i A'),
                'instructor_name' => $lesson->instructor->first_name . " " . $lesson->instructor->last_name,
                'instructor_avatar' => $lesson->instructor->user->present()->gravatar(),
                'eval' => json_decode($lesson->instructor_evaluation)
            ];
        }
        return $evaluation;
    }
}
