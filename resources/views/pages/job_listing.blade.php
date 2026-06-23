@extends('layouts.dashboard')

@section('title', 'Job Listing - CVision')

@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Job Listing</h1>
        <p class="text-gray-500 text-sm">
            Manage and screen applicant CVs for each available position
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
            <input type="text" id="searchJobInput" placeholder="Search Job"
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
    <div id="jobTable">
        @php
        $jobsList = $jobsList ?? [
            ['id' => 1, 'title' => 'Frontend Developer', 'applicants' => 15],
            ['id' => 2, 'title' => 'UI/UX Designer',      'applicants' => 8],
            ['id' => 3, 'title' => 'Backend Developer',   'applicants' => 23],
            ['id' => 4, 'title' => 'Data Scientist',      'applicants' => 6],
            ['id' => 5, 'title' => 'System Analyst',      'applicants' => 27],
        ];
        @endphp

        @foreach($jobsList as $index => $job)
        <div class="job-row grid grid-cols-12 px-6 py-5 border-b border-gray-100 items-center hover:bg-gray-50 transition-colors"
             data-title="{{ strtolower($job['title']) }}">

            <div class="col-span-1">
                <span class="text-lg font-bold text-gray-800">{{ $index + 1 }}.</span>
            </div>

            <div class="col-span-5">
                <span class="font-semibold text-gray-800 text-sm">{{ $job['title'] }}</span>
            </div>

            <div class="col-span-3">
                <span class="text-gray-700 text-sm">{{ $job['applicants'] }}</span>
            </div>

            <div class="col-span-3">
                <button type="button"
                        onclick="openScreeningModal('{{ $job['title'] }}', {{ $job['applicants'] }}, {{ $job['id'] }})"
                        class="flex items-center gap-2 border-2 border-[#2D3799] text-[#2D3799] font-semibold text-sm px-4 py-2 rounded-lg hover:bg-[#2D3799] hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    Screening
                </button>
            </div>

        </div>
        @endforeach
    </div>

    {{-- Empty state --}}
    <div id="emptyJobState" class="hidden py-16 text-center text-gray-400 text-sm">
        No job matches your search.
    </div>

</div>

{{-- ===================== MODAL 1: CONFIRM SCREENING ===================== --}}
<div id="confirmModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-8 text-center">

        <div class="w-16 h-16 rounded-full bg-[#E8EAFF] flex items-center justify-center mx-auto mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#4B52B0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 2v4M8 2l3 3"/>
            </svg>
        </div>

        <h3 id="confirmJobTitle" class="font-bold text-gray-900 text-lg mb-1">Frontend Developer</h3>
        <p id="confirmCvCount" class="text-gray-500 text-sm mb-6">15 CVs found</p>

        <div class="bg-[#E8EAFF] text-gray-700 text-sm rounded-xl px-4 py-3 mb-6">
            The system will analyze all CVs for this position using AI.
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="closeAllModals()"
                    class="flex-1 border-2 border-gray-200 text-gray-600 font-semibold text-sm py-3 rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button" onclick="startScreening()"
                    class="flex-1 flex items-center justify-center gap-2 bg-[#2D3799] hover:bg-[#232d85] text-white font-semibold text-sm py-3 rounded-xl transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                Start Screening
            </button>
        </div>

    </div>
</div>

{{-- ===================== MODAL 2: LOADING ===================== --}}
<div id="loadingModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-10 text-center">

        <div class="w-12 h-12 mx-auto mb-6">
            <svg class="animate-spin w-12 h-12 text-[#4B52B0]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>

        <p class="font-semibold text-gray-800 text-base mb-1">Currently analyzing CVs...</p>
        <p class="text-gray-400 text-sm">Please wait a moment</p>

    </div>
</div>

{{-- ===================== MODAL 3: SUCCESS ===================== --}}
<div id="successModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-8 text-center">

        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h3 class="font-bold text-gray-900 text-lg mb-2">Screening Successful!</h3>
        <p id="successDescription" class="text-gray-500 text-sm mb-6 leading-relaxed">
            15 CVs have been analyzed for the position of Frontend Developer
        </p>

        <button type="button" id="viewResultsBtn"
                class="w-full bg-[#2D3799] hover:bg-[#232d85] text-white font-semibold text-sm py-3 rounded-xl transition-colors">
            View Results
        </button>

    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentJobId = null;

    // ===== Open Modal 1: Confirm =====
    function openScreeningModal(title, count, jobId) {
        currentJobId = jobId;
        document.getElementById('confirmJobTitle').textContent = title;
        document.getElementById('confirmCvCount').textContent = count + ' CVs found';
        document.getElementById('successDescription').textContent =
            count + ' CVs have been analyzed for the position of ' + title;
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    // ===== Close all modals =====
    function closeAllModals() {
        document.getElementById('confirmModal').classList.add('hidden');
        document.getElementById('loadingModal').classList.add('hidden');
        document.getElementById('successModal').classList.add('hidden');
    }

    // ===== Start screening: Modal 1 -> Modal 2 -> Modal 3 =====
    function startScreening() {
        document.getElementById('confirmModal').classList.add('hidden');
        document.getElementById('loadingModal').classList.remove('hidden');

        // Simulasi proses screening AI (nanti diganti dengan AJAX call ke backend)
        // fetch(`/jobs/${currentJobId}/screen`, { method: 'POST', headers: {...} })
        //     .then(res => res.json())
        //     .then(data => { ... });

        setTimeout(function() {
            document.getElementById('loadingModal').classList.add('hidden');
            document.getElementById('successModal').classList.remove('hidden');
        }, 2500);
    }

    // ===== View Results: redirect ke halaman Matching Results =====
    document.getElementById('viewResultsBtn').addEventListener('click', function() {
        if (currentJobId) {
            window.location.href = `/matching_results?job_id=${currentJobId}`;
        }
    });

    // ===== Search filter =====
    document.getElementById('searchJobInput').addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.job-row');
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

        document.getElementById('emptyJobState').classList.toggle('hidden', found > 0);
    });

    // Close modal kalau klik area gelap di luar
    ['confirmModal', 'successModal'].forEach(function(id) {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) closeAllModals();
        });
    });
</script>
@endpush