<form method="POST"
      action="{{ isset($event)
                    ? route('dashboard.events.update', $event)
                    : route('dashboard.events.store') }}"
      enctype="multipart/form-data">

    @csrf
    @if(isset($event))
        @method('PUT')
    @endif

    <!-- Title -->
    <div class="mb-4">
        <label class="block font-medium">Title</label>
        <input
                type="text"
                name="title"
                value="{{ old('title', $event->title ?? '') }}"
                class="w-full border rounded p-2"
                required
        />
        @error('title')
        <div class="text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <!-- Type -->
    <div class="mb-4">
        <label class="block font-medium">Type</label>
        <select name="type_id" class="w-full border rounded p-2" required>
            @foreach($types as $name => $id)
                <option value="{{ $id }}"
                        {{ old('type_id', $event->type_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Description -->
    <div class="mb-4">
        <label class="block font-medium">Description</label>
        <textarea
                name="description"
                rows="4"
                class="w-full border rounded p-2"
        >{{ old('description', $event->description ?? '') }}</textarea>
        @error('description')
        <div class="text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <!-- Event Image -->
    <div class="mb-4">
        <label class="block font-medium">Event Image</label>

        @if(isset($event) && $event->events_image)
            <div class="mb-2">
                <img
                        src="{{ asset('storage/events/' . $event->events_image) }}"
                        alt="Event Image"
                        class="h-24 rounded border"
                />
            </div>
        @endif

        <input type="file" name="events_image" class="w-full border rounded p-2" />
        @error('events_image')
        <div class="text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <!-- Start Time -->
    <div class="mb-4">
        <label class="block font-medium">Start Time</label>
        <input
                type="datetime-local"
                name="start_time"
                value="{{ old('start_time',
                        isset($event->start_time)
                        ? \Carbon\Carbon::parse($event->start_time)->format('Y-m-d\TH:i')
                        : '') }}"
                class="w-full border rounded p-2"
                required
        />
        @error('start_time')
        <div class="text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <!-- End Time -->
    <div class="mb-4">
        <label class="block font-medium">End Time</label>
        <input
                type="datetime-local"
                name="end_time"
                value="{{ old('end_time',
                        isset($event->end_time)
                        ? \Carbon\Carbon::parse($event->end_time)->format('Y-m-d\TH:i')
                        : '') }}"
                class="w-full border rounded p-2"
        />
        @error('end_time')
        <div class="text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <!-- Location -->
    <div class="mb-4">
        <label class="block font-medium">Location</label>
        <select name="location_id" class="w-full border rounded p-2" required>
            @foreach($locations as $name => $loc_id)
                <option value="{{ $loc_id }}"
                        {{ old('location_id', $event->location_id ?? '') == $loc_id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Capacity -->
    <div class="mb-4">
        <label class="block font-medium">Capacity</label>
        <input
                type="number"
                name="capacity"
                value="{{ old('capacity', $event->capacity ?? 0) }}"
                class="w-full border rounded p-2
                @if(isset($event))
                    bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed
                 @endif"
                {{ isset($event) ? 'disabled' : '' }}
                required
        />
        @if(isset($event))
            <p class="text-sm text-gray-500 mt-1">
                Capacity cannot be changed after event creation.
            </p>
        @endif
    </div>

    <!-- URL Key -->
    <div class="mb-4">
        <label class="block font-medium">URL Key</label>
        <input
                type="text"
                name="url_key"
                value="{{ old('url_key', $event->url_key ?? '') }}"
                class="w-full border rounded p-2"
                required
        />
    </div>

    <div class="mt-6">
        <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
            {{ isset($event) ? 'Save Changes' : 'Create Event' }}
        </button>
    </div>

</form>

@if(isset($event) && $event->status === 'pending')
    <div class="mt-4 flex flex-wrap items-center gap-3">
        <form
                action="{{ route('dashboard.events.approve', $event) }}"
                method="POST"
                onsubmit="return confirm('Approve this event?');"
        >
            @csrf
            @method('PATCH')
            <button
                    type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
            >
                Approve
            </button>
        </form>

        <form
                action="{{ route('dashboard.events.reject', $event) }}"
                method="POST"
                onsubmit="return confirm('Reject this event?');"
        >
            @csrf
            @method('PATCH')
            <button
                    type="submit"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
            >
                Reject
            </button>
        </form>
    </div>
@endif
