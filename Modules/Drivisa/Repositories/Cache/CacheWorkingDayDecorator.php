<?php

namespace Modules\Drivisa\Repositories\Cache;

use Illuminate\Http\Request;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\WorkingDayRepository;

class CacheWorkingDayDecorator extends BaseCacheDecorator implements WorkingDayRepository
{
    public function __construct(WorkingDayRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.workingDay';
        $this->repository = $repository;
    }

    public function serverPaginationFilteringFor(Request $request)
    {
        $page = $request->get('page');
        $order = $request->get('order');
        $orderBy = $request->get('order_by');
        $perPage = $request->get('per_page');
        $search = $request->get('search');

        $key = $this->getBaseKey() . "serverPaginationFilteringFor.{$page}-{$order}-{$orderBy}-{$perPage}-{$search}";

        return $this->remember(function () use ($request) {
            return $this->repository->serverPaginationFilteringFor($request);
        }, $key);
    }
}