<header class="bg-white border-2 border-[#6B74C8] px-6 py-3 flex items-center justify-between flex-shrink-0">

    {{-- Kiri: Logo bulat + nama app + separator + welcome --}}
    <div class="flex items-center gap-3">
        {{-- Logo bulat - ganti dengan logo asli --}}
        <div class="w-8 h-8 rounded-full bg-[#E8EAFF] flex items-center justify-center overflow-hidden border-2 border-[#6B74C8]/30 flex-shrink-0">
            {{-- Ganti baris ini dengan: <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover"> --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#4B52B0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>

        {{-- Nama aplikasi (HARDCODE - ganti teks CVision sesuai kebutuhan) --}}
        <span class="text-gray-500 font-semibold text-sm">CVision</span>

        <span class="text-gray-300 font-light">|</span>

        {{-- Welcome user --}}
        <span class="text-[#4B52B0] font-bold text-sm">
            Welcome, {{ auth()->user()->name ?? 'User' }}!
        </span>
    </div>

    {{-- Kanan: Feedback + Avatar inisial --}}
    <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-500 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            Feedback
        </button>

        {{-- Avatar inisial --}}
        <div class="w-8 h-8 rounded-full bg-[#6B74C8] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    </div>

</header>