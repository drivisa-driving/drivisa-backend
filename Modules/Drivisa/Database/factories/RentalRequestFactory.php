<?php

namespace Modules\Drivisa\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Drivisa\Entities\RentalRequest;

class RentalRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\RentalRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'package_id' => 1,
            'location' => '381 Select Dr #1-5, Kingston, ON K7M 8R1, Canada',
            'latitude' => '44.2564394',
            'longitude' => '-76.5643867',
            'booking_date' => '2022-06-22',
            'booking_time' => '16:00:00',
            'status' => RentalRequest::STATUS['registered'],
            'type' => RentalRequest::TYPE['g_test'],
        ];
    }
}

