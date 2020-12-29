<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $user;
    public $payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $product, $payment)
    {
        $this->user = $user;
        $this->product = $product;
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->user->firstname . ', Only One Step Left!')->view('emails.receipt');
    }
}
