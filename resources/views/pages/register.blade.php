<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CVision</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#7B82C9] flex items-center justify-center px-4">

    <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl px-12 py-10">

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Row 1: Email + Password --}}
            <div class="grid grid-cols-2 gap-4 mb-4">

                {{-- Email --}}
                <div class="flex items-center bg-[#C8CBEE]/40 rounded-full overflow-hidden">
                    <div class="w-12 h-12 bg-[#7B82C9] rounded-full flex items-center justify-center ml-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="Email address"
                           class="flex-1 bg-transparent px-4 py-3.5 text-sm text-gray-700 placeholder-gray-500 focus:outline-none min-w-0">
                </div>

                {{-- Password --}}
                <div class="flex items-center bg-[#C8CBEE]/40 rounded-full overflow-hidden">
                    <div class="w-12 h-12 bg-[#7B82C9] rounded-full flex items-center justify-center ml-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input type="password" name="password"
                           placeholder="Password"
                           class="flex-1 bg-transparent px-4 py-3.5 text-sm text-gray-700 placeholder-gray-500 focus:outline-none min-w-0">
                </div>

            </div>

            {{-- Row 2: Username + Confirm Password --}}
            <div class="grid grid-cols-2 gap-4 mb-6">

                {{-- Username --}}
                <div class="flex items-center bg-[#C8CBEE]/40 rounded-full overflow-hidden">
                    <div class="w-12 h-12 bg-[#7B82C9] rounded-full flex items-center justify-center ml-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Username"
                           class="flex-1 bg-transparent px-4 py-3.5 text-sm text-gray-700 placeholder-gray-500 focus:outline-none min-w-0">
                </div>

                {{-- Confirm Password --}}
                <div class="flex items-center bg-[#C8CBEE]/40 rounded-full overflow-hidden">
                    <div class="w-12 h-12 bg-[#7B82C9] rounded-full flex items-center justify-center ml-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input type="password" name="password_confirmation"
                           placeholder="Confirm Password"
                           class="flex-1 bg-transparent px-4 py-3.5 text-sm text-gray-700 placeholder-gray-500 focus:outline-none min-w-0">
                </div>

            </div>

            {{-- Make Account --}}
            <button type="submit"
                    class="w-full bg-[#C8CBEE]/60 hover:bg-[#7B82C9] hover:text-white text-gray-700 font-semibold py-3.5 rounded-full transition-all duration-200 text-sm mb-5">
                Make account
            </button>

        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="flex-1 h-px bg-gray-300"></div>
            <span class="text-sm text-gray-500">Or sign up with</span>
            <div class="flex-1 h-px bg-gray-300"></div>
        </div>


        <p class="text-center text-sm text-gray-600">
            Already have an account ?
            <a href="{{ route('login') }}" class="text-[#4B52B0] font-semibold hover:underline ml-1">Sign in</a>
        </p>

    </div>

</body>
</html>