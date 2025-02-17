<?php

namespace Modules\Drivisa\Database\factories;

use Modules\Drivisa\Entities\Point;
use Modules\Drivisa\Entities\WorkingDay;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkingHourFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\WorkingHour::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => 1,
            'open_at' => now()->addMinutes(10),
            'close_at' => now()->addMinutes(70),
            'working_day_id' => WorkingDay::factory()->create()->id,
            'point_id' => Point::factory()->create()->id
        ];
    }
}
