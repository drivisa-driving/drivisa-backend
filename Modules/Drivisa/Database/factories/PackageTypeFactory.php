<?php

namespace Modules\Drivisa\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Drivisa\Entities\Package;

class PackageTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\PackageType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Test Package',
            'instructor' => 50.00,
            'drivisa' => 5.00,
            'pdio' => 0.00,
            'mto' => 0.00,
            'instructor_cancel_fee' => 0.00,
            'drivisa_cancel_fee' => 0.00,
            'pdio_cancel_fee' => 0.00,
            'mto_cancel_fee' => 0.00,
        ];
    }
}

