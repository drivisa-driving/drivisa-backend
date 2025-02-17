<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class DailyActivityMail
{
    use SerializesModels;

    public $data;
    public $lessons_count;

    public function __construct($data,  $lessons_count)
    {
        $this->data = $data;
        $this->lessons_count = $lessons_count;
    }
}
