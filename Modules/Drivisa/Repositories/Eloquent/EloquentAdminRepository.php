<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use \Modules\Drivisa\Repositories\AdminRepository;
use Modules\User\Entities\Sentinel\User;

class EloquentAdminRepository extends EloquentBaseRepository implements AdminRepository
{
    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $admins = $this->allWithBuilder();

        if ($request->get('search') !== null) {
            $term = strtolower($request->get('search'));
            $admins->where('first_name', 'LIKE', "%{$term}%")
                ->orWhere('last_name', 'LIKE', "%{$term}%")
                ->orWhere('no', 'LIKE', "%{$term}%")
                ->orWhere('id', $term);
        }


        $orderBy = $request->get('order_by', 'first_name');
        $order = $request->get('order', 'asc');
        $admins->orderBy($orderBy, $order);

        return $admins->paginate($request->get('per_page', 50));
    }
}