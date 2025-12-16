import React, { useEffect, useState } from "react";
import CreateNew from "./CreateNew";
import CreatedByMe from "./CreatedByMe";

export default function MyEvents() {
    const [showCreate, setShowCreate] = useState(false);

    useEffect(() => {
        const handler = () => setShowCreate(false);
        window.addEventListener("close-create-event-modal", handler);
        return () => window.removeEventListener("close-create-event-modal", handler);
    }, []);

    return (
        <div className="p-4 bg-white shadow rounded mt-6">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-bold">My Events</h2>
                <button
                    type="button"
                    onClick={() => setShowCreate(true)}
                    className="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700"
                >
                    Create New
                </button>



            </div>
            <CreatedByMe />
            {showCreate && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div
                        className="absolute inset-0 bg-black/40"
                        onClick={() => setShowCreate(false)}
                    />
                    <div className="relative z-10 w-full max-w-3xl mx-4 bg-white rounded shadow-lg p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold">Create Event</h3>
                            <button
                                type="button"
                                onClick={() => setShowCreate(false)}
                                className="text-gray-600 hover:text-gray-900"
                            >
                                ✕
                            </button>
                        </div>
                        <CreateNew />
                    </div>
                </div>
            )}
        </div>
    );
}
