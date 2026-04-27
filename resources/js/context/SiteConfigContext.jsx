import React, { createContext, useContext } from "react";

const SiteConfigContext = createContext({
    config: {},
    configList: [],
    loading: false,
});

export function SiteConfigProvider({ value, children }) {
    return (
        <SiteConfigContext.Provider value={value}>
            {children}
        </SiteConfigContext.Provider>
    );
}

export function useSiteConfig() {
    return useContext(SiteConfigContext);
}
