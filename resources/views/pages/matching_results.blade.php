@extends('layouts.dashboard')

@section('title', 'Matching Results - CVision')

@section('content')

{{-- Header Row --}}
<div class="flex items-start justify-between mb-2">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Matching Results</h1>
        <p class="text-gray-500 text-sm mt-1">Here are the candidates that best match your criteria</p>

        {{-- Job Badge --}}
        <div class="flex items-center gap-2 mt-3">
            <div class="w-8 h-8 rounded-lg bg-[#E8EAFF] flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#4B52B0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-[#4B52B0] font-semibold text-sm">{{ $job->title ?? 'Backend Developer' }}</span>
        </div>
    </div>

    {{-- View History + New Job Buttons --}}
    <div class="flex items-center gap-3 flex-shrink-0">
        <a href="{{ route('matching.index') }}"
           class="flex items-center gap-2 border-2 border-[#2D3799] text-[#2D3799] font-semibold text-sm px-5 py-2.5 rounded-xl hover:bg-[#2D3799] hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            View History
        </a>
        <a href="{{ route('job_listing.create') }}"
           class="flex items-center gap-2 bg-[#2D3799] hover:bg-[#232d85] text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            New Job
        </a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 mt-6">

    {{-- Matches --}}
    <div class="bg-white rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Matches</p>
            <p class="text-lg font-bold text-gray-800 leading-tight">{{ $stats['matches'] ?? 5 }}</p>
        </div>
    </div>

    {{-- Candidates --}}
    <div class="bg-white rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Candidates</p>
            <p class="text-lg font-bold text-gray-800 leading-tight">{{ $stats['candidates'] ?? 20 }}</p>
        </div>
    </div>

    {{-- Matching Accuracy --}}
    <div class="bg-white rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Matching Accuracy</p>
            <p class="text-lg font-bold text-gray-800 leading-tight">{{ $stats['accuracy'] ?? '82%' }}</p>
        </div>
    </div>

    {{-- Date Matched --}}
    <div class="bg-white rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Date Matched</p>
            <p class="text-sm font-bold text-gray-800 leading-tight">{{ $stats['date'] ?? 'Apr 16, 2026' }}</p>
        </div>
    </div>

</div>

{{-- Search & Download Bar --}}
<div class="flex items-center justify-between mb-4 gap-3">

    {{-- Search --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" id="searchInput" placeholder="Search Candidate"
               class="pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/30 transition shadow-sm w-64">
    </div>

    {{-- Download + Filter --}}
    <div class="flex items-center gap-2">
        <button class="flex items-center gap-2 bg-[#2D3799] hover:bg-[#232d85] text-white font-semibold text-sm px-5 py-2 rounded-xl transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download (csv)
        </button>
        <button class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 8h12M9 12h6"/>
            </svg>
        </button>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">

    {{-- Table Header --}}
    <div class="grid grid-cols-12 px-6 py-3 bg-[#E8EAFF] text-sm font-semibold text-gray-600">
        <div class="col-span-1">Rank</div>
        <div class="col-span-4">Candidate</div>
        <div class="col-span-3 text-center">Matching Score</div>
        <div class="col-span-2">Skills Matched</div>
        <div class="col-span-2 text-center">Resume</div>
    </div>

    {{-- Table Rows --}}
    <div id="candidateTable">
        @php
        $candidates = $candidates ?? [
            ['rank'=>1,'initials'=>'BS','name'=>'Budi Santoso','role'=>'Software Engineer','score'=>92,'top'=>true, 'skills'=>['Python','SQL','REST API']],
            ['rank'=>2,'initials'=>'BS','name'=>'Budi Santoso','role'=>'Software Engineer','score'=>92,'top'=>false,'skills'=>['Python','SQL','REST API']],
            ['rank'=>3,'initials'=>'BS','name'=>'Budi Santoso','role'=>'Software Engineer','score'=>92,'top'=>false,'skills'=>['Python','SQL','REST API']],
            ['rank'=>4,'initials'=>'BS','name'=>'Budi Santoso','role'=>'Software Engineer','score'=>92,'top'=>false,'skills'=>['Python','SQL','REST API']],
            ['rank'=>5,'initials'=>'BS','name'=>'Budi Santoso','role'=>'Software Engineer','score'=>92,'top'=>false,'skills'=>['Python','SQL','REST API']],
        ];
        @endphp

        @foreach($candidates as $c)
        <div class="candidate-row grid grid-cols-12 px-6 py-4 border-b border-gray-100 items-center hover:bg-gray-50 transition-colors"
             data-name="{{ strtolower($c['name']) }}">

            {{-- Rank --}}
            <div class="col-span-1">
                <span class="text-xl font-bold text-gray-800">{{ $c['rank'] }}.</span>
            </div>

            {{-- Candidate --}}
            <div class="col-span-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-[#C8CBEE] flex items-center justify-center text-[#4B52B0] font-bold text-sm flex-shrink-0">
                    {{ $c['initials'] }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $c['name'] }}</p>
                    <p class="text-gray-400 text-xs">{{ $c['role'] }}</p>
                </div>
            </div>

            {{-- Matching Score --}}
            <div class="col-span-3 flex items-center justify-center gap-3">
                {{-- Circle progress --}}
                <div class="relative w-12 h-12 flex-shrink-0">
                    <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#E8EAFF" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#38BDF8" stroke-width="3"
                            stroke-dasharray="{{ $c['score'] }}, 100"
                            stroke-linecap="round"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-700">{{ $c['score'] }}%</span>
                </div>

                {{-- Top Match badge --}}
                @if($c['top'])
                <span class="inline-flex items-center gap-1 bg-green-100 text-green-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
                    </svg>
                    Top Match
                </span>
                @endif
            </div>

            {{-- Skills --}}
            <div class="col-span-2 flex flex-col gap-1">
                @foreach($c['skills'] as $skill)
                <span class="inline-block bg-[#E8EAFF] text-[#4B52B0] text-xs font-medium px-2.5 py-0.5 rounded-full w-fit">{{ $skill }}</span>
                @endforeach
            </div>

            {{-- Resume --}}
            <div class="col-span-2 flex justify-center">
                <a href="{{ route('candidate.resume', ['id' => $c['matching_result_id']]) }}"
                   class="flex items-center gap-1.5 border border-[#4B52B0] text-[#4B52B0] text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-[#E8EAFF] transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Resume
                </a>
            </div>

        </div>
        @endforeach
    </div>

    {{-- Empty state --}}
    <div id="emptyState" class="hidden py-16 text-center text-gray-400 text-sm">
        Tidak ada kandidat yang cocok dengan pencarian.
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Search filter
    document.getElementById('searchInput').addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        const rows    = document.querySelectorAll('.candidate-row');
        let found     = 0;

        rows.forEach(row => {
            const name = row.dataset.name || '';
            if (name.includes(keyword)) {
                row.classList.remove('hidden');
                found++;
            } else {
                row.classList.add('hidden');
            }
        });

        document.getElementById('emptyState').classList.toggle('hidden', found > 0);
    });
</script>
@endpush