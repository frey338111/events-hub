import React, { useEffect, useState } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";

const UNREAD_MESSAGES_QUERY = gql`
    query UnreadMessages {
        unreadCustomerMessages {
            id
            title
            message
            created_at
        }
    }
`;

export default function NewMessage({ customer, token, customerLoading = false }) {
    const [messages, setMessages] = useState([]);
    const [expanded, setExpanded] = useState(false);
    const { data, loading } = useQuery(UNREAD_MESSAGES_QUERY, {
        skip: !token,
        fetchPolicy: "network-only",
        context: token ? { headers: { Authorization: `Bearer ${token}` } } : undefined,
    });

    useEffect(() => {
        if (data?.unreadCustomerMessages) {
            setMessages(data.unreadCustomerMessages);
        } else if (!loading && !token) {
            setMessages([]);
        }
    }, [data, loading, token]);

    if (customerLoading || !customer) {
        return null;
    }

    if (!loading && messages.length === 0) {
        return null;
    }

    const countLabel = `You got ${messages.length} unread message${messages.length !== 1 ? "s" : ""}`;

    return (
        <div className="bg-white border border-blue-100 rounded shadow-sm">
            <button
                onClick={() => setExpanded((prev) => !prev)}
                className="w-full text-left px-4 py-3 flex items-center justify-between focus:outline-none focus:ring focus:ring-blue-200"
            >
                <div>
                    <p className="text-sm text-blue-600 font-semibold">
                        {loading ? "Loading your messages..." : countLabel}
                    </p>
                    {!loading && (
                        <p className="text-xs text-gray-500">Click to view details</p>
                    )}
                </div>
                <span className="text-blue-500 text-lg">{expanded ? "–" : "+"}</span>
            </button>

            {expanded && !loading && (
                <div className="border-t border-blue-50 px-4 py-3 space-y-3">
                    {messages.map((msg) => (
                        <div key={msg.id} className="p-3 bg-blue-50 rounded">
                            <p className="text-sm font-semibold text-blue-800">{msg.title}</p>
                            <p className="text-sm text-blue-900">{msg.message}</p>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
