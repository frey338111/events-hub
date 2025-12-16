import React from "react";
import QRCode from "react-qr-code";

export default function EventTicket({ ticketId, hashKey, customerId }) {
    const ticketUrl = `/event/ticket/${ticketId}/${hashKey}/${customerId}`;

    return (
        <div className="p-6 bg-white shadow rounded mt-6 text-left">

            <div className="flex justify-left mb-4">
                <QRCode value={ticketUrl} size={400}/>
            </div>

            <a
                href={ticketUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="text-blue-600 underline break-all hover:text-blue-800"
            >
               View Ticket
            </a>
        </div>
    );
}
