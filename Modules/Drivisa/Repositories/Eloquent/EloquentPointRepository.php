<?php


namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\PointRepository;

class EloquentPointRepository extends EloquentBaseRepository implements PointRepository
{

    public function findNearestPoints($latitude, $longitude)
    {
        $points = $this->model
            ->select('*', DB::raw(sprintf(
                '(6371 * acos(cos(radians(%1$.7f)) * cos(radians(source_latitude)) * cos(radians(source_longitude) - radians(%2$.7f)) + sin(radians(%1$.7f)) * sin(radians(source_latitude)))) AS distance',
                $latitude,
                $longitude
            )))
            ->where('is_active', 1)
            ->having('distance', '<', 50)
            ->orderBy('distance', 'asc')
            ->get();

        return $points;
    }
}