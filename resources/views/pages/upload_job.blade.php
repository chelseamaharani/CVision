@extends('layouts.dashboard')

@section('title', 'Upload New Job - CVision')

@section('content')

{{-- Judul --}}
<h1 class="text-2xl font-bold text-[#7B82C9] text-center mb-6">Upload New Job</h1>

<form action="{{ route('jobs.store') }}" method="POST" id="uploadJobForm">
    @csrf

    <div class="bg-[#DDE0F5] rounded-2xl p-8 flex flex-col gap-5 max-w-3xl mx-auto">

        {{-- Job Title --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-1.5 text-sm">Job Title</label>
            <input type="text" name="job_title" value="{{ old('job_title') }}"
                   placeholder="Enter job title"
                   class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm">
            @error('job_title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Job Category --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-1.5 text-sm">Job Category</label>
            <div class="relative">
                <select name="job_category" id="jobCategory"
                        class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-400 appearance-none focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm cursor-pointer"
                        onchange="handleCategoryChange(this)">
                    <option value="" disabled {{ old('job_category') ? '' : 'selected' }}>Select job category</option>
                    <option value="IT"              {{ old('job_category')=='IT'              ? 'selected':'' }}>Information Technology (IT)</option>
                    <option value="Marketing"       {{ old('job_category')=='Marketing'       ? 'selected':'' }}>Marketing & Sales</option>
                    <option value="Finance"         {{ old('job_category')=='Finance'         ? 'selected':'' }}>Finance & Accounting</option>
                    <option value="HR"              {{ old('job_category')=='HR'              ? 'selected':'' }}>Human Resources (HR)</option>
                    <option value="Operations"      {{ old('job_category')=='Operations'      ? 'selected':'' }}>Operations</option>
                    <option value="CustomerService" {{ old('job_category')=='CustomerService' ? 'selected':'' }}>Customer Service</option>
                    <option value="Administration"  {{ old('job_category')=='Administration'  ? 'selected':'' }}>Administration</option>
                    <option value="Engineering"     {{ old('job_category')=='Engineering'     ? 'selected':'' }}>Engineering (Non-IT)</option>
                    <option value="Design"          {{ old('job_category')=='Design'          ? 'selected':'' }}>Design & Creative</option>
                    <option value="Education"       {{ old('job_category')=='Education'       ? 'selected':'' }}>Education & Training</option>
                    <option value="Healthcare"      {{ old('job_category')=='Healthcare'      ? 'selected':'' }}>Healthcare</option>
                    <option value="Others"          {{ old('job_category')=='Others'          ? 'selected':'' }}>Others</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
            {{-- Input muncul kalau pilih Others --}}
            <input type="text" name="job_category_other" id="jobCategoryOther"
                   value="{{ old('job_category_other') }}"
                   placeholder="Tuliskan kategori pekerjaan kamu..."
                   class="hidden w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm mt-2">
            @error('job_category')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Job Description --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-1.5 text-sm">Job Description</label>
            <textarea name="job_description" rows="5"
                      placeholder="Enter detailed job description"
                      class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm resize-none">{{ old('job_description') }}</textarea>
            @error('job_description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Required Skills --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-1.5 text-sm">Required Skills</label>
            <div class="bg-white rounded-xl px-4 py-3 shadow-sm focus-within:ring-2 focus-within:ring-[#4B52B0]/40 transition">
                <input type="text" id="skillInput"
                       placeholder="Enter 5–10 key skills (press Enter after each skill) to improve candidate matching"
                       class="w-full text-sm text-gray-700 placeholder-gray-400 focus:outline-none bg-transparent">
                <input type="hidden" name="required_skills" id="skillsHidden" value="{{ old('required_skills') }}">
                <div id="skillTags" class="flex flex-wrap gap-2 mt-2"></div>
            </div>
            @error('required_skills')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Minimum Experience --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-1.5 text-sm">Minimum Experience</label>
            <div class="relative">
                <select name="min_experience"
                        class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-400 appearance-none focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm cursor-pointer">
                    <option value="" disabled {{ old('min_experience') ? '' : 'selected' }}>Select minimum experience</option>
                    <option value="0-1" {{ old('min_experience')=='0-1' ? 'selected':'' }}>0–1 years</option>
                    <option value="1-2" {{ old('min_experience')=='1-2' ? 'selected':'' }}>1–2 years</option>
                    <option value="2-3" {{ old('min_experience')=='2-3' ? 'selected':'' }}>2–3 years</option>
                    <option value="3-5" {{ old('min_experience')=='3-5' ? 'selected':'' }}>3–5 years</option>
                    <option value="5+"  {{ old('min_experience')=='5+'  ? 'selected':'' }}>5+ years</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
            @error('min_experience')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Education Requirement --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-1.5 text-sm">Education Requirement</label>
            <div class="relative">
                <select name="education_requirement"
                        class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-400 appearance-none focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm cursor-pointer">
                    <option value="" disabled {{ old('education_requirement') ? '' : 'selected' }}>Select Educational Requirement</option>
                    <option value="SMA" {{ old('education_requirement')=='SMA' ? 'selected':'' }}>SMA</option>
                    <option value="D3"  {{ old('education_requirement')=='D3'  ? 'selected':'' }}>D3</option>
                    <option value="D4"  {{ old('education_requirement')=='D4'  ? 'selected':'' }}>D4</option>
                    <option value="S1"  {{ old('education_requirement')=='S1'  ? 'selected':'' }}>S1</option>
                    <option value="S2"  {{ old('education_requirement')=='S2'  ? 'selected':'' }}>S2</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
            @error('education_requirement')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol NEXT --}}
        <div class="flex justify-end pt-2">
            <button type="submit"
                    class="bg-white hover:bg-gray-50 text-gray-600 font-semibold text-sm px-10 py-2.5 rounded-xl shadow-sm border border-gray-200 transition-colors">
                NEXT
            </button>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
    // ===== Skills Tag Input =====
    let skills = [];

    const skillInput  = document.getElementById('skillInput');
    const skillTags   = document.getElementById('skillTags');
    const skillsHidden = document.getElementById('skillsHidden');

    if (skillsHidden.value) {
        skills = skillsHidden.value.split(',').filter(s => s.trim());
        renderTags();
    }

    skillInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value.trim();
            if (val && !skills.includes(val)) {
                skills.push(val);
                renderTags();
                skillsHidden.value = skills.join(',');
            }
            this.value = '';
        }
    });

    function renderTags() {
        skillTags.innerHTML = '';
        skills.forEach((skill, i) => {
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-1 bg-white border border-[#7B82C9]/40 text-[#4B52B0] text-xs font-medium px-3 py-1 rounded-full';
            tag.innerHTML = `${skill} <button type="button" onclick="removeSkill(${i})" class="text-[#4B52B0]/50 hover:text-red-400 font-bold ml-0.5 leading-none transition-colors">✕</button>`;
            skillTags.appendChild(tag);
        });
    }

    function removeSkill(index) {
        skills.splice(index, 1);
        renderTags();
        skillsHidden.value = skills.join(',');
    }

    // ===== Job Category Others =====
    function handleCategoryChange(select) {
        const otherInput = document.getElementById('jobCategoryOther');
        if (select.value === 'Others') {
            otherInput.classList.remove('hidden');
            otherInput.required = true;
        } else {
            otherInput.classList.add('hidden');
            otherInput.required = false;
            otherInput.value = '';
        }
    }
</script>
@endpush