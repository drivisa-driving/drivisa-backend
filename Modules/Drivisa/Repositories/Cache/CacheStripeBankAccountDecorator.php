<?php


namespace Modules\Drivisa\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Drivisa\Repositories\StripeBankAccountRepository;

class CacheStripeBankAccountDecorator extends BaseCacheDecorator implements StripeBankAccountRepository
{
    public function __construct(StripeBankAccountRepository $repository)
    {
        parent::__construct();
        $this->entityName = 'drivisa.stripe_account_bank';
        $this->repository = $repository;
    }

}