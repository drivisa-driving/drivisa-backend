<?php

namespace Modules\Drivisa\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Drivisa\Entities\Package;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\PackageTypeRepository;

class PackageService
{
    /**
     * @var PackageRepository
     */
    private $packageRepository;
    /**
     * @var PackageTypeRepository
     */
    private $packageTypeRepository;


    /**
     * @param PackageRepository $packageRepository
     * @param PackageTypeRepository $packageTypeRepository
     */
    public function __construct(PackageRepository $packageRepository, PackageTypeRepository $packageTypeRepository)
    {
        $this->packageRepository = $packageRepository;
        $this->packageTypeRepository = $packageTypeRepository;
    }

    public function findOne($id)
    {
        return $this->packageRepository->find($id);
    }

    public function save($data): void
    {
        $package = new Package;
        $package->name = $data->name;
        $package->package_type_id = $data->package_type_id;
        $package->save();

        $this->addPackageData($package, $data);

    }

    public function update($data, $id): void
    {
        $package = $this->packageRepository->find($id);
        $package->name = $data->name;
        $package->package_type_id = $data->package_type_id;
        $package->save();

        $this->updatePackageData($package, $data);
    }

    public function delete($id): void
    {
        $package = $this->packageRepository->find($id);
        $package->delete();
    }

    protected function addPackageData($package, $data)
    {
        $package->packageData()->create($this->getStoreData($data));
    }

    protected function updatePackageData($package, $data)
    {
        $package->packageData()->update($this->getStoreData($data));
    }

    /**
     * @param $data
     * @return array
     */
    protected function getStoreData($data): array
    {
        return [
            'instructor' => $data->instructor ?? 0,
            'drivisa' => $data->drivisa ?? 0,
            'pdio' => $data->pdio ?? 0,
            'mto' => $data->mto ?? 0,
            'instructor_cancel_fee' => $data->instructor_cancel_fee ?? 0,
            'drivisa_cancel_fee' => $data->drivisa_cancel_fee ?? 0,
            'pdio_cancel_fee' => $data->pdio_cancel_fee ?? 0,
            'mto_cancel_fee' => $data->mto_cancel_fee ?? 0,
            'hours' => $data->hours,
            'hour_charge' => $data->hour_charge,
            'amount' => $data->amount,
            'discount_price' => $data->discount_price,
            'additional_information' => $data->additional_information,
        ];
    }

    /**
     * @throws \Throwable
     */
    public function getPackageByType($type)
    {
        $packageType = $this->packageTypeRepository->where('name', $type)->first();

        throw_if($packageType == null, ModelNotFoundException::class);

        return $this->packageRepository
            ->where('package_type_id', $packageType->id)
            ->get()->sortByDesc('packageData.hours');

    }
}