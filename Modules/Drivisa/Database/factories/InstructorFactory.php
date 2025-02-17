<?php

namespace Modules\Drivisa\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Drivisa\Entities\Instructor;
use Modules\User\Entities\Sentinel\User;

class InstructorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Instructor::class;

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
            'kyc_verification' => Instructor::KYC['Approved'],
            'promotion_level' => 0,
            'signed_agreement' => 1,
            'di_number' => random_int(100000, 999999),
            'di_end_date' => now()->addYears(5),
            'signed_at' => now(),
            'user_id' => User::factory()->instructor()->create()->id
        ];
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
                'kyc_verification' => Instructor::KYC['pending'],
            ];
        });
    }

    public function rejectedKycVerification()
    {
        return $this->state(function (array $attributes) {
            return [
                'kyc_verification' => Instructor::KYC['Rejected'],
            ];
        });
    }

    public function inProgressKycVerification()
    {
        return $this->state(function (array $attributes) {
            return [
                'kyc_verification' => Instructor::KYC['InProgress'],
            ];
        });
    }

    public function approvedKycVerification()
    {
        return $this->state(function (array $attributes) {
            return [
                'kyc_verification' => Instructor::KYC['Approved'],
            ];
        });
    }
}

