<?php

namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class AccountActivated
{
    use SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
