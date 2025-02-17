<?php

namespace Modules\Drivisa\Repositories\Cache;

use Illuminate\Http\Request;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\CourseRepository;

class CacheCourseDecorator extends BaseCacheDecorator implements CourseRepository
{
    public function __construct(CourseRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.courses';
        $this->repository = $repository;
    }

    public function serverPaginationFilteringFor(Request $request)
    {
        $page = $request->get('page');
        $order = $request->get('order');
        $orderBy = $request->get('order_by');
        $perPage = $request->get('per_page');
        $key = $this->getBaseKey() . "serverPaginationFilteringFor.{$page}-{$order}-{$orderBy}-{$perPage}";
        return $this->remember(function () use ($request) {
            return $this->repository->serverPaginationFilteringFor($request);
        }, $key);
    }
}