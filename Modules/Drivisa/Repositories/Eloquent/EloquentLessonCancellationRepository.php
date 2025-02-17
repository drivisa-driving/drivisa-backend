<?php


namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Http\Request;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\LessonCancellationRepository;
use Modules\Drivisa\Repositories\LessonRepository;

class EloquentLessonCancellationRepository extends EloquentBaseRepository implements LessonCancellationRepository
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