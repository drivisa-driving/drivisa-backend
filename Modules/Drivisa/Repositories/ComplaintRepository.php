<?php

namespace Modules\Drivisa\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Modules\Core\Repositories\BaseRepository;

interface ComplaintRepository extends BaseRepository
{
    public function serverPaginationFilteringFor(Request $request);   
}