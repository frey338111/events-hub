<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\EventApproved;
use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Services\EventImageService;
use App\Services\EventTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventsController extends Controller
{
    public function index()
    {
        $sort = request('sort', 'start_time');      // default sort column
        $direction = request('direction', 'asc');   // default sort direction
        // Validate allowed columns
        $allowedSorts = [
            'id',
            'title',
            'start_time',
            'end_time',
            'capacity',
        ];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'start_time';
        }
        $events = Events::where('status', 'approved')
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->appends([
                'sort' => $allowedSorts,
                'direction' => $direction,
            ]);

        return view('dashboard.events.index', compact('events', 'sort', 'direction'));
    }

    public function pending()
    {
        $sort = request('sort', 'start_time');      // default sort column
        $direction = request('direction', 'asc');   // default sort direction
        // Validate allowed columns
        $allowedSorts = [
            'id',
            'title',
            'start_time',
            'end_time',
            'capacity',
        ];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'start_time';
        }
        $events = Events::where('status', 'pending')
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->appends([
                'sort' => $allowedSorts,
                'direction' => $direction,
            ]);

        return view('dashboard.events.index', compact('events', 'sort', 'direction'));
    }

    public function canceled()
    {
        $sort = request('sort', 'start_time');      // default sort column
        $direction = request('direction', 'asc');   // default sort direction
        // Validate allowed columns
        $allowedSorts = [
            'id',
            'title',
            'start_time',
            'end_time',
            'capacity',
        ];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'start_time';
        }
        $events = Events::where('status', 'canceled')
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->appends([
                'sort' => $allowedSorts,
                'direction' => $direction,
            ]);

        return view('dashboard.events.index', compact('events', 'sort', 'direction'));
    }

    public function edit(Events $event)
    {
        $types = DB::table('events_type')->pluck('id', 'name')->toArray();
        $locations = DB::table('events_location')->pluck('id', 'name')->toArray();

        return view('dashboard.events.edit', compact('event', 'types', 'locations'));
    }

    public function update(Request $request, Events $event, EventImageService $imageService)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type_id' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'location_id' => 'required|integer|min:1',
            'capacity' => 'nullable|integer|min:5',
            'events_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'url_key' => 'required|string|unique:events,url_key,'.$event->id,
        ]);
        $event->update($validated);
        // Handle image using the service
        if ($request->hasFile('events_image')) {
            $fileData = $imageService->upload($event, $request->file('events_image'));
            $event->update($fileData);
        }

        return redirect()
            ->route('dashboard.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function create()
    {
        $types = DB::table('events_type')->pluck('id', 'name')->toArray();
        $locations = DB::table('events_location')->pluck('id', 'name')->toArray();

        return view('dashboard.events.create', compact('types', 'locations'));
    }

    public function store(
        Request $request,
        EventImageService $imageService,
        EventTicketService $eventTicketService
    ) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'location_id' => 'required|integer|min:1',
            'type_id' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:0',
            'url_key' => 'required|string|unique:events,url_key',
            'events_image' => 'nullable|image|max:2048',
        ]);
        // ---- SAVE EVENT ----
        $event = Events::create($validated);
        // Handle image using the service
        if ($request->hasFile('events_image')) {
            $fileData = $imageService->upload($event, $request->file('events_image'));
            $event->update($fileData);
        }
        $eventTicketService->initTickets($event);

        return redirect()
            ->route('dashboard.events.index')
            ->with('success', 'Event created successfully!');
    }

    public function approve(Events $event)
    {
        if ($event->status !== 'pending') {
            return redirect()
                ->route('dashboard.events.pending')
                ->with('success', 'Only pending events can be approved.');
        }

        $event->update(['status' => 'approved']);

        event(new EventApproved($event));

        return redirect()
            ->route('dashboard.events.pending')
            ->with('success', 'Event approved successfully.');
    }

    public function reject(Events $event)
    {
        if ($event->status !== 'pending') {
            return redirect()
                ->route('dashboard.events.pending')
                ->with('success', 'Only pending events can be rejected.');
        }

        $event->update(['status' => 'rejected']);

        return redirect()
            ->route('dashboard.events.pending')
            ->with('success', 'Event rejected successfully.');
    }

    public function destroy(Events $event)
    {
        // delete image file
        if ($event->events_image && \Storage::disk('public')->exists('events/'.$event->events_image)) {
            \Storage::disk('public')->delete('events/'.$event->events_image);
        }
        // delete thumbnail if exists
        if ($event->events_thumbnail && \Storage::disk('public')
            ->exists('events/thumbnails/'.$event->events_thumbnail)) {
            \Storage::disk('public')->delete('events/thumbnails/'.$event->events_thumbnail);
        }
        $event->delete();

        return redirect()
            ->route('dashboard.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
