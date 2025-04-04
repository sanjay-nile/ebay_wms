<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DefaultMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $array;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->array = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->array['subject'])
             ->from($this->array['from_email'], $this->array['site_title'])
             ->view($this->array['view'])
             ->with($this->array);
    }
}
