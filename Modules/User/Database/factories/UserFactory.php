<?php

namespace Modules\User\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Entities\Sentinel\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\User\Entities\Sentinel\User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'password' => bcrypt('password'),
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'address' => "TEST address",
            'phone_number' => '+919812447202',
            'city' => 'Test City',
            'postal_code' => '123456',
            'province' => 'Test',
            'street' => 'Test',
            'unit_no' => 'Test',
            'username' => $this->faker->userName(),
            'user_type' => User::USER_TYPES['instructor']
        ];
    }

    public function trainee()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => User::USER_TYPES['trainee'],
            ];
        });
    }

    public function instructor()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => User::USER_TYPES['instructor'],
            ];
        });
    }

}

