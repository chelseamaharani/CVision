@extends('layouts.dashboard')

@section('title', 'Candidates - CVision')

@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Candidates</h1>
        <p class="text-gray-500 text-sm">
            The following is a list of candidates who have applied.
        </p>
    </div>

    <a href="{{ route('job_listing.create') }}"
       class="flex items-center gap-2 bg-[#2D3799] hover:bg-[#232d85] text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors shadow-md flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        New Job
    </a>
</div>

{{-- Table Card --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">

    {{-- Search --}}
    <div class="p-5 border-b border-gray-100">
        <div class="relative max-w-xs">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" id="searchCandidateInput" placeholder="Search Candidate"
                   class="w-full pl-11 pr-4 py-2.5 bg-[#2D3799] text-white placeholder-white/70 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#2D3799]/50 transition">
        </div>
    </div>

    {{-- Table Header --}}
    <div class="grid grid-cols-12 px-6 py-3 bg-[#9AA0D8] text-sm font-semibold text-gray-800">
        <div class="col-span-1">No</div>
        <div class="col-span-3">Name Candidate</div>
        <div class="col-span-3">Position</div>
        <div class="col-span-5">Action</div>
    </div>

    {{-- Table Rows --}}
    <div id="candidatesTable">
        @php
        $candidatesList = $candidatesList ?? [
            ['id' => 1, 'name' => 'Helena',   'position' => 'Frontend Developer', 'cv_path' => null],
            ['id' => 2, 'name' => 'Chelsea',  'position' => 'UI/UX Designer',     'cv_path' => null],
            ['id' => 3, 'name' => 'Arabella', 'position' => 'Backend Developer',  'cv_path' => null],
            ['id' => 4, 'name' => 'Maya',     'position' => 'Data Scientist',     'cv_path' => null],
            ['id' => 5, 'name' => 'Mika',     'position' => 'System Analyst',     'cv_path' => null],
        ];
        @endphp

        @forelse($candidatesList as $index => $candidate)
        <div class="candidate-row grid grid-cols-12 px-6 py-5 border-b border-gray-100 items-center hover:bg-gray-50 transition-colors"
             data-name="{{ strtolower($candidate['name']) }}">

            <div class="col-span-1">
                <span class="text-lg font-bold text-gray-800">{{ $index + 1 }}.</span>
            </div>

            <div class="col-span-3">
                <span class="font-semibold text-gray-800 text-sm">{{ $candidate['name'] }}</span>
            </div>

            <div class="col-span-3">
                <span class="text-gray-700 text-sm">{{ $candidate['position'] }}</span>
            </div>

            <div class="col-span-5 flex items-center gap-2">
                @if($candidate['cv_path'])
                    <a href="{{ route('cv.file', $candidate['id']) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 border-2 border-[#2D3799] text-[#2D3799] font-semibold text-xs px-3 py-2 rounded-lg hover:bg-[#2D3799] hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View CV
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 border-2 border-gray-200 text-gray-400 font-semibold text-xs px-3 py-2 rounded-lg cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View CV
                    </span>
                @endif

                <button onclick="deleteCandidate({{ $candidate['id'] }}, this)"
                        class="inline-flex items-center gap-1.5 border-2 border-red-200 text-red-500 font-semibold text-xs px-3 py-2 rounded-lg hover:bg-red-500 hover:text-white hover:border-red-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </div>

        </div>
        @empty
        <div class="py-16 text-center text-gray-400 text-sm">
            No candidates have applied yet.
        </div>
        @endforelse
    </div>

    {{-- Empty state for search --}}
    <div id="emptyCandidateState" class="hidden py-16 text-center text-gray-400 text-sm">
        No candidate matches your search.
    </div>

</div>

@endsection

@push('scripts')
<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    // Search filter
    document.getElementById('searchCandidateInput').addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.candidate-row');
        let found = 0;

        rows.forEach(row => {
            const name = row.dataset.name || '';
            if (name.includes(keyword)) {
                row.classList.remove('hidden');
                found++;
            } else {
                row.classList.add('hidden');
            }
        });

        document.getElementById('emptyCandidateState').classList.toggle('hidden', found > 0);
    });

    // Delete candidate
    async function deleteCandidate(candidateId, button) {
        if (!confirm('Yakin ingin menghapus kandidat ini? Semua data terkait (CV, hasil screening) akan dihapus permanen.')) {
            return;
        }

        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `<span class="w-4 h-4 border-2 border-red-500 border-t-transparent rounded-full animate-spin inline-block"></span>`;

        try {
            const response = await fetch(`/candidates/${candidateId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                // Hapus row dari tabel dengan animasi fade
                const row = button.closest('.candidate-row');
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                
                setTimeout(() => {
                    row.remove();
                }, 300);

                alert('✅ ' + data.message);
            } else {
                alert('❌ ' + data.message);
                button.disabled = false;
                button.innerHTML = originalHTML;
            }

        } catch (error) {
            console.error('Delete error:', error);
            alert('❌ Terjadi kesalahan. Silakan coba lagi.');
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
    }
</script>
@endpush
