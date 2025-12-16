<?php

namespace App\GraphQL\Mutations;

use App\Events\EventCanceledByCreator;
use App\Models\Events;
use App\Services\EventImageService;
use App\Services\EventTicketService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class CustomerEventMutation
{
    /**
     * Create a new event for the authenticated customer.
     */
    public function create($_, array $args)
    {
        auth()->shouldUse('customer_jwt');
        $customer = request()->user('customer_jwt')
            ?? auth('customer_jwt')->user()
            ?? request()->user('customer')
            ?? auth('customer')->user();

        if (! $customer) {
            throw new AuthenticationException('Unauthenticated.');
        }

        $input = $args['input'] ?? [];

        $validator = Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'location_id' => ['required', 'integer', 'exists:events_location,id'],
            'type_id' => ['required', 'integer', 'exists:events_type,id'],
            'capacity' => ['required', 'integer', 'min:1'],
            'url_key' => ['nullable', 'string', 'unique:events,url_key'],
            'events_image' => ['nullable', 'file', 'image', 'max:2048'],
        ]);

        $validator->validate();

        $payload = [
            'title' => $input['title'],
            'description' => $input['description'] ?? null,
            'start_time' => $input['start_time'],
            'end_time' => $input['end_time'] ?? null,
            'location_id' => $input['location_id'],
            'type_id' => $input['type_id'],
            'capacity' => $input['capacity'],
            'url_key' => $input['url_key'] ?? Str::slug($input['title']),
            'customer_id' => $customer->id,
            'status' => 'pending',
        ];

        $event = Events::create($payload);

        /** @var UploadedFile|null $upload */
        $upload = $input['events_image'] ?? null;
        if ($upload instanceof UploadedFile) {
            $fileData = app(EventImageService::class)->upload($event, $upload);
            $event->update($fileData);
        }

        app(EventTicketService::class)->initTickets($event);

        return $event->fresh();
    }

    /**
     * Cancel an event created by the authenticated customer.
     */
    public function cancel($_, array $args)
    {
        auth()->shouldUse('customer_jwt');
        $customer = request()->user('customer_jwt')
            ?? auth('customer_jwt')->user()
            ?? request()->user('customer')
            ?? auth('customer')->user();

        if (! $customer) {
            throw new AuthenticationException('Unauthenticated.');
        }

        $eventId = $args['event_id'] ?? null;
        if (! $eventId) {
            throw new \InvalidArgumentException('Event ID is required.');
        }

        $event = Events::where('id', $eventId)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $event) {
            throw new \RuntimeException('Event not found.');
        }

        $event->update(['status' => 'canceled']);

        event(new EventCanceledByCreator($event));

        return $event->fresh();
    }
}
