<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class LessonReschedule
{
    use SerializesModels;

    public $trainee;
    public $lesson;

    public function __construct($trainee, $lesson)
    {
        $this->trainee = $trainee;
        $this->lesson = $lesson;
    }
}
