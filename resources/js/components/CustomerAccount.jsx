import React from 'react';
import MyEvents from "./events/MyEvents.jsx";
import UpcomingEvents from "./homepage/UpcomingEvents.jsx";
import NewMessage from "./homepage/NewMessage.jsx";
import useCustomer from "../hooks/useCustomer";


export default function CustomerAccount() {
    const { customer, loading: customerLoading, token } = useCustomer();

    return (
        <main className="p-6 space-y-6">
            <h3 className="text-lg font-semibold">

                {!customerLoading && !customer && (
                    <>
                     Default Content
                    </>
                )}
                {!customerLoading && customer && (
                    <>
                        Welcome Back, {customer.name}
                        <div className="mt-4">
                            <NewMessage customer={customer} token={token} customerLoading={customerLoading} />
                        </div>
                        <UpcomingEvents />
                        <MyEvents />
                    </>
                )}
            </h3>
        </main>
    );
}
