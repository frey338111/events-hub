import React from "react";
import { Link } from "react-router-dom";
import useCustomer from "../hooks/useCustomer";

export default function Nav() {
    const { customer, loading, logout } = useCustomer();

    return (
        <nav className="bg-gray-800 text-white px-6 py-3 shadow">
            <ul className="flex items-center">

                {/* LEFT SIDE */}
                <div className="flex space-x-6">
                    <li>
                        <Link to="/" className="hover:text-gray-300">
                            Home
                        </Link>
                    </li>
                    <li>
                        <Link to="/list" className="hover:text-gray-300">
                            Find Events
                        </Link>
                    </li>
                </div>

                {/* RIGHT SIDE */}
                <div className="flex space-x-6 ml-auto">
                    {!loading && !customer && (
                        <>
                            <li>
                                <Link
                                    to="/customer/register"
                                    className="hover:text-gray-300"
                                >
                                    Register
                                </Link>
                            </li>
                            <li>
                                <Link
                                    to="/customer/login"
                                    className="hover:text-gray-300"
                                >
                                    Login
                                </Link>
                            </li>
                        </>
                    )}

                    {!loading && customer && (
                        <>
                            <li>
                                <Link
                                    to="/customer/account"
                                    className="hover:text-gray-300"
                                >
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
                </div>

            </ul>
        </nav>
    );
}
