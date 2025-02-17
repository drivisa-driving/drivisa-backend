<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Notifications\SendLessonNotStartedNotification;

class SendNotificationToInstructorForStartLessonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //1. get all today lesson which is not started yet

        $lessons = $this->getAllLessonForToday();

        foreach ($lessons as $lesson) {
            // 2. check the time already passed

            if (now()->greaterThan(Carbon::parse($lesson->start_at)->copy()->addMinutes(10))) {
                // 3. send notification
                $user = $lesson->instructor->user;
                $user->notify(new SendLessonNotStartedNotification($lesson));
            }

        }
    }

    // get all lessons
    public function getAllLessonForToday()
    {
        $current_time = now()->format('H:i:s');
        return Lesson::whereDate('start_at', today())
            ->whereIn('status', [Lesson::STATUS['reserved']])
            ->whereNull('started_at')
            ->whereTime('start_at', '<=', $current_time) // 1200 > 1300
            ->whereTime('end_at', '>=', $current_time) // 1400 < 1300
            ->get();
    }
}
