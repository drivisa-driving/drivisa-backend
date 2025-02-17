<?php

namespace App\Console;

use App\Jobs\SendDailyActivityMailToAdminJob;
use App\Jobs\MakeRentalRequestAvailableJob;
use App\Jobs\SendNotificationToInstructorForEndLessonJob;
use App\Jobs\SendNotificationToInstructorForStartLessonJob;
use App\Jobs\SendNotificationToUserAboutLesson;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Drivisa\Notifications\SendLessonIsAboutToStartNotification;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SyncInstructorEarnings::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new SendNotificationToUserAboutLesson())
            ->everyMinute();

        $schedule->job(new SendNotificationToInstructorForStartLessonJob())
            ->everyTenMinutes();

        $schedule->job(new SendNotificationToInstructorForEndLessonJob())
            ->everyTenMinutes();

        $schedule->job(new SendDailyActivityMailToAdminJob())
            ->dailyAt('23:00')
            ->between('23:00', '23:59');

        // $schedule->command('telescope:prune --hours=48')->daily();

        $schedule->job(new \App\Jobs\ClearTelescopeEntriesJob())->dailyAt('23:59');

        $schedule->job(new MakeRentalRequestAvailableJob())
            ->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
