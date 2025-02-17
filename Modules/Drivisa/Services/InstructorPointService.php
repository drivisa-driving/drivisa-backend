<?php


namespace Modules\Drivisa\Services;


use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Exceptions\InstructorNotFoundException;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\PointRepository;

class InstructorPointService
{
    protected $pointRepository;
    protected $instructorRepository;

    public function __construct(PointRepository      $pointRepository,
                                InstructorRepository $instructorRepository)
    {
        $this->pointRepository = $pointRepository;
        $this->instructorRepository = $instructorRepository;
    }

    public function getPoints($instructor)
    {
        return $this->pointRepository->getByAttributes(['instructor_id' => $instructor->id]);

    }

    public function getActivePoints($instructor)
    {
        $points = $this->pointRepository->getByAttributes(['instructor_id' => $instructor->id]);

        return $points->where('is_active', 1)->all();
    }

    public function getPoint($user, $point)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($instructor->id != $point->instructor_id) {
            throw new AuthorizationException();
        }
        return $point;
    }

    public function setPoint($user, $data)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        $point = $instructor->points()->create($data);
        $point->is_active = true;
        $point->save();

        return $point;
    }

    public function deletePoint($user, $point)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($instructor->id != $point->instructor_id) {
            throw new AuthorizationException();
        }
        return $this->pointRepository->destroy($point);
    }

    /**
     * @throws InstructorNotFoundException
     * @throws AuthorizationException
     */
    public function updatePoint($user, $point, $data)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($instructor->id != $point->instructor_id) {
            throw new AuthorizationException();
        }

        $point = $this->pointRepository->update($point, $data);
        $point->is_active = true;
        $point->save();

        return $point;
    }

    public function togglePoint($user, $point, $data)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($instructor->id != $point->instructor_id) {
            throw new AuthorizationException();
        }
        $instructor->points()->where([
                ['id', '<>', $point->id],
                ['is_active', '=', true]
            ]
        )->update(['is_active' => false]);
        $this->pointRepository->update($point, ['is_active' => $data['status']]);
        return $instructor->points;
    }

    private function deactivateOtherPoints($instructor, $point)
    {
        $instructor->points()->where([
                ['id', '<>', $point->id]
            ]
        )->update(['is_active' => false]);
    }

    public function getNearestInstructors($data)
    {
        if (isset($data['latitude']) && isset($data['longitude'])) {
            return $this->pointRepository->findNearestPoints($data['latitude'], $data['longitude']);
        } else {
            return $this->pointRepository->all()->where('is_active', 1);
        }
    }
}