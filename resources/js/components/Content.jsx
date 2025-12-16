import React, { useEffect, useState } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";

const FILTER_CACHE_KEY = "event_filters_cache_v2";
//const FILTER_CACHE_TTL_MS = 60 * 60 * 1000; // 1 hour
const FILTER_CACHE_TTL_MS = 60 * 60 * 1; // 1 hour

const FILTERS_QUERY = gql`
    query Filters {
        listEventTypes { id name }
        listEventLocation { id name }
        eventMonths { year month label start }
    }
`;

const EVENTS_QUERY = gql`
    query Events($page: Int, $search: String, $type: Int, $location: Int, $monthStart: DateTime) {
        events(first: 10, page: $page, search: $search, type: $type, location: $location, monthStart: $monthStart) {
            data {
                id
                title
                url_key
                description
                start_time
                end_time
                events_thumbnail
                type { name }
                location { name }
            }
            currentPage
            lastPage
            total
        }
    }
`;

export default function Content() {
    const [events, setEvents] = useState([]);
    const [eventTypes, setEventTypes] = useState([]);

    const [selectedType, setSelectedType] = useState(null);
    const [eventLocations, setEventLocation] = useState([]);
    const [selectedLocation, setSelectedLocation] = useState(null);
    const [months, setMonths] = useState([]);
    const [selectedMonth, setSelectedMonth] = useState(null);

    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [search, setSearch] = useState("");
    const [searchTerm, setSearchTerm] = useState("");
    const [typingTimeout, setTypingTimeout] = useState(null);
    const [animating, setAnimating] = useState(false);
    const [skipFiltersQuery, setSkipFiltersQuery] = useState(true);

    // Load filters with Apollo, while still honoring simple local cache
    useEffect(() => {
        const now = Date.now();
        try {
            const cached = JSON.parse(localStorage.getItem(FILTER_CACHE_KEY) || "null");
            if (cached && cached.savedAt && now - cached.savedAt < FILTER_CACHE_TTL_MS) {
                setEventTypes(cached.types ?? []);
                setEventLocation(cached.locations ?? []);
                setMonths(cached.months ?? []);
                return;
            }
        } catch (_) {
            // ignore cache parse errors
        }
        // if cache missing/expired, allow query
        setSkipFiltersQuery(false);
    }, []);

    const {
        data: filtersData,
        loading: filtersLoading,
        error: filtersError,
    } = useQuery(FILTERS_QUERY, {
        fetchPolicy: "cache-first",
        nextFetchPolicy: "cache-first",
        skip: skipFiltersQuery,
    });

    useEffect(() => {
        if (filtersData) {
            const types = filtersData?.listEventTypes ?? [];
            const locations = filtersData?.listEventLocation ?? [];
            const monthsResult = filtersData?.eventMonths ?? [];
            setEventTypes(types);
            setEventLocation(locations);
            setMonths(monthsResult);
            try {
                localStorage.setItem(
                    FILTER_CACHE_KEY,
                    JSON.stringify({ types, locations, months: monthsResult, savedAt: Date.now() })
                );
            } catch (_) {
                // ignore storage errors
            }
        }
    }, [filtersData]);

    useEffect(() => {
        if (filtersError) {
            console.error('filters error', filtersError);
        }
    }, [filtersError]);

    useEffect(() => {
        setAnimating(true);
    }, [page, searchTerm, selectedType, selectedLocation, selectedMonth]);

    const {
        data: eventsData,
        loading: eventsLoading,
    } = useQuery(EVENTS_QUERY, {
        variables: {
            page,
            search: searchTerm,
            type: selectedType ? parseInt(selectedType, 10) : null,
            location: selectedLocation ? parseInt(selectedLocation, 10) : null,
            monthStart: selectedMonth || null,
        },
        fetchPolicy: "cache-first",
        nextFetchPolicy: "cache-first",
    });

    // Handle search typing
    // Debounce search
    function handleSearch(e) {
        const value = e.target.value;
        setSearch(value);

        // cancel previous timer
        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }

        // Apply debounce if 2+ chars
        if (value.length > 2) {
            const timer = setTimeout(() => {
                setSearchTerm(value);
                setPage(1);
            }, 500);

            setTypingTimeout(timer);
        }

        // Clear search
        if (value.length === 0) {
            setSearchTerm("");
            setPage(1);
        }
    }

    useEffect(() => {
        if (eventsData?.events) {
            setEvents(eventsData.events.data);
            setPage(eventsData.events.currentPage);
            setLastPage(eventsData.events.lastPage);
            setTimeout(() => setAnimating(false), 200);
        }
    }, [eventsData]);

    useEffect(() => {
        if (!eventsLoading && !eventsData) {
            setTimeout(() => setAnimating(false), 200);
        }
    }, [eventsLoading, eventsData]);

    const isInitialLoading = filtersLoading && eventTypes.length === 0;

    if (isInitialLoading) {
        return <p className="p-6 text-gray-500">Loading events...</p>;
    }

    return (
        <main className="p-6">
            <div className="flex flex-col md:flex-row gap-6">
                {/* Left column: search + filters */}
                <aside className="md:w-1/3 lg:w-1/4 space-y-4">
                    <div className="p-4 bg-white border rounded shadow">
                        <input
                            type="text"
                            placeholder="Search events..."
                            value={search}
                            onChange={handleSearch}
                            className="w-full p-3 border rounded focus:ring focus:ring-blue-300"
                        />
                    </div>

                    <div className="p-4 bg-white border rounded shadow space-y-3">
                        <h4 className="text-sm font-semibold text-gray-700">Event Types</h4>
                        <div className="flex flex-wrap gap-2">
                            {eventTypes.map((type) => (
                                <button
                                    key={type.id}
                                    onClick={() =>
                                        {
                                            setSelectedType(selectedType === type.id ? null : type.id);
                                            setPage(1);
                                        }
                                    }
                                    className={
                                        "px-4 py-2 rounded border transition " +
                                        (selectedType === type.id
                                            ? "bg-blue-600 text-white border-blue-700"
                                            : "bg-gray-100 text-gray-800 border-gray-300 hover:bg-gray-200")
                                    }
                                >
                                    {type.name}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className="p-4 bg-white border rounded shadow space-y-3">
                        <h4 className="text-sm font-semibold text-gray-700">Locations</h4>
                        <div className="flex flex-wrap gap-2">
                            {eventLocations.map((location) => (
                                <button
                                    key={location.id}
                                    onClick={() =>
                                        {
                                            setSelectedLocation(selectedLocation === location.id ? null : location.id);
                                            setPage(1);
                                        }
                                    }
                                    className={
                                        "px-4 py-2 rounded border transition " +
                                        (selectedLocation === location.id
                                            ? "bg-blue-600 text-white border-blue-700"
                                            : "bg-gray-100 text-gray-800 border-gray-300 hover:bg-gray-200")
                                    }
                                >
                                    {location.name}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className="p-4 bg-white border rounded shadow space-y-3">
                        <h4 className="text-sm font-semibold text-gray-700">Date & Time</h4>
                        <div className="flex flex-wrap gap-2">
                            {months.map((month) => (
                                <button
                                    key={month.start}
                                    onClick={() =>
                                        {
                                            setSelectedMonth(selectedMonth === month.start ? null : month.start);
                                            setPage(1);
                                        }
                                    }
                                    className={
                                        "px-4 py-2 rounded border transition " +
                                        (selectedMonth === month.start
                                            ? "bg-blue-600 text-white border-blue-700"
                                            : "bg-gray-100 text-gray-800 border-gray-300 hover:bg-gray-200")
                                    }
                                >
                                    {month.label}
                                </button>
                            ))}
                        </div>
                    </div>
                </aside>

                {/* Right column: results */}
                <section className="flex-1 space-y-4">
                    <h3 className="text-lg font-semibold">
                        {events.length === 0 ? "No events found" : "Events found"}
                    </h3>
                    <div
                        className={`transition-opacity duration-300 ${animating ? "opacity-0" : "opacity-100"}`}
                    >
                        <ul className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {events.map(event => (
                                <li key={event.id} className="border rounded shadow bg-white overflow-hidden flex flex-col">
                                    <div className="w-full">
                                        <img
                                            src={
                                                event.events_thumbnail
                                                    ? `/storage/events/thumbs/${event.events_thumbnail}`
                                                    : `/storage/events/thumbs/default-event.jpg`
                                            }
                                            alt={event.title}
                                            className="object-cover w-full h-56"
                                        />
                                    </div>

                                    <div className="p-4 flex-1 flex flex-col">
                                        <a
                                            href={`/events/${event.url_key}`}
                                            className="text-2xl font-bold text-blue-600 hover:underline hover:text-blue-800"
                                        >
                                            {event.title}
                                        </a>

                                        <p className="text-gray-700 mt-3 flex-1">{event.description}</p>

                                        <div className="mt-3 space-y-1 text-sm text-gray-500">
                                            <p>Type: {event.type?.name} — Location: {event.location?.name}</p>
                                            <p>From: {event.start_time} — To: {event.end_time}</p>
                                        </div>
                                    </div>
                                </li>
                            ))}
                        </ul>


                    </div>
                    {/* Pagination */}
                    <div className="flex items-center space-x-4 mt-6">
                        <button
                            onClick={() => setPage((prev) => Math.max(1, prev - 1))}
                            disabled={page === 1}
                            className={`px-4 py-2 rounded ${page === 1 ? "bg-gray-300 text-gray-500 cursor-not-allowed" : "bg-blue-600 text-white hover:bg-blue-700"}`}
                        >
                            Prev
                        </button>

                        <span>
                            Page <strong>{page}</strong> of {lastPage}
                        </span>

                        <button
                            onClick={() => setPage((prev) => Math.min(lastPage, prev + 1))}
                            disabled={page === lastPage}
                            className={`px-4 py-2 rounded ${page === lastPage ? "bg-gray-300 text-gray-500 cursor-not-allowed" : "bg-blue-600 text-white hover:bg-blue-700"}`}
                        >
                            Next
                        </button>
                    </div>
                </section>
            </div>
        </main>
    );
}
