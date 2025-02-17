<?php

namespace Modules\Drivisa\Services;

use Berkayk\OneSignal\OneSignalFacade;

class OneSignalNotificationService
{
    public static function cancelNotificationById($lesson, $notification_of = 'trainee')
    {
        $notification_type = $notification_of == 'trainee' ? 'trainee_notification_id' : 'instructor_notification_id';
        if ($lesson->{$notification_type} != null) {
            OneSignalFacade::deleteNotification($lesson->{$notification_type});
            $lesson->update([$notification_type => null]);
        }
    }
}