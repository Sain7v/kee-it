<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="flex justify-center w-full sm:max-w-md px-6">
                <a href="/">
                    <svg viewBox="10 40 470 100" xmlns="http://www.w3.org/2000/svg" class="h-14 w-full">
                        <style>.guest-wordmark { font-family: sans-serif; font-size: 72px; font-weight: 500; letter-spacing: -2px; fill: #1a1a1a; }</style>
                        <g transform="translate(80, 50)">
                            <rect x="0" y="0" width="80" height="80" rx="20" fill="#2D6A4F"/>
                            <line x1="18" y1="40" x2="30" y2="56" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="30" y1="56" x2="62" y2="20" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="18" y1="24" x2="30" y2="40" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" opacity="0.45"/>
                            <line x1="30" y1="40" x2="62" y2="4" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" opacity="0.45"/>
                            <text x="100" y="68" class="guest-wordmark">keepit</text>
                        </g>
                    </svg>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
