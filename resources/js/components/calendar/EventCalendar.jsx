import React, { useState } from "react";
import dayjs from "dayjs";

export default function EventCalendar({ events }) {
    const [currentMonth, setCurrentMonth] = useState(dayjs());
    const [hoverEvent, setHoverEvent] = useState(null);

    const startOfMonth = currentMonth.startOf("month");
    const endOfMonth = currentMonth.endOf("month");

    // Build an array of days for the calendar grid
    const days = [];
    let day = startOfMonth.startOf("week");

    while (day.isBefore(endOfMonth.endOf("week"))) {
        days.push(day);
        day = day.add(1, "day");
    }

    // Convert event dates into a map by date string (YYYY-MM-DD)
    const eventsByDate = {};
    events.forEach((event) => {
        const date = dayjs(event.start_time).format("YYYY-MM-DD");
        if (!eventsByDate[date]) eventsByDate[date] = [];
        eventsByDate[date].push(event);
    });

    return (
        <div className="p-4 bg-white shadow rounded mt-6">

            {/* Header */}
            <div className="flex justify-between items-center mb-4">
                <button
                    className="px-3 py-1 bg-gray-200 rounded"
                    onClick={() => setCurrentMonth(currentMonth.subtract(1, "month"))}
                >
                    ←
                </button>

                <h2 className="text-xl font-bold">
                    {currentMonth.format("MMMM YYYY")}
                </h2>

                <button
                    className="px-3 py-1 bg-gray-200 rounded"
                    onClick={() => setCurrentMonth(currentMonth.add(1, "month"))}
                >
                    →
                </button>
            </div>

            {/* Calendar Grid */}
            <div className="grid grid-cols-7 gap-2 text-center font-semibold text-gray-600 mb-2">
                {["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"].map((d) => (
                    <div key={d}>{d}</div>
                ))}
            </div>

            <div className="grid grid-cols-7 gap-2 text-center">
                {days.map((d, idx) => {
                    const formatted = d.format("YYYY-MM-DD");
                    const isCurrentMonth = d.month() === currentMonth.month();
                    const dayEvents = eventsByDate[formatted];
                    const backgroundStyle = dayEvents
                        ? {
                            backgroundImage: `linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(${
                                dayEvents[0].events_thumbnail
                                    ? `/storage/events/thumbs/${dayEvents[0].events_thumbnail}`
                                    : `/storage/events/thumbs/default-event.jpg`
                            })`,
                            backgroundSize: "cover",
                            backgroundPosition: "center",
                          }
                        : {};
                    const linkTarget =
                        dayEvents && dayEvents[0]?.url_key
                            ? `/events/${dayEvents[0].url_key}`
                            : null;

                    return (
                        <div
                            key={idx}
                            className={`p-2 border rounded relative h-20 ${
                                linkTarget ? "cursor-pointer" : ""
                            }
                                ${isCurrentMonth ? "bg-white" : "bg-gray-100 text-gray-400"}
                                ${dayEvents ? "border-blue-500 bg-blue-50" : ""}
                            `}
                            style={backgroundStyle}
                            role={linkTarget ? "link" : undefined}
                            tabIndex={linkTarget ? 0 : undefined}
                            onClick={() => linkTarget && (window.location.href = linkTarget)}
                            onKeyDown={(e) => {
                                if (!linkTarget) return;
                                if (e.key === "Enter" || e.key === " ") {
                                    e.preventDefault();
                                    window.location.href = linkTarget;
                                }
                            }}
                            onMouseEnter={() => dayEvents && setHoverEvent({ date: formatted, events: dayEvents })}
                            onMouseLeave={() => setHoverEvent(null)}
                        >
                            <div className="font-medium inline-block px-2 py-1 rounded border border-white bg-white/70">
                                {d.date()}
                            </div>

                            {/* Background now uses event thumbnail with soft overlay (set on parent) */}

                            {/* Tooltip */}
                            {hoverEvent &&
                             hoverEvent.date === formatted && (
                                 <div className="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 z-10 bg-white border shadow-lg rounded p-2 w-48 text-sm text-left">
                                     <strong className="block mb-1">Events:</strong>
                                     {hoverEvent.events.map(ev => (
                                         <div key={ev.id} className="mb-2">
                                             <div className="font-semibold">

                                                 <a
                                                     href={`/events/${ev.url_key}`}
                                                     className="text-1xl font-bold text-blue-600 hover:underline hover:text-blue-800"
                                                 >
                                                     {ev.title}
                                                 </a>

                                             </div>
                                             <div className="text-gray-500 text-xs">
                                                 {dayjs(ev.start_time).format("HH:mm")}
                                             </div>
                                         </div>
                                     ))}
                                 </div>
                             )}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
