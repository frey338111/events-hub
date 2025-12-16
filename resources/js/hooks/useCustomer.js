import { useEffect, useState, useCallback } from "react";

const TOKEN_KEY = "customer_jwt";

const getToken = () => localStorage.getItem(TOKEN_KEY);
const setToken = (token) => {
    if (token) {
        localStorage.setItem(TOKEN_KEY, token);
    } else {
        localStorage.removeItem(TOKEN_KEY);
    }
};

export default function useCustomer() {
    const [customer, setCustomer] = useState(null);
    const [loading, setLoading] = useState(true);

    const fetchCustomer = useCallback(async () => {
        const token = getToken();
        if (!token) {
            setCustomer(null);
            setLoading(false);
            return;
        }

        try {
            const res = await fetch("/customer/jwt/me", {
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`,
                },
            });

            if (res.ok) {
                const data = await res.json();
                setCustomer(data);
            } else {
                setToken(null);
                setCustomer(null);
            }
        } catch (error) {
            setCustomer(null);
            setLoading(false);
            return;
        } finally {
            setLoading(false);
        }
    }, []);

    const login = useCallback(async (email, password) => {
        const res = await fetch("/customer/jwt/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({ email, password }),
        });

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            throw new Error(data.message || "Login failed");
        }

        const data = await res.json();
        if (data?.access_token) {
            setToken(data.access_token);
            await fetchCustomer();
        }
    }, [fetchCustomer]);

    const logout = useCallback(async () => {
        const token = getToken();
        try {
            await fetch("/customer/jwt/logout", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Authorization": token ? `Bearer ${token}` : "",
                },
            });
        } catch (_) {
            // ignore
        } finally {
            setToken(null);
            setCustomer(null);
        }
    }, []);

    useEffect(() => {
        fetchCustomer();
    }, [fetchCustomer]);

    return { customer, loading, refreshCustomer: fetchCustomer, login, logout, token: getToken() };
}
