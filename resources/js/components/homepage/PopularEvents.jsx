import React, { useEffect, useState } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import { Link } from "react-router-dom";

const POPULAR_EVENTS_QUERY = gql`
    query PopularEvents {
        popularEvents {
            id
            title
            url_key
            capacity
            start_time
            events_thumbnail
            booked_count
            type { name }
            location { name }
        }
    }
`;

export default function PopularEvents() {
    const [events, setEvents] = useState([]);
    const { data, loading } = useQuery(POPULAR_EVENTS_QUERY);

    useEffect(() => {
        if (data?.popularEvents) {
            setEvents(data.popularEvents);
        }
    }, [data]);

    if (loading) {
        return <p className="text-gray-600">Loading popular events…</p>;
    }

    return (
        <div className="p-4 bg-white shadow rounded mt-6 mx-auto">
            <h2 className="text-xl font-bold mb-4">Popular Events</h2>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {events.map(event => {
                    const popularity = event.capacity
                        ? ((event.booked_count / event.capacity) * 100).toFixed(0)
                        : 0;

                    return (
                        <div
                            key={event.id}
                            className="bg-gray-50 rounded shadow hover:shadow-lg transition flex flex-col overflow-hidden w-full max-w-sm mx-auto"
                        >
                            <Link to={`/events/${event.url_key}`} className="block">
                                <img
                                    src={
                                        event.events_thumbnail
                                            ? `/storage/events/thumbs/${event.events_thumbnail}`
                                            : `/storage/events/thumbs/default-event.jpg`
                                    }
                                    alt={event.title}
                                    className="w-full h-40 object-cover hover:opacity-90 transition"
                                />
                            </Link>

                            <div className="p-3 flex-1 flex flex-col">
                                <h3 className="font-semibold text-lg text-blue-700 hover:underline">
                                    <Link to={`/events/${event.url_key}`}>{event.title}</Link>
                                </h3>

                                <div className="mt-2 space-y-1 text-sm text-gray-600 flex-1">
                                    <p>{event.start_time}</p>
                                    <p>{event.type.name}</p>
                                    <p>{event.location.name}</p>
                                </div>
                                <p className="text-sm text-gray-700 mt-2">
                                    🔥 {popularity}% booked
                                </p>
                            </div>
                        </div>
                    );
                })}

            </div>
        </div>
    );
}
