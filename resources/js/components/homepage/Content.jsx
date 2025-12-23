import React, { useEffect, useState } from "react";
import { useQuery } from "@apollo/client/react";
import { gql } from "graphql-tag";

const WELCOME_QUERY = gql`
    query WelcomeText($name: String!) {
        configByName(name: $name) { value }
    }
`;

const EVENT_IMAGES_QUERY = gql`
    query EventImages {
        eventImages
    }
`;

export default function Content() {
    const [welcomeText, setWelcomeText] = useState("");
    const [backgroundImage, setBackgroundImage] = useState("");
    const { data, loading } = useQuery(WELCOME_QUERY, {
        variables: { name: "welcome-text" },
    });
    const { data: imagesData } = useQuery(EVENT_IMAGES_QUERY);

    useEffect(() => {
        if (data?.configByName?.value) {
            setWelcomeText(data.configByName.value);
        } else if (!loading) {
            setWelcomeText("");
        }
    }, [data, loading]);

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

    if (loading) {
        return <div className="p-4 text-gray-600">Loading...</div>;
    }

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
                dangerouslySetInnerHTML={{ __html: welcomeText || "Welcome" }}
            />
        </div>
    );
}
