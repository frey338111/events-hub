import React, { useEffect, useState } from "react";
import { useMutation, useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import useCustomer from "../../hooks/useCustomer";

const OPTIONS_QUERY = gql`
    query FormOptions {
        types: listEventTypes { id name }
        locations: listEventLocation { id name }
    }
`;

const CREATE_EVENT_MUTATION = gql`
    mutation CreateCustomerEvent($input: CreateCustomerEventInput!) {
        createCustomerEvent(input: $input) {
            id
            title
            start_time
            end_time
            capacity
            url_key
            events_image
        }
    }
`;

export default function CreateNew() {
    const { token } = useCustomer();
    const [form, setForm] = useState({
        title: "",
        type_id: "",
        description: "",
        start_time: "",
        end_time: "",
        location_id: "",
        capacity: "",
        url_key: "",
        events_image: null,
    });
    const [types, setTypes] = useState([]);
    const [locations, setLocations] = useState([]);
    const [submitting, setSubmitting] = useState(false);
    const [message, setMessage] = useState("");
    const [closeAfterSuccess, setCloseAfterSuccess] = useState(false);

    const { data: optionsData, loading: loadingOptions, error: optionsError } = useQuery(OPTIONS_QUERY);
    const [createEventMutation] = useMutation(CREATE_EVENT_MUTATION, {
        context: token ? { headers: { Authorization: `Bearer ${token}` } } : undefined,
    });

    useEffect(() => {
        if (optionsData) {
            setTypes(optionsData.types ?? []);
            setLocations(optionsData.locations ?? []);
        }
        if (optionsError) {
            setMessage("Unable to load options.");
        }
    }, [optionsData, optionsError]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
    };

    const handleFileChange = (e) => {
        const file = e.target.files?.[0] ?? null;
        setForm((prev) => ({ ...prev, events_image: file }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setMessage("");

        const formatDateTime = (value) => {
            if (!value) return null;
            // datetime-local returns "YYYY-MM-DDTHH:MM"; append seconds for backend format
            return value.replace("T", " ") + ":00";
        };

        try {
            const hasFile = form.events_image instanceof File;
            // Apollo's default link doesn't handle uploads, so fall back to multipart fetch when a file is present.
            if (hasFile) {
                const operations = {
                    query: `
                        mutation CreateCustomerEvent($input: CreateCustomerEventInput!) {
                            createCustomerEvent(input: $input) {
                                id
                                title
                                start_time
                                end_time
                                capacity
                                url_key
                                events_image
                            }
                        }
                    `,
                    variables: {
                        input: {
                            title: form.title,
                            type_id: form.type_id,
                            description: form.description,
                            start_time: formatDateTime(form.start_time),
                            end_time: formatDateTime(form.end_time),
                            location_id: form.location_id,
                            capacity: form.capacity ? parseInt(form.capacity, 10) : null,
                            url_key: form.url_key || null,
                            events_image: null,
                        },
                    },
                };

                const map = { 0: ["variables.input.events_image"] };
                const formData = new FormData();
                formData.append("operations", JSON.stringify(operations));
                formData.append("map", JSON.stringify(map));
                formData.append("0", form.events_image);

                const res = await fetch("/graphql", {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        ...(token ? { Authorization: `Bearer ${token}` } : {}),
                    },
                    body: formData,
                });

                const json = await res.json().catch(() => ({}));
                const errors = json.errors || [];
                if (!res.ok || errors.length) {
                    const errMsg = errors[0]?.message || json?.message || "Unable to create event.";
                    setMessage(errMsg === "Unauthenticated." ? "Please log in as a customer to create events." : errMsg);
                } else {
                    setMessage("Event created successfully.");
                    window.dispatchEvent(new CustomEvent("customer-event-created"));
                    setForm({
                        title: "",
                        type_id: "",
                        description: "",
                        start_time: "",
                        end_time: "",
                        location_id: "",
                        capacity: "",
                        url_key: "",
                        events_image: null,
                    });
                    setCloseAfterSuccess(true);
                }
            } else {
                const { data, errors } = await createEventMutation({
                    variables: {
                        input: {
                            title: form.title,
                            type_id: form.type_id,
                            description: form.description,
                            start_time: formatDateTime(form.start_time),
                            end_time: formatDateTime(form.end_time),
                            location_id: form.location_id,
                            capacity: form.capacity ? parseInt(form.capacity, 10) : null,
                            url_key: form.url_key || null,
                            events_image: null,
                        },
                    },
                    errorPolicy: "all",
                });

                if (errors && errors.length) {
                    const errMsg = errors[0]?.message || "Unable to create event.";
                    setMessage(errMsg === "Unauthenticated." ? "Please log in as a customer to create events." : errMsg);
                } else if (!data?.createCustomerEvent) {
                    setMessage("Unable to create event.");
                } else {
                    setMessage("Event created successfully.");
                    window.dispatchEvent(new CustomEvent("customer-event-created"));
                    setForm({
                        title: "",
                        type_id: "",
                        description: "",
                        start_time: "",
                        end_time: "",
                        location_id: "",
                        capacity: "",
                        url_key: "",
                        events_image: null,
                    });
                    setCloseAfterSuccess(true);
                }
            }
        } catch (error) {
            setMessage("Something went wrong. Please try again.");
        } finally {
            setSubmitting(false);
        }
    };

    useEffect(() => {
        if (!closeAfterSuccess) return;
        const timer = setTimeout(() => {
            window.dispatchEvent(new CustomEvent("close-create-event-modal"));
            setCloseAfterSuccess(false);
        }, 2000);

        return () => clearTimeout(timer);
    }, [closeAfterSuccess]);

    return (
        <div className="space-y-4">
            {message && (
                <div className="rounded border border-gray-200 bg-gray-50 p-3 text-sm text-blue-800">
                    {message}
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                    <label className="block font-medium mb-1">Title</label>
                    <input
                        type="text"
                        name="title"
                        value={form.title}
                        onChange={handleChange}
                        className="w-full border rounded p-2"
                        required
                    />
                </div>

                <div>
                    <label className="block font-medium mb-1">Type</label>
                    <select
                        name="type_id"
                        value={form.type_id}
                        onChange={handleChange}
                        className="w-full border rounded p-2"
                        required
                        disabled={loadingOptions}
                    >
                        <option value="">Select type</option>
                        {types.map((type) => (
                            <option key={type.id} value={type.id}>
                                {type.name}
                            </option>
                        ))}
                    </select>
                </div>

                <div>
                    <label className="block font-medium mb-1">Description</label>
                    <textarea
                        name="description"
                        rows="4"
                        value={form.description}
                        onChange={handleChange}
                        className="w-full border rounded p-2"
                    />
                </div>

                <div>
                    <label className="block font-medium mb-1">Event Image</label>
                    <input
                        type="file"
                        name="events_image"
                        accept="image/*"
                        onChange={handleFileChange}
                        className="w-full border rounded p-2"
                    />
                </div>

                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label className="block font-medium mb-1">Start Time</label>
                        <input
                            type="datetime-local"
                            name="start_time"
                            value={form.start_time}
                            onChange={handleChange}
                            className="w-full border rounded p-2"
                            required
                        />
                    </div>
                    <div>
                        <label className="block font-medium mb-1">End Time</label>
                        <input
                            type="datetime-local"
                            name="end_time"
                            value={form.end_time}
                            onChange={handleChange}
                            className="w-full border rounded p-2"
                        />
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label className="block font-medium mb-1">Location</label>
                        <select
                            name="location_id"
                            value={form.location_id}
                            onChange={handleChange}
                            className="w-full border rounded p-2"
                            required
                            disabled={loadingOptions}
                        >
                            <option value="">Select location</option>
                            {locations.map((location) => (
                                <option key={location.id} value={location.id}>
                                    {location.name}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block font-medium mb-1">Capacity</label>
                        <input
                            type="number"
                            name="capacity"
                            min="1"
                            value={form.capacity}
                            onChange={handleChange}
                            className="w-full border rounded p-2"
                            required
                        />
                    </div>
                </div>

                <div>
                    <label className="block font-medium mb-1">URL Key</label>
                    <input
                        type="text"
                        name="url_key"
                        value={form.url_key}
                        onChange={handleChange}
                        className="w-full border rounded p-2"
                        placeholder="leave empty to auto-generate"
                    />
                </div>

                <div className="pt-2">
                    <button
                        type="submit"
                        className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-60"
                        disabled={submitting}
                    >
                        {submitting ? "Creating…" : "Create Event"}
                    </button>
                </div>
            </form>
        </div>
    );
}
