<?php

namespace Modules\Drivisa\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInstructorRejectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $instructor;
    public $msg;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($instructor, $msg)
    {
        $this->instructor = $instructor;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('drivisa::emails.instructor-rejected');
    }
}
