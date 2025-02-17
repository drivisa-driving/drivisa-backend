<?php


namespace Modules\Drivisa\Events\Handlers;

use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Events\UserHasActivatedAccount;

class CreateInstructorProfile
{
    /**
     * @var InstructorRepository
     */
    private $instructor;

    public function __construct(InstructorRepository $instructor)
    {
        $this->instructor = $instructor;
    }

    public function handle(UserHasActivatedAccount $event)
    {
        $user = $event->user;
        if ($user->user_type == User::USER_TYPES['instructor']) {
            $this->instructor->create([
                'no' => $this->instructor->generateNo(),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_id' => $user->id,
            ]);
        }
    }
}