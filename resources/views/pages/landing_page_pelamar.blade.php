@extends('layouts.pelamar')

@section('title', 'Upload CV - CVision')

@section('content')

<section class="bg-[#E8EAFF] px-8 py-12 md:py-16">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10 items-start">

        {{-- ===================== KIRI: SAPAAN + INFO ===================== --}}
        <div>
            <p class="text-gray-600 text-base mb-1">
                Halo, {{ auth()->check() ? auth()->user()->name : 'Pelamar' }}
            </p>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight mb-1">
                Siap untuk bekerja?
            </h1>
            <h2 class="text-3xl md:text-4xl font-bold text-[#4B52B0] leading-tight mb-5">
                Upload CV-mu disini!
            </h2>

            <p class="text-gray-600 text-base leading-relaxed mb-8 max-w-lg">
                Unggah CV terbaikmu dan temukan peluang kerja yang sesuai dengan kemampuanmu.
                Sistem kami akan mencocokkan CV dengan lowongan yang paling relevan untukmu.
            </p>

            {{-- Feature Icons --}}
            <div class="grid grid-cols-3 gap-4 mb-8">

                <div>
                    <div class="w-14 h-14 rounded-full bg-[#DDE0F5] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-[#4B52B0] font-semibold text-sm mb-1">Format PDF/DOCS</p>
                    <p class="text-gray-500 text-xs leading-relaxed">Hanya file PDF atau DOC/DOCS</p>
                </div>

                <div>
                    <div class="w-14 h-14 rounded-full bg-[#DDE0F5] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="text-[#4B52B0] font-semibold text-sm mb-1">Aman & Terpecaya</p>
                    <p class="text-gray-500 text-xs leading-relaxed">Data CV-mu aman dan terlindungi</p>
                </div>

                <div>
                    <div class="w-14 h-14 rounded-full bg-[#DDE0F5] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="text-[#4B52B0] font-semibold text-sm mb-1">Proses Cepat</p>
                    <p class="text-gray-500 text-xs leading-relaxed">Dapatkan rekomendasi dalam hitungan cepat</p>
                </div>

            </div>

            {{-- Tips Box --}}
            <div class="bg-[#7B82C9] text-white rounded-xl px-6 py-4 max-w-md">
                <p class="text-sm font-medium leading-relaxed">
                    <span class="font-bold">Tips :</span> Pastikan CV-mu terbaru dan informatif agar peluangmu semakin besar!
                </p>
            </div>
        </div>

        {{-- ===================== KANAN: CARD UPLOAD/RIWAYAT ===================== --}}
        <div class="bg-white rounded-2xl shadow-md p-8">

            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 mb-6">
                <button type="button" onclick="switchTab('upload')" id="tabUploadBtn"
                        class="flex-1 text-center pb-3 font-semibold text-sm text-[#4B52B0] border-b-2 border-[#4B52B0] transition-colors">
                    Upload CV
                </button>
                <button type="button" onclick="switchTab('riwayat')" id="tabRiwayatBtn"
                        class="flex-1 text-center pb-3 font-semibold text-sm text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition-colors">
                    Riwayat CV
                </button>
            </div>

            {{-- ============ TAB: UPLOAD CV ============ --}}
            <div id="tabUpload">

                <form action="{{ route('cv.store') }}" method="POST" enctype="multipart/form-data" id="uploadCvForm">
                    @csrf

                    {{-- Posisi yang Dilamar --}}
                    <div class="mb-5">
                        <label class="block font-semibold text-gray-800 mb-2 text-sm">Posisi yang Dilamar</label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}"
                               placeholder="Tulis posisi yang dilamar"
                               class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/30 transition">
                        @error('job_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Upload CV --}}
                    <div class="mb-5">
                        <label class="block font-semibold text-gray-800 mb-2 text-sm">Upload CV</label>

                        {{-- Dropzone (sebelum file dipilih) --}}
                        <label for="cvFileInput" id="dropzone"
                               class="cursor-pointer flex flex-col items-center justify-center text-center border-2 border-dashed border-gray-300 rounded-xl py-10 px-4 hover:border-[#4B52B0] hover:bg-[#F5F6FF] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm text-gray-700 mb-1">
                                Drag dan drop <span class="text-[#4B52B0] font-semibold">file CV</span> kamu disini
                                <br>atau klik untuk memilih file
                            </p>
                            <p class="text-xs text-gray-400 mt-2">Format yang didukung: PDF, DOC, DOCS</p>
                            <p class="text-xs text-gray-400">Maksimal ukuran file 5MB</p>
                        </label>

                        {{-- File preview (setelah file dipilih) --}}
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

                    {{-- Simpan CV --}}
                    @guest
                        {{-- Belum login: arahkan ke halaman login, bukan submit form --}}
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

            {{-- ============ TAB: RIWAYAT CV ============ --}}
            <div id="tabRiwayat" class="hidden">

                @guest
                    {{-- Belum login: tampil pesan, bukan list riwayat --}}
                    <div class="text-center py-16">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="text-gray-500 text-sm mb-4">Login dulu untuk melihat riwayat CV kamu.</p>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 bg-[#3B44A9] hover:bg-[#2F3890] text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition-colors">
                            Login Sekarang
                        </a>
                    </div>
                @else

                <div class="flex items-center justify-between mb-1">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-base">Riwayat CV yang Diunggah</h3>
                        <p class="text-gray-400 text-xs mt-0.5">Kelola CV yang pernah kamu unggah disini.</p>
                    </div>
                    <button type="button" onclick="switchTab('upload')"
                            class="flex items-center gap-1.5 bg-[#3B44A9] hover:bg-[#2F3890] text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload CV Baru
                    </button>
                </div>

                <div class="mt-5 flex flex-col gap-3">

                    @php
                    $riwayatCv = $riwayatCv ?? [
                        ['posisi'=>'Frontend Developer','filename'=>'CV_Salsabila.pdf','tipe'=>'pdf','tanggal'=>'12 Juni 2024','jam'=>'10:30 WIB'],
                        ['posisi'=>'UI/UX Designer','filename'=>'CV_Salsabila_UIUX.docx','tipe'=>'doc','tanggal'=>'28 Mei 2024','jam'=>'14:20 WIB'],
                        ['posisi'=>'Data Analyst','filename'=>'CV_Salsabila_Data.pdf','tipe'=>'pdf','tanggal'=>'15 Mei 2024','jam'=>'09:15 WIB'],
                    ];
                    @endphp

                    @forelse($riwayatCv as $cv)
                    <div class="flex items-center gap-3 border border-gray-100 rounded-xl px-4 py-3 hover:bg-gray-50 transition-colors">

                        {{-- Icon file --}}
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $cv['tipe'] === 'pdf' ? 'bg-red-50' : 'bg-blue-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $cv['tipe'] === 'pdf' ? 'text-red-500' : 'text-blue-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $cv['posisi'] }}</p>
                            <p class="text-gray-400 text-xs truncate">{{ $cv['filename'] }}</p>
                        </div>

                        {{-- Tanggal --}}
                        <div class="hidden sm:flex items-center gap-1.5 text-gray-400 text-xs flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $cv['tanggal'] }}<br>{{ $cv['jam'] }}</span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button type="button" class="flex items-center gap-1 text-[#4B52B0] text-xs font-semibold hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                            <button type="button" class="flex items-center gap-1 text-red-500 text-xs font-semibold hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </div>

                    </div>
                    @empty
                    <p class="text-center text-gray-400 text-sm py-10">Belum ada CV yang diunggah.</p>
                    @endforelse

                </div>

                {{-- Pagination --}}
                <div class="flex justify-center items-center gap-2 mt-6">
                    <button class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <span class="w-7 h-7 flex items-center justify-center bg-[#3B44A9] text-white text-xs font-semibold rounded-lg">1</span>
                    <button class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
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
        const uploadTab   = document.getElementById('tabUpload');
        const riwayatTab  = document.getElementById('tabRiwayat');
        const uploadBtn   = document.getElementById('tabUploadBtn');
        const riwayatBtn  = document.getElementById('tabRiwayatBtn');

        if (tab === 'upload') {
            uploadTab.classList.remove('hidden');
            riwayatTab.classList.add('hidden');
            uploadBtn.classList.add('text-[#4B52B0]', 'border-[#4B52B0]');
            uploadBtn.classList.remove('text-gray-400', 'border-transparent');
            riwayatBtn.classList.remove('text-[#4B52B0]', 'border-[#4B52B0]');
            riwayatBtn.classList.add('text-gray-400', 'border-transparent');
        } else {
            riwayatTab.classList.remove('hidden');
            uploadTab.classList.add('hidden');
            riwayatBtn.classList.add('text-[#4B52B0]', 'border-[#4B52B0]');
            riwayatBtn.classList.remove('text-gray-400', 'border-transparent');
            uploadBtn.classList.remove('text-[#4B52B0]', 'border-[#4B52B0]');
            uploadBtn.classList.add('text-gray-400', 'border-transparent');
        }
    }

    // ===== File Upload Preview =====
    const cvFileInput = document.getElementById('cvFileInput');
    const dropzone     = document.getElementById('dropzone');
    const filePreview  = document.getElementById('filePreview');
    const fileName     = document.getElementById('fileName');
    const fileSize      = document.getElementById('fileSize');

    cvFileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            showFilePreview(this.files[0]);
        }
    });

    function showFilePreview(file) {
        fileName.textContent = file.name;
        fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        dropzone.classList.add('hidden');
        filePreview.classList.remove('hidden');
    }

    function removeFile() {
        cvFileInput.value = '';
        dropzone.classList.remove('hidden');
        filePreview.classList.add('hidden');
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