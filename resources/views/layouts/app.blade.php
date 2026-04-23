<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KeeIt') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">

<div class="min-h-screen">

    @include('layouts.navigation')

    {{-- Toast notifications --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
             x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-16 sm:top-20 right-2 sm:right-4 z-50 w-[calc(100vw-1rem)] sm:w-auto sm:max-w-sm
                    bg-white border border-green-200 text-green-800
                    px-4 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-16 sm:top-20 right-2 sm:right-4 z-50 w-[calc(100vw-1rem)] sm:w-auto sm:max-w-sm
                    bg-white border border-red-200 text-red-800
                    px-4 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @isset($header)
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>

</div>

@stack('scripts')
</body>
</html>
