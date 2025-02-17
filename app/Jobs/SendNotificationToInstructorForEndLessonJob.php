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
use Modules\Drivisa\Notifications\SendLessonNotEndNotification;
use Modules\Drivisa\Notifications\SendLessonNotStartedNotification;

class SendNotificationToInstructorForEndLessonJob implements ShouldQueue
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
        $lessons = $this->getAllLessonForToday();

        foreach ($lessons as $lesson) {
            // 2. check the time already passed

            $lesson_duration = ($lesson->duration * 60) + 10;

            $end_time = Carbon::parse($lesson->started_at)->copy()->addMinutes($lesson_duration);

            if (now()->greaterThanOrEqualTo($end_time)) {
                // 3. send notification
                $user = $lesson->instructor->user;
                $user->notify(new SendLessonNotEndNotification($lesson));

                $lesson->lesson_end_notification_sent_at = now();
                $lesson->lesson_end_notification_count = $lesson->lesson_end_notification_count + 1;
                $lesson->save();
            }
        }
    }


    // get all lessons
    public function getAllLessonForToday()
    {
        return Lesson::whereDate('start_at', today())
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->where('lesson_end_notification_count', '<', 3)
            ->get();
    }
}
