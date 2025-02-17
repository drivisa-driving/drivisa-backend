<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use \Modules\Drivisa\Repositories\AdminRepository;
use Modules\Drivisa\Repositories\PurchaseRepository;
use Modules\Drivisa\Repositories\TransactionRepository;
use Modules\User\Entities\Sentinel\User;

class EloquentTransactionRepository extends EloquentBaseRepository implements TransactionRepository
{
    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $admins = $this->allWithBuilder();
        if(isset($request->type))
        {
          //  $admins=$admins->where('instructor.id',$request->id);
        }
        return $admins->paginate($request->get('per_page', 50));
    }
}