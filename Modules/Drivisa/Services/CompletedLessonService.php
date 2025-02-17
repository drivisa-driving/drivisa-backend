<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Repositories\BDELogRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Repositories\MarkingKeyLogRepository;
use Modules\Drivisa\Services\BDEService;
use Modules\Drivisa\Services\LessonService;
use Modules\Drivisa\Services\MarkingKeyLogService;

class CompletedLessonService
{
    public function __construct(
        public LessonRepository $lessonRepository,
        public LessonService $lessonService,
        public MarkingKeyLogService $markingKeyLogService,
        public MarkingKeyLogRepository  $markingKeyLogRepository,
        public BDELogRepository $bdeLogRepository
    ) {
        $this->lessonRepository = $lessonRepository;
        $this->lessonService = $lessonService;
        $this->markingKeyLogService = $markingKeyLogService;
        $this->markingKeyLogRepository = $markingKeyLogRepository;
        $this->bdeLogRepository = $bdeLogRepository;
    }

    public function instructorCompletedLessons($instructor)
    {
        return $this->lessonRepository->where('instructor_id', $instructor->id)
            ->whereDate('start_at', "<=", today())
            ->where('status', Lesson::STATUS['completed'])
            ->orderByRaw('DATE(start_at) DESC')->orderBy('start_time')
            ->get();
    }

    public function traineeCompletedLessons($trainee)
    {
        return $this->lessonRepository->where('trainee_id', $trainee->id)
            ->whereDate('start_at', "<=", today())
            ->where('status', Lesson::STATUS['completed'])
            ->orderByRaw('DATE(start_at) DESC')->orderBy('start_time')
            ->get();
    }

    public function endBdeLessonByAdmin($data, $lesson)
    {
        if ($data['fillMarkingKeys']) {
            $traineeBdeLog = BDELog::where('trainee_id', $lesson->trainee_id)
                ->latest('created_at')->first();
            $instructorBdeLog = BDELog::where('instructor_id', $lesson->instructor_id)
                ->latest('created_at')->first();

            if ($lesson->duration == 2) {
                for ($i = 0; $i <= 1; $i++) {
                    $newBdeLog = $this->storeBdeLog($lesson, $traineeBdeLog, $instructorBdeLog);
                    $this->addMarkings($data, $newBdeLog);
                }
            } else {
                $newBdeLog = $this->storeBdeLog($lesson, $traineeBdeLog, $instructorBdeLog);
                $this->addMarkings($data, $newBdeLog);
            }
        }

        if (in_array($lesson->status, [Lesson::STATUS['reserved'], Lesson::STATUS['inProgress']])) {
            $this->lessonService->updateEndedAt($lesson->instructor, $lesson);
        }
    }

    private function storeBdeLog($lesson, $traineeBdeLog = null, $instructorBdeLog = null)
    {
        $bdeNumber = BDEService::getTotalBdeLog($lesson->trainee_id, $lesson->id) + 1;
        $instructorSign = optional($instructorBdeLog)->instructor_sign;
        $traineeSign = optional($traineeBdeLog)->trainee_sign;
        $notes = optional($traineeBdeLog)->notes;

        return $this->bdeLogRepository->create([
            'trainee_id' => $lesson->trainee_id,
            'instructor_id' => $lesson->instructor_id,
            'lesson_id' => $lesson->id,
            'bde_number' => $bdeNumber,
            'instructor_sign' => $instructorSign,
            'trainee_sign' => $traineeSign,
            'notes' => $notes,
        ]);
    }

    private function addMarkings($data, $newBdeLog)
    {
        foreach ($data['markings'] as $marking) {
            if ($marking['mark']) {
                $this->markingKeyLogRepository->create([
                    'bde_log_id' => $newBdeLog->id,
                    'marking_key_id' => $marking['marking_key_id'],
                    'mark' => $marking['mark']
                ]);
            }
        }
    }
}
