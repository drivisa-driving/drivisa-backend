<?php


namespace Modules\Drivisa\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\LessonRepository;

class CacheLessonDecorator extends BaseCacheDecorator implements LessonRepository
{
    public function __construct(LessonRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.instructor_lessons';
        $this->repository = $repository;
    }

    public function searchLessons($data)
    {
        $page = $data['page'] ?? '';
        $perPage = $data['per_page'] ?? 50;
        $start_at = $filter['start_at'] ?? null;
        $end_at = $filter['end_at'] ?? null;

        $key = $this->getBaseKey() . "searchLessons.{$page}-{$perPage}-{$end_at}-{$start_at}";
        return $this->remember(function () use ($data) {
            return $this->repository->searchLessons($data);
        }, $key);
    }
}