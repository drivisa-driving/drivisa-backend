<?php


namespace Modules\Drivisa\Repositories\Eloquent;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\WorkingDayRepository;

class EloquentWorkingDayRepository extends EloquentBaseRepository implements WorkingDayRepository
{

    public function serverPaginationFilteringFor(Request $request)
    {
        $workingDays = $this->allWithBuilder()->where('instructor_id', $request->get('instructor_id'));
        if ($request->exists('from') && $request->exists('to')) {
            $workingDays->whereBetween('date', [$request->get('from'), $request->get('to')]);
        } else {
            $from = Carbon::now()->startOfMonth()->format('Y-m-d');
            $to = Carbon::now()->endOfMonth()->format('Y-m-d');
            $workingDays->whereBetween('date', [$from, $to]);
        }
        if ($request->get('working_day_status') !== null) {
            $workingDays->where('status', $request->get('working_day_status'));
        }
        $orderBy = $request->get('order_by', 'date');
        $order = $request->get('order', 'asc');
        $workingDays->orderBy($orderBy, $order);
        return $workingDays->paginate($request->get('per_page', 50));
    }
}