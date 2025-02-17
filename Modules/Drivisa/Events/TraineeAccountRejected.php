<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;

class TraineeAccountRejected
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

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
