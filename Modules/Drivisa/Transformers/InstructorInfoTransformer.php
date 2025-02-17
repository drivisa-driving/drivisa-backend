<?php

namespace Modules\Drivisa\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Instructor;

class InstructorInfoTransformer extends JsonResource
{
    public function toArray($request)
    {
        $lessons = $this->lessons;
        $instructor = $this->instructor;
        return [
            'no' => $instructor->no,
            'id' => $instructor->id,
            'phoneNumber' => $instructor->user->phone_number,
            'fullName' => $instructor->user->present()->fullname(),
            'avatar' => $instructor->user->present()->gravatar(),
            'cover' => $instructor->user->present()->carCover(),
            'email' => $instructor->user->email,
            'cars' => $instructor->cars,
            'kycVerification' => array_search($instructor->kyc_verification, Instructor::KYC),
            'evaluation' => [
                'avg' => $instructor->lessons()->avg('trainee_evaluation->value'),
                'count' => $instructor->lessons()->whereNotNull('trainee_evaluation')->count(),
                'comments' => InstructorCommentTransformer::collection($instructor->lessons()->whereNotNull('trainee_note')->get())
            ],
            'lessons' =>
                [
                    'count' => $lessons->count,
                    'hours' => $lessons->hours,
                    'trainee' => $lessons->trainee,
                    'today' => LessonInstructorTransformer::collection($lessons->today),
                    'upcoming' => LessonInstructorTransformer::collection($lessons->upcoming),
                ]
        ];
    }
}