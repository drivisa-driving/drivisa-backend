<?php


namespace Modules\Drivisa\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Modules\Core\Repositories\BaseRepository;

interface TraineeRepository extends BaseRepository
{
    /**
     * Generate unique id for instructor
     *
     * @return mixed
     */
    public function generateNo();

    /**
     * Paginating, ordering and searching through pages for server side index table
     * @param Request $request
     * @return Collection
     */
    public function serverPaginationFilteringFor(Request $request);
}