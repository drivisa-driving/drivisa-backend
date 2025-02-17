<?php

namespace Modules\Drivisa\Services;

use Exception;
use Cartalyst\Sentinel\Activations\EloquentActivation;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Entities\TrainingLocation;
use Modules\Drivisa\Repositories\TrainingLocationRepository;
use Modules\User\Entities\Sentinel\User;

class TrainingLocationService
{

    private TrainingLocationRepository $trainingLocationRepository;

    /**
     * @param TrainingLocationRepository $trainingLocationRepository
     */


    public function __construct(TrainingLocationRepository $trainingLocationRepository)
    {
        $this->trainingLocationRepository = $trainingLocationRepository;
    }

    public function addLocation($data)
    {
        return $this->trainingLocationRepository->create([
            'source_address' => $data['source_address'],
            'source_latitude' => $data['source_latitude'],
            'source_longitude' => $data['source_longitude'],
        ]);
    }

    public function updateLocation($trainingLocation, $data)
    {
        return $this->trainingLocationRepository->update($trainingLocation, $data);
    }

    public function deleteLocation($id)
    {
        TrainingLocation::destroy($id);
    }
}
