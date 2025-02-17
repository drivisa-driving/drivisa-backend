<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\TraineeRepository;

class EloquentTraineeRepository extends EloquentBaseRepository implements TraineeRepository
{
    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $trainees = $this->allWithBuilder();

        if ($request->get('search') !== null) {
            $term = strtolower($request->get('search'));
            $trainees->where('first_name', 'LIKE', "%{$term}%")
                ->orWhere('last_name', 'LIKE', "%{$term}%")
                ->orWhere('no', 'LIKE', "%{$term}%")
                ->orWhere('id', $term);
        }

        if ($request->get('only_verified') === "true") {
            $trainees->where('verified', $request->get('only_verified'));
        }

        $orderBy = $request->get('order_by', 'first_name');
        $order = $request->get('order', 'asc');
        $trainees->orderBy($orderBy, $order);

        return $trainees->paginate($request->get('per_page', 50));
    }

    public function generateNo()
    {
        return Carbon::now()->timestamp;
    }
}