<?php


namespace Modules\Drivisa\Services;


use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Events\CarPictureWasUploaded;
use Modules\Drivisa\Exceptions\InstructorNotFoundException;
use Modules\Drivisa\Repositories\CarRepository;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;

class CarService
{
    protected $carRepository;
    protected $instructorRepository;
    private $fileService;
    private $imagy;
    private $file;
    public function __construct(CarRepository $carRepository,
                                FileService $fileService,
                                Imagy $imagy,
                                FileRepository $file,
                                InstructorRepository $instructorRepository)
    {
        $this->carRepository = $carRepository;
        $this->instructorRepository = $instructorRepository;
        $this->fileService = $fileService;
        $this->imagy = $imagy;
        $this->file = $file;

    }

    public function getCars($instructor)
    {
        return $instructor->cars;
    }

    public function getCar($instructor, $car)
    {
        if ($instructor->id != $car->instructor_id) {
            throw new AuthorizationException();
        }
        return $car;
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

    public function addCar($instructor, $data)
    {
        $car = $instructor->cars()->create($data);
        if ($data['picture'] ?? null) {
            $savedFile = $this->fileService->store($data['picture']);
            if (is_string($savedFile)) {
                throw new Exception($savedFile, 409);
            }
            $data['medias_single'] = [
                'car_picture' => $savedFile->id
            ];
            event(new CarPictureWasUploaded($car, $data));
        }
    }

    public function deleteCar($instructor, $car)
    {
        if ($instructor->id != $car->instructor_id) {
            throw new AuthorizationException();
        }
        return $this->carRepository->destroy($car);
    }

    public function updateCar($instructor, $car, $data)
    {
        if ($instructor === null) {
            throw new InstructorNotFoundException(trans('drivisa::drivisa.messages.instructor_not_found'));
        }
        if ($instructor->id != $car->instructor_id) {
            throw new AuthorizationException();
        }
        $this->carRepository->update($car, $data);
        if ($data['picture'] ?? null) {
            $previousFile = $car->files()->first();
            $savedFile = $this->fileService->store($data['picture']);
            if (is_string($savedFile)) {
                throw new Exception($savedFile, 409);
            }
            $data['medias_single'] = [
                'car_picture' => $savedFile->id
            ];
            event(new CarPictureWasUploaded($car, $data));
            if ($previousFile) {
                $this->imagy->deleteAllFor($previousFile);
                $this->file->destroy($previousFile);
            }

        }
    }

    public function getCarMaker(){
       return $this->carRepository->allWithBuilder()->distinct()->get(['make']);
    }
}