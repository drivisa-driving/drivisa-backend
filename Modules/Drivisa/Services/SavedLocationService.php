<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Repositories\SavedLocationRepository;

class SavedLocationService
{
    private SavedLocationRepository $savedLocationRepository;

    /**
     * @param SavedLocationRepository $savedLocationRepository
     */
    public function __construct(
        SavedLocationRepository $savedLocationRepository
    )
    {
        $this->savedLocationRepository = $savedLocationRepository;
    }

    public function create($trainee, $data)
    {
        $this->SaveLocation($data, $trainee);
    }

    public function saveCombinedLocation($trainee, $data)
    {
        $this->SaveLocation($data, $trainee);
    }

    public function delete($trainee, $savedLocation)
    {
        $savedLocation->delete();
    }

    /**
     * @param $data
     * @param $trainee
     * @return void
     */
    protected function SaveLocation($data, $trainee): void
    {
        $this->savedLocationRepository->create([
            'source_latitude' => $data['source_latitude'],
            'source_longitude' => $data['source_longitude'],
            'source_address' => $data['source_address'],
            'destination_latitude' => $data['destination_latitude'],
            'destination_longitude' => $data['destination_longitude'],
            'destination_address' => $data['destination_address'],
            'default' => $data['default'] === 'yes' ? 1 : 0,
            'trainee_id' => $trainee->id,
            'user_id' => $trainee->user_id,
        ]);
    }
}