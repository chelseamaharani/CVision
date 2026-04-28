<nav class="bg-[#5C63B8] px-6 py-4 flex items-center justify-between shadow-md">

    {{-- Left: Logo + Name --}}
    <div class="flex items-center gap-3">

        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center border-2 border-white/30">
            {{-- Kalau nanti ada logo asli, ganti SVG ini --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>

        {{-- Nama aplikasi (HARDCODE) --}}
        <span class="text-white font-bold text-lg tracking-wide">
            CVision
        </span>

    </div>

    {{-- Right: Actions --}}
    <div class="flex items-center gap-3">

        <a href="{{ route('register') }}"
           class="bg-white text-[#4B52B0] font-semibold text-sm px-5 py-2 rounded-lg hover:bg-gray-100 transition-colors">
            Daftar Akun
        </a>

        <a href="{{ route('login') }}"
           class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center border border-white/30 hover:bg-white/30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </a>

    </div>

</nav>