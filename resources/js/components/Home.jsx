import React from 'react';
import useCustomer from "../hooks/useCustomer";
import PopularEvents from "./homepage/PopularEvents";
import UpcomingHomepage from "./homepage/UpcomingHomepage";
import Content from "./homepage/Content";

export default function Header() {
    const { customer, loading: customerLoading } = useCustomer();

    return (
        <main className="p-6 space-y-6">
            <>
            <Content />
            <PopularEvents />
            <UpcomingHomepage />
            </>
        </main>
    );
}
