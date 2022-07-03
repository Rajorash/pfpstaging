<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mints CD Consulting') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{url('/favicons/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{url('/favicons/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{url('/favicons/favicon-16x16.png')}}">
    <link rel="manifest" href="{{url('/favicons/site.webmanifest')}}">
    <link rel="mask-icon" href="{{url('/favicons/safari-pinned-tab.svg')}}" color="#3e04e2">
    <link rel="shortcut icon" href="{{url('/favicons/favicon.ico')}}">
    <meta name="msapplication-TileColor" content="#3e04e2">
    <meta name="msapplication-config" content="{{url('/favicons/browserconfig.xml')}}">
    <meta name="theme-color" content="#ffffff">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Questrial&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ url('css/app.css') }}">
    <link rel="stylesheet" href="{{ url('css/all.css') }}">

    <script src="{{ url('js/app.js') }}" defer></script>
</head>
<body class="relative font-sans antialiased text-light_gray">
<div class="min-h-screen bg-right-bottom bg-no-repeat bg-light_purple2"
     style="background-image: url({{url('images/bg.svg')}})">
    {{ $slot }}
</div>
</body>
</html>
