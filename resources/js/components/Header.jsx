import React from 'react';

export default function Header() {
    return (
        <header className="bg-white-600 text-white p-4 shadow flex items-center space-x-4">
            <img
                src="/images/events_logo.jpg"
                alt="Logo"
                className="h-24 w-auto"
            />

            <h2 className="text-xl text-black font-bold">The Events Hub</h2>
        </header>
    );
}
