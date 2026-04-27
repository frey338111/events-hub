import React from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import { Link } from "react-router-dom";
import useCustomer from "../hooks/useCustomer";

const SITE_NAV_QUERY = gql`
    query SiteNavConfig($name: String!) {
        configByName(name: $name) {
            value
        }
    }
`;

export default function Nav({ isMobileNavOpen = false, onCloseMobileNav }) {
    const { customer, loading, logout } = useCustomer();
    const { data } = useQuery(SITE_NAV_QUERY, {
        variables: { name: "site-nav" },
    });
    const [expandedItems, setExpandedItems] = React.useState({});
    const getIsMobile = React.useCallback(() => {
        if (typeof window === "undefined") {
            return false;
        }

        return typeof window.matchMedia === "function"
            ? window.matchMedia("(max-width: 767px)").matches
            : window.innerWidth < 768;
    }, []);
    const [isMobile, setIsMobile] = React.useState(getIsMobile);

    const menuItems = React.useMemo(() => {
        const rawValue = data?.configByName?.value;

        if (!rawValue) {
            return [];
        }

        try {
            const parsed = JSON.parse(rawValue);

            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            console.error("Failed to parse site-nav config.", error);

            return [];
        }
    }, [data]);

    React.useEffect(() => {
        if (typeof window === "undefined") {
            return undefined;
        }

        const mediaQuery = typeof window.matchMedia === "function"
            ? window.matchMedia("(max-width: 767px)")
            : null;

        const updateViewportMode = () => {
            setIsMobile(getIsMobile());
        };

        updateViewportMode();

        if (mediaQuery) {
            mediaQuery.addEventListener("change", updateViewportMode);

            return () => {
                mediaQuery.removeEventListener("change", updateViewportMode);
            };
        }

        window.addEventListener("resize", updateViewportMode);

        return () => {
            window.removeEventListener("resize", updateViewportMode);
        };
    }, [getIsMobile]);

    const toggleExpandedItem = React.useCallback((key) => {
        setExpandedItems((current) => ({
            ...current,
            [key]: !current[key],
        }));
    }, []);

    const renderDesktopMenuItems = (items, level = 0) =>
        items.map((item, index) => {
            const label = item.title || item.label || "Untitled";
            const url = item.url || "#";
            const children = Array.isArray(item.children) ? item.children : [];
            const key = `desktop-${level}-${item.id ?? index}-${label}-${url}`;

            if (children.length > 0) {
                return (
                    <li key={key} className="group relative">
                        <Link
                            to={url}
                            className="inline-flex items-center gap-2 hover:text-gray-300"
                        >
                            <span>{label}</span>
                            <span className="text-xs text-gray-400">▾</span>
                        </Link>

                        <ul
                            className={
                                level === 0
                                    ? "invisible absolute left-0 top-full z-50 min-w-52 pt-2 opacity-0 transition-opacity duration-150 group-hover:visible group-hover:opacity-100"
                                    : "invisible absolute left-full top-0 z-50 min-w-52 ps-2 opacity-0 transition-opacity duration-150 group-hover:visible group-hover:opacity-100"
                            }
                        >
                            <div className="rounded-md bg-gray-900 py-2 shadow-lg">
                                {renderDesktopMenuItems(children, level + 1)}
                            </div>
                        </ul>
                    </li>
                );
            }

            return (
                <li key={key} className={level > 0 ? "px-4 py-2" : ""}>
                    <Link to={url} className="block hover:text-gray-300">
                        {label}
                    </Link>
                </li>
            );
        });

    const renderMobileMenuItems = (items, level = 0) =>
        items.map((item, index) => {
            const label = item.title || item.label || "Untitled";
            const url = item.url || "#";
            const children = Array.isArray(item.children) ? item.children : [];
            const key = `mobile-${level}-${item.id ?? index}-${label}-${url}`;
            const paddingLeft = `${level * 16}px`;
            const isExpanded = Boolean(expandedItems[key]);

            if (children.length > 0) {
                return (
                    <li key={key} className="mb-2 last:mb-0">
                        <div
                            className="flex items-center justify-between gap-3 rounded-lg bg-gray-900/50 px-4"
                            style={{ marginLeft: paddingLeft }}
                        >
                            <Link
                                to={url}
                                className="block min-w-0 flex-1 py-3 text-white hover:text-gray-300"
                            >
                                <span className="font-medium">{label}</span>
                            </Link>
                            <button
                                type="button"
                                onClick={() => toggleExpandedItem(key)}
                                className="flex items-center py-3 text-white"
                                aria-expanded={isExpanded}
                                aria-label={isExpanded ? `Collapse ${label}` : `Expand ${label}`}
                            >
                                <span className={`text-lg font-bold leading-none text-gray-300 transition-transform ${isExpanded ? "rotate-180" : ""}`}>
                                    ▾
                                </span>
                            </button>
                        </div>

                        {isExpanded && (
                            <ul className="mt-2 ps-4">
                                {renderMobileMenuItems(children, level + 1)}
                            </ul>
                        )}
                    </li>
                );
            }

            return (
                <li
                    key={key}
                    className="mb-2 last:mb-0 rounded-lg bg-gray-900/50 px-4"
                    style={{ marginLeft: paddingLeft }}
                >
                    <Link
                        to={url}
                        className="block py-3 text-white hover:text-gray-300"
                    >
                        {label}
                    </Link>
                </li>
            );
        });

    if (!isMobile) {
        return (
            <nav className="bg-gray-800 px-6 py-3 text-white shadow">
                <div className="flex items-center">
                    <ul className="flex items-center gap-6">
                        {renderDesktopMenuItems(menuItems)}
                    </ul>

                    <ul className="ml-auto flex items-center gap-6">
                        {!loading && !customer && (
                            <>
                                <li>
                                    <Link to="/customer/register" className="hover:text-gray-300">
                                        Register
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/customer/login" className="hover:text-gray-300">
                                        Login
                                    </Link>
                                </li>
                            </>
                        )}

                        {!loading && customer && (
                            <>
                                <li>
                                    <Link to="/customer/account" className="hover:text-gray-300">
                                        My Account
                                    </Link>
                                </li>
                                <li>
                                    <button
                                        onClick={async () => {
                                            await logout();
                                            window.location.reload();
                                        }}
                                        className="hover:text-gray-300"
                                    >
                                        Logout
                                    </button>
                                </li>
                            </>
                        )}
                    </ul>
                </div>
            </nav>
        );
    }

    return (
        <div
            className={`absolute inset-0 z-50 transition-opacity duration-200 ${
                isMobileNavOpen ? "opacity-100" : "pointer-events-none opacity-0"
            }`}
            onClick={onCloseMobileNav}
        >
            <nav
                className="w-[200px] max-w-[200px] bg-white/35 py-3 pr-3 shadow backdrop-blur-sm"
                onClick={(event) => event.stopPropagation()}
            >
                <ul className="rounded-lg bg-white/70 py-2">
                    {renderMobileMenuItems(menuItems)}

                    {!loading && !customer && (
                        <>
                            <li className="mb-2 last:mb-0 rounded-lg bg-gray-900/50 px-4">
                                <Link to="/customer/register" className="block py-3 text-white hover:text-gray-300">
                                    Register
                                </Link>
                            </li>
                            <li className="mb-2 last:mb-0 rounded-lg bg-gray-900/50 px-4">
                                <Link to="/customer/login" className="block py-3 text-white hover:text-gray-300">
                                    Login
                                </Link>
                            </li>
                        </>
                    )}

                    {!loading && customer && (
                        <>
                            <li className="mb-2 last:mb-0 rounded-lg bg-gray-900/50 px-4">
                                <Link to="/customer/account" className="block py-3 text-white hover:text-gray-300">
                                    My Account
                                </Link>
                            </li>
                            <li className="mb-2 last:mb-0 rounded-lg bg-gray-900/50 px-4">
                                <button
                                    onClick={async () => {
                                        await logout();
                                        window.location.reload();
                                    }}
                                    className="block w-full py-3 text-left text-white hover:text-gray-300"
                                >
                                    Logout
                                </button>
                            </li>
                        </>
                    )}

                </ul>


            </nav>
        </div>
    );
}
