<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendPaymentReceipt;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $product;
    protected $payment;

    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new SendPaymentReceipt($this->user, $this->product, $this->payment);
        Mail::to($this->user->email)->send($email);
    }
}
