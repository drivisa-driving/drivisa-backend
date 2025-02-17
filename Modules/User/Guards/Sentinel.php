<?php

namespace Modules\User\Guards;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel as SentinelFacade;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard as LaravelGuard;
use Modules\User\Repositories\UserRepository;

class Sentinel implements LaravelGuard
{
    /**
     * Determine if the current user is authenticated.
     * @return bool
     */
    public function check()
    {
        if (SentinelFacade::check()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the current user is a guest.
     * @return bool
     */
    public function guest()
    {
        return SentinelFacade::guest();
    }

    /**
     * Get the currently authenticated user.
     * @return Authenticatable|null
     */
    public function user()
    {
        return SentinelFacade::getUser();
    }

    /**
     * Get the ID for the currently authenticated user.
     * @return int|null
     */
    public function id()
    {
        if ($user = SentinelFacade::check()) {
            return $user->id;
        }

        return null;
    }

    /**
     * Validate a user's credentials.
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return SentinelFacade::validForCreation($credentials);
    }

    /**
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt(array $credentials, $remember = false)
    {
        return SentinelFacade::authenticate($credentials, $remember);
    }

    /**
     * @return bool
     */
    public function logout()
    {
        return SentinelFacade::logout();
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function loginUsingId($userId)
    {
        $user = app(UserRepository::class)->find($userId);

        return $this->login($user);
    }

    /**
     * Alias to set the current user.
     * @param Authenticatable $user
     * @return bool
     */
    public function login(Authenticatable $user)
    {
        return $this->setUser($user);
    }

    /**
     * Set the current user.
     * @param Authenticatable $user
     * @return bool
     */
    public function setUser(Authenticatable $user)
    {
        return SentinelFacade::login($user);
    }
}
