<?php

namespace Modules\Drivisa\Traits;

use Carbon\Carbon;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\WorkingHour;

trait AvailabilityTrait
{
    /**
     * @return array
     */
    public function availabilityData($point)
    {
        $availability = $this->workingDays()
            ->whereDate('date', ">=", today())
            ->whereHas('workingHoursWithActivePoint')
            ->get();

        $available = [];

        foreach ($availability as $single) {
            if ($times = $this->getTimes($single, $point)) {
                $available[] = [
                    'date' => $single->date,
                    'date_formatted' => Carbon::parse($single->date)->format('D, F d, Y'),
                    'total_available' => count(array_filter($times, function ($time) {
                        return isset($time['status']) && $time['status'] === 'available';
                    })),
                    'times' => $times
                ];
            }
        }

        return $available;
    }


    /**
     * @param $single
     * @return array
     */
    private function getTimes($single, $point)
    {
        $times = [];

        foreach ($single->workingHoursWithActivePoint as $time) {

            if ($time->point_id !== $point->id) continue;

            // Check if date is today, and skip if true
            if (Carbon::parse($single->date)->isToday()) continue;

            $open_at = Carbon::parse($time->open_at);
            $open_at_formatted = $open_at->format('h:i a');
            $close_at = Carbon::parse($time->close_at);
            $close_at_formatted = $close_at->format('h:i a');

            $duration = $open_at->diffInHours($close_at);

            if (isset($this->duration) && $this->duration != $duration) continue; // skip when duration not match

            $times[] = [
                'working_hour_id' => $time->id,
                'duration' => $duration,
                'status' => array_search($time->status, WorkingHour::STATUS),
                'time_formatted' => $open_at_formatted . " - " . $close_at_formatted,
                'open_at' => $time->open_at,
                'open_at_formatted' => $open_at_formatted,
                'close_at' => $time->close_at,
                'close_at_formatted' => $close_at_formatted,
                'point'=>$point
            ];
        }
        return $times;
    }
}
