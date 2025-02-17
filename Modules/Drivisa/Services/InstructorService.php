<?php

namespace Modules\Drivisa\Services;

use Carbon\Carbon;
use Exception;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Events\InstructorLicenceWasUploaded;
use Modules\Drivisa\Exceptions\InstructorNotFoundException;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\User\Exceptions\UserNotFoundException;
use Modules\User\Repositories\UserRepository;
use stdClass;

class InstructorService
{
    /**
     * @var InstructorRepository
     */
    private $instructor;

    /**
     * @var UserRepository
     */
    private $user;
    private $fileService;
    private $imagy;
    private $file;

    public function __construct(
        InstructorRepository $instructor,
        UserRepository       $user,
        Imagy                $imagy,
        FileRepository       $file,
        FileService          $fileService
    ) {
        $this->instructor = $instructor;
        $this->user = $user;
        $this->fileService = $fileService;
        $this->imagy = $imagy;
        $this->file = $file;
    }

    /**
     * Find instructor by the use's username
     * @param $username
     * @return mixed
     * @throws InstructorNotFoundException
     * @throws UserNotFoundException
     */
    public function findByUsername($username)
    {
        $user = $this->user->findByAttributes(['username' => $username]);
        if (!$user) {
            throw new UserNotFoundException(trans('user::users.messages.user_not_found'));
        }

        $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
        if (!$instructor) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        return $instructor;
    }

    public function uploadSingleDocument($user, $data)
    {
        $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }

        if (!str_contains($data['zone'], 'instructor_business_license') && !str_contains($data['zone'], 'car_business_license')) {
            $this->unverifyInstructor($instructor);
        }

        $file = $data['file'] ?? null;
        $zone = $data['zone'] ?? null;
        if ($file && $zone) {
            $previousFile = $instructor->filesByZone($zone)->first();
            $savedFile = $this->fileService->store($file);
            if (is_string($savedFile)) {
                throw new Exception($savedFile, 409);
            }
            $data['medias_single'] = [
                $data['zone'] => $savedFile->id
            ];
            event(new InstructorLicenceWasUploaded($instructor, $data));
            if ($previousFile) {
                $this->imagy->deleteAllFor($previousFile);
                $this->file->destroy($previousFile);
            }
        }
    }

    public function uploadDocuments($user, $data)
    {
        $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        $files = $data['files'] ?? null;
        foreach ($files as $file) {
            $previousFile = $instructor->filesByZone($file['zone'])->first();
            $savedFile = $this->fileService->store($file['file']);
            if (is_string($savedFile)) {
                throw new Exception($savedFile, 409);
            }
            $data['medias_single'] = [
                $file['zone'] => $savedFile->id
            ];
            event(new InstructorLicenceWasUploaded($instructor, $data));
            if ($previousFile) {
                $this->imagy->deleteAllFor($previousFile);
                $this->file->destroy($previousFile);
            }
        }
    }

    public function searchInstructors($data)
    {
        return $this->instructor->searchInstructors($data);
    }

    public function getProfileInfo($instructor)
    {
        $info = new stdClass();
        $info->instructor = $instructor;
        $info->lessons = new stdClass();
        $info->lessons->count = $instructor->lessons()->count();
        $info->lessons->trainee = $instructor->lessons()->distinct()->count('trainee_id');
        $info->lessons->hours = $instructor->lessons->whereNotNull('ended_at')->sum(function ($item) {
            $start_at = Carbon::parse($item->end_at);
            $end_at = Carbon::parse($item->start_at);
            $duration = $start_at->diffInHours($end_at);
            return $duration;
        });
        $info->lessons->today = $instructor->lessons()->whereDate('start_at', today())->get();
        $info->lessons->upcoming = $instructor->lessons()->whereDate('start_at', '>', today())->take(10)->get();
        return $info;
    }

    public function getTopInstructors()
    {
        return $this->instructor->paginate(10);
    }

    public function getCurrentOngoingLesson($instructor)
    {
        return $instructor->lessons()->whereStatus(Lesson::STATUS['inProgress'])->first();
    }

    /**
     * @param $instructor
     * @return void
     */
    private function unverifyInstructor($instructor): void
    {
        $instructor->verified = 0;
        $instructor->save();
    }
}
