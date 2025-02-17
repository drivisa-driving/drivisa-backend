<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Repositories\AdminRepository;
use Modules\Drivisa\Repositories\PackageDataRepository;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\User\Entities\Sentinel\User;

class PackageDataService
{
    /**
     * @var PackageDataRepository
     */
    private $packageDataRepository;

    /**
     * @param PackageDataRepository $packageDataRepository
     */
    public function __construct(PackageDataRepository $packageDataRepository)
    {
        $this->packageDataRepository = $packageDataRepository;
    }
}