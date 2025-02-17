<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use \Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use \Modules\Drivisa\Repositories\PackageTypeRepository;

class EloquentPackageTypeRepository extends EloquentBaseRepository implements PackageTypeRepository
{

    public function serverPaginationFilteringFor(Request $request)
    {
        $packageTypes = $this->allWithBuilder();

        if ($request->get('search') !== null) {
            $term = strtolower($request->get('search'));
            $packageTypes->where('name', 'LIKE', "%{$term}%")
                ->orWhere('id', $term);
        }

        $orderBy = $request->get('order_by', 'name');
        $order = $request->get('order', 'asc');
        $packageTypes->orderBy($orderBy, $order);

        return $packageTypes->paginate($request->get('per_page', 50));
    }
}