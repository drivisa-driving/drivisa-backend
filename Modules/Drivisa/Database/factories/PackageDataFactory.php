<?php

namespace Modules\Drivisa\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Drivisa\Entities\Package;

class PackageDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\PackageData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'hours' => 10,
            'hour_charge' => 12.00,
            'amount' => 120.00,
            //'package_id' => Package::factory()->create()->id
        ];
    }
}

