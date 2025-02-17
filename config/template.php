<?php

return [
    'postmark' => [
        // authentication mail template
        'welcome_mail' => 27351334,
        'account_confirmation' => 27348209,
        'password_reset' => 27349612,

        'change_user_password' => 29668789,

        //instructor account and document mail template
        'instructor_document_approved' => 27353170,
        'instructor_document_rejected' => 27353067,
        'instructor_account_approved' => 27382305,
        'instructor_account_rejected' => 27382306,
        'instructor_document_upload' => 29837432,

        // booking mail template
        'student_lesson_confirmation' => 27349617,
        'student_purchase_receipt' => 27350883,
        'instructor_lesson_booking' => 27351310,
        'student_package_purchase_receipt' => 28336029,
        'student_car_rental_purchase_receipt' => 28336610,

        // booking cancellation
        'student_lesson_cancellation' => 27349620,
        'instructor_lesson_cancellation' => 27402281,
        'instructor_cancel_lesson_after_time' => 28289468,
        'instructor_cancel_lesson_after_time_trainee_notify' => 28289469,
        'instructor_cancel_lesson_trainee_notify' => 28289012,
        'instructor_cancel_lesson_confirmation' => 28289013,

        // cancel lesson receipt
        'student_cancel_lesson_receipt' => 28886328,
        'instructor_cancel_lesson_receipt_to_trainee' => 28894252,
        'cancel_lesson_receipt_to_instructor' => 28975307,

        // lesson completed feedback template
        'student_lesson_feedback' => 27351309,

        // lesson completed receipt
        'instructor_receipt_after_lesson_complete' => 28973399,

        // Lesson Reschedule 
        'lesson_rescheduled_trainee_notify' => 28289948,
        'lesson_rescheduled_instructor_notify' => 28289494,

        // daily activity mail to admin
        'daily_activity_mail_to_admin' => 30171869,

        // send message to user
        'send_message_to_user' => 31154857,

    ],


    'sendgrid' => [
        // authentication mail template
        'welcome_mail' => 'd-3c9b0f6e5ce14db592b9fd910c06e43a',
        'account_confirmation' => 'd-7c9eca17262141b6a98f7cce6ab502b1',
        'password_reset' => 'd-00dfdb60d32343e3af05cd8e60b4d1b8',

        //instructor account and document mail template
        'instructor_document_approved' => 'd-78f09b8db46849a4bf20bf90279e9991',
        'instructor_document_rejected' => 'd-3e58d272a0884df2ae3730bb1a3e01f2',
        'instructor_account_approved' => 'd-1bb4c84f4fca45268b9ddd757ee73217',
        'instructor_account_rejected' => 'd-76375e714f004e0c98037e3e92dfca3a',

        // booking mail template
        'student_lesson_confirmation' => 'd-5f9be44b3252412aa1d589bfe736d1ca',
        'student_purchase_receipt' => 'd-bd82b6fafa034568ba19aa12ff3bedb7',
        'instructor_lesson_booking' => 'd-c8718c9e96134c6383d5511ba6ca7c71',

        // booking cancellation
        'student_lesson_cancellation' => 'd-a3850b49ac064ed7b23215c6044de449',
        'instructor_lesson_cancellation' => 'd-8737831ba1b6451eba9e153d92b71aa0',

        // lesson completed feedback template
        'student_lesson_feedback' => 'd-5ffb4feda1d84b9088f2966662d4edce',
    ]
];
