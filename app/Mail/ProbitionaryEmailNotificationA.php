<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProbitionaryEmailNotificationA extends Mailable
{
    use Queueable, SerializesModels;

    public $obj_details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($obj)
    {
        $this->obj_details = $obj;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Upcoming Third Month Evaluation for: ".$this->obj_details['emp_name']." - ".date("M d, Y"))
                ->view('mail.employee.probi_notification_a');
    }
}
