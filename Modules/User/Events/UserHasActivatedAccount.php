<?php

namespace Modules\User\Events;

use Modules\User\Entities\Sentinel\User;

class UserHasActivatedAccount
{
    /**
     * @var User
     */
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
