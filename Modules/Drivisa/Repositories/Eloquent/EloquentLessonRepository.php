<?php


namespace Modules\Drivisa\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Repositories\LessonRepository;

class EloquentLessonRepository extends EloquentBaseRepository implements LessonRepository
{
    public function searchLessons($data)
    {
        $query = $this->allWithBuilder();

        $instructor_id = $data['instructor_id'] ?? null;
        $trainee_id = $data['trainee_id'] ?? null;
        $start_at = $data['start_at'] ?? null;
        $end_at = $data['end_at'] ?? null;
        $status = $data['status'] ?? null;
        $except = $data['except'] ?? null;

        if ($instructor_id) {
            $query->where('instructor_id', $instructor_id);
        } elseif ($trainee_id) {
            $query->where('trainee_id', $trainee_id);
        }

        if ($start_at && $end_at) {
            $query->whereBetween('start_at', [$start_at, $end_at]);
        } elseif ($start_at || $end_at) {
            if ($start_at) {
                $query->whereDate('start_at', '>=', $start_at);
            }
            if ($end_at) {
                $query->whereDate('start_at', '<=', $end_at);
            }
        }

        if ($status && array_key_exists($status, Lesson::STATUS)) {
            $query->whereStatus(Lesson::STATUS[$status]);
        }
        if ($except) {
            $arrayOfStatus = explode(',', $except);

            $arrayOfStatusIds = collect();

            foreach ($arrayOfStatus as $single_status) {
                $arrayOfStatusIds->push(Lesson::STATUS[$single_status]);
            }
            $query->whereNotIn('status', $arrayOfStatusIds->values());
        }

        $per_page = $data['per_page'] ?? 100;

        return $query->orderByRaw('DATE(start_at) DESC')->orderBy('start_time')->paginate($per_page);
    }
}
