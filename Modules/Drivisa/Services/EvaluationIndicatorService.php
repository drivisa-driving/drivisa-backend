<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;

class EvaluationIndicatorService
{

    public function __construct(public EvaluationIndicatorRepository $evaluationIndicatorRepository)
    {
        $this->evaluationIndicatorRepository = $evaluationIndicatorRepository;
    }

    public function getEvaluations($trainee_id)
    {
        $lesson = Lesson::where('trainee_id', $trainee_id)->latest('ended_at')->where('lesson_type', 1)->where('status', 3)->first();
        $evaluations = $this->evaluationIndicatorRepository
            ->allWithBuilder()
            ->orderBy('order')
            ->get()
            ->toArray();

        $new_evals = [];

        if ($lesson) {

            $instructor_evaluations = collect(json_decode($lesson->instructor_evaluation));

            foreach ($evaluations as $evaluation) {

                $lastEval = $instructor_evaluations->where('id', $evaluation['id'])->first();

                $evaluation['last_value'] = $lastEval != null ? $lastEval->value : null;

                $new_evals[] = $evaluation;
            }

            return $new_evals;
        } else {
            foreach ($evaluations as $evaluation) {
                $evaluation['last_value'] = null;

                $new_evals[] = $evaluation;
            }
            return $new_evals;
        }
    }
}
