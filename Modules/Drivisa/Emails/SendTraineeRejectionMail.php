<?php

namespace Modules\Drivisa\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTraineeRejectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainee;
    public $msg;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($trainee, $msg)
    {
        //
        $this->trainee = $trainee;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('drivisa::emails.trainee-rejected');
    }
}
