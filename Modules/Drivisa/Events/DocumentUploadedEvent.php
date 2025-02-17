<?php

namespace Modules\Drivisa\Events;

use Modules\User\Entities\Sentinel\User;
use Modules\Drivisa\Entities\Instructor;
use Illuminate\Queue\SerializesModels;

class DocumentUploadedEvent
{
    use SerializesModels;

    public User $user;

    public Instructor $instructor;

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct(User $user, Instructor $instructor)
    {

        $this->user = $user;
        $this->instructor = $instructor;
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
