@extends('layouts.pelamar')

@section('title', 'Upload CV - CVision')

@section('content')

<section class="bg-[#E8EAFF] px-8 py-12 md:py-16">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10 items-start">

        {{-- ===================== LEFT: GREETING + INFO ===================== --}}
        <div>
            <p class="text-gray-600 text-base mb-1">
                Hello, {{ auth()->check() ? auth()->user()->name : 'Applicant' }}
            </p>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight mb-1">
                Ready to work?
            </h1>
            <h2 class="text-3xl md:text-4xl font-bold text-[#4B52B0] leading-tight mb-5">
                Upload your CV here!
            </h2>

            <p class="text-gray-600 text-base leading-relaxed mb-8 max-w-lg">
                Upload your best CV and find job opportunities that match your skills.
                Our system will match your CV with the most relevant job openings for you.
            </p>

            {{-- Feature Icons --}}
            <div class="grid grid-cols-3 gap-4 mb-8">

                <div>
                    <div class="w-14 h-14 rounded-full bg-[#DDE0F5] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-[#4B52B0] font-semibold text-sm mb-1">PDF/DOCX Format</p>
                    <p class="text-gray-500 text-xs leading-relaxed">PDF or DOC/DOCX files only</p>
                </div>

                <div>
                    <div class="w-14 h-14 rounded-full bg-[#DDE0F5] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="text-[#4B52B0] font-semibold text-sm mb-1">Safe & Trusted</p>
                    <p class="text-gray-500 text-xs leading-relaxed">Your CV data is safe and protected</p>
                </div>

                <div>
                    <div class="w-14 h-14 rounded-full bg-[#DDE0F5] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="text-[#4B52B0] font-semibold text-sm mb-1">Fast Process</p>
                    <p class="text-gray-500 text-xs leading-relaxed">Get recommendations in no time</p>
                </div>

            </div>

            {{-- Tips Box --}}
            <div class="bg-[#7B82C9] text-white rounded-xl px-6 py-4 max-w-md">
                <p class="text-sm font-medium leading-relaxed">
                    <span class="font-bold">Tip:</span> Make sure your CV is up to date and informative to increase your chances!
                </p>
            </div>
        </div>

        {{-- ===================== RIGHT: UPLOAD/HISTORY CARD ===================== --}}
        <div class="bg-white rounded-2xl shadow-md p-8">

            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 mb-6">
                <button type="button" onclick="switchTab('upload')" id="tabUploadBtn"
                        class="flex-1 text-center pb-3 font-semibold text-sm text-[#4B52B0] border-b-2 border-[#4B52B0] transition-colors">
                    Upload CV
                </button>
                <button type="button" onclick="switchTab('history')" id="tabHistoryBtn"
                        class="flex-1 text-center pb-3 font-semibold text-sm text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition-colors">
                    CV History
                </button>
            </div>

            {{-- Success / Error messages --}}
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
                {{ $errors->first() }}
            </div>
            @endif

            {{-- ============ TAB: UPLOAD CV ============ --}}
            <div id="tabUpload">

                <form action="{{ route('cv.store') }}" method="POST" enctype="multipart/form-data" id="uploadCvForm">
                    @csrf

                    {{-- Position Applied --}}
                    <div class="mb-5">
                        <label class="block font-semibold text-gray-800 mb-2 text-sm">Position Applied</label>
                        <div class="relative">
                            <select name="upload_job_id"
                                    class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/30 transition cursor-pointer">
                                <option value="" disabled selected>Select the position you're applying for</option>
                                @forelse($jobs ?? [] as $job)
                                    <option value="{{ $job->id }}" {{ old('upload_job_id') == $job->id ? 'selected' : '' }}>
                                        {{ $job->title }}
                                    </option>
                                @empty
                                    <option value="" disabled>No job openings available yet</option>
                                @endforelse
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('upload_job_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Upload CV --}}
                    <div class="mb-5">
                        <label class="block font-semibold text-gray-800 mb-2 text-sm">Upload CV</label>

                        {{-- Dropzone (before file selected) --}}
                        <label for="cvFileInput" id="dropzone"
                               class="cursor-pointer flex flex-col items-center justify-center text-center border-2 border-dashed border-gray-300 rounded-xl py-10 px-4 hover:border-[#4B52B0] hover:bg-[#F5F6FF] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm text-gray-700 mb-1">
                                Drag and drop your <span class="text-[#4B52B0] font-semibold">CV file</span> here
                                <br>or click to browse
                            </p>
                            <p class="text-xs text-gray-400 mt-2">Supported formats: PDF, DOC, DOCX</p>
                            <p class="text-xs text-gray-400">Maximum file size 5MB</p>
                        </label>

                        {{-- File preview (after file selected) --}}
                        <div id="filePreview" class="hidden border border-gray-200 rounded-xl px-4 py-3 flex items-center gap-3">
                            <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p id="fileName" class="text-sm font-medium text-gray-800 truncate"></p>
                                <p id="fileSize" class="text-xs text-gray-400"></p>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-gray-400 hover:text-red-500 transition-colors flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <input type="file" id="cvFileInput" name="cv_file" accept=".pdf,.doc,.docx" class="hidden">
                        @error('cv_file')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    @guest
                        <a href="{{ route('login') }}"
                           class="w-full bg-[#3B44A9] hover:bg-[#2F3890] text-white font-semibold text-sm py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Login to Upload CV
                        </a>
                    @else
                        <button type="submit"
                                class="w-full bg-[#3B44A9] hover:bg-[#2F3890] text-white font-semibold text-sm py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload CV
                        </button>
                    @endguest

                </form>
            </div>

            {{-- ============ TAB: CV HISTORY ============ --}}
            <div id="tabHistory" class="hidden">

                @guest
                    <div class="text-center py-16">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="text-gray-500 text-sm mb-4">Log in to view your CV history.</p>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 bg-[#3B44A9] hover:bg-[#2F3890] text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition-colors">
                            Login Now
                        </a>
                    </div>
                @else

                <div class="flex items-center justify-between mb-1">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-base">Uploaded CV History</h3>
                        <p class="text-gray-400 text-xs mt-0.5">Manage the CVs you've previously uploaded here.</p>
                    </div>
                    <button type="button" onclick="switchTab('upload')"
                            class="flex items-center gap-1.5 bg-[#3B44A9] hover:bg-[#2F3890] text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload New CV
                    </button>
                </div>

                <div class="mt-5 flex flex-col gap-3">

                    @forelse($riwayatCv as $cv)
                    <div class="border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                        {{-- Main row --}}
                        <div class="flex items-center gap-3 px-4 py-3">
                            {{-- File icon --}}
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ str_ends_with(strtolower($cv->file_name), '.pdf') ? 'bg-red-50' : 'bg-blue-50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ str_ends_with(strtolower($cv->file_name), '.pdf') ? 'text-red-500' : 'text-blue-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate">{{ $cv->uploadJob->title ?? 'Unknown Position' }}</p>
                                <p class="text-gray-400 text-xs truncate">{{ $cv->file_name }}</p>
                            </div>

                            {{-- Date --}}
                            <div class="hidden sm:flex items-center gap-1.5 text-gray-400 text-xs flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $cv->created_at->format('M d, Y') }}<br>{{ $cv->created_at->format('H:i') }}</span>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ asset('storage/' . $cv->file_path) }}" target="_blank"
                                   class="flex items-center gap-1 text-[#4B52B0] text-xs font-semibold hover:underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                                <button type="button" onclick="toggleRec('rec-{{ $cv->id }}')"
                                   class="flex items-center gap-1 text-[#4B52B0] text-xs font-semibold hover:underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    Recommendations
                                </button>
                                <form action="{{ route('cv.destroy', $cv->id) }}" method="POST" onsubmit="return confirm('Delete this CV?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-1 text-red-500 text-xs font-semibold hover:underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Recommendations panel --}}
                        <div id="rec-{{ $cv->id }}" class="hidden border-t border-gray-100 px-4 py-3 bg-[#F8F9FF]">
                            @if(!empty($cv->recommendations))
                                <p class="font-semibold text-gray-700 text-xs mb-2">AI Job Recommendations</p>
                                <div class="space-y-2">
                                    @foreach($cv->recommendations as $rec)
                                    <div>
                                        {{-- Clickable row --}}
                                        <button type="button" onclick="toggleRecDetail('rec-detail-{{ $cv->id }}-{{ $loop->index }}')"
                                           class="w-full flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-100 hover:bg-gray-50 transition-colors text-left">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="text-xs font-bold text-gray-400 w-4 flex-shrink-0">#{{ $rec['rank'] ?? $loop->iteration }}</span>
                                                <span class="text-sm font-semibold text-gray-800 truncate">{{ $rec['job_title'] }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                                <span class="text-xs font-bold {{ ($rec['confidence'] ?? 0) >= 80 ? 'text-green-600' : (($rec['confidence'] ?? 0) >= 50 ? 'text-yellow-500' : 'text-red-400') }}">
                                                    {{ $rec['confidence'] ?? 0 }}%
                                                </span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </button>
                                        {{-- Hidden detail (reasoning) --}}
                                        <div id="rec-detail-{{ $cv->id }}-{{ $loop->index }}" class="hidden px-3 py-2 text-xs text-gray-500 italic bg-white rounded-b-lg border-x border-b border-gray-100">
                                            @if(!empty($rec['reasoning']))
                                                {{ $rec['reasoning'] }}
                                            @else
                                                No additional details available.
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-gray-400 text-xs py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>AI analysis is still in progress or no recommendations available yet.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-400 text-sm py-10">No CVs uploaded yet.</p>
                    @endforelse

                </div>

                @endguest

            </div>

        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
    // ===== Tab Switching =====
    function switchTab(tab) {
        const uploadTab  = document.getElementById('tabUpload');
        const historyTab = document.getElementById('tabHistory');
        const uploadBtn  = document.getElementById('tabUploadBtn');
        const historyBtn = document.getElementById('tabHistoryBtn');

        if (tab === 'upload') {
            uploadTab.classList.remove('hidden');
            historyTab.classList.add('hidden');
            uploadBtn.classList.add('text-[#4B52B0]', 'border-[#4B52B0]');
            uploadBtn.classList.remove('text-gray-400', 'border-transparent');
            historyBtn.classList.remove('text-[#4B52B0]', 'border-[#4B52B0]');
            historyBtn.classList.add('text-gray-400', 'border-transparent');
        } else {
            historyTab.classList.remove('hidden');
            uploadTab.classList.add('hidden');
            historyBtn.classList.add('text-[#4B52B0]', 'border-[#4B52B0]');
            historyBtn.classList.remove('text-gray-400', 'border-transparent');
            uploadBtn.classList.remove('text-[#4B52B0]', 'border-[#4B52B0]');
            uploadBtn.classList.add('text-gray-400', 'border-transparent');
        }
    }

    // ===== File Upload Preview =====
    const cvFileInput = document.getElementById('cvFileInput');
    const dropzone    = document.getElementById('dropzone');
    const filePreview = document.getElementById('filePreview');
    const fileNameEl  = document.getElementById('fileName');
    const fileSizeEl  = document.getElementById('fileSize');

    cvFileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            showFilePreview(this.files[0]);
        }
    });

    function showFilePreview(file) {
        fileNameEl.textContent = file.name;
        fileSizeEl.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        dropzone.classList.add('hidden');
        filePreview.classList.remove('hidden');
    }

    function removeFile() {
        cvFileInput.value = '';
        dropzone.classList.remove('hidden');
        filePreview.classList.add('hidden');
    }

    // ===== Toggle Recommendations Panel =====
    function toggleRec(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.toggle('hidden');
        }
    }

    // ===== Toggle Recommendation Detail (reasoning) =====
    function toggleRecDetail(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.toggle('hidden');
        }
    }

    // Drag & drop support
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-[#4B52B0]', 'bg-[#F5F6FF]');
    });

    dropzone.addEventListener('dragleave', function(e) {
        this.classList.remove('border-[#4B52B0]', 'bg-[#F5F6FF]');
    });

    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-[#4B52B0]', 'bg-[#F5F6FF]');
        const file = e.dataTransfer.files[0];
        if (file) {
            cvFileInput.files = e.dataTransfer.files;
            showFilePreview(file);
        }
    });
</script>
@endpush