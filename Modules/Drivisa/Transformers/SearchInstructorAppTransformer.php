<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Traits\AvailabilityTraitWithAllWorkingHours;

class SearchInstructorAppTransformer extends JsonResource
{
    use AvailabilityTraitWithAllWorkingHours;

    public function toArray($request)
    {
        $filter_duration = null;
        if ($request->filter && isset($request->filter['duration'])) {
            $filter_duration = $request->filter['duration'];
        }

        $point = [];
        $points = [];

        if ($request->filter && isset($request->filter['term'])) {
            $point = $this->getNearestPoint($request->filter ?? null) ?? $this->points()->where('is_active', true)->get();
            $points = new  PointTransformer($point->first());
        } else {
            $point= $this->getNearestPoint($request->filter ?? null) ?? $this->points()->where('is_active', true)->first();
            $points = new  PointTransformer($point);
        }
        return [
            'no' => $this->no,
            'id' => $this->id,
            'avatar' => $this->resource->user->present()->gravatar(),
            'cover' => $this->resource->user->present()->cover(),
            'username' => $this->resource->user->username,
            'fullName' => $this->resource->user->present()->fullname(),
            'bio' => $this->resource->bio,
            'evaluation' => [
                'avg' => $this->resource->lessons()->avg('trainee_evaluation->value'),
                'count' => $this->resource->lessons()->whereNotNull('trainee_evaluation')->count(),
            ],
            'point' =>$points,
            'availability' => $this->availabilityData($filter_duration, $point),
        ];
    }

    private function getNearestPoint($filter)
    {
        if (!isset($filter['lat']) && !isset($filter['lng'])) return;
        if (empty($filter['lat']) && empty($filter['lng'])) return;

        // 1. distance calculation and filtration
        $points = DB::table('drivisa__points')->where('deleted_at',null)
            ->select('drivisa__points.*', DB::raw("6371 * acos(cos(radians(" . $filter['lat'] . "))
             * cos(radians(source_latitude)) 
             * cos(radians(source_longitude) - radians(" . $filter['lng'] . ")) 
             + sin(radians(" . $filter['lat'] . ")) 
             * sin(radians(source_latitude))) AS distance"))
            ->having('distance', '<', $filter['within_km'] ?? 50000)
            ->whereIsActive(true)
            ->where('instructor_id', $this->id)
            ->get();

        // sort filtered points by distance
        $points = $points->sortBy('distance');

        return $points->first();
    }
}
