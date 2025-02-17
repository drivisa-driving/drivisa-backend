<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use \Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use \Modules\Drivisa\Repositories\PackageRepository;

class EloquentPackageRepository extends EloquentBaseRepository implements PackageRepository
{

    public function serverPaginationFilteringFor(Request $request)
    {
        $package = $this->allWithBuilder();

        if ($request->get('search') !== null) {
            $term = strtolower($request->get('search'));
            $package->where('name', 'LIKE', "%{$term}%")
                ->orWhere('id', $term);
        }

        $orderBy = $request->get('order_by', 'name');
        $order = $request->get('order', 'asc');
        $package->orderBy($orderBy, $order);

        return $package->paginate($request->get('per_page', 50));
    }
}