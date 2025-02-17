<?php


namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Http\Request;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\TrainingLocationRepository;

class EloquentTrainingLocationRepository extends EloquentBaseRepository implements TrainingLocationRepository
{
    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $trainingLocations = $this->allWithBuilder();
        return $trainingLocations->paginate($request->get('per_page', 50));
    }
}
