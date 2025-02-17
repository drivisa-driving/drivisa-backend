<?php

namespace Modules\Drivisa\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Drivisa\Transformers\LessonTransformer;

class SendDailyActivityMailAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $lessons_count;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $lessons_count)
    {
        $this->data = $data;
        $this->lessons_count = $lessons_count;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Today's activity on Drivisa")
            ->view(
                'drivisa::emails.daily-activity-mail',
                [
                    'data' => $this->data,
                    'lessons_count' => $this->lessons_count
                ]
            );
    }
}
