<?php

namespace App\GraphQL\Queries;

use App\Models\Events;
use App\Models\EventsType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EventQuery
{
    public function events($_, array $args): Collection
    {
        $query = Events::with(['type', 'location']);

        if (! empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        $query->where('status', 'EQ', 'approved');

        return $query->orderBy('start_time', 'asc')->get();
    }

    public function paginatedEvents($_, array $args): array
    {
        $page = $args['page'] ?? 1;
        $first = $args['first'] ?? 5;
        $monthStart = $args['monthStart'] ?? null;

        $query = Events::query();

        if (! empty($args['search'])) {
            $query->where(function ($q) use ($args) {
                $q->where('title', 'LIKE', '%'.$args['search'].'%')
                    ->orWhere('description', 'LIKE', '%'.$args['search'].'%');
            });
        }

        if (! empty($args['type']) && $args['type'] > 0) {
            $query->where('type_id', $args['type']);
        }

        if (! empty($args['location']) && $args['location'] > 0) {
            $query->where('location_id', $args['location']);
        }

        if (! empty($monthStart)) {
            $start = Carbon::parse($monthStart)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $query->whereBetween('start_time', [$start, $end]);
        }
        $query->where('status', 'approved');
        $paginator = $query->paginate($first, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ];
    }

    public function eventByUrlKey($_, array $args)
    {
        return Events::with(['type', 'location', 'customer'])
            ->where('url_key', $args['url_key'])
            ->first();
    }

    public function listEventTypes()
    {
        return EventsType::all();
    }

    public function popularEvents($_, array $args)
    {
        return Events::select('events.*')
            ->leftJoin('event_ticket', function ($join) {
                $join->on('event_ticket.event_id', '=', 'events.id')
                    ->where('event_ticket.status', '=', 'hold');
            })
            ->selectRaw('COUNT(event_ticket.id) as booked_count')
            ->selectRaw('capacity')
            ->groupBy('events.id')
            ->orderByRaw('(COUNT(event_ticket.id) / capacity) DESC')
            ->limit(5)
            ->get();
    }

    public function upcomingEventsHomepage()
    {
        return Events::where('status', 'approved')
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->limit(6)
            ->get();
    }

    public function monthsWithEvents(): Collection
    {
        $months = Events::whereNotNull('start_time')
            ->where('status', 'approved')
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->pluck('start_time')
            ->map(fn ($date) => Carbon::parse($date)->startOfMonth())
            ->unique()
            ->sort()
            ->take(6)
            ->values();

        return $months->map(function (Carbon $month) {
            return [
                'year' => $month->year,
                'month' => $month->month,
                'label' => $month->format('F Y'),
                'start' => $month->format('Y-m-d H:i:s'),
            ];
        });
    }
}
