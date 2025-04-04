<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MainTemplate extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(isset($this->data['attach_pdf']) && $this->data['attach_pdf'] != ''){
            return $this->view($this->data['view'])->attach($this->data['attach_pdf'], [
                        'as' => $this->data['pdf_filename'],
                        'mime' => 'application/pdf',
                    ])->subject($this->data['subject'])->with($this->data);
        } elseif (isset($this->data['attach_file']) && $this->data['attach_file'] != '') {
            return $this->view($this->data['view'])->attach($this->data['attach_file'], [
                        'as' => $this->data['pdf_filename'],
                        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])->subject($this->data['subject'])->with($this->data);
        } else {
            return $this->view($this->data['view'])
                    ->subject($this->data['subject'])
                    ->with($this->data);
        }        
        /*return $this->subject($this->data['subject'])
             // ->from($this->data['from_email'], $this->data['site_title'])
             ->view($this->data['view'])
             ->with($this->data);*/
    }
}
