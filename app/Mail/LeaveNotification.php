<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\LeaveRequest;

class LeaveNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leave_request;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($leave_request)
    {
        $this->leave_request = $leave_request;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Leave Request - ".$this->leave_request['leave']->employee->first_name. " ".$this->leave_request['leave']->employee->last_name." ". uniqid())
                ->view('mail.leave.request');
    }
}
