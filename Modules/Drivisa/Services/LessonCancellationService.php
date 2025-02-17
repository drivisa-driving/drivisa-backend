<?php

namespace Modules\Drivisa\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;
use Modules\Drivisa\Repositories\LessonCancellationRepository;
use Modules\Drivisa\Repositories\LessonRepository;

class LessonCancellationService
{

    private LessonCancellationRepository $lessonCancellationRepository;

    /**
     * @param LessonCancellationRepository $lessonCancellationRepository
     */
    public function __construct(
        LessonCancellationRepository $lessonCancellationRepository
    )
    {
        $this->lessonCancellationRepository = $lessonCancellationRepository;
    }

    public function cancel($lesson, $data)
    {
        return $this->lessonCancellationRepository->create([
            'lesson_id' => $lesson->id,
            'cancel_at' => now(),
            'cancel_by' => $data['cancel_by'],
            'reason' => $data['reason']
        ]);
    }
}