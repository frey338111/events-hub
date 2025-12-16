import React from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import { ApolloProvider } from '@apollo/client/react'
import AppLayout from './components/AppLayout'
import client from './apolloClient'

export default function loadReactApp() {
    const rootElement = document.getElementById('react-root')

    if (rootElement) {
        createRoot(rootElement).render(
            <ApolloProvider client={client}>
                <BrowserRouter>
                    <AppLayout/>
                </BrowserRouter>
            </ApolloProvider>
        )
    }
}
