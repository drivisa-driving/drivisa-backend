<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Drivisa\Entities\Lesson;
use App\Notifications\OneSignalChannel;
use Berkayk\OneSignal\OneSignalFacade;
use Carbon\Carbon;

class InstructorTraineeLessonRescheduledNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public Lesson $lesson,
        public        $workingHour,
        public        $oldWorkingHour
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [OneSignalChannel::class, 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toOneSignal($notifiable)
    {
        list($lesson_type, $trainee_name, $old_training_date_time, $new_training_date_time) = $this->getLessonInformation();
        $message = $this->getMessage($lesson_type, $trainee_name, $old_training_date_time, $new_training_date_time);

        if ($notifiable->player_id == null) return;
        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Lesson Rescheduled');
        OneSignalFacade::sendNotificationToUser($message, $notifiable->player_id);
        NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee',
            $notifiable->player_id, $message,'Lesson Rescheduled');
    }

    public function toArray($notifiable)
    {
        list($lesson_type, $trainee_name, $old_training_date_time, $new_training_date_time) = $this->getLessonInformation();
        $avatar = $this->lesson->trainee->user->present()->gravatar();
        $message = $this->getMessage($lesson_type, $trainee_name, $old_training_date_time, $new_training_date_time);

        return [
            'message' => $message,
            'avatar' => $avatar
        ];
    }

    /**
     * @return array
     */
    private function getLessonInformation(): array
    {
        $lesson_type = ucwords(str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE)));
        $lesson_type = $lesson_type === 'Driving' ? "Lesson" : strtoupper($lesson_type);
        $trainee_name = $this->lesson->trainee->full_name;


        $old_training_date_time = Carbon::parse($this->oldWorkingHour->workingDay->date)->format('M d Y') . ' ' . Carbon::parse($this->oldWorkingHour->open_at)->format('h:i A');
        $new_training_date_time = Carbon::parse($this->workingHour->workingDay->date)->format('M d Y') . ' ' . Carbon::parse($this->workingHour->open_at)->format('h:i A');
        return array($lesson_type, $trainee_name, $old_training_date_time, $new_training_date_time);
    }

    private function getMessage($lesson_type, $trainee_name, $old_training_date_time, $new_training_date_time)
    {
        $isInstructorChanged = $this->workingHour->workingDay->instructor_id !== $this->oldWorkingHour->workingDay->instructor_id;

        if ($isInstructorChanged) {
            return "$lesson_type at $old_training_date_time has been rescheduled by $trainee_name to another instructor";
        } else {
            return "$lesson_type at $old_training_date_time is rescheduled by $trainee_name to $new_training_date_time";
        }
    }
}
