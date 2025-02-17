<?php

namespace Modules\Drivisa\Repositories\Cache;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\TraineeRepository;

class CacheTraineeDecorator extends BaseCacheDecorator implements TraineeRepository
{
    public function __construct(TraineeRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.trainees';
        $this->repository = $repository;
    }


    /**
     * Paginating, ordering and searching through pages for server side index table
     * @param Request $request
     * @return LengthAwarePaginator
     */
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

    public function generateNo()
    {
        return $this->repository->generateNo();
    }
}