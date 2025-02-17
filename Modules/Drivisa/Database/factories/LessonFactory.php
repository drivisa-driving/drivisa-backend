<?php

namespace Modules\Drivisa\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Trainee;

class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'no' => time(),
            'start_at' => now()->addMinutes(10),
            'start_time' => now()->addMinutes(10)->format('H:i:s'),
            'end_at' => now()->addMinutes(70),
            'status' => Lesson::STATUS['inProgress'],
        ];
    }

    public function drivingLesson()
    {
        return $this->state(function (array $attributes) {
            return [
                'lesson_type' => Lesson::TYPE['driving'],
                'cost' => 60.00,
                'commission' => 5.00,
                'net_amount' => 55.00,
                'tax' => 5.2,
                'paid_at' => now(),
            ];
        });
    }

    public function bdeLesson()
    {
        return $this->state(function (array $attributes) {
            return [
                'lesson_type' => Lesson::TYPE['bde'],
                'cost' => 60.00,
                'commission' => 5.00,
                'net_amount' => 55.00,
                'tax' => 5.2,
                'paid_at' => now(),
            ];
        });
    }

    public function g2TestLesson()
    {
        return $this->state(function (array $attributes) {
            return [
                'lesson_type' => Lesson::TYPE['g2_test'],
                'cost' => 125.00,
                'commission' => 25.00,
                'net_amount' => 116.25,
                'tax' => 16.25,
                'paid_at' => now(),
            ];
        });
    }

    public function gTestLesson()
    {
        return $this->state(function (array $attributes) {
            return [
                'lesson_type' => Lesson::TYPE['g_test'],
                'cost' => 140.00,
                'commission' => 30.00,
                'net_amount' => 128.20,
                'tax' => 18.20,
                'paid_at' => now(),
            ];
        });
    }
}

