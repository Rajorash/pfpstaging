<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mints CD Consulting') }} :: {{strip_tags($header)}}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{url('/favicons/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{url('/favicons/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{url('/favicons/favicon-16x16.png')}}">
    <link rel="manifest" href="{{url('/favicons/site.webmanifest')}}">
    <link rel="mask-icon" href="{{url('/favicons/safari-pinned-tab.svg')}}" color="#3e04e2">
    <link rel="shortcut icon" href="{{url('/favicons/favicon.ico')}}">
    <meta name="msapplication-TileColor" content="#3e04e2">
    <meta name="msapplication-config" content="{{url('/favicons/browserconfig.xml')}}">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    {{--    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">--}}
    <link href="https://fonts.googleapis.com/css2?family=Questrial&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">

    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased relative text-light_gray">
{{--<x-jet-banner/>--}}

<div class="min-h-screen bg-light_purple2 bg-right-bottom bg-no-repeat"
     style="background-image: url({{asset('images/bg.svg')}})">
    <div class="bg-white shadow-shadow4">
        <livewire:navigation-menu/>
    </div>

    <!-- Page Heading -->
    @if(isset($header))
        <header class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex content-between">
                <h2 class="font-normal text-4xl text-dark_gray2 leading-tight mt-8">
                    {{ $header }}
                </h2>
                @if(isset($subMenu))
                    {{$subMenu}}
                @endif
            </div>
            @if(isset($subHeader))
                {{$subHeader}}
            @endif
        </header>
    @endif

<!-- Page Content -->
    {{ $slot }}

</div>

@stack('modals')

@livewireScripts
</body>
</html>
