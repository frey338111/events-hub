import React, { useEffect, useState } from "react";
import { useMutation } from "@apollo/client/react";
import { gql } from "graphql-tag";
import useCustomer from "../hooks/useCustomer";
import { useParams } from "react-router-dom";

const VERIFY_EMAIL_MUTATION = gql`
    mutation VerifyEmailAddress($hashKey: String!) {
        verifyEmailAddress(hash_key: $hashKey)
    }
`;

const RESEND_VERIFICATION_MUTATION = gql`
    mutation ResendVerificationEmail($email: String!) {
        resendVerificationEmail(email: $email)
    }
`;

const RESET_PASSWORD_MUTATION = gql`
    mutation ResetPassword($email: String!) {
        resetPassword(email: $email)
    }
`;

export default function CustomerLogin() {
    const params = useParams();
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");
    const [resendStatus, setResendStatus] = useState("");
    const [info, setInfo] = useState("");
    const [resetMode, setResetMode] = useState(false);
    const { login } = useCustomer();
    const [verifyEmail] = useMutation(VERIFY_EMAIL_MUTATION);
    const [resendEmail] = useMutation(RESEND_VERIFICATION_MUTATION);
    const [requestReset, { loading: resetLoading }] = useMutation(RESET_PASSWORD_MUTATION);

    useEffect(() => {
        const hashKey = params?.hash_key || params?.hashKey;
        if (!hashKey) return;

        const verify = async () => {
            try {
                const { data } = await verifyEmail({ variables: { hashKey } });
                if (data?.verifyEmailAddress) {
                    setInfo("Your email address is verified, please login.");
                } else {
                    setInfo("Verification failed. Please try again or request a new link.");
                }
            } catch (_) {
                setInfo("Verification failed. Please try again or request a new link.");
            }
        };

        verify();
    }, [params]);

    const handleLogin = async (e) => {
        e.preventDefault();
        setError("");
        setResendStatus("");

        try {
            await login(email, password);
            window.location.href = "/customer/account";
        } catch (err) {
            setError(err.message || "Login failed. Please try again.");
        }
    };

    const handleResend = async () => {
        setError("");
        setResendStatus("");
        try {
            const { data, errors } = await resendEmail({ variables: { email } });
            if (errors && errors.length) {
                throw new Error(errors[0]?.message || "Failed to send verification email.");
            }

            if (data?.resendVerificationEmail) {
                setResendStatus("Verification email sent. Please check your inbox.");
            } else {
                setResendStatus("Account not found or already verified.");
            }
        } catch (err) {
            setResendStatus(err.message || "Failed to send verification email.");
        }
    };

    const resetPassword = async () => {
        setResetMode(true);
        setError("");
        setResendStatus("");
    }

    return (
        <div className="max-w-md mx-auto p-6 bg-white shadow rounded">
            <h2 className="text-2xl font-bold mb-4">
                {resetMode ? "Reset Password" : "Customer Login"}
            </h2>

            {error && (
                <div className="bg-red-100 text-red-600 p-2 mb-3 rounded">
                    {error}
                    {error.toLowerCase().includes("verify your email") && (
                        <button
                            type="button"
                            onClick={handleResend}
                            className="block mt-2 text-sm text-blue-700 underline"
                        >
                            Resend verification email
                        </button>
                    )}
                    {error.toLowerCase().includes("invalid credentials") && (
                        <button
                            type="button"
                            onClick={resetPassword}
                            className="block mt-2 text-sm text-blue-700 underline"
                        >
                            Send password reset email
                        </button>
                    )}
                </div>
            )}

            {resendStatus && (
                <div className="bg-blue-50 text-blue-700 p-2 mb-3 rounded">
                    {resendStatus}
                </div>
            )}

            {info && (
                <div className="bg-green-50 text-green-700 p-2 mb-3 rounded">
                    {info}
                </div>
            )}

            {resetMode ? (
                <form
                    onSubmit={async (e) => {
                        e.preventDefault();
                        setResendStatus("");
                        setError("");
                        try {
                            const { data, errors } = await requestReset({ variables: { email } });
                            if (errors && errors.length) {
                                throw new Error(errors[0]?.message || "Unable to send reset email.");
                            }
                            if (data?.resetPassword) {
                                setResendStatus("If your email exists, a password reset link has been sent.");
                            } else {
                                setResendStatus("Unable to send reset email. Please try again.");
                            }
                        } catch (err) {
                            setResendStatus(err.message || "Unable to send reset email.");
                        }
                    }}
                    className="space-y-4"
                >
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
                    <button
                        type="submit"
                        className="bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700 disabled:opacity-60"
                        disabled={resetLoading}
                    >
                        {resetLoading ? "Sending..." : "Send reset email"}
                    </button>
                    <button
                        type="button"
                        onClick={() => setResetMode(false)}
                        className="text-sm text-blue-700 underline"
                    >
                        Back to login
                    </button>
                </form>
            ) : (
                <form onSubmit={handleLogin} className="space-y-4">
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
                    </div>

                    <button
                        type="submit"
                        className="bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700"
                    >
                        Login
                    </button>
                </form>
            )}
        </div>
    );
}
