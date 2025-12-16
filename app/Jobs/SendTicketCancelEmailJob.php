<?php

namespace App\Jobs;

use App\Mail\TicketCanceledEmail;
use App\Models\EventTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketCancelEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public EventTicket $ticket) {}

    public function handle(): void
    {
        $customer = $this->ticket->customer;

        if (! $customer || ! $customer->email) {
            return;
        }

        Mail::to($customer->email)
            ->send(new TicketCanceledEmail($this->ticket));
    }
}
