import React, { Suspense, lazy } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";

const EventCalendar = lazy(() => import("./calendar/EventCalendar"));

const CALENDAR_PAGE_QUERY = gql`
    query CalendarPage($introSlug: String!) {
        upcomingEvents {
            id
            title
            events_thumbnail
            url_key
            start_time
        }
        pageBySlug(slug: $introSlug) {
            title
            slug
            content
        }
    }
`;

export default function CalendarPage() {
    const { data, loading, error } = useQuery(CALENDAR_PAGE_QUERY, {
        fetchPolicy: "network-only",
        variables: { introSlug: "calendar-intro" },
    });
    const events = data?.upcomingEvents ?? [];
    const introPage = data?.pageBySlug ?? null;

    if (loading) {
        return <p className="text-gray-500">Loading calendar...</p>;
    }

    if (error) {
        console.error("Failed to load calendar events.", error);

        return <p className="text-gray-600">Unable to load calendar.</p>;
    }

    return (
        <div className="p-4 bg-white shadow rounded mt-6">

            <h2 className="text-xl font-bold mb-4">Upcoming Events</h2>

            {introPage?.content ? (
                <div
                    className="prose prose-slate max-w-none mb-6"
                    dangerouslySetInnerHTML={{ __html: introPage.content }}
                />
            ) : null}



            {events.length === 0 ? (
                <p className="text-gray-600">No upcoming events found.</p>
            ) : (
                <Suspense fallback={<div>Loading calendar...</div>}>
                    <EventCalendar events={events} />
                </Suspense>
            )}
        </div>
    );
}
