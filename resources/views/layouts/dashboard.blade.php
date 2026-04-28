<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CVision')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-[#F0F2FF] antialiased flex flex-col h-screen">

{{-- Topbar full width di paling atas --}}
@include('components.topbar')

<div class="flex flex-1 overflow-hidden">

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Page Content --}}
    <main class="flex-1 overflow-y-auto p-8 bg-[#F0F2FF]">
        @yield('content')
    </main>

</div>

@stack('scripts')
</body>
</html>