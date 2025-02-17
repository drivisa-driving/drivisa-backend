<?php

namespace Modules\Drivisa\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Modules\Drivisa\Entities\WorkingDay;
use Modules\Drivisa\Exceptions\InstructorNotFoundException;
use Modules\Drivisa\Exceptions\InstructorNotHasActivePointException;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\PointRepository;
use Modules\Drivisa\Repositories\WorkingDayRepository;
use Modules\Drivisa\Repositories\WorkingHourRepository;

class ScheduleService
{

    private $instructorRepository;
    private $workingDayRepository;
    private $workingHourRepository;
    private $pointRepository;

    public function __construct(
        InstructorRepository  $instructorRepository,
        WorkingDayRepository  $workingDayRepository,
        WorkingHourRepository $workingHourRepository,
        PointRepository       $pointRepository
    ) {
        $this->instructorRepository = $instructorRepository;
        $this->workingDayRepository = $workingDayRepository;
        $this->workingHourRepository = $workingHourRepository;
        $this->pointRepository = $pointRepository;
    }

    public function saveSchedule($user, $data)
    {
        $end_time =  Carbon::parse("23:00");
        $start_time =  Carbon::parse("01:00");

        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }

        $workingHours = [];
        foreach ($data['working_hours'] as &$working_hour) {
            if (!isset($working_hour['point_id'])) {
                throw new AuthorizationException();
            }

            $point = $this->pointRepository->find($working_hour['point_id']);
            if ($instructor->id != $point->instructor_id) {
                throw new AuthorizationException();
            }

            $workingDay = $instructor->WorkingDays()->firstOrCreate(['date' => $data['date']]);
            $openAt = Carbon::parse($working_hour['open_at']);
            $closeAt = $openAt->copy()->addMinutes($working_hour['shift']);
            $totalDuration = $openAt->diffInMinutes($closeAt);
            if ($totalDuration < 60) {
                throw new Exception(trans('drivisa::drivisa.messages.less_duration_60_min'), Response::HTTP_UNPROCESSABLE_ENTITY);
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

                $working_hour['open_at'] = $openAt->format('H:i');
                $working_hour['close_at'] = $closeAt->format('H:i');
                $isHasWorkingHour = $this->checkAvailabilityWorkingHour($workingDay->id, $openAt->addMinutes(-15)->format('H:i'), $closeAt->addMinutes(15)->format('H:i'));
                if (!$isHasWorkingHour) {
                    array_push($workingHours, $working_hour);
                } else {
                    throw new Exception(trans('drivisa::drivisa.messages.lesson_time_conflict'), Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
        }
        $workingDay->workingHours()->createMany($workingHours);
        if ($data['copy'] ?? null) {
            $period = CarbonPeriod::create($data['from'], $data['to']);
            $excludeDay = $this->getExcludeDay($data['exclude']);
            foreach ($period as $date) {
                if (!in_array($date->dayName, $excludeDay)) {
                    $newWorkingDay = $instructor->WorkingDays()->firstOrCreate(['date' => $date->format('Y-m-d')]);
                    $newWorkingDay->workingHours()->createMany($workingHours);
                }
            }
        }
        return $workingDay;
    }

    private function checkAvailabilityWorkingHour($workingDayId, $openAt, $closeAt)
    {
        $result = $this->workingHourRepository->allWithBuilder()
            ->where('working_day_id', $workingDayId)
            ->whereHas('point',function ($query){
                $query->where('deleted_at',null);
            })->whereNested(function ($query) use ($openAt, $closeAt) {
                $query->where(function ($query) use ($openAt, $closeAt) {
                    $query->where('open_at', '>', $openAt)->where('open_at', '<', $closeAt);
                });
                $query->orWhere(function ($query) use ($openAt, $closeAt) {
                    $query->Where('close_at', '>', $openAt)->where('close_at', '<', $closeAt);
                });
                $query->orwhere(function ($query) use ($openAt, $closeAt) {
                    $query->where('open_at', '<=', $openAt)->where('close_at', '>=', $closeAt);
                });
            })
            ->exists();
        return $result;
    }

    private function getExcludeDay($exclude): array
    {
        $excludeDay = [];
        foreach ($exclude as $value) {
            $day = array_flip(WorkingDay::DAYS_OF_WEEK)[$value];
            array_push($excludeDay, $day);
        }
        return $excludeDay;
    }

    public function getSchedule($instructor, $request)
    {
        $request['instructor_id'] = $instructor->id;
        return $this->workingDayRepository->serverPaginationFilteringFor($request);
    }

    public function copySchedule($user, $data)
    {
        $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        $workingDayCopy = $this->workingDayRepository->find($data['working_day_id']);
        if ($instructor->id != $workingDayCopy->instructor_id) {
            throw new AuthorizationException();
        }
        $workingHoursCopy = $workingDayCopy->workingHours;
        $period = CarbonPeriod::create($data['from'], $data['to']);
        $excludeDay = $this->getExcludeDay($data['exclude']);
        foreach ($period as $date) {
            if (!in_array($date->dayName, $excludeDay)) {
                $workingHoursPast = [];
                $workingDayPast = $instructor->WorkingDays()->firstOrCreate(['date' => $date->format('Y-m-d')]);
                foreach ($workingHoursCopy as $workingHourCopy) {
                    $workingHourCopy->status = 1;
                    $isHasWorkingHour = $this->checkAvailabilityWorkingHour($workingDayPast->id, $workingHourCopy->open_at, $workingHourCopy->close_at);
                    if (!$isHasWorkingHour) {
                        $workingHoursPast[] = $workingHourCopy->toarray();
                    }
                }
                $workingDayPast->workingHours()->createMany($workingHoursPast);
            }
        }
    }
}
