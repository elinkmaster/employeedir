<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ACCMailNotification extends Mailable
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
        return $this->subject("[".crypt($this->mail_object['hash'],'CE')."] Accountability Session - ".date("F d, Y"))
            ->view('mail.coaching.acc_notification');
    }
}
