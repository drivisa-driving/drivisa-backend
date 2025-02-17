<?php

namespace Modules\Drivisa\Repositories;

use Illuminate\Http\Request;
use Modules\Core\Repositories\BaseRepository;

interface TrainingLocationRepository extends BaseRepository
{
    public function serverPaginationFilteringFor(Request $request);
}
