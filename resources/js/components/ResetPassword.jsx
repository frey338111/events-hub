import React, { useState } from "react";
import { useParams } from "react-router-dom";
import { useMutation } from "@apollo/client/react";
import { gql } from "graphql-tag";

const CHANGE_PASSWORD_MUTATION = gql`
    mutation ChangePassword($token: String!, $password: String!, $password_confirmation: String!) {
        changePassword(
            token: $token
            password: $password
            password_confirmation: $password_confirmation
        )
    }
`;

export default function ResetPassword() {
    const { token } = useParams();
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const [error, setError] = useState("");
    const [success, setSuccess] = useState("");
    const passwordChecks = [
        { label: "At least 8 characters", test: (p) => p.length >= 8 },
        { label: "Uppercase letter", test: (p) => /[A-Z]/.test(p) },
        { label: "Lowercase letter", test: (p) => /[a-z]/.test(p) },
        { label: "Number", test: (p) => /\d/.test(p) },
    ];
    const passwordIsStrong = passwordChecks.every((rule) => rule.test(password));
    const passwordsMatch = confirmPassword.length === 0 ? true : password === confirmPassword;


    const [changePassword, { loading }] = useMutation(CHANGE_PASSWORD_MUTATION);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError("");
        setSuccess("");


        if (!token) {
            setError("Reset token is missing from the URL.");
            return;
        }

        if (!passwordIsStrong) {
            setError("Password does not meet complexity requirements.");
            return;
        }

        if (!passwordsMatch) {
            setError("Passwords do not match.");
            return;
        }

        if (password !== confirmPassword) {
            setError("Passwords do not match.");
            return;
        }

        try {
            const { data, errors } = await changePassword({
                variables: {
                    token,
                    password,
                    password_confirmation: confirmPassword,
                },
            });

            if (errors && errors.length) {
                throw new Error(errors[0]?.message || "Unable to reset password.");
            }

            if (!data?.changePassword) {
                setError("Invalid or expired token. Please request a new reset email.");
                return;
            }

            setSuccess("Password updated. You can now log in with your new password.");
            setPassword("");
            setConfirmPassword("");
        } catch (err) {
            setError(err.message || "Unable to reset password. Please try again.");
        }
    };

    return (
        <div className="max-w-md mx-auto p-6 bg-white shadow rounded">
            <h2 className="text-2xl font-bold mb-4">Reset Password</h2>

            {error && (
                <div className="mb-4 rounded bg-red-50 border border-red-200 p-3 text-red-700">
                    {error}
                </div>
            )}
            {success && (
                <div className="mb-4 rounded bg-green-50 border border-green-200 p-3 text-green-700">
                    {success}
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
                <input type="hidden" name="token" value={token || ""} readOnly />

                <div>
                    <label className="block text-sm font-medium mb-1">New Password</label>
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
                        value={confirmPassword}
                        onChange={(e) => setConfirmPassword(e.target.value)}
                        required
                    />
                </div>

                <button
                    type="submit"
                    className="bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700 disabled:opacity-60"
                    disabled={loading}
                >
                    {loading ? "Updating..." : "Reset Password"}
                </button>
            </form>
        </div>
    );
}
