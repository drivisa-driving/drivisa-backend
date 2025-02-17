<?php


namespace Modules\Drivisa\Events;


use Modules\Drivisa\Entities\Trainee;
use Modules\Media\Contracts\StoringMedia;

class TraineeLicenceWasUploaded implements StoringMedia
{
    public $data;

    public $trainee;

    public function __construct(Trainee $trainee, array $data)
    {
        $this->data = $data;
        $this->trainee = $trainee;
    }

    public function getEntity()
    {
        return $this->trainee;
    }

    public function getSubmissionData()
    {
        return $this->data;
    }
}