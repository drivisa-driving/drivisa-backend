<?php

namespace Modules\Drivisa\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Drivisa\Entities\Instructor;

class PointFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\Point::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'source_name' => 'test',
            'destination_name' => 'test',
            'source_address' => 'Vaughan Mills, Bass Pro Mills Drive, Vaughan, ON, Canada',
            'destination_address' => 'Vaughan Mills, Bass Pro Mills Drive, Vaughan, ON, Canada',
            'source_latitude' => 43.8253305,
            'source_longitude' => -79.5381769,
            'destination_latitude' => 43.8253305,
            'destination_longitude' => -79.5381769,
            'is_active' => 1,
            'instructor_id' => Instructor::factory()->create()->id
        ];
    }
}
