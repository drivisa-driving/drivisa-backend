<?php

namespace Modules\Drivisa\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Drivisa\Entities\Trainee;

class TraineeAccountVerified
{
    use SerializesModels;

    /**
     * @var Trainee
     */
    public $trainee;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Trainee $trainee, $message)
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
