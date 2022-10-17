<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PendingLIMailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $mail_object;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_obj)
    {
        $this->mail_object = $mail_obj;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Pending ".$this->mail_object['link_type']." For Acknowledgement")
            ->view('mail.coaching.pending_notification');
    }
}
