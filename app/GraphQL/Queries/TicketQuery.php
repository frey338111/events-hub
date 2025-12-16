<?php

namespace App\GraphQL\Queries;

use App\Models\Customer;
use App\Models\Events;
use App\Models\EventTicket;

class TicketQuery
{
    public function validate($_, array $args): array
    {
        $ticket = EventTicket::find($args['ticket_id']);

        if (! $ticket) {
            return [
                'valid' => false,
                'message' => 'Ticket not found.',
                'customer' => null,
                'event' => null,
            ];
        }

        $customer = Customer::find($args['customer_id']);
        $event = Events::find($ticket->event_id);

        if (! $customer || $customer->id != $ticket->customer_id) {
            return [
                'valid' => false,
                'message' => 'Customer does not match this ticket.',
                'customer' => null,
                'event' => null,
            ];
        }

        $reconstructed = hash('sha256', implode('|', [
            $ticket->customer_id,
            $ticket->event_id,
            $customer->email,
            $customer->name,
            $ticket->updated_at,
        ]));

        $isValid = hash_equals($reconstructed, $args['hash_key']);

        return [
            'valid' => $isValid,
            'message' => $isValid ? 'Ticket is valid!' : 'Ticket is NOT valid!',
            'customer' => $customer,
            'event' => $event,
        ];
    }
}
