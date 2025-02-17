<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class InstructorAccountRejected
{
    use SerializesModels;

    public $instructor;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($instructor, $message)
    {
        $this->instructor = $instructor;
        $this->message = $message;
    }
}
