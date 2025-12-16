import React, { useEffect, useState } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import useCustomer from "../../hooks/useCustomer";

const MY_EVENTS_QUERY = gql`
    query MyEvents {
        myEvents {
            id
            title
            status
            events_thumbnail
            url_key
            type { name }
            location { name }
        }
    }
`;

export default function CreatedByMe() {
    const { customer, loading: loadingCustomer, token } = useCustomer();
    const [events, setEvents] = useState([]);
    const [error, setError] = useState("");

    const {
        data,
        loading: eventsLoading,
        error: queryError,
        refetch,
    } = useQuery(MY_EVENTS_QUERY, {
        skip: !customer,
        fetchPolicy: "network-only",
        context: token ? { headers: { Authorization: `Bearer ${token}` } } : undefined,
    });

    useEffect(() => {
        if (data?.myEvents) {
            setEvents(data.myEvents);
            setError("");
        }
    }, [data]);

    useEffect(() => {
        if (queryError) {
            setError(queryError.message || "Unable to load your events right now.");
        }
    }, [queryError]);

    useEffect(() => {
        const handler = () => refetch?.();
        window.addEventListener("customer-event-created", handler);
        return () => window.removeEventListener("customer-event-created", handler);
    }, [refetch]);

    if (loadingCustomer || (eventsLoading && events.length === 0)) {
        return <p className="text-gray-500">Loading your events…</p>;
    }

    if (!customer) {
        return <p className="text-gray-600">Log in as a customer to see events you created.</p>;
    }

    if (error) {
        return (
            <div className="rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {error}
            </div>
        );
    }

    if (events.length === 0) {
        return <p className="text-gray-600">You have not created any events yet.</p>;
    }

    const statusBadgeClasses = (status) => {
        const normalized = (status || "").toLowerCase();
        if (normalized === "approved") return "bg-green-100 text-green-800";
        if (normalized === "rejected") return "bg-red-100 text-red-800";
        return "bg-yellow-100 text-yellow-800";
    };

    return (
        <div className="grid gap-4 md:grid-cols-2">
            {events.map((event) => {
                const thumbnail = event.events_thumbnail
                    ? `/storage/events/thumbs/${event.events_thumbnail}`
                    : null;

                return (
                    <div key={event.id} className="flex gap-4 rounded border border-gray-200 p-3">
                        <div className="h-20 w-20 flex-shrink-0 overflow-hidden rounded bg-gray-100">
                            {thumbnail ? (
                                <img
                                    src={thumbnail}
                                    alt={event.title}
                                    className="h-full w-full object-cover"
                                />
                            ) : (
                                <div className="flex h-full w-full items-center justify-center text-sm text-gray-400">
                                    No image
                                </div>
                            )}
                        </div>
                        <div className="flex flex-col justify-between">
                            <div>
                                <a
                                    href={`/events/${event.url_key}`}
                                    className="text-2xl font-bold text-blue-600 hover:underline hover:text-blue-800"
                                >
                                    <h3 className="text-lg font-semibold">{event.title}</h3>
                                </a>
                                    <p className="text-sm text-gray-600">
                                        {event.type?.name || "No type"} • {event.location?.name || "No location"}
                                    </p>
                            </div>
                            <span
                                className={`inline-block w-fit rounded px-2 py-1 text-xs font-semibold ${statusBadgeClasses(event.status)}`}
                            >
                                {event.status || "Pending"}
                            </span>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
