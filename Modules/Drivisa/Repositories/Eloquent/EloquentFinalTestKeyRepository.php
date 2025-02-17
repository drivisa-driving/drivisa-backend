<?php


namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Http\Request;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\FinalTestKeyRepository;

class EloquentFinalTestKeyRepository extends EloquentBaseRepository implements FinalTestKeyRepository
{
    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $keys = $this->allWithBuilder();
        return $keys->paginate($request->get('per_page', 50));
    }
}
