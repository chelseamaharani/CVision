<nav class="bg-[#3730A3] px-6 py-4 flex items-center justify-between shadow-sm relative">

    {{-- Kiri: Logo + Nama Aplikasi --}}
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-white/30 flex items-center justify-center overflow-hidden flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="CVision Logo" class="w-full h-full object-cover">
        </div>

        <span class="text-white font-semibold text-lg">
            CVision
        </span>
    </div>

    {{-- Kanan: Tombol --}}
    <div class="flex items-center gap-3">
        @guest
            {{-- Belum login --}}
            <a href="{{ route('register') }}"
               class="bg-white text-gray-700 font-semibold text-sm px-5 py-2 rounded-lg border border-white hover:bg-gray-50 transition-colors">
                Daftar Akun
            </a>
            <a href="{{ route('login') }}"
               class="w-9 h-9 bg-transparent rounded-lg flex items-center justify-center border border-white hover:bg-white/20 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </a>
        @else
            {{-- Sudah login: nama + avatar dropdown --}}
            <span class="text-white font-medium text-sm hidden sm:block">
                Hi, {{ auth()->user()->name }}
            </span>

            <div class="relative" id="profileDropdownWrapper">
                {{-- Avatar trigger --}}
                <button type="button" onclick="toggleProfileDropdown()"
                        class="w-9 h-9 bg-[#3730A3] rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 hover:opacity-90 transition-opacity">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </button>

                {{-- Dropdown popup --}}
                <div id="profileDropdown"
                     class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">

                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        @endguest
    </div>

</nav>

@once
@push('scripts')
<script>
    function toggleProfileDropdown() {
        document.getElementById('profileDropdown').classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('profileDropdownWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('profileDropdown').classList.add('hidden');
        }
    });
</script>
@endpush
@endonce