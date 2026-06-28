@extends('layouts.dashboard')

@section('title', 'Settings - CVision')

@section('content')

<h1 class="text-2xl font-bold text-[#7B82C9] text-center mb-6">Settings</h1>

<form action="{{ route('settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="bg-[#DDE0F5] rounded-2xl p-8 flex flex-col gap-5 max-w-3xl mx-auto">

        {{-- Avatar initial (display only, no upload) --}}
        <div class="flex justify-center mb-2">
            <div class="w-20 h-20 rounded-full bg-[#B8BFEA] flex items-center justify-center text-white text-3xl font-bold shadow-md">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
        </div>

        {{-- Full Name --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-1.5 text-sm">Full Name</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}"
                   placeholder="Enter your full name"
                   class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-1.5 text-sm">Email</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}"
                   placeholder="Enter your email address"
                   class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <hr class="border-gray-300/60 my-1">

        {{-- Change Password --}}
        <div>
            <label class="block font-semibold text-gray-800 mb-3 text-sm">Change Password</label>

            <div class="flex flex-col gap-3">
                <input type="password" name="old_password"
                       placeholder="Enter your old password"
                       class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm">
                @error('old_password')
                    <p class="text-red-500 text-xs -mt-1">{{ $message }}</p>
                @enderror

                <input type="password" name="new_password"
                       placeholder="Enter your new password"
                       class="w-full bg-white border-0 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4B52B0]/40 transition shadow-sm">
                @error('new_password')
                    <p class="text-red-500 text-xs -mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Success message --}}
        @if(session('success'))
        <div class="bg-green-100 text-green-700 text-sm px-4 py-2.5 rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        {{-- Save Changes --}}
        <div class="flex justify-end pt-2">
            <button type="submit"
                    class="bg-white hover:bg-gray-50 text-gray-600 font-semibold text-sm px-8 py-2.5 rounded-xl shadow-sm border border-gray-200 transition-colors">
                Save Changes
            </button>
        </div>

    </div>
</form>

@endsection