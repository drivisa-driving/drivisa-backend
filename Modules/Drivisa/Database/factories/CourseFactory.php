<?php

namespace Modules\Drivisa\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Drivisa\Entities\Course;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'package_id' => 1,
            'user_id' => \Modules\User\Entities\Sentinel\User::factory()->trainee()->create()->id,
            'status' => Course::STATUS['progress'],
            'credit' => 10,
            'payment_intent_id' => "pi_3MYr9NBt0VLpt9dF3lfZBk0b",
            'transaction_id' => "txn_3MYr9NBt0VLpt9dF3wmKISDP",
            'charge_id' => "ch_3MYr9NBt0VLpt9dF3BCKgT1f",
            'type' => Course::TYPE['Package']
        ];
    }
}
