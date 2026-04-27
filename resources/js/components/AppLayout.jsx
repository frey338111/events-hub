import React from 'react';
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
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
import CmsPage from "./CmsPage";
import CalendarPage from "./CalendarPage";
import { SiteConfigProvider } from "../context/SiteConfigContext";

const ALL_CONFIGS_QUERY = gql`
    query AllConfigs {
        allConfigs {
            name
            value
        }
    }
`;

export default function AppLayout() {
    const { data, loading } = useQuery(ALL_CONFIGS_QUERY);
    const [isMobileNavOpen, setIsMobileNavOpen] = React.useState(false);

    const configList = data?.allConfigs ?? [];
    const config = configList.reduce((result, item) => {
        result[item.name] = item.value;

        return result;
    }, {});
    const toggleMobileNav = React.useCallback(() => {
        setIsMobileNavOpen((isOpen) => !isOpen);
    }, []);
    const closeMobileNav = React.useCallback(() => {
        setIsMobileNavOpen(false);
    }, []);

    return (
        <SiteConfigProvider value={{ config, configList, loading }}>
            <div className="font-sans w-full max-w-6xl mx-auto px-4">
                <Header
                    isMobileNavOpen={isMobileNavOpen}
                    onToggleMobileNav={toggleMobileNav}
                />
                <div className="relative">
                    <Nav
                        isMobileNavOpen={isMobileNavOpen}
                        onCloseMobileNav={closeMobileNav}
                    />
                    <main
                        className={`w-full min-w-0 transition-transform duration-200 md:translate-x-0 ${
                            isMobileNavOpen ? "translate-x-[200px]" : "translate-x-0"
                        }`}
                    >
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
                            <Route path="/calendar" element={<CalendarPage />} />
                            <Route path="/:slug" element={<CmsPage />} />
                        </Routes>
                        <Footer />
                    </main>
                </div>
            </div>
        </SiteConfigProvider>
    );
}
