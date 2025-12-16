import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";

const TICKET_VALIDATE_QUERY = gql`
    query ValidateTicket($ticketId: ID!, $hashKey: String!, $customerId: ID!) {
        validateTicket(ticket_id: $ticketId, hash_key: $hashKey, customer_id: $customerId) {
            valid
            message
            customer {
                name
                email
            }
            event {
                title
                start_time
            }
        }
    }
`;

export default function TicketViewer() {
    const { ticketId, hashKey, customerId } = useParams();
    const { data, loading } = useQuery(TICKET_VALIDATE_QUERY, {
        variables: {
            ticketId,
            hashKey,
            customerId,
        },
        fetchPolicy: "network-only",
    });

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <p className="text-gray-500 text-lg">Validating ticket…</p>
            </div>
        );
    }

    const result = data?.validateTicket ?? { valid: false, message: "Unable to load ticket." };

    return (
        <div className="flex items-top justify-center min-h-screen bg-gray-100">
            <div className="bg-white shadow-lg rounded p-8 max-w-lg w-full text-center">

                {result.valid ? (
                    <h1 className="text-3xl font-bold text-green-600 mb-4">
                        ✔ Ticket is valid
                    </h1>
                ) : (
                    <h1 className="text-3xl font-bold text-red-600 mb-4">
                        ✘ Ticket is NOT valid
                    </h1>
                )}

                {/* Message */}
                <p className="text-gray-700 mb-6 text-lg">{result.message}</p>

                {/* CUSTOMER INFO */}
                {result.customer && (
                    <div className="mb-6">
                        <p className="text-gray-900">
                            <strong>Name:</strong> {result.customer.name}
                        </p>
                        <p className="text-gray-900">
                            <strong>Email:</strong> {result.customer.email}
                        </p>
                    </div>
                )}

                {/* EVENT INFO */}
                {result.event && (
                    <div className="mt-4">
                        <p className="text-gray-900">
                            <strong>Event:</strong> {result.event.title}
                        </p>
                        <p className="text-gray-600 text-sm">
                            Starts: {result.event.start_time}
                        </p>
                    </div>
                )}
            </div>
        </div>
    );
}
