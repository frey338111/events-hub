import React, { useEffect, useState } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";

const WELCOME_QUERY = gql`
    query WelcomeText($name: String!) {
        configByName(name: $name) { value }
    }
`;

export default function Content() {
    const [welcomeText, setWelcomeText] = useState("");
    const { data, loading } = useQuery(WELCOME_QUERY, {
        variables: { name: "welcome-text" },
    });

    useEffect(() => {
        if (data?.configByName?.value) {
            setWelcomeText(data.configByName.value);
        } else if (!loading) {
            setWelcomeText("");
        }
    }, [data, loading]);

    if (loading) {
        return <div className="p-4 text-gray-600">Loading...</div>;
    }

    return (
        <div
            className="p-5 rounded bg-blue-50 border border-blue-200 text-blue-900 shadow-sm leading-relaxed text-2g font-medium"
            dangerouslySetInnerHTML={{ __html: welcomeText || "Welcome" }}
        />
    );
}
