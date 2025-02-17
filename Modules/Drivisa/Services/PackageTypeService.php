<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\PackageType;
use Modules\Drivisa\Repositories\AdminRepository;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\PackageTypeRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\User\Entities\Sentinel\User;

class PackageTypeService
{
    /**
     * @var PackageRepository
     */
    private $packageTypeRepository;


    /**
     * @param PackageTypeRepository $packageTypeRepository
     */
    public function __construct(PackageTypeRepository $packageTypeRepository)
    {
        $this->packageTypeRepository = $packageTypeRepository;
    }

    public function allPackageType()
    {
        return $this->packageTypeRepository->all();
    }

    public function findOne($id)
    {
        return $this->packageTypeRepository->find($id);
    }

    public function save($data): void
    {
        $packageType = new PackageType;
        $this->OnUpdateAndCreate($data, $packageType);
    }

    public function update($data, $id): void
    {
        $packageType = $this->packageTypeRepository->find($id);
        $this->OnUpdateAndCreate($data, $packageType);
    }

    public function delete($id): void
    {
        $packageType = $this->packageTypeRepository->find($id);
        $packageType->delete();
    }

    /**
     * @param $data
     * @param $packageType
     */
    private function OnUpdateAndCreate($data, $packageType): void
    {
        $packageType->name = $data->name;
        $packageType->instructor = $data->instructor ?? 0;
        $packageType->drivisa = $data->drivisa ?? 0;
        $packageType->pdio = $data->pdio ?? 0;
        $packageType->mto = $data->mto ?? 0;
        $packageType->instructor_cancel_fee = $data->instructor_cancel_fee ?? 0;
        $packageType->drivisa_cancel_fee = $data->drivisa_cancel_fee ?? 0;
        $packageType->pdio_cancel_fee = $data->pdio_cancel_fee ?? 0;
        $packageType->mto_cancel_fee = $data->mto_cancel_fee ?? 0;
        $packageType->save();
    }

    public function withPackagesCount()
    {
        $packages = PackageType::withCount('packages')->get();

        return $packages;
    }
}
