<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Setting\Facades\Settings;

class WorkingHourWithCostTransformer extends JsonResource
{
    const LESSON_1_HOUR_COST = 60;
    const LESSON_2_HOUR_COST = 115;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $open_at = Carbon::parse($this->open_at);
        $close_at = Carbon::parse($this->close_at);
        $duration = $open_at->diffInMinutes($close_at);
        $cost = $duration == 60 ? self::LESSON_1_HOUR_COST : self::LESSON_2_HOUR_COST;
        $tax = $cost * Settings::get('lesson_tax') / 100;
        //$course_available = $this->trainee->courses()->wherePivot('available_hours', '>=', $duration / 60)->exists
        //(); @TODO removed by thetestcoder
        return [
            'id' => $this->id,
            'status' => $this->status,
            'openAt' => $this->open_at,
            'closeAt' => $this->close_at,
            'duration' => $duration / 60,
            'point' => new PointTransformer($this->point),
            'createdAt' => $this->created_at,
            'costs' => [
                'cost' => round($cost, 2),
                'tax' => round($tax, 2)
            ],
            //'is-available-pay-by-course' => $course_available @TODO removed by thetestcoder
        ];
    }
}
