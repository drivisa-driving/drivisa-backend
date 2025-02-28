<?php

namespace Modules\User\Repositories\Sentinel;

use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Users\UserInterface;
use Exception;
use Modules\User\Contracts\Authentication;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Events\UserHasActivatedAccount;
use Modules\User\Events\UserHasRegistered;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Modules\User\Repositories\UserTokenRepository;

class SentinelAuthentication implements Authentication
{
    private $userTokenRepository;

    public function __construct(UserTokenRepository $userTokenRepository)
    {
        $this->userTokenRepository = $userTokenRepository;
    }

    /**
     * Authenticate a user
     * @param array $credentials
     * @param bool $remember Remember the user
     * @return mixed
     */
    public function login(array $credentials, $remember = false)
    {
        try {
            $user = Sentinel::authenticate($credentials, $remember);
            if ($user) {
                $this->userTokenRepository->generateFor($user->id);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Register a new user.
     * @param array $user
     * @return bool
     */
    public function register(array $user)
    {
        $newUser = Sentinel::getUserRepository()->create((array)$user);

        if (isset($user['from_hear'])) {
            $newUser->from_hear = $user['from_hear'];
            $newUser->save();
        }

        if (isset($user['hear_from'])) {
            $newUser->from_hear = $user['hear_from'];
            $newUser->save();
        }

        return $newUser;
    }

    /**
     * Assign a role to the given user.
     * @param UserRepository $user
     * @param RoleRepository $role
     * @return mixed
     */
    public function assignRole($user, $role)
    {
        return $role->users()->attach($user);
    }

    /**
     * Log the user out of the application.
     * @return bool
     */
    public function logout()
    {
        return Sentinel::logout();
    }

    /**
     * Activate the given used id
     * @param int $userId
     * @param string $code
     * @return mixed
     */
    public function activate($userId, $code)
    {
        $user = Sentinel::findById($userId);

        $success = Activation::complete($user, $code);
        if ($success) {
            event(new UserHasActivatedAccount($user));
        }
        return $success;
    }

    /**
     * Create an activation code for the given user
     * @param UserRepository $user
     * @return mixed
     */
    public function createActivation($user)
    {
        $activation = Activation::create($user);
        $code = substr(str_shuffle("0123456789"), 0, 6);
        $isCodeExists = Activation::Where('code', $code)->exists();
        while ($isCodeExists) {
            $code = substr(str_shuffle("0123456789"), 0, 6);
            $isCodeExists = Activation::Where('code', $code)->exists();
        }
        $activation->update(['code' => $code]);
        return $activation->code;
    }

    /**
     * Create a reminders code for the given user
     * @param UserRepository $user
     * @return mixed
     */
    public function createReminderCode($user)
    {
        $reminder = null;
        $isReminderExists = Reminder::exists($user);
        if ($isReminderExists) {
            $reminder = Reminder::get($user);
        } else {
            $reminder = Reminder::create($user);
            $code = substr(str_shuffle("0123456789"), 0, 6);
            $isCodeExists = Reminder::Where('code', $code)->exists();
            while ($isCodeExists) {
                $code = substr(str_shuffle("0123456789"), 0, 6);
                $isCodeExists = Reminder::Where('code', $code)->exists();
            }
            $reminder->update(['code' => $code]);
        }
        return $reminder->code;
    }

    /**
     * Completes the reset password process
     * @param $user
     * @param string $code
     * @param string $password
     * @return bool
     */
    public function completeResetPassword($user, $code, $password)
    {
        return Reminder::complete($user, $code, $password);
    }

    /**
     * Determines if the current user has access to given permission
     * @param $permission
     * @return bool
     */
    public function hasAccess($permission): bool
    {
        if (!Sentinel::check()) {
            return false;
        }

        return Sentinel::hasAccess($permission);
    }

    /**
     * Check if the user is logged in
     * @return bool
     */
    public function check(): bool
    {
        $user = Sentinel::check();

        if ($user) {
            return true;
        }

        return false;
    }

    /**
     * Get the ID for the currently authenticated user
     * @return int
     */
    public function id(): int
    {
        $user = $this->user();

        if ($user === false) {
            return 0;
        }

        return $user->id;
    }

    /**
     * Get the currently logged in user
     * @return \Modules\User\Entities\UserInterface
     */
    public function user()
    {
        return Sentinel::getUser();
    }

    /**
     * Log a user manually in
     * @param UserInterface $user
     * @return UserInterface
     */
    public function logUserIn(UserInterface $user): UserInterface
    {
        return Sentinel::login($user);
    }

    /**
     * Resend activation code
     * @param User $user
     * @return bool
     */
    public function resendActivationCode($user)
    {
        if ($user->isActivated()) {
            return false;
        }
        event(new UserHasRegistered($user));
        return true;
    }
}
