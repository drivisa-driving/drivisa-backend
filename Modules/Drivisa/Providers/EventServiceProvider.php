<?php

namespace Modules\Drivisa\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Drivisa\Events\CancelLessonAfterTime;
use Modules\Drivisa\Events\CancelLessonByInstructor;
use Modules\Drivisa\Events\CancelLessonByTrainee;
use Modules\Drivisa\Events\DailyActivityMail;
use Modules\Drivisa\Events\DocumentUploadedEvent;
use Modules\Drivisa\Events\Handlers\CreateInstructorProfile;
use Modules\Drivisa\Events\Handlers\CreateTrainee;
use Modules\Drivisa\Events\InstructorAccountRejected;
use Modules\Drivisa\Events\InstructorAccountVerified;
use Modules\Drivisa\Events\InstructorDocumentApproved;
use Modules\Drivisa\Events\InstructorDocumentRejected;
use Modules\Drivisa\Events\LessonReschedule;
use Modules\Drivisa\Events\NewBuyPackage;
use Modules\Drivisa\Events\NewCarRentalBooked;
use Modules\Drivisa\Events\NewLessonBooked;
use Modules\Drivisa\Events\SendMessage;
use Modules\Drivisa\Events\TraineeAccountRejected;
use Modules\Drivisa\Events\TraineeAccountVerified;
use Modules\Drivisa\Listeners\NotifyAdminAboutDailyActivity;
use Modules\Drivisa\Listeners\NotifyAdminDocumentUploadedListener;
use Modules\Drivisa\Listeners\NotifyInstructorBookingConfirmation;
use Modules\Drivisa\Listeners\NotifyInstructorDocumentApproved;
use Modules\Drivisa\Listeners\NotifyInstructorDocumentRejected;
use Modules\Drivisa\Listeners\NotifyInstructorLessonCancellation;
use Modules\Drivisa\Listeners\NotifyInstructorLessonCancellationAfterTime;
use Modules\Drivisa\Listeners\NotifyInstructorLessonReschedule;
use Modules\Drivisa\Listeners\NotifyInstructorOfAccountRejection;
use Modules\Drivisa\Listeners\NotifyInstructorOfAccountVerified;
use Modules\Drivisa\Listeners\NotifyInstructorOfLessonCancellation;
use Modules\Drivisa\Listeners\NotifyStudentLessonCancellation;
use Modules\Drivisa\Listeners\NotifyTraineeBookingConfirmation;
use Modules\Drivisa\Listeners\NotifyTraineeInstructorLessonCancellation;
use Modules\Drivisa\Listeners\NotifyTraineeLessonCancellationAfterTime;
use Modules\Drivisa\Listeners\NotifyTraineeLessonReschedule;
use Modules\Drivisa\Listeners\NotifyTraineeOfAccountRejection;
use Modules\Drivisa\Listeners\NotifyTraineeOfAccountVerified;
use Modules\Drivisa\Listeners\SendCancelLessonByInstructorAfterTimeReceiptToInstructor;
use Modules\Drivisa\Listeners\SendCancelLessonByInstructorAfterTimeReceiptToTrainee;
use Modules\Drivisa\Listeners\SendCancelLessonByInstructorReceiptToTrainee;
use Modules\Drivisa\Listeners\SendCancelLessonByTraineeReceiptToInstructor;
use Modules\Drivisa\Listeners\SendCancelLessonByTraineeReceiptToTrainee;
use Modules\Drivisa\Listeners\SendMessageToUser;
use Modules\Drivisa\Listeners\SendPurchaseReceiptBuyCarRentalToTrainee;
use Modules\Drivisa\Listeners\SendPurchaseReceiptBuyPackageToTrainee;
use Modules\Drivisa\Listeners\SendPurchaseReceiptToTrainee;
use Modules\User\Events\UserHasActivatedAccount;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserHasActivatedAccount::class => [
            CreateInstructorProfile::class,
            CreateTrainee::class
        ],
        InstructorAccountRejected::class => [
            NotifyInstructorOfAccountRejection::class
        ],
        InstructorAccountVerified::class => [
            NotifyInstructorOfAccountVerified::class
        ],
        InstructorDocumentApproved::class => [
            NotifyInstructorDocumentApproved::class
        ],
        InstructorDocumentRejected::class => [
            NotifyInstructorDocumentRejected::class
        ],
        TraineeAccountVerified::class => [
            NotifyTraineeOfAccountVerified::class
        ],
        TraineeAccountRejected::class => [
            NotifyTraineeOfAccountRejection::class
        ],
        DocumentUploadedEvent::class => [
            NotifyAdminDocumentUploadedListener::class
        ],
        NewLessonBooked::class => [
            NotifyInstructorBookingConfirmation::class,
            SendPurchaseReceiptToTrainee::class,
            NotifyTraineeBookingConfirmation::class
        ],
        CancelLessonAfterTime::class => [
            NotifyInstructorLessonCancellationAfterTime::class,
            NotifyTraineeLessonCancellationAfterTime::class,
            SendCancelLessonByInstructorAfterTimeReceiptToTrainee::class,
            SendCancelLessonByInstructorAfterTimeReceiptToInstructor::class,
        ],
        CancelLessonByTrainee::class => [
            NotifyStudentLessonCancellation::class,
            NotifyInstructorLessonCancellation::class,
            SendCancelLessonByTraineeReceiptToTrainee::class,
            SendCancelLessonByTraineeReceiptToInstructor::class,
        ],
        CancelLessonByInstructor::class => [
            NotifyInstructorOfLessonCancellation::class,
            NotifyTraineeInstructorLessonCancellation::class,
            SendCancelLessonByInstructorReceiptToTrainee::class,
        ],
        LessonReschedule::class => [
            NotifyInstructorLessonReschedule::class,
            NotifyTraineeLessonReschedule::class
        ],
        NewBuyPackage::class => [
            SendPurchaseReceiptBuyPackageToTrainee::class
        ],
        NewCarRentalBooked::class => [
            SendPurchaseReceiptBuyCarRentalToTrainee::class
        ],
        DailyActivityMail::class => [
            NotifyAdminAboutDailyActivity::class
        ],
        SendMessage::class => [
            SendMessageToUser::class
        ]
    ];
}
