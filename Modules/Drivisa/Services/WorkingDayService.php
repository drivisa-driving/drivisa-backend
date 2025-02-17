<?php

namespace Modules\Drivisa\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Exceptions\InstructorNotFoundException;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\WorkingDayRepository;

class WorkingDayService
{

    private $instructorRepository;
    private $workingDayRepository;

    public function __construct(InstructorRepository $instructorRepository,
                                WorkingDayRepository $workingDayRepository)
    {
        $this->instructorRepository = $instructorRepository;
        $this->workingDayRepository = $workingDayRepository;
    }

    public function updateWorkingDayStatus($user, $workingDay, $data)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($instructor->id != $workingDay->instructor_id) {
            throw new AuthorizationException();
        }
        $this->workingDayRepository->update($workingDay, $data);
    }


}