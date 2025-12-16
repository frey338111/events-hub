import React, { useEffect, useState } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import { Link } from "react-router-dom";

const UPCOMING_EVENTS_HOMEPAGE_QUERY = gql`
    query UpcomingEventsHomepage {
        upcomingEventsHomepage {
            id
            title
            url_key
            capacity
            start_time
            events_thumbnail
            type { name }
            location { name }
        }
    }
`;

export default function PopularEvents() {
    const [events, setEvents] = useState([]);
    const { data, loading } = useQuery(UPCOMING_EVENTS_HOMEPAGE_QUERY);

    useEffect(() => {
        if (data?.upcomingEventsHomepage) {
            setEvents(data.upcomingEventsHomepage);
        }
    }, [data]);

    if (loading) {
        return <p className="text-gray-600">Loading upcoming events…</p>;
    }

    return (
        <div className="p-4 bg-white shadow rounded mt-6">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-bold">Upcoming Events</h2>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                {events.map(event => {
                    return (
                        <div
                            key={event.id}
                            className="bg-gray-50 rounded shadow hover:shadow-lg transition flex flex-col overflow-hidden"
                        >
                            <Link to={`/events/${event.url_key}`} className="block">
                                <img
                                    src={
                                        event.events_thumbnail
                                            ? `/storage/events/thumbs/${event.events_thumbnail}`
                                            : `/storage/events/thumbs/default-event.jpg`
                                    }
                                    alt={event.title}
                                    className="w-full h-48 object-cover hover:opacity-90 transition"
                                />
                            </Link>

                            <div className="p-4 flex-1 flex flex-col">
                                <h3 className="font-semibold text-lg text-blue-700 hover:underline line-clamp-2">
                                    <Link to={`/events/${event.url_key}`}>{event.title}</Link>
                                </h3>

                                <div className="mt-3 space-y-1 text-sm text-gray-600 flex-1">
                                    <p>{event.start_time}</p>
                                    <p>{event.type.name}</p>
                                    <p>{event.location.name}</p>
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
