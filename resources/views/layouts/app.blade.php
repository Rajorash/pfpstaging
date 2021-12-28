<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mints CD Consulting') }} :: {!! $titleHeader ?? strip_tags($header) !!}</title>

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

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/all.css') }}">

@livewireStyles

    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="relative font-sans antialiased text-light_gray">

<div class="min-h-screen bg-right-bottom bg-no-repeat bg-light_purple2"
     style="background-image: url({{mix('images/bg.svg')}})">
    <div class="bg-white shadow-shadow4">
        <livewire:navigation-menu/>
    </div>

    <!-- Page Heading -->
    @if(isset($header))
        <header class="px-4 py-2 mx-auto max-w-7xl sm:px-6 lg:px-8 relative">
            <div class="flex content-between">
                <h2 class="mt-8 text-4xl font-normal leading-tight text-dark_gray2">
                    {{ $header }}
                </h2>
                @if(isset($subMenu))
                    {{$subMenu}}
                @endif
            </div>
            @if(isset($subHeader))
                <div class="py-2">
                    {{$subHeader}}
                </div>
            @endif
        </header>
    @endif

    {{ $slot }}

</div>

<div id="delay_progress" class="fixed z-50 h-1 rounded bg-blue bottom-2 right-4 sm:right-6 lg:right-8"></div>

@stack('modals')
@livewire('livewire-ui-modal')
@livewireScripts
{{-- commented below line, removes small box at bottom of layout. --}}
{{-- <div id="ddd" class="absolute z-50 w-2 h-2 bg-red-600"></div> --}}
</body>
</html>
