<footer class="bg-[#4B52B0] text-white mt-auto">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            
            {{-- Logo & Brand --}}
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="font-semibold text-lg">CVision</span>
            </div>

            {{-- Copyright --}}
            <p class="text-sm text-white/80">
                &copy; {{ date('Y') }} CVision. All rights reserved.
            </p>

            {{-- Links --}}
            <div class="flex items-center gap-6">
                <a href="#" class="text-sm text-white/80 hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="text-sm text-white/80 hover:text-white transition-colors">Terms of Service</a>
                <a href="#" class="text-sm text-white/80 hover:text-white transition-colors">Contact</a>
            </div>
        </div>
    </div>
</footer>