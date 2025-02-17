<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilityTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'date' => $this->date,
            'date_formatted' => Carbon::parse($this->date)->format('D, F d, Y'),
            'times' => $this->getTimes()
        ];
    }

    private function getTimes()
    {
        $times = [];

        foreach ($this->workingHoursWithActivePoint as $time) {

            if (Carbon::parse($time->workingDay->date)->isToday())
                if (Carbon::parse($time->open_at)->lte(now())) continue;


            $open_at = Carbon::parse($time->open_at);
            $open_at_formatted = $open_at->format('h:i a');
            $close_at = Carbon::parse($time->close_at);
            $close_at_formatted = $close_at->format('h:i a');

            $duration = $open_at->diffInHours($close_at);

            $times[] = [
                'duration' => $duration,
                'time_formatted' => $open_at_formatted . " - " . $close_at_formatted,
                'open_at' => $time->open_at,
                'open_at_formatted' => $open_at_formatted,
                'close_at' => $time->close_at,
                'close_at_formatted' => $close_at_formatted,
            ];
        }
        return $times;
    }
}
