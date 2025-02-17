<?php

namespace Modules\User\Services;

use Carbon\Carbon;
use Modules\User\Contracts\Authentication;
use Modules\User\Events\UserHasRegistered;

class UserRegistration
{
    /**
     * @var Authentication
     */
    private $auth;

    /**
     * @var array
     */
    private $input;

    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param array $input
     * @return mixed
     */
    public function register(array $input)
    {
        $this->input = $input;

        //get username
        $this->input['username'] = $this->generateUsername();

        $user = $this->createUser();
        $user->update(['created_by' => $user->id]);

        event(new UserHasRegistered($user));

        return $user;
    }

    private function generateUsername()
    {
        return strtolower($this->input['first_name']) . '-' . strtolower($this->input['last_name']) . '-' . Carbon::now()->timestamp;
    }

    private function createUser()
    {
        return $this->auth->register((array)$this->input);
    }
}
