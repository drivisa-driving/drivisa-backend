<?php

namespace Modules\Setting\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use \Modules\Core\Repositories\BaseRepository;

interface SettingRepository extends BaseRepository
{
    public function findOrFail($id);
}