<footer class="bg-white border-t border-gray-200 mt-auto w-full">
    <div class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
            
            {{-- Brand --}}
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#4B52B0] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="font-semibold text-gray-800">CVision</span>
            </div>

            {{-- Copyright --}}
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} CVision. All rights reserved.
            </p>

            {{-- Links --}}
            <div class="flex items-center gap-6">
                <a href="#" class="text-sm text-gray-500 hover:text-[#4B52B0] transition-colors">Privacy Policy</a>
                <a href="#" class="text-sm text-gray-500 hover:text-[#4B52B0] transition-colors">Terms of Service</a>
                <a href="#" class="text-sm text-gray-500 hover:text-[#4B52B0] transition-colors">Contact</a>
            </div>
        </div>
    </div>
</footer>