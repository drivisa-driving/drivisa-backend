<?php

namespace Modules\Drivisa\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\CarRepository;

class CacheCarDecorator extends BaseCacheDecorator implements CarRepository
{
    public function __construct(CarRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.cars';
        $this->repository = $repository;
    }
}