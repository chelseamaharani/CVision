<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between flex-shrink-0">

    {{-- Kiri: Logo bulat + nama app + separator + welcome --}}
    <div class="flex items-center gap-3">
        {{-- Logo bulat - logo CVision --}}
        <div class="w-8 h-8 rounded-full bg-[#E8EAFF] flex items-center justify-center overflow-hidden border-2 border-[#3730A3]/30 flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="CVision Logo" class="w-full h-full object-cover">
        </div>

        {{-- Nama aplikasi (HARDCODE - ganti teks CVision sesuai kebutuhan) --}}
        <span class="text-gray-500 font-semibold text-sm">CVision</span>

        <span class="text-gray-300 font-light">|</span>

        {{-- Welcome user --}}
        <span class="text-[#3730A3] font-bold text-sm">
            Welcome, {{ auth()->user()->name ?? 'User' }}!
        </span>
    </div>

    {{-- Kanan: Avatar inisial --}}
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-[#3730A3] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    </div>

</header>