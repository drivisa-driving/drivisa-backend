<?php

namespace Modules\Drivisa\Services;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Repositories\BDELogRepository;
use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Repositories\LessonRepository;

class BDELogService
{
    protected $bdeLogRepository;
    private LessonRepository $lessonRepository;

    /**
     * @param BDELogRepository $bdeLogRepository
     * @param LessonRepository $lessonRepository
     */
    public function __construct(BDELogRepository $bdeLogRepository, LessonRepository $lessonRepository)
    {
        $this->bdeLogRepository = $bdeLogRepository;
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * @param $data
     */
    public function addBde($data)
    {
        $lesson = $this->lessonRepository->find($data['lesson_id']);

        $collect = collect();

        if ($lesson->duration == 2) {
            for ($i = 0; $i <= 1; $i++) {
                $collect->push($this->storeBdeLesson($data, $i));
            }
        } else {
            $collect->push($this->storeBdeLesson($data));
        }

        return $collect;
    }

    private function storeBdeLesson($data, $bde_increment = 0)
    {
        return $this->bdeLogRepository->create([
            'trainee_id' => $data['trainee_id'],
            'instructor_id' => $data['instructor_id'],
            'lesson_id' => $data['lesson_id'],
            'bde_number' => BDEService::getTotalBdeLog($data['trainee_id'], $data['lesson_id']) + 1,
            'instructor_sign' => $data['instructor_sign'],
            'trainee_sign' => $data['trainee_sign'],
            'notes' => $data['notes'],
        ]);
    }

    public function getLatestBdeLog($lesson_id)
    {
        $lesson = $this->lessonRepository->find($lesson_id);
        $diffInHours = $lesson->getDurationAttribute();
        $bdeLog = BDELog::where('lesson_id', $lesson_id)->latest('created_at')->take($diffInHours)->get();
        return $bdeLog;
    }
}
