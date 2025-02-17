<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Trainee;

class TraineeInfoTransformer extends JsonResource
{
    public function toArray($request)
    {
        $lessons = $this->lessons;
        $trainee = $this->trainee;

        $totalCredit = 0;
        $creditUsed = 0;
        $remainingCredit = 0;

        $allCourses = $trainee->user->courses()->with('creditUseHistories')->get();
        if ($allCourses) {
            $totalCredit = $allCourses->sum('credit');
            $creditUsed = 0;

            foreach ($allCourses as $course) {
                $creditUsed += $course->creditUseHistories->sum('credit_used');
            }

            $remainingCredit = $totalCredit - $creditUsed;
        }

        return [
            'no' => $trainee->no,
            'id' => $trainee->id,
            'phoneNumber' => $trainee->user->phone_number,
            'fullName' => $trainee->user->present()->fullname(),
            'avatar' => $trainee->user->present()->gravatar(),
            'cover' => $trainee->user->present()->cover(),
            'email' => $trainee->user->email,
            'kycVerification' => array_search($trainee->kyc_verification, Trainee::KYC),
            'lessons' =>
                [
                    'count' => $lessons->count,
                    'hours' => $lessons->hours,
                    'instructor' => $lessons->instructor,
                    'today' => LessonTraineeTransformer::collection($lessons->today),
                    'upcoming' => LessonTraineeTransformer::collection($lessons->upcoming),
                ],
            'credit' => [
                'total_lesson_hours' => (float)$totalCredit,
                'used_hours' => $creditUsed,
                'remaining_hours' => $remainingCredit
            ]
        ];
    }
}