<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailResetPasswordNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $client_user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token, $client_user = null)
    {
        $this->user = $user;
        $this->token = $token;
        $this->client_user = $client_user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.resetPasswordToken')->subject('ShipCycle: Reset Password Email');
    }
}
