<?php

namespace Modules\Drivisa\Traits;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Transformers\PointTransformer;

trait AvailabilityTraitWithAllWorkingHours
{
    /**
     * @return array
     */
    public function availabilityData($filter_duration, $point,$point_id=0)
    {
        $availability = $this->workingDays()
            ->whereDate('date', ">=", today())
            ->whereHas('allWorkingHoursWithActivePoint')
            ->get();

        $available = [];

        if(request('filter') && request('filter.term') ){
            foreach ($availability as $single) {
                foreach ($point->toArray() as $p) {
                    $p = (object)$p;
                    if ($times = $this->getTimes($single, $filter_duration, $p,$point_id)) {
                        $available[] = [
                            'date' => $single->date,
                            'date_formatted' => Carbon::parse($single->date)->format('D, F d, Y'),
                            'total_available' => count(collect($times)->where('status','available')),
                            'times' => $times
                        ];
                    }
                }
            }
        }else{
            foreach ($availability as $single) {
                if ($times = $this->getTimes($single, $filter_duration, $point,$point_id)) {
                    $available[] = [
                        'date' => $single->date,
                        'date_formatted' => Carbon::parse($single->date)->format('D, F d, Y'),
                        'total_available' => count(collect($times)->where('status','available')),
                        'times' => $times
                    ];
                }
            }

        }

        return $available;
    }


    /**
     * @param $single
     * @return array
     */
    private function getTimes($single, $filter_duration, $point,$point_id)
    {
        $times = [];
        $allWorkingHoursWithActivePoint =$single->allWorkingHoursWithActivePoint;
        if(count($allWorkingHoursWithActivePoint)) {
            $time_ids = $allWorkingHoursWithActivePoint->pluck('id');
            $point_ids = $allWorkingHoursWithActivePoint->pluck('point_id');

            $lessons = Lesson::whereIn('working_hour_id', $time_ids)->get();
            $points = Point::whereIn('id', $point_ids)->get();
            foreach ($allWorkingHoursWithActivePoint as $time) {
                $point = $points->where('id', $time->point_id)->first();
                if($point_id > 0) {
                    if ($time->point_id !== (int)$point_id) continue;
                }

                 // Check if date is today, and skip if true
                 if (Carbon::parse($single->date)->isToday()) continue;

                $lesson = $lessons->where('working_hour_id', $time->id)->first();
                if ($time->status === WorkingHour::STATUS['available'] || ($lesson && $lesson->status === Lesson::STATUS['reserved'])) {

                    $open_at = Carbon::parse($time->open_at);
                    $close_at = Carbon::parse($time->close_at);
                    $duration = $open_at->diffInHours($close_at);

                    if (isset($this->duration) && $this->duration != $duration) continue; // skip when duration not match
                    $open_at_formatted = $open_at->format('h:i a');
                    $close_at_formatted = $close_at->format('h:i a');

                    $times[] = [
                        'working_hour_id' => $time->id,
                        'duration' => $duration,
                        'status' => array_search($time->status, WorkingHour::STATUS),
                        'time_formatted' => $open_at_formatted . " - " . $close_at_formatted,
                        'open_at' => $time->open_at,
                        'open_at_formatted' => $open_at_formatted,
                        'close_at' => $time->close_at,
                        'close_at_formatted' => $close_at_formatted,
                        'point' => $point
                    ];
                }
            }

            if ($filter_duration) {
                $times =collect($times)->where('duration',(int)$filter_duration)->all();
            }
        }
        return $times;
    }

}
