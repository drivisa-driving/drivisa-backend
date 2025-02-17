<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Events\DailyActivityMail;
use Modules\Drivisa\Transformers\LessonTransformer;

class SendDailyActivityMailToAdminJob implements ShouldQueue
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
     * @param $data
     * @return void
     */
    private function allEventVariablesForDailyActivityMail($data, $lessons_count): void
    {
        $eventVariables = [
            $data,
            $lessons_count
        ];

        event(new DailyActivityMail(...$eventVariables));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // get all today bde lessons
        $data = $this->getTodayLessons();
        $lessons_count = $this->getTodayLessonsCount();

        if ($lessons_count > 0) {
            $this->allEventVariablesForDailyActivityMail($data, $lessons_count);
        }
    }

    private function getTodayLessons()
    {
        return Lesson::whereDate('start_at', today())
            ->whereIn('lesson_type', [Lesson::TYPE['bde']])
            ->orderByRaw('DATE(start_at)')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    private function getTodayLessonsCount()
    {
        return Lesson::whereDate('start_at', today())
            ->where('lesson_type', Lesson::TYPE['bde'])
            ->count();
    }
}
