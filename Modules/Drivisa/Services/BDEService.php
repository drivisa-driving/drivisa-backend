<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\Lesson;

class BDEService
{
    public static function getTotalBdeDone($trainee_id)
    {
        $lessons = Lesson::where('trainee_id', $trainee_id)
            ->where('lesson_type', Lesson::TYPE['bde'])
            ->whereNotNull('ended_at')
            ->get();

        return $lessons->sum('duration');
    }

    public static function getTotalBdeLog($trainee_id, $lesson_id)
    {
        return BdeLog::where('trainee_id', $trainee_id)
            ->count();
    }
}