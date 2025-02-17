<?php


namespace Modules\Drivisa\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface LessonRepository extends BaseRepository
{
    public function searchLessons($data);
}