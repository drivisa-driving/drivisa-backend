<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class CancelLesson
{
    use SerializesModels;

    public $lesson;

    public function __construct($lesson)
    {
        $this->lesson = $lesson;
    }
}
