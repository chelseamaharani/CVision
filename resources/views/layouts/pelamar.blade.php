<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CVision')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-white antialiased">

    {{-- Navbar khusus pelamar (bukan navbar admin) --}}
    @include('components.navbar_pelamar')

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
