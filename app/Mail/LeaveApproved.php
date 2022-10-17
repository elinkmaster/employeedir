<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\LeaveRequest;

class LeaveApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $leave_request;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(LeaveRequest $leave_request)
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
        return $this->subject("Leave Request Approved ". uniqid())
                ->view('mail.leave.approved');
    }
}