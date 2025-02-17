<?php

namespace Modules\Drivisa\Repositories\Cache;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\InstructorRepository;

class CacheInstructorDecorator extends BaseCacheDecorator implements InstructorRepository
{
    public function __construct(InstructorRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.instructors';
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

    public function searchInstructors($data)
    {
        $page = $data['page'] ?? '';
        $perPage = $data['per_page'] ?? 50;
        $filter = $data['filter'] ?? null;
        $term = $filter['term'] ?? null;
        $make = $filter['make'] ?? null;
        $language = $filter['language'] ?? null;
        $address = $filter['address'] ?? null;
        $date = $filter['date'] ?? null;
        $open_at = $filter['open_at'] ?? null;
        $close_at = $filter['close_at'] ?? null;

        $key = $this->getBaseKey() . "searchInstructors.{$page}-{$perPage}-{$term}-{$address}-{$make}-{$language}-{$date}-{$open_at}-{$close_at}";
        return $this->remember(function () use ($data) {
            return $this->repository->searchInstructors($data);
        }, $key);
    }
}