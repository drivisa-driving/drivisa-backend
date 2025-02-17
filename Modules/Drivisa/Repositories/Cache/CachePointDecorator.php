<?php


namespace Modules\Drivisa\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\PointRepository;

class CachePointDecorator extends BaseCacheDecorator implements PointRepository
{
    public function __construct(PointRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.instructor_points';
        $this->repository = $repository;
    }

    public function findNearestPoints($latitude, $longitude)
    {
        $key = $this->getBaseKey() . "findNearestPoints.{$latitude}-{$longitude}";
        return $this->remember(function () use ($latitude, $longitude) {
            return $this->repository->findNearestPoints($latitude, $longitude);
        }, $key);
    }
}