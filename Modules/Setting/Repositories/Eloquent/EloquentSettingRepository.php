<?php

namespace Modules\Setting\Repositories\Eloquent;

use \Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use \Modules\Setting\Repositories\SettingRepository;

class EloquentSettingRepository extends EloquentBaseRepository implements SettingRepository
{

    public function findOrFail($id)
    {
        $this->model->findOrFail($id);
    }
}