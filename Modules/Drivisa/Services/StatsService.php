<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Repositories\TraineeRepository;

class StatsService
{
    private TraineeRepository $traineeRepository;

    /**
     * @param TraineeRepository $traineeRepository
     */
    public function __construct(
        TraineeRepository $traineeRepository
    ) {
        $this->traineeRepository = $traineeRepository;
    }

    public function getStatsByType($user, $type = 'BDE', $response_as_array = false)
    {
        $data = [
            'data' => [
                'total_hours' => 0,
                'used_hours' => 0,
                'remaining_hours' => 0
            ]
        ];

        $course_type = [Course::TYPE[$type]];

        if ($type == 'BDE') {
            $course_type = array_merge($course_type, [Course::TYPE['Bonus_BDE']]);
            $refund_type = Course::TYPE['Refund_BDE'];
        } else {
            $course_type = array_merge($course_type, [Course::TYPE['Cancelled_Credits'], Course::TYPE['Bonus']]);
            $refund_type = Course::TYPE['Refund'];
        }

        $all_courses = $user->courses()
            ->whereNotIn('status', [Course::STATUS['canceled']])
            ->whereIn('type', $course_type)
            ->get();

        $refund_credit_total = $user->courses()
            ->whereNotIn('status', [Course::STATUS['canceled'], Course::STATUS['completed']])
            ->where('type', $refund_type)
            ->get()
            ->sum('credit');

        $data['data']['total_hours'] = $all_courses->sum('credit');
        $data['data']['used_hours'] = 0;


        foreach ($all_courses as $course) {
            $data['data']['used_hours'] += $course->creditUseHistories()->sum('credit_used');
        }

        $data['data']['used_hours'] -= $refund_credit_total;


        $data['data']['remaining_hours'] = $data['data']['total_hours'] - $data['data']['used_hours'];


        return $response_as_array ? $data : response($data);
    }
}
