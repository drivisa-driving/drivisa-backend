<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use \Modules\Drivisa\Repositories\AdminRepository;
use Modules\Drivisa\Repositories\RentalRequestRepository;
use Modules\User\Entities\Sentinel\User;

class EloquentRentalRequestRepository extends EloquentBaseRepository implements RentalRequestRepository
{
    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $rentalRequests = $this->allWithBuilder();

        $rentalRequests->latest();

        return $rentalRequests->paginate($request->get('per_page', 50));
    }
}