<?php


namespace Modules\Drivisa\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;

class CacheEvaluationIndicatorDecorator extends BaseCacheDecorator implements EvaluationIndicatorRepository
{
    public function __construct(EvaluationIndicatorRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.instructor_evaluation_indicator';
        $this->repository = $repository;
    }

}