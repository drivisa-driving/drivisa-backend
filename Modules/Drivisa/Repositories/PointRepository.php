<?php


namespace Modules\Drivisa\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface PointRepository extends BaseRepository
{
    public function findNearestPoints($latitude, $longitude);


}