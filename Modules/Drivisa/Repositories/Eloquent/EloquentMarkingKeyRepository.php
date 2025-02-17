<?php


namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Http\Request;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\MarkingKeyRepository;

class EloquentMarkingKeyRepository extends EloquentBaseRepository implements MarkingKeyRepository
{
/**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $complaints = $this->allWithBuilder();
        return $complaints->paginate($request->get('per_page', 50));
    }
}