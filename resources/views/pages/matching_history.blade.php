@extends('layouts.dashboard')

@section('title', 'Matching History - CVision')

@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Matching History</h1>
        <p class="text-gray-500 text-sm">
            History of all CV screening processes that have been carried out
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
            <input type="text" id="searchHistoryInput" placeholder="Search Job"
                   class="w-full pl-11 pr-4 py-2.5 bg-[#2D3799] text-white placeholder-white/70 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#2D3799]/50 transition">
        </div>
    </div>

    {{-- Table Header --}}
    <div class="grid grid-cols-12 px-6 py-3 bg-[#9AA0D8] text-sm font-semibold text-gray-800">
        <div class="col-span-1">No</div>
        <div class="col-span-5">Position</div>
        <div class="col-span-3">Applicants</div>
        <div class="col-span-3">Action</div>
    </div>

    {{-- Table Rows --}}
    <div id="historyTable">
        @php
        $historyList = $historyList ?? [
            ['id' => 1, 'title' => 'Frontend Developer', 'applicants' => 15],
            ['id' => 2, 'title' => 'UI/UX Designer',      'applicants' => 8],
            ['id' => 3, 'title' => 'Backend Developer',   'applicants' => 23],
            ['id' => 4, 'title' => 'Data Scientist',      'applicants' => 6],
            ['id' => 5, 'title' => 'System Analyst',      'applicants' => 27],
        ];
        @endphp

        @forelse($historyList as $index => $item)
        <div class="history-row grid grid-cols-12 px-6 py-5 border-b border-gray-100 items-center hover:bg-gray-50 transition-colors"
             data-title="{{ strtolower($item['title']) }}">

            <div class="col-span-1">
                <span class="text-lg font-bold text-gray-800">{{ $index + 1 }}.</span>
            </div>

            <div class="col-span-5">
                <span class="font-semibold text-gray-800 text-sm">{{ $item['title'] }}</span>
            </div>

            <div class="col-span-3">
                <span class="text-gray-700 text-sm">{{ $item['applicants'] }}</span>
            </div>

            <div class="col-span-3">
                <a href="{{ route('matching.results', ['job_id' => $item['id']]) }}"
                   class="inline-flex items-center gap-2 border-2 border-[#2D3799] text-[#2D3799] font-semibold text-sm px-4 py-2 rounded-lg hover:bg-[#2D3799] hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    View Results
                </a>
            </div>

        </div>
        @empty
        <div class="py-16 text-center text-gray-400 text-sm">
            No screening history yet. Go to Job Listing to start screening.
        </div>
        @endforelse
    </div>

    {{-- Empty state for search --}}
    <div id="emptyHistoryState" class="hidden py-16 text-center text-gray-400 text-sm">
        No job matches your search.
    </div>

</div>

@endsection

@push('scripts')
<script>
    document.getElementById('searchHistoryInput').addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.history-row');
        let found = 0;

        rows.forEach(row => {
            const title = row.dataset.title || '';
            if (title.includes(keyword)) {
                row.classList.remove('hidden');
                found++;
            } else {
                row.classList.add('hidden');
            }
        });

        document.getElementById('emptyHistoryState').classList.toggle('hidden', found > 0);
    });
</script>
@endpush