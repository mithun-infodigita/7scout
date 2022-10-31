<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockUpdateFailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;
    public $producer;

    public function __construct($producer, $exception)
    {
        $this->exception = $exception;
        $this->producer = $producer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@seventools.com')
            ->markdown('emails.stockUpdateFailMail')
            ->subject('Job update fail')
            ->with([
                'producer' => $this->producer,
                'exception' =>  $this->exception
            ]);
    }
}
