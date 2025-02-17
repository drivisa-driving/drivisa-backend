<?php

namespace Modules\Drivisa\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Exceptions\InstructorNotFoundException;
use Modules\Drivisa\Exceptions\InstructorNotHasActivePointException;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\PointRepository;
use Modules\Drivisa\Repositories\WorkingHourRepository;

class WorkingHourService
{

    private $instructorRepository;
    private $workingHourRepository;
    private $pointRepository;

    public function __construct(
        InstructorRepository  $instructorRepository,
        WorkingHourRepository $workingHourRepository,
        PointRepository       $pointRepository
    ) {
        $this->instructorRepository = $instructorRepository;
        $this->workingHourRepository = $workingHourRepository;
        $this->pointRepository = $pointRepository;
    }


    public function updateWorkingHour($workingHour, $user, $data)
    {

        $end_time =  Carbon::parse("23:00");
        $start_time =  Carbon::parse("01:00");

        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($workingHour->workingDay->instructor_id !== $instructor->id) {
            throw new AuthorizationException();
        }
        if ($data['point_id'] ?? null) {
            $point = $this->pointRepository->find($data['point_id']);
            if ($instructor->id != $point->instructor_id) {
                throw new AuthorizationException();
            }
        }
        if (isset($data['working_hours'][0]['open_at']) && isset($data['working_hours'][0]['close_at'])) {
            $openAt = Carbon::parse($data['working_hours'][0]['open_at']);
            $closeAt = Carbon::parse($data['working_hours'][0]['close_at']);
            $totalDuration = $openAt->diffInMinutes($closeAt);
            if ($totalDuration < 60 || $totalDuration > 120) {
                throw new Exception(trans('drivisa::drivisa.messages.lesson_duration_between', [
                    'min' => 60,
                    'max' => 120,
                ]), Response::HTTP_CONFLICT);
            } else {

                // instructor can't add schedule between 11:00 PM to 01:00 AM
                if (
                    $openAt > $end_time ||
                    $closeAt > $end_time ||
                    $openAt < $start_time ||
                    $closeAt < $start_time
                ) {
                    throw new Exception(trans('drivisa::drivisa.messages.can\'t_schedule_lesson_this_time'), Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $workingDay = $workingHour->workingDay;
                $openAt = $openAt->addMinutes(-15)->format('H:i');
                $closeAt = $closeAt->addMinutes(15)->format('H:i');
                $isHasWorkingHour = $this->checkAvailabilityWorkingHourExecptCurrent($workingDay->id, $workingHour->id, $openAt, $closeAt);
                $availableWorkingHours = $this->checkAvailabilityWorkingHourExecptCurrent($workingDay->id, $workingHour->id, $openAt, $closeAt, 'get');

                if ($isHasWorkingHour) {
                    if (!$this->isAllWorkingHoursCanceled($availableWorkingHours))
                        throw new Exception(trans('drivisa::drivisa.messages.lesson_time_conflict'), Response::HTTP_CONFLICT);
                }
            }
        }
        $this->workingHourRepository->update($workingHour, $data['working_hours'][0]);
        return $workingHour;
    }

    private function checkAvailabilityWorkingHourExecptCurrent($workingDayId, $workingHourId, $openAt, $closeAt, $method = 'exists')
    {
        $result = $this->workingHourRepository->allWithBuilder()
            ->where('id', '<>', $workingHourId)
            ->where('working_day_id', $workingDayId)
            ->whereNested(function ($query) use ($openAt, $closeAt) {
                $query->where(function ($query) use ($openAt, $closeAt) {
                    $query->where('open_at', '>', $openAt)->where('open_at', '<', $closeAt);
                });
                $query->orWhere(function ($query) use ($openAt, $closeAt) {
                    $query->where('close_at', '>', $openAt)->where('close_at', '<', $closeAt);
                });
                $query->orwhere(function ($query) use ($openAt, $closeAt) {
                    $query->where('open_at', '<=', $openAt)->where('close_at', '>=', $closeAt);
                });
            });

        if ($method === 'get') {
            return $result->get();
        }

        return $result->exists();
    }

    public function deleteWorkingHour($workingHour, $user)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($workingHour->workingDay->instructor_id !== $instructor->id) {
            throw new AuthorizationException();
        }
        $this->workingHourRepository->destroy($workingHour);
    }

    public function updateWorkingHourStatus($user, $workingHour, $data)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($instructor->id != $workingHour->workingDay->instructor_id) {
            throw new AuthorizationException();
        }
        $this->workingHourRepository->update($workingHour, $data);
    }

    public function makeWorkingHourAvailable($workingHour, $user)
    {

        if (!$workingHour) {
            throw new Exception('Working hour not available', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($workingHour->status == WorkingHour::STATUS['available']) {
            throw new Exception('Working hour already created', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if (!$instructor) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($instructor->id != $workingHour->workingDay->instructor_id) {
            throw new AuthorizationException();
        }

        $openAt = Carbon::parse($workingHour->open_at)->addMinutes(-15)->format('H:i');
        $closeAt = Carbon::parse($workingHour->close_at)->addMinutes(15)->format('H:i');
        $isHasWorkingHour = $this->checkAvailabilityWorkingHourExecptCurrent($workingHour->workingDay->id, $workingHour->id, $openAt, $closeAt);

        if ($isHasWorkingHour) {
            throw new Exception(trans('drivisa::drivisa.messages.lesson_time_conflict'), Response::HTTP_CONFLICT);
        }

        $totalDuration = Carbon::parse($workingHour->open_at)->diffInMinutes($workingHour->close_at);

        $totalDuration > 120 ? $closeAt = Carbon::parse($workingHour->open_at)->addMinutes(120)->format('H:i:s') : $closeAt = $workingHour->close_at;

        $workingHour =  $this->workingHourRepository->create([
            'working_day_id' => $workingHour->working_day_id,
            'status' => 1,
            'open_at' => $workingHour->open_at,
            'close_at' => $closeAt,
            'point_id' => $workingHour->point_id
        ]);
    }

    private function isAllWorkingHoursCanceled($availableWorkingHours)
    {
        return Lesson::whereIn('working_hour_id', $availableWorkingHours->pluck('id'))->where('status', Lesson::STATUS['canceled'])->exists();
    }
}
