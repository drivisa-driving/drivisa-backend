<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use \Modules\Drivisa\Repositories\AdminRepository;
use Modules\Drivisa\Repositories\PurchaseRepository;
use Modules\User\Entities\Sentinel\User;

class EloquentPurchaseRepository extends EloquentBaseRepository implements PurchaseRepository
{
    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $admins = $this->allWithBuilder();

        return $admins->paginate($request->get('per_page', 50));
    }
}