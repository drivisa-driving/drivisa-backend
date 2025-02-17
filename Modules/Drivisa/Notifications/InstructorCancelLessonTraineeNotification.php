<?php

namespace Modules\Drivisa\Notifications;

use App\Jobs\NotificationLogCreate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Modules\Drivisa\Entities\Lesson;
use Berkayk\OneSignal\OneSignalFacade;
use App\Notifications\OneSignalChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Drivisa\Services\OneSignalNotificationService;

class InstructorCancelLessonTraineeNotification extends Notification
{
    use Queueable;

    public Lesson $lesson;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
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
        $training_date = Carbon::parse($this->lesson->start_at)->format('M d, Y');
        $training_time = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $lesson_type = ucwords(str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE)));
        $lesson_type = $lesson_type === 'Driving' ? "Lesson" : strtoupper($lesson_type);
        $instructor_name = $this->lesson->instructor->full_name;

        if ($notifiable->player_id == null) return;

        NotificationLogCreate::dispatch(1,$this->lesson,'notification ready to send','-','','Lesson Cancelled by Instructor');
        if ($lesson_type == "BDE") {
            OneSignalFacade::sendNotificationToUser(
                "$lesson_type lesson on $training_date at $training_time is cancelled by $instructor_name. Hours has been refunded to your account",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee',
                $notifiable->player_id, "$lesson_type lesson on $training_date at $training_time is cancelled by $instructor_name. Hours has been refunded to your account",
                'Lesson Cancelled by Instructor');
        } else {
            OneSignalFacade::sendNotificationToUser(
                "$lesson_type on $training_date at $training_time is cancelled by $instructor_name",
                $notifiable->player_id
            );
            NotificationLogCreate::dispatch(1,$this->lesson, 'notification sent to trainee',
                $notifiable->player_id,  "$lesson_type on $training_date at $training_time is cancelled by $instructor_name",
                'Lesson Cancelled by Instructor');
        }
    }

    public function toArray($notifiable)
    {
        $training_date = Carbon::parse($this->lesson->start_at)->format('M d, Y');
        $training_time = Carbon::parse($this->lesson->start_time)->format('h:i A');
        $lesson_type = ucwords(str_replace('_', " ", array_search($this->lesson->lesson_type, Lesson::TYPE)));
        $lesson_type = $lesson_type === 'Driving' ? "Lesson" : strtoupper($lesson_type);
        $instructor_name = $this->lesson->instructor->full_name;
        $avatar = $this->lesson->instructor->user->present()->gravatar();

        if ($lesson_type == "BDE") {
            return [
                'message' => "$lesson_type lesson on $training_date at $training_time is cancelled by $instructor_name. Hours has been refunded to your account",
                'avatar' => $avatar
            ];
        } else {
            return [
                'message' => "$lesson_type on $training_date at $training_time is cancelled by $instructor_name",
                'avatar' => $avatar
            ];
        }
    }
}
