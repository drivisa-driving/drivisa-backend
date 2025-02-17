<?php

namespace Modules\Drivisa\Transformers\admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Drivisa\Services\StatsService;
use Modules\User\Transformers\UserTransformer;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\FinalTestResult;

class CourseAdminTransformer extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->statsService = app(StatsService::class);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */

    public function toArray($request)
    {
        $finalTestLog = FinalTestResult::whereIn('bde_log_id', $this->bdeLog->pluck('id')->toArray())
            ->latest('created_at')
            ->first();
        list($stats, $total_hours, $completed_hours) = $this->getCourseHours();

        return [
            'trainee_id' => $this->id,
            'licence_issued' => $this->licence_issued,
            'user' => new UserTransformer($this->user),
            'course_available' => $this->user
                ->courses()
                ->where('type', Course::TYPE['BDE'])
                ->count(),
            'total_hours' => $stats['data']['total_hours'],
            'completed_hours' => $completed_hours,
            'remaining_hours' => $total_hours - $completed_hours,
            'is_pass' => $finalTestLog ? ($finalTestLog->is_pass ? 'Pass' : 'Fail') : '-',
        ];
    }

    private function getCourseHours()
    {
        $stats = $this->statsService->getStatsByType($this->user, "BDE", true);

        $completed_hours = $this->lessons?->whereNotNull('ended_at')
            ->where('lesson_type', Lesson::TYPE['bde'])
            ->sum('duration');

        return [$stats, $stats['data']['total_hours'], $completed_hours];
    }
}
