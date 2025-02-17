<?php


namespace Modules\Drivisa\Events\Handlers;

use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Events\UserHasActivatedAccount;
use Stripe\Customer;
use Stripe\Stripe;

class CreateTrainee
{
    private $trainee;

    public function __construct(TraineeRepository $trainee)
    {
        $this->trainee = $trainee;
    }

    public function handle(UserHasActivatedAccount $event)
    {
        $user = $event->user;
        if ($user->user_type == User::USER_TYPES['trainee']) {
            $this->trainee->create([
                'no' => $this->trainee->generateNo(),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_id' => $user->id,
                'stripe_customer_id' => $this->getCustomerIdFromStripe($user)->id,
            ]);
        }
    }

    private function getCustomerIdFromStripe($user)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        return Customer::create([
            'email' => $user->email,
            'name' => $user->first_name,
        ]);
    }
}