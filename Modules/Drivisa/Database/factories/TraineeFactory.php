<?php

namespace Modules\Drivisa\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Drivisa\Entities\Trainee;
use Modules\User\Entities\Sentinel\User;

class TraineeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\Trainee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'no' => time(),
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
        ];
    }

    public function user($user_id)
    {
        return $this->state(function (array $attributes) use ($user_id) {
            return [
                'user_id' => $user_id,
            ];
        });
    }

    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'verified' => 1,
                'verified_at' => now(),
            ];
        });
    }

    public function pendingKycVerification()
    {
        return $this->state(function (array $attributes) {
            return [
                'kyc_verification' => Trainee::KYC['pending'],
            ];
        });
    }

    public function rejectedKycVerification()
    {
        return $this->state(function (array $attributes) {
            return [
                'kyc_verification' => Trainee::KYC['Rejected'],
            ];
        });
    }

    public function inProgressKycVerification()
    {
        return $this->state(function (array $attributes) {
            return [
                'kyc_verification' => Trainee::KYC['InProgress'],
            ];
        });
    }

    public function approvedKycVerification()
    {
        return $this->state(function (array $attributes) {
            return [
                'kyc_verification' => Trainee::KYC['Approved'],
            ];
        });
    }
}

