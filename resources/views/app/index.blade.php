<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Events Hub</title>
    <meta name="description" content="Discover upcoming events, browse event details, book tickets, and manage your event activity with The Events Hub.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="The Events Hub">
    <meta property="og:title" content="The Events Hub">
    <meta property="og:description" content="Discover upcoming events, browse event details, book tickets, and manage your event activity with The Events Hub.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/events_logo.jpg') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="The Events Hub">
    <meta name="twitter:description" content="Discover upcoming events, browse event details, book tickets, and manage your event activity with The Events Hub.">
    <meta name="twitter:image" content="{{ asset('images/events_logo.jpg') }}">
    <meta name="theme-color" content="#ffffff">

    @viteReactRefresh
    @vite('resources/js/app.js')
</head>
<body>
<div id="react-root"></div>
</body>
</html>
