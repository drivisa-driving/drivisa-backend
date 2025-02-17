<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class SendMessage
{
    use SerializesModels;

    public $trainee;
    public $message;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($trainee, $message)
    {
        $this->trainee = $trainee;
        $this->message = $message;
    }
}
