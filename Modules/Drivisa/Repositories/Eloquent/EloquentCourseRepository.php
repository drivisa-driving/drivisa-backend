<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Http\Request;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\CourseRepository;

class EloquentCourseRepository extends EloquentBaseRepository implements CourseRepository
{
    public function serverPaginationFilteringFor(Request $request)
    {
        $courses = $this->allWithBuilder();
        $orderBy = $request->get('order_by', 'id');
        $order = $request->get('order', 'asc');
        $courses->orderBy($orderBy, $order);
        $per_page = $request->get('per_page', 50);
        return $courses->paginate($per_page);

    }
}