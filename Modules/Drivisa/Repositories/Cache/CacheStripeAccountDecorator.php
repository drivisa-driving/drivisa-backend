<?php


namespace Modules\Drivisa\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\StripeAccountRepository;

class CacheStripeAccountDecorator extends BaseCacheDecorator implements StripeAccountRepository
{
    public function __construct(StripeAccountRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.stripe_account';
        $this->repository = $repository;
    }

}