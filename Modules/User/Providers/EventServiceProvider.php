<?php

namespace Modules\User\Providers;

use Modules\User\Events\UserHasRegistered;
use Modules\User\Events\UserHasBegunResetProcess;
use Modules\User\Events\Handlers\SendResetCodeEmail;
use Modules\User\Events\Handlers\SendRegistrationConfirmationEmail;
use Modules\User\Events\Handlers\SendAccountActivationEmail;
use Modules\User\Events\Handlers\ChangeUserPasswordEmail;
use Modules\User\Events\ChangeUserPassword;
use Modules\User\Events\AccountActivated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserHasRegistered::class => [
            SendRegistrationConfirmationEmail::class,
        ],
        UserHasBegunResetProcess::class => [
            SendResetCodeEmail::class,
        ],
        AccountActivated::class => [
            SendAccountActivationEmail::class
        ],
        ChangeUserPassword::class => [
            ChangeUserPasswordEmail::class
        ]
    ];
}
