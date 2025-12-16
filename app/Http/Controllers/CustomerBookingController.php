<?php

namespace App\Http\Controllers;

use App\Events\EventBooked;
use App\Events\TicketCanceled;
use App\Models\Customer;
use App\Models\Events;
use App\Models\EventTicket;
use Illuminate\Http\Request;

class CustomerBookingController extends Controller
{
    /**
     * Cancel a customer's ticket and reopen it.
     */
    public function cancel(Request $request)
    {
        $validated = $request->validate([
            'ticketId' => 'required|integer|exists:event_ticket,id',
        ]);
        $ticketId = $validated['ticketId'];

        $ticket = EventTicket::find($ticketId);

        if (! $ticket) {
            return response()->json([
                'message' => 'Ticket not found.',
            ], 404);
        }

        $customerId = auth()->id();
        if ($ticket->customer_id !== $customerId) {
            return response()->json([
                'message' => 'You are not authorized to cancel this ticket.',
            ], 403);
        }

        // trigger event booked event
        event(new TicketCanceled($ticket));

        $ticket->customer_id = 0;
        $ticket->hash_key = null;
        $ticket->status = 'open';

        try {
            $ticket->save();
        } catch (\Throwable $e) {
            \Log::error('Failed to cancel ticket', [
                'ticket_id' => $ticketId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to cancel ticket at this time.',
            ], 500);
        }

        return response()->json([
            'message' => 'Ticket cancelled successfully.',
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $customer = auth()->user();
        $customerId = auth()->id();

        // check if already booked

        $bookedTicket = EventTicket::where('event_id', $request->event_id)
            ->where('customer_id', $customerId)
            ->where('status', 'hold')
            ->first();

        if ($bookedTicket) {
            return response()->json([
                'message' => 'You have booked for this event already.',
            ], 422);
        }

        // Find the first open ticket
        $ticket = EventTicket::where('event_id', $request->event_id)
            ->where('status', 'open')
            ->first();

        if (! $ticket) {
            return response()->json([
                'message' => 'No available tickets left.',
            ], 422);
        }

        // Assign to customer
        $ticket->customer_id = $customerId;
        $ticket->status = 'hold';  // or 'closed'
        $ticket->save();

        // generate hashkey
        $hashKey = hash('sha256', implode('|', [
            $ticket->customer_id,
            $ticket->event_id,
            $customer->email,
            $customer->name,
            $ticket->updated_at,
        ]));

        $ticket->hash_key = $hashKey;
        $ticket->save();

        // trigger event booked event
        event(new EventBooked($ticket));

        return response()->json(['message' => 'Ticket booked', 'ticketId' => $ticket->id], 200);
    }

    public function check($eventId)
    {
        $customerId = auth()->id();

        $ticket = EventTicket::where('event_id', $eventId)
            ->where('customer_id', $customerId)
            ->first();

        if ($ticket) {
            return response()->json([
                'booked' => true,
                'hash_key' => $ticket->hash_key,
                'ticket_id' => $ticket->id,
            ]);
        }

        return response()->json(['booked' => false]);
    }

    public function viewTicket($ticketId, $hashKey, $customerId)
    {
        $ticket = EventTicket::find($ticketId);

        if (! $ticket) {
            return response()->json([
                'valid' => false,
                'message' => 'Ticket not found.',
            ], 404);
        }

        $customer = Customer::find($customerId);
        $event = Events::find($ticket->event_id);

        if (! $customer || $customer->id != $ticket->customer_id) {
            return response()->json([
                'valid' => false,
                'message' => 'Customer does not match this ticket.',
            ], 400);
        }

        // Rebuild hash
        $reconstructed = hash('sha256', implode('|', [
            $ticket->customer_id,
            $ticket->event_id,
            $customer->email,
            $customer->name,
            $ticket->updated_at,
        ]));

        $isValid = hash_equals($reconstructed, $hashKey);

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Ticket is valid!' : 'Ticket is NOT valid!',
            'customer' => $customer,
            'event' => $event,
        ]);
    }
}
