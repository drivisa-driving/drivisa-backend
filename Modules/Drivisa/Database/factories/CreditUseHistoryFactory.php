<?php

namespace Modules\Drivisa\Database\factories;

use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Entities\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditUseHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Drivisa\Entities\CreditUseHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $lesson = $this->createLesson();

        return [
            'course_id' => Course::factory()->create()->id,
            'lesson_id' => $lesson->id,
            'used_at' => now(),
            'credit_used' => $lesson->duration
        ];
    }

    public function createLesson()
    {
        $traineeUser = \Modules\User\Entities\Sentinel\User::factory()->trainee()->create();
        $instructorUser = \Modules\User\Entities\Sentinel\User::factory()->instructor()->create();

        $trainee = Trainee::factory()
            ->verified()
            ->approvedKycVerification()
            ->user($traineeUser->id)
            ->create();

        $instructor = Instructor::factory()->create([
            'user_id' => $instructorUser->id,
            'promotion_level' => 1
        ]);

        return Lesson::factory()
            ->drivingLesson()
            ->create([
                'trainee_id' => $trainee->id,
                'instructor_id' => $instructor->id,
                'created_by' => $traineeUser->id,
            ]);
    }
}
