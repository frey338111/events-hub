@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="max-w-7xl mx-auto mt-4 sm:px-6 lg:px-8">
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif
    <div class="container mx-auto px-4 py-6">
        @php
            $pageTitle = request()->routeIs('dashboard.events.pending')
                ? 'Pending Events'
                : 'Live Events';
        @endphp

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">{{$pageTitle}}</h1>

            <a href="{{ route('dashboard.events.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                + Add New Event
            </a>
        </div>

        @php
            $sortRouteName = request()->routeIs('dashboard.events.pending')
                ? 'dashboard.events.pending'
                : 'dashboard.events.index';
        @endphp

        <table class="min-w-full bg-white shadow rounded-lg">
            <thead>
            <tr class="bg-gray text-left">
                {{-- ID --}}
                <th class="py-2 px-3">
                    <a href="{{ route($sortRouteName, [
                'sort' => 'id',
                'direction' => ($sort === 'id' && $direction === 'asc') ? 'desc' : 'asc'
            ]) }}" class="hover:underline">
                        ID
                        @if($sort === 'id')
                            {!! $direction === 'asc' ? '&uarr;' : '&darr;' !!}
                        @endif
                    </a>
                </th>
                {{-- Title --}}
                <th class="py-2 px-3">
                    <a href="{{ route($sortRouteName, [
                'sort' => 'title',
                'direction' => ($sort === 'title' && $direction === 'asc') ? 'desc' : 'asc'
            ]) }}" class="hover:underline">
                        Title
                        @if($sort === 'title')
                            {!! $direction === 'asc' ? '&uarr;' : '&darr;' !!}
                        @endif
                    </a>
                </th>
                <th class="py-2 px-3">Description</th>
                <th class="py-2 px-3">Image</th>

                {{-- Start --}}
                <th class="py-2 px-3">
                    <a href="{{ route($sortRouteName, [
                'sort' => 'start_time',
                'direction' => ($sort === 'start_time' && $direction === 'asc') ? 'desc' : 'asc'
            ]) }}" class="hover:underline">
                        Start
                        @if($sort === 'start_time')
                            {!! $direction === 'asc' ? '&uarr;' : '&darr;' !!}
                        @endif
                    </a>
                </th>

                {{-- End --}}
                <th class="py-2 px-3">
                    <a href="{{ route($sortRouteName, [
                'sort' => 'end_time',
                'direction' => ($sort === 'end_time' && $direction === 'asc') ? 'desc' : 'asc'
            ]) }}" class="hover:underline">
                        End
                        @if($sort === 'end_time')
                            {!! $direction === 'asc' ? '&uarr;' : '&darr;' !!}
                        @endif
                    </a>
                </th>

                {{-- Capacity --}}
                <th class="py-2 px-3">
                    <a href="{{ route($sortRouteName, [
                'sort' => 'capacity',
                'direction' => ($sort === 'capacity' && $direction === 'asc') ? 'desc' : 'asc'
            ]) }}" class="hover:underline">
                        Capacity
                        @if($sort === 'capacity')
                            {!! $direction === 'asc' ? '&uarr;' : '&darr;' !!}
                        @endif
                    </a>
                </th>

                <th class="py-2 px-3">Actions</th>
            </tr>
            </thead>


            <tbody>
            @foreach ($events as $event)
                <tr class="border-b">
                    <td class="py-2 px-2">{{ $event->id }}</td>

                    <td class="py-2 px-3">{{ $event->title }}</td>
                    <td class="py-2 px-3">
                        @php
                            $cleanDescription = strip_tags($event->description);
                        @endphp
                        @if($cleanDescription)
                            <button
                                type="button"
                                class="text-black-600 hover:underline"
                                data-full-description="{{ e($cleanDescription) }}"
                            >
                                {{ \Illuminate\Support\Str::words($cleanDescription, 10) }}
                            </button>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if($event->events_thumbnail)
                            <img
                                    src="{{ asset('storage/events/thumbs/' . $event->events_thumbnail) }}"
                                    class="h-16 w-16 rounded object-cover border"
                                    alt="Thumbnail"
                            />
                        @else
                            <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                                No image
                            </div>
                        @endif
                    </td>
                    <td class="py-2 px-3">{{ $event->start_time->format('Y-m-d H:i') }}</td>
                    <td class="py-2 px-3">{{ $event->end_time->format('Y-m-d H:i') }}</td>
                    <td class="py-2 px-3">{{ $event->capacity }}</td>

                    <td class="py-2 px-3">
                        @if(request()->routeIs('dashboard.events.pending'))
                            <form action="{{ route('dashboard.events.approve', $event) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="text-green-600 hover:text-green-800 font-semibold">
                                    Approve
                                </button>
                            </form>
                            <span class="mx-1">|</span>
                            <form action="{{ route('dashboard.events.reject', $event) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Reject this event?');">
                                @csrf
                                @method('PATCH')
                                <button class="text-red-600 hover:text-red-800 font-semibold">
                                    Reject
                                </button>
                            </form>
                        @else
                            <a href="{{ route('dashboard.events.edit', $event) }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>
                            |
                            <form action="{{ route('dashboard.events.destroy', $event) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this event?');"
                                  class="inline-block ml-2">

                                @csrf
                                @method('DELETE')

                                <button class="text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $events->links() }}
        </div>
    </div>

    <div id="description-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div id="description-modal-overlay" class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded shadow-lg max-w-2xl w-full mx-4 p-6">
            <h2 class="text-xl font-semibold mb-3">Description</h2>
            <div id="description-modal-content" class="text-gray-800 leading-relaxed"></div>
            <div class="mt-6 flex justify-end">
                <button id="description-modal-close"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('description-modal');
            const modalContent = document.getElementById('description-modal-content');
            const closeBtn = document.getElementById('description-modal-close');
            const overlay = document.getElementById('description-modal-overlay');

            const closeModal = () => modal.classList.add('hidden');

            document.querySelectorAll('[data-full-description]').forEach(button => {
                button.addEventListener('click', () => {
                    const description = button.getAttribute('data-full-description') || '';
                    modalContent.innerHTML = description
                        ? description.replace(/\n/g, '<br>')
                        : '—';
                    modal.classList.remove('hidden');
                });
            });

            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', closeModal);
        });
    </script>
@endsection
