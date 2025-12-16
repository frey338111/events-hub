import React, { useState } from "react";
import { useLocation } from "react-router-dom";
import useCustomer from "../hooks/useCustomer";

export default function CustomerRegister() {
    const location = useLocation();
    const params = new URLSearchParams(location.search);
    const tokenParam = params.get("token");

    const [name, setName] = useState("");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [passwordConfirm, setPasswordConfirm] = useState("");
    const [error, setError] = useState("");
    const [success, setSuccess] = useState("");

    const passwordChecks = [
        { label: "At least 8 characters", test: (p) => p.length >= 8 },
        { label: "Uppercase letter", test: (p) => /[A-Z]/.test(p) },
        { label: "Lowercase letter", test: (p) => /[a-z]/.test(p) },
        { label: "Number", test: (p) => /\d/.test(p) },
    ];

    const passwordIsStrong = passwordChecks.every((rule) => rule.test(password));
    const passwordsMatch = passwordConfirm.length === 0 ? true : password === passwordConfirm;

    const handleRegister = async (e) => {
        e.preventDefault();
        setError("");
        setSuccess("");

        if (!passwordIsStrong) {
            setError("Password does not meet complexity requirements.");
            return;
        }

        if (!passwordsMatch) {
            setError("Passwords do not match.");
            return;
        }

        // Submit registration
        const res = await fetch("/customer/register", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ name, email, password, passwordConfirm }),
        });
        const data = await res.json();

        if (res.ok) {
            setSuccess("Registration successful! Redirecting...");
            setTimeout(() => {
                window.location.href = "/customer/account";
            }, 800);
        } else {
            setError(data.message || "Registration failed.");
        }
    };

    return (
        <div className="max-w-md mx-auto p-6 bg-white shadow rounded">
            <h2 className="text-2xl font-bold mb-4">Customer Registration</h2>

            {tokenParam && (
                <div className="mb-4 rounded bg-blue-50 border border-blue-200 p-3 text-sm text-blue-800">
                    Token: <span className="font-mono">{tokenParam}</span>
                </div>
            )}

            {error && <div className="bg-red-100 text-red-600 p-2 mb-3 rounded">{error}</div>}
            {success && <div className="bg-green-100 text-green-700 p-2 mb-3 rounded">{success}</div>}

            <form onSubmit={handleRegister} className="space-y-4">

                <div>
                    <label className="block text-sm font-medium mb-1">Name</label>
                    <input
                        type="text"
                        className="border p-2 w-full rounded"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        required
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium mb-1">Email</label>
                    <input
                        type="email"
                        className="border p-2 w-full rounded"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium mb-1">Password</label>
                    <input
                        type="password"
                        className="border p-2 w-full rounded"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                    <ul className="mt-2 space-y-1 text-sm">
                        {passwordChecks.map((rule) => (
                            <li
                                key={rule.label}
                                className={rule.test(password) ? "text-green-700" : "text-gray-500"}
                            >
                                {rule.test(password) ? "✓" : "•"} {rule.label}
                            </li>
                        ))}
                    </ul>
                </div>

                <div>
                    <label className="block text-sm font-medium mb-1">Confirm Password</label>
                    <input
                        type="password"
                        className="border p-2 w-full rounded"
                        value={passwordConfirm}
                        onChange={(e) => setPasswordConfirm(e.target.value)}
                        required
                    />
                    {passwordConfirm.length > 0 && (
                        <p className={`mt-1 text-sm ${passwordsMatch ? "text-green-700" : "text-red-600"}`}>
                            {passwordsMatch ? "Passwords match" : "Passwords do not match"}
                        </p>
                    )}
                </div>

                <button
                    type="submit"
                    className="bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700 disabled:opacity-50"
                    disabled={!passwordIsStrong || !passwordsMatch}
                >
                    Register
                </button>
            </form>
        </div>
    );
}
