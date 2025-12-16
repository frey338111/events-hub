import React, { useEffect, useState, lazy, Suspense } from "react";
import { useMutation, useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import { useParams } from "react-router-dom";
import useCustomer from "../hooks/useCustomer";
const EventTicket = lazy(() => import("./EventTicket"));

const EVENT_QUERY = gql`
    query EventByUrl($url_key: String!) {
        eventByUrlKey(url_key: $url_key) {
            id
            title
            description
            start_time
            end_time
            status
            customer_id
            customer_name
            capacity
            events_image
            type { name }
            location { name }
        }
    }
`;

const CANCEL_EVENT_MUTATION = gql`
    mutation CancelCustomerEvent($eventId: ID!) {
        cancelCustomerEvent(event_id: $eventId) {
            id
            status
        }
    }
`;

export default function EventDetail() {
    const { url_key } = useParams();
    const [event, setEvent] = useState(null);
    const { customer, loading: customerLoading, token } = useCustomer();
    const [message, setMessage] = useState("");
    const [hasTicket, setHasTicket] = useState(false);
    const [ticketId, setTicketId] = useState(0);
    const [ticketHashKey, setTicketHashKey] = useState('');
    const [showTicketView, setShowTicketView] = useState(false);
    const [countdown, setCountdown] = useState(null);
    const isEventCreator = customer && event && Number(event.customer_id) === Number(customer.id);
    const [canceling, setCanceling] = useState(false);

    const { data: eventData, loading: eventLoading } = useQuery(EVENT_QUERY, {
        variables: { url_key },
    });

    const [cancelEventMutation] = useMutation(CANCEL_EVENT_MUTATION, {
        context: token ? { headers: { Authorization: `Bearer ${token}` } } : undefined,
    });

    useEffect(() => {
        if (eventData?.eventByUrlKey) {
            setEvent(eventData.eventByUrlKey);
        }
    }, [eventData]);

    useEffect(() => {
        if (!event?.start_time) return;

        const parseDate = (value) => {
            // Normalize to ISO-ish string for consistent parsing
            const normalized = value.replace(" ", "T");
            const date = new Date(normalized);
            return isNaN(date.getTime()) ? null : date;
        };

        const target = parseDate(event.start_time);
        if (!target) {
            setCountdown(null);
            return;
        }

        const updateCountdown = () => {
            const now = new Date().getTime();
            const diff = target.getTime() - now;

            if (diff <= 0) {
                setCountdown({ label: "Event started", expired: true });
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((diff / (1000 * 60)) % 60);
            const seconds = Math.floor((diff / 1000) % 60);

            setCountdown({
                label: `${days}d ${hours}h ${minutes}m ${seconds}s`,
                expired: false,
            });
        };

        updateCountdown();
        const timer = setInterval(updateCountdown, 1000);

        return () => clearInterval(timer);
    }, [event?.start_time]);

    useEffect(() => {
        if (customerLoading) return; // wait for customer load to avoid skipping check
        if (!customer || !event || isEventCreator) return;

        async function checkTicket() {
            const res = await fetch(`/customer/ticket/status/${event.id}`, {
                headers: token ? { "Authorization": `Bearer ${token}` } : {},
            });

            const json = await res.json();
            setHasTicket(json.booked);
            if(json.booked === true){
                setTicketId(json.ticket_id);
                setTicketHashKey(json.hash_key);
            }
        }
        checkTicket();
    }, [event, customer, customerLoading, isEventCreator, token]);

    const handleCancelTicket = async () => {
        setMessage("");

        if (!customer) {
            setMessage("Please log in to cancel this event.");
            return;
        }

        if (!window.confirm("Are you sure you want to cancel this ticket?")) {
            return;
        }

        // Fetch CSRF cookie first
        const res = await fetch("/customer/cancel-ticket", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                ...(token ? { "Authorization": `Bearer ${token}` } : {}),
            },
            body: JSON.stringify({
                                     ticketId: ticketId,
                                 }),
        });

        if (res.status === 200) {
            setMessage("Successfully Canceled!");
            setHasTicket(false);
        } else {
            const data = await res.json().catch(() => ({}));
            setMessage(data.message || "Cancel failed.");
        }
    }

    const handleCancelEvent = async () => {
        if (!customer || !isEventCreator) {
            setMessage("You are not allowed to cancel this event.");
            return;
        }

        if (!window.confirm("Are you sure you want to cancel this event?")) {
            return;
        }

        setCanceling(true);
        setMessage("");
        try {
            const { data } = await cancelEventMutation({
                variables: { eventId: event.id },
            });
            const updated = data?.cancelCustomerEvent;
            if (updated?.status === "canceled") {
                setEvent(prev => ({ ...prev, status: updated.status }));
                setMessage("event successfully canceled");
            } else {
                setMessage("Cancel event failed.");
            }
        } catch (error) {
            setMessage("Cancel event failed.");
        } finally {
            setCanceling(false);
        }
    };
    const handleBookEvent = async () => {
        setMessage("");

        // Ensure logged in
        if (!customer) {
            setMessage("Please log in to book this event.");
            return;
        }

        const res = await fetch("/customer/book-event", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                ...(token ? { "Authorization": `Bearer ${token}` } : {}),
            },
            body: JSON.stringify({
                                     event_id: event.id,
                                 }),
        });

        if (res.status === 200) {
            const data = await res.json().catch(() => ({}));
            setHasTicket(true);
            setTicketId(data.ticketId);
            setMessage("Successfully booked!");
        } else {
            const data = await res.json().catch(() => ({}));
            setMessage(data.message || "Booking failed.");
        }
    };

    if (!event || eventLoading) return <p className="p-6">Loading event...</p>;

    return (
        <>
            {message && (
                <div
                    className="fixed inset-0 z-40 flex items-center justify-center"
                    onClick={() => setMessage("")}
                >
                    <div className="absolute inset-0 bg-black/50" />
                    <div
                        className={`relative mx-4 w-full max-w-sm rounded border p-4 text-center shadow-lg ${
                            message.toLowerCase().includes("success")
                                ? "bg-green-100 text-green-700 border-green-300"
                                : "bg-red-100 text-red-700 border-red-300"
                        }`}
                    >
                        {message}
                        <div className="mt-2 text-xs text-gray-600">
                            Click anywhere to close
                        </div>
                    </div>
                </div>
            )}
        <div className="p-6 space-y-4">
            <div className="w-full flex-shrink-0">
                <img
                    src={
                        event.events_image
                            ? `/storage/events/${event.events_image}`
                            : `/storage/events/default-event.jpg`
                    }
                    alt={event.title}
                    className="rounded border object-cover w-full"
                />

            </div>

            <h1 className="text-3xl font-bold">{event.title} </h1>
            <p className="text-gray-600">
                <strong>Created by:</strong>{" "}
                {isEventCreator
                    ? "Me"
                    : Number(event.customer_id) === 0
                        ? "System"
                        : event.customer_name || "System"}
            </p>
            {countdown && (
                <div className="inline-flex items-center gap-3 rounded bg-slate-900 px-4 py-3 text-sm font-semibold text-blue-100 shadow-md">
                    <span className="uppercase tracking-wide text-slate-300">Starts in</span>
                    <span
                        className={`font-mono text-xl sm:text-2xl tabular-nums px-3 py-1 rounded ${
                            countdown.expired ? "bg-red-600 text-white" : "bg-slate-800 text-blue-100"
                        }`}
                    >
                        {countdown.label}
                    </span>
                </div>
            )}
            <p className="text-gray-700">{event.description}</p>
            <p><strong>Type:</strong> {event.type.name}</p>
            <p><strong>Start:</strong> {event.start_time}</p>
            <p><strong>End:</strong> {event.end_time}</p>
            <p><strong>Location:</strong> {event.location.name}</p>
            <p><strong>Remaining Space:</strong> {event.capacity}</p>
            {event.status && (
                <p><strong>Status:</strong> {event.status}</p>
            )}

            {isEventCreator && event.status !== "canceled" && (
                <button
                    onClick={handleCancelEvent}
                    disabled={canceling}
                    className="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                >
                    {canceling ? "Canceling..." : "Cancel Event"}
                </button>
            )}

            {!customerLoading && customer && !isEventCreator && (
                hasTicket ? (
                    <>
                        <button
                            onClick={() => setShowTicketView(true)}
                            className="mt-6 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                        >
                            Show Ticket
                        </button>
                        <button
                            onClick={handleCancelTicket}
                            className="mt-6 ml-3 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                        >
                            Cancel Ticket
                        </button>
                    </>
                ) : (
                    <button
                        onClick={handleBookEvent}
                        className="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                        Book Event
                    </button>
                )
            )}
            {showTicketView && !isEventCreator && (
                <Suspense fallback={<div>Loading ticket...</div>}>
                    <EventTicket
                        ticketId={ticketId}
                        hashKey={ticketHashKey}
                        customerId={customer.id}
                    />
                </Suspense>
            )}
        </div>
        </>
    );
}
