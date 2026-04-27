import React from 'react';

export default function Header({ isMobileNavOpen, onToggleMobileNav }) {
    return (
        <header className="bg-white-600 text-white p-4 shadow flex items-center space-x-4">
            <button
                type="button"
                onClick={onToggleMobileNav}
                className="md:hidden inline-flex h-10 w-10 shrink-0 items-center justify-center rounded border border-gray-300 text-gray-800 hover:bg-gray-100"
                aria-label={isMobileNavOpen ? "Close menu" : "Open menu"}
                aria-expanded={isMobileNavOpen}
            >
                <span className="sr-only">
                    {isMobileNavOpen ? "Close menu" : "Open menu"}
                </span>
                <span className="block text-2xl leading-none" aria-hidden="true">
                    {isMobileNavOpen ? "×" : "☰"}
                </span>
            </button>

            <img
                src="/images/events_logo.jpg"
                alt="Logo"
                className="hidden h-24 w-auto md:block"
            />

            <div className="flex flex-1 flex-col items-center justify-center md:hidden">
                <img
                    src="/images/events_logo.jpg"
                    alt="Logo"
                    className="h-16 w-auto"
                />
                <h2 className="text-xl font-bold text-black">
                    The Events Hub
                </h2>
            </div>

            <h2 className="hidden text-xl text-black font-bold md:block">
                The Events Hub
            </h2>
        </header>
    );
}
