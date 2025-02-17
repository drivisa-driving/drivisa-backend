<?php


namespace Modules\Drivisa\Events;


use Modules\Drivisa\Entities\Instructor;
use Modules\Media\Contracts\StoringMedia;

class InstructorLicenceWasUploaded implements StoringMedia
{
    public $data;

    public $instructor;

    public function __construct(Instructor $instructor, array $data)
    {
        $this->data = $data;
        $this->instructor = $instructor;
    }

    public function getEntity()
    {
        return $this->instructor;
    }

    public function getSubmissionData()
    {
        return $this->data;
    }
}