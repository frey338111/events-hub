<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Events;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $customerTotals = [
            'total' => Customer::count(),
            'past_week' => Customer::where('created_at', '>=', now()->subWeek())->count(),
            'created_events_total' => Events::where('customer_id', '>', 0)->count(),
        ];

        $eventTotals = [
            'created_last_week' => Events::where('created_at', '>=', now()->subWeek())->count(),
            'approved_last_week' => Events::where('status', 'approved')
                ->where('created_at', '>=', now()->subWeek())
                ->count(),
            'pending_total' => Events::where('status', 'pending')->count(),
            'rejected_total' => Events::where('status', 'rejected')->count(),
        ];

        $pendingEvents = Events::with('customer')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $upcomingEvents = Events::where('status', 'approved')
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->limit(3)
            ->get();

        return view('dashboard', compact(
            'customerTotals',
            'eventTotals',
            'pendingEvents',
            'upcomingEvents'
        ));
    }
}
