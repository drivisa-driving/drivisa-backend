<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;

class TraineeProfileTransformer extends JsonResource
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
            'birth_date' => $this->resource->birth_date,
            'credit_remaining' => $this->resource->user->credit,
            'fullName' => $this->resource->user->present()->fullname(),
            'avatar' => $this->resource->user->present()->gravatar(),
            'cover' => $this->resource->user->present()->cover(),
            'kycVerification' => array_search($this->resource->kyc_verification, Trainee::KYC),
            'credit' => $this->resource->user->credit,
            'evaluations' => $this->getEvaluations($lessons),
            'lessons' => LessonTraineeTransformer::collection($lessons),
            'reviews' => $this->getReviews($lessons)
        ];
    }

    private function getReviews($lessons)
    {
        $reviews = [];
        foreach ($lessons as $lesson) {
            if ($lesson->instructor_note == null) continue;

            $reviews[] = [
                'lesson_date' => $lesson->end_at,
                'lesson_date_formatted' => Carbon::parse($lesson->end_at)->format('d-m-Y H:i A'),
                'instructor_name' => $lesson->instructor->first_name . " " . $lesson->instructor->last_name,
                'instructor_avatar' => $lesson->instructor->user->present()->gravatar(),
                'review' => $lesson->instructor_note
            ];
        }
        return $reviews;
    }

    private function getEvaluations($lessons)
    {
        $finalresult = [];
        $evaluationIndicators = $this->evaluationIndicator->allWithBuilder()->orderBy('order')->get();
        foreach ($evaluationIndicators as $evaluationIndicator) {
            $result = [];
            foreach ($lessons as $lesson) {
                $evaluations = json_decode($lesson->instructor_evaluation);
                $searchedValue = $evaluationIndicator->id;
                $evaluation = null;
                if ($evaluations != null) {
                    $evaluation = current(array_filter(
                        $evaluations,
                        function ($e) use ($searchedValue) {
                            return $e->id == $searchedValue;
                        }
                    ));
                }
                $result = array_merge($result, [
                    'title' => $evaluationIndicator->title,
                    'lesson-' . $lesson->id => $evaluation->value ?? '-',
                ]);
            }
            array_push($finalresult, $result);
        }
        return $finalresult;
    }
}
