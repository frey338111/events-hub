import React, { useEffect, useState } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";
import { useSiteConfig } from "../../context/SiteConfigContext";

const GET_PAGE_QUERY = gql`
    query GetPage($slug: String!) {
        pageBySlug(slug: $slug) {
            content
        }
    }
`;

const EVENT_IMAGES_QUERY = gql`
    query EventImages {
        eventImages
    }
`;

export default function Content() {
    const [backgroundImage, setBackgroundImage] = useState("");
    const { config, loading: configLoading } = useSiteConfig();
    const homepageSlug = config["homepage-url"]?.trim() || "";

    const { data: pageData, loading: pageLoading } = useQuery(GET_PAGE_QUERY, {
        variables: { slug: homepageSlug },
        skip: !homepageSlug,
    });
    const { data: imagesData } = useQuery(EVENT_IMAGES_QUERY);

    useEffect(() => {
        const images = imagesData?.eventImages ?? [];
        if (!Array.isArray(images) || images.length === 0) {
            setBackgroundImage("");
            return;
        }

        const chooseRandom = () => {
            const randomIndex = Math.floor(Math.random() * images.length);
            setBackgroundImage(images[randomIndex]);
        };

        chooseRandom();
        const intervalId = setInterval(chooseRandom, 4000);

        return () => clearInterval(intervalId);
    }, [imagesData]);

    if (configLoading || (homepageSlug && pageLoading)) {
        return <div className="p-4 text-gray-600">Loading...</div>;
    }

    const pageContent = pageData?.pageBySlug?.content || "Welcome";

    return (
        <div className="relative flex flex-col items-center w-full max-w-5xl mx-auto">
            <div
                className="w-full h-72 rounded-xl border border-blue-200 shadow-sm bg-cover bg-center bg-blue-50"
                style={
                    backgroundImage
                        ? { backgroundImage: `url('${backgroundImage}')` }
                        : undefined
                }
            />
            <div
                className="w-4/5 -mt-[7%] rounded-xl bg-white/80 backdrop-blur-md border border-blue-200 text-slate-900 shadow-lg leading-relaxed text-1m font-medium p-5 z-10"
                dangerouslySetInnerHTML={{ __html: pageContent }}
            />
        </div>
    );
}
