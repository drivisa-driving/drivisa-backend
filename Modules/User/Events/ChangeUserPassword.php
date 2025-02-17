<?php

namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class ChangeUserPassword
{
    use SerializesModels;

    public $user;
    public $password;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }
}
