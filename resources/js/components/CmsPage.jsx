import React from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import { useParams } from "react-router-dom";

const GET_PAGE_QUERY = gql`
    query GetPage($slug: String!) {
        pageBySlug(slug: $slug) {
            title
            slug
            content
        }
    }
`;

export default function CmsPage() {
    const { slug } = useParams();
    const { data, loading, error } = useQuery(GET_PAGE_QUERY, {
        variables: { slug },
    });
    const page = data?.pageBySlug ?? null;
    const formattedTitle = page?.title
        ? page.title.charAt(0).toUpperCase() + page.title.slice(1)
        : "";

    if (loading) {
        return <main className="p-6 text-gray-600">Loading page...</main>;
    }

    if (error) {
        console.error("Failed to load CMS page.", error);
    }

    if (error || !page) {
        return <main className="p-6 text-gray-600">Page not found.</main>;
    }

    return (
        <main className="p-6">
            <div className="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h1 className="mb-6 text-3xl font-bold text-slate-900">
                    {formattedTitle}
                </h1>

                <div
                    className="prose prose-slate max-w-none"
                    dangerouslySetInnerHTML={{ __html: page.content }}
                />
            </div>
        </main>
    );
}
