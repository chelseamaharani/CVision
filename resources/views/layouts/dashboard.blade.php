<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CVision')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-100 antialiased">

<div class="min-h-screen">
    <div class="flex">

        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Main Area --}}
        <div class="flex-1 flex flex-col">

            {{-- Top Navbar --}}
            @include('components.topbar')

            {{-- Scrollable Content --}}
            <main class="flex-1 p-6 bg-gray-100">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Footer di bawah sidebar dan konten --}}
    @include('components.footer_dashboard')
</div>

@stack('scripts')
</body>
</html>
