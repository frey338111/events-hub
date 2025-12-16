<div class="space-y-2">
    <h3 class="text-lg font-semibold text-gray-800">Customers</h3>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded border border-gray-200 bg-gray-50 p-3">
            <p class="text-sm text-gray-600">Total Registered</p>
            <p class="text-2xl font-bold text-gray-900">{{ $customerTotals['total'] ?? 0 }}</p>
        </div>
        <div class="rounded border border-gray-200 bg-gray-50 p-3">
            <p class="text-sm text-gray-600">Registered Past 7 Days</p>
            <p class="text-2xl font-bold text-gray-900">{{ $customerTotals['past_week'] ?? 0 }}</p>
        </div>
        <div class="rounded border border-gray-200 bg-gray-50 p-3">
            <p class="text-sm text-gray-600">Customer-Created Events</p>
            <p class="text-2xl font-bold text-gray-900">{{ $customerTotals['created_events_total'] ?? 0 }}</p>
        </div>
    </div>
</div>
