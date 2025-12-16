<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Info') }}
        </h2>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                @include('dashboard.static.user')
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Events (last 7 days)</h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded border border-gray-200 bg-gray-50 p-3">
                        <p class="text-sm text-gray-600">Created</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $eventTotals['created_last_week'] ?? 0 }}</p>
                    </div>
                    <div class="rounded border border-gray-200 bg-gray-50 p-3">
                        <p class="text-sm text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $eventTotals['approved_last_week'] ?? 0 }}</p>
                    </div>
                    <div class="rounded border border-gray-200 bg-gray-50 p-3">
                        <p class="text-sm text-gray-600">Pending (total)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $eventTotals['pending_total'] ?? 0 }}</p>
                    </div>
                    <div class="rounded border border-gray-200 bg-gray-50 p-3">
                        <p class="text-sm text-gray-600">Rejected (total)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $eventTotals['rejected_total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Recent Pending Events</h3>
                @if($pendingEvents->isEmpty())
                    <p class="text-gray-600 text-sm">No pending events.</p>
                @else
                    <div class="space-y-3">
                        @foreach($pendingEvents as $event)
                            <div class="rounded border border-gray-200 bg-gray-50 p-3 flex items-start justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">#{{ $event->id }}</p>
                                    <p class="font-semibold text-gray-900">{{ $event->title }}</p>
                                    <p class="text-xs text-gray-600">
                                        {{ optional($event->start_time)->format('Y-m-d H:i') ?? 'No start time' }}
                                        • {{ $event->customer?->name ?? 'Unknown customer' }}
                                    </p>
                                </div>
                                <a href="{{ route('dashboard.events.edit', $event) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    Edit
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Upcoming Events</h3>
                @if($upcomingEvents->isEmpty())
                    <p class="text-gray-600 text-sm">No upcoming events.</p>
                @else
                    <div class="space-y-3">
                        @foreach($upcomingEvents as $event)
                            <div class="rounded border border-gray-200 bg-gray-50 p-3 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $event->title }}</p>
                                    <p class="text-xs text-gray-600">
                                        {{ optional($event->start_time)->format('Y-m-d H:i') ?? 'No start time' }}
                                    </p>
                                </div>
                                <a href="{{ route('dashboard.events.edit', $event) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    Edit
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
