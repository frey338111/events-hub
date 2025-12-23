import React from 'react';
import Header from './Header';
import Nav from './Nav';
import Content from './Content';
import CustomerRegister from './CustomerRegister';
import CustomerAccount from './CustomerAccount';
import Home from './Home';
import Footer from './Footer';
import { Routes, Route } from "react-router-dom";
import EventDetail from "./EventDetail";
import CustomerLogin from "./CustomerLogin";
import TicketViewer from "./TicketViewer";
import ResetPassword from "./ResetPassword";
export default function AppLayout() {
    return (
        <div className="font-sans w-full max-w-6xl mx-auto px-4">
            <Header />
            <Nav />
            <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/list" element={<Content />} />
                <Route path="/events/:url_key" element={<EventDetail />} />
                <Route path="/customer/register" element={<CustomerRegister />} />
                <Route path="/customer/account" element={<CustomerAccount />} />
                <Route path="/customer/login" element={<CustomerLogin />} />
                <Route path="/customer/verify/:hash_key" element={<CustomerLogin />} />
                <Route
                    path="/event/ticket/:ticketId/:hashKey/:customerId"
                    element={<TicketViewer />}
                />
                <Route path="/customer/reset-password/:token" element={<ResetPassword />} />
            </Routes>
            <Footer />
        </div>
    );
}
