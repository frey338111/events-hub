import React, { useEffect, useState, Suspense, lazy } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import useCustomer from "../../hooks/useCustomer";

// Lazy load the calendar component
const EventCalendar = lazy(() => import("../calendar/EventCalendar"));

const UPCOMING_EVENTS_QUERY = gql`
    query UpcomingEvents($customer_id: ID!) {
        upcomingEvents(customer_id: $customer_id) {
            id
            title
            events_thumbnail
            url_key
            start_time
        }
    }
`;

export default function UpcomingEvents() {
    const { customer, loading: loadingCustomer, token } = useCustomer();
    const [events, setEvents] = useState([]);
    const { data, loading } = useQuery(UPCOMING_EVENTS_QUERY, {
        skip: !customer,
        variables: { customer_id: customer?.id },
        fetchPolicy: "network-only",
        context: token ? { headers: { Authorization: `Bearer ${token}` } } : undefined,
    });

    useEffect(() => {
        if (data?.upcomingEvents) {
            setEvents(data.upcomingEvents);
        }
    }, [data]);

    if (loadingCustomer || loading) {
        return <p className="text-gray-500">Loading upcoming events…</p>;
    }

    if (!customer) {
        return (
            <p className="text-gray-600">
                Log in to see your upcoming events.
            </p>
        );
    }

    return (
        <div className="p-4 bg-white shadow rounded mt-6">
            <h2 className="text-xl font-bold mb-4">My Upcoming Events</h2>

            {events.length === 0 ? (
                <p className="text-gray-600">You have no upcoming booked events.</p>
            ) : (
                <Suspense fallback={<div>Loading calendar...</div>}>
                    <EventCalendar events={events} />
                </Suspense>
            )}
        </div>
    );
}
