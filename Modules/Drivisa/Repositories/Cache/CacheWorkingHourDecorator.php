<?php

namespace Modules\Drivisa\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\WorkingHourRepository;

class CacheWorkingHourDecorator extends BaseCacheDecorator implements WorkingHourRepository
{
    public function __construct(WorkingHourRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.workingHour';
        $this->repository = $repository;
    }

}