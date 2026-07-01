@extends('layouts.dashboard')

@section('title', 'Screening CVs - ' . ($job->title ?? 'Job') . ' - CVision')

@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Screening CVs</h1>
        <p class="text-gray-500 text-sm mt-1">
            Pilih CV yang ingin di-screen menggunakan AI Engine
        </p>
        <div class="flex items-center gap-2 mt-3">
            <div class="w-8 h-8 rounded-lg bg-[#E8EAFF] flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#4B52B0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-[#4B52B0] font-semibold text-sm">{{ $job->title ?? 'Unknown Position' }}</span>
        </div>
    </div>

    <div class="flex items-center gap-3 flex-shrink-0">
        <a href="{{ route('matching.index') }}"
           class="flex items-center gap-2 border-2 border-[#2D3799] text-[#2D3799] font-semibold text-sm px-5 py-2.5 rounded-xl hover:bg-[#2D3799] hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            View History
        </a>
        <button onclick="screenAllCvs()"
                class="flex items-center gap-2 bg-[#2D3799] hover:bg-[#232d85] text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Screen All CVs
        </button>
    </div>
</div>

{{-- Info Box --}}
<div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 mb-6">
    <div class="flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-blue-800 text-sm font-semibold mb-1">Screening Per CV</p>
            <p class="text-blue-600 text-xs leading-relaxed">
                Klik tombol <strong>"Screen"</strong> pada setiap CV untuk menganalisis menggunakan AI Engine.
                Proses ini akan memanggil TF-IDF, SBERT, dan Gemini API untuk setiap CV.
                Untuk menghemat token, Anda bisa memilih CV mana yang akan di-screen.
            </p>
        </div>
    </div>
</div>

{{-- CV List --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-base">
            Daftar CV ({{ $cvs->count() }} total)
        </h3>
    </div>

    <div class="divide-y divide-gray-100">
        @foreach($cvs as $cv)
        <div class="cv-row px-6 py-4 hover:bg-gray-50 transition-colors" data-cv-id="{{ $cv['id'] }}">
            <div class="flex items-center justify-between gap-4">
                
                {{-- CV Info --}}
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ $cv['name'] }}</p>
                        <p class="text-gray-400 text-xs truncate">{{ $cv['file_name'] }}</p>
                        <p class="text-gray-400 text-xs">Uploaded: {{ $cv['uploaded_at'] }}</p>
                    </div>
                </div>

                {{-- Status Badge --}}
                <div class="flex-shrink-0">
                    @if($cv['has_result'])
                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-600 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3l14 9-14 9V3z"/>
                            </svg>
                            {{ $cv['status'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pending
                        </span>
                    @endif
                </div>

                {{-- Score --}}
                @if($cv['has_result'])
                <div class="flex-shrink-0 w-16 text-center">
                    <p class="text-lg font-bold text-gray-800">{{ $cv['score'] }}%</p>
                    <p class="text-xs text-gray-400">Rank #{{ $cv['rank'] }}</p>
                </div>
                @endif

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    @php
                        // Get matching result ID for the "View" link
                        $matchingResultId = $cv['has_result']
                            ? \App\Models\MatchingResult::where('cv_id', $cv['id'])->value('id')
                            : null;
                    @endphp

                    @if($matchingResultId)
                        <a href="{{ route('candidate.resume', ['id' => $matchingResultId]) }}"
                           class="flex items-center gap-1.5 border border-[#4B52B0] text-[#4B52B0] text-xs font-semibold px-3 py-2 rounded-lg hover:bg-[#E8EAFF] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            View
                        </a>
                    @endif

                    <button onclick="screenSingleCv({{ $cv['id'] }}, this)"
                            class="flex items-center gap-1.5 bg-[#2D3799] hover:bg-[#232d85] text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors {{ $cv['has_result'] ? 'opacity-50' : '' }}"
                            {{ $cv['has_result'] ? 'disabled' : '' }}>
                        @if($cv['has_result'])
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Screened
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Screen
                        @endif
                    </button>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if($cvs->isEmpty())
    <div class="py-16 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-400 text-sm">Belum ada CV untuk job ini.</p>
    </div>
    @endif
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full mx-4 text-center">
        <div class="w-12 h-12 border-4 border-[#4B52B0] border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-700 font-semibold text-sm mb-1">Sedang menganalisis CV...</p>
        <p class="text-gray-400 text-xs">Mohon tunggu, ini memakan waktu 10-30 detik</p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    /**
     * Screen SATU CV via AJAX
     */
    async function screenSingleCv(cvId, button) {
        const originalHTML = button.innerHTML;
        
        // Disable button & show loading
        button.disabled = true;
        button.innerHTML = `
            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            Processing...
        `;

        try {
            const response = await fetch(`/screening/${cvId}/screen`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
            });

            const data = await response.json();

            if (data.success) {
                // Update UI: show success badge + score
                const row = document.querySelector(`.cv-row[data-cv-id="${cvId}"]`);
                const statusCell = row.querySelector('.flex-shrink-0:first-of-type');
                const scoreCell = row.querySelector('.flex-shrink-0:nth-child(2)');
                const actionCell = row.querySelector('.flex-shrink-0:last-child');

                // Update status badge
                statusCell.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-600 text-xs font-semibold px-3 py-1.5 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3l14 9-14 9V3z"/>
                        </svg>
                        ${data.data.status}
                    </span>
                `;

                // Update score
                if (data.data.score !== null) {
                    scoreCell.innerHTML = `
                        <p class="text-lg font-bold text-gray-800">${data.data.score}%</p>
                        <p class="text-xs text-gray-400">Rank #${data.data.rank ?? '-'}</p>
                    `;
                }

                // Update action buttons
                actionCell.innerHTML = `
                    <a href="/candidate/${data.data.id}"
                       class="flex items-center gap-1.5 border border-[#4B52B0] text-[#4B52B0] text-xs font-semibold px-3 py-2 rounded-lg hover:bg-[#E8EAFF] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View
                    </a>
                    <button disabled class="flex items-center gap-1.5 bg-green-100 text-green-600 text-xs font-semibold px-3 py-2 rounded-lg cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3l14 9-14 9V3z"/>
                        </svg>
                        Done
                    </button>
                `;

                alert('✅ ' + data.message);
            } else {
                alert('❌ ' + data.message);
                button.disabled = false;
                button.innerHTML = originalHTML;
            }

        } catch (error) {
            console.error('Screening error:', error);
            alert('❌ Terjadi kesalahan. Silakan coba lagi.');
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
    }

    /**
     * Screen SEMUA CV (batch)
     */
    async function screenAllCvs() {
        if (!confirm('Yakin ingin screen SEMUA CV? Proses ini akan memakan waktu beberapa menit.')) {
            return;
        }

        const jobId = {{ $job->id }};
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.remove('hidden');

        try {
            const response = await fetch(`/screening/${jobId}/screen-all`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
            });

            const data = await response.json();

            overlay.classList.add('hidden');

            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }

        } catch (error) {
            console.error('Batch screening error:', error);
            overlay.classList.add('hidden');
            alert('❌ Terjadi kesalahan. Silakan coba lagi.');
        }
    }
</script>
@endpush