<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Notifications\SendLessonIsAboutToStartNotification;
use Modules\Drivisa\Repositories\LessonRepository;
use Modules\Drivisa\Services\LogService;

class SendNotificationToUserAboutLesson implements ShouldQueue
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
        // 1. get all today lessons

        $lessons = $this->getAllLessonForToday();

        foreach ($lessons as $lesson) {
            // 2. Is notification sent
            if ($lesson->isNotificationSent()) continue;

            // 3. Check lesson is under 30 min
            if (!$lesson->lessonUnder30Minutes()) continue;

            // 4. Send Notification to user
            $this->sendNotificationToUser($lesson);

            // 5. Update into lessons table
            $lesson->is_notification_sent = true;
            $lesson->notification_sent_at = now();
            $lesson->save();
        }
    }

    // get all lessons
    private function getAllLessonForToday()
    {
        return Lesson::
            whereIn('status', [Lesson::STATUS['reserved']])->whereDate('start_at',Carbon::today())
            ->get();
    }

    private function sendNotificationToUser($lesson)
    {
        $trainee = $lesson->trainee->user;
        $instructor = $lesson->instructor->user;

        $trainee->notify(new SendLessonIsAboutToStartNotification($lesson,'Trainee'));

        $instructor->notify(new SendLessonIsAboutToStartNotification($lesson,'Instructor'));

    }
}
