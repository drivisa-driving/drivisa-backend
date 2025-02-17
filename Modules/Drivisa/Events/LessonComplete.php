<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class LessonComplete
{
    use SerializesModels;

    public $instructor;
    public $lesson;
    public $amount;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($instructor, $lesson, $amount)
    {
        $this->instructor = $instructor;
        $this->lesson = $lesson;
        $this->amount = $amount;
    }
}
