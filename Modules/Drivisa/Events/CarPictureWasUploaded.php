<?php


namespace Modules\Drivisa\Events;


use Modules\Drivisa\Entities\Car;
use Modules\Media\Contracts\StoringMedia;

class CarPictureWasUploaded implements StoringMedia
{
    public $data;

    public $car;

    public function __construct(Car $car, array $data)
    {
        $this->data = $data;
        $this->car = $car;
    }

    public function getEntity()
    {
        return $this->car;
    }

    public function getSubmissionData()
    {
        return $this->data;
    }
}