<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CVision</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#7B82C9] flex items-center justify-center px-4 py-10">

    {{-- ===================== CARD: PELAMAR LOGIN ===================== --}}
    <div id="pelamarLoginCard" class="bg-white rounded-3xl shadow-xl w-full max-w-lg px-12 py-10">

        @if($errors->any() && !old('hrd_login'))
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="login_as" value="pelamar">

            {{-- Email --}}
            <div class="mb-4">
                <div class="flex items-center bg-[#C8CBEE]/40 rounded-full overflow-hidden">
                    <div class="w-12 h-12 bg-[#7B82C9] rounded-full flex items-center justify-center ml-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input type="text" name="email" value="{{ old('email') }}"
                           placeholder="Email or Username"
                           class="flex-1 bg-transparent px-4 py-3.5 text-sm text-gray-700 placeholder-gray-500 focus:outline-none">
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <div class="flex items-center bg-[#C8CBEE]/40 rounded-full overflow-hidden">
                    <div class="w-12 h-12 bg-[#7B82C9] rounded-full flex items-center justify-center ml-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input type="password" name="password"
                           placeholder="Password"
                           class="flex-1 bg-transparent px-4 py-3.5 text-sm text-gray-700 placeholder-gray-500 focus:outline-none">
                </div>
            </div>

            {{-- Remember me + Forgot Password --}}
            <div class="flex items-center justify-between mb-5 px-1">
                <label class="flex items-center gap-2 text-sm text-[#7B82C9] font-medium cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded accent-[#4B52B0]">
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-[#7B82C9] font-medium hover:text-[#4B52B0] transition-colors">
                    Forgot Password?
                </a>
            </div>

            {{-- Login Button --}}
            <button type="submit"
                    class="w-full bg-[#C8CBEE]/60 hover:bg-[#7B82C9] hover:text-white text-gray-700 font-semibold py-3.5 rounded-full transition-all duration-200 text-sm mb-5">
                Login
            </button>

        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="flex-1 h-px bg-gray-300"></div>
            <span class="text-sm text-gray-500">Or sign in with</span>
            <div class="flex-1 h-px bg-gray-300"></div>
        </div>

        {{-- Google --}}
        <a href="{{ route('auth.google') }}"
           class="w-full flex items-center justify-center gap-3 bg-[#C8CBEE]/40 hover:bg-[#C8CBEE]/60 text-gray-700 font-semibold py-3.5 rounded-full transition-all duration-200 text-sm mb-6">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Sign in with google
        </a>

        <p class="text-center text-sm text-gray-600 mb-2">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-[#4B52B0] font-semibold hover:underline ml-1">Sign up</a>
        </p>

        {{-- Toggle to HRD form --}}
        <p class="text-center text-sm">
            <button type="button" onclick="showHrdLogin()"
                    class="text-[#7B82C9] font-medium hover:text-[#4B52B0] hover:underline transition-colors bg-transparent border-0 cursor-pointer">
                Login as HRD?
            </button>
        </p>

    </div>

    {{-- ===================== CARD: HRD/ADMIN LOGIN ===================== --}}
    <div id="hrdLoginCard" class="hidden bg-white rounded-3xl shadow-xl w-full max-w-md px-10 py-10">

        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            HRD <span class="text-[#4B52B0]">Login</span>
        </h2>

        @if($errors->any() && old('hrd_login'))
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="login_as" value="admin">
            <input type="hidden" name="hrd_login" value="1">

            {{-- Email --}}
            <div class="mb-4">
                <div class="flex items-center bg-[#C8CBEE]/40 rounded-full overflow-hidden">
                    <div class="w-11 h-11 bg-[#7B82C9] rounded-full flex items-center justify-center ml-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input type="text" name="email_hrd"
                           placeholder="Email or Username"
                           class="flex-1 bg-transparent px-4 py-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none">
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-6">
                <div class="flex items-center bg-[#C8CBEE]/40 rounded-full overflow-hidden">
                    <div class="w-11 h-11 bg-[#7B82C9] rounded-full flex items-center justify-center ml-1 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input type="password" name="password_hrd"
                           placeholder="Password"
                           class="flex-1 bg-transparent px-4 py-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none">
                </div>
            </div>

            {{-- Login Button --}}
            <button type="submit"
                    class="w-full bg-[#C8CBEE]/60 hover:bg-[#7B82C9] hover:text-white text-gray-700 font-semibold py-3.5 rounded-full transition-all duration-200 text-sm mb-5">
                Login
            </button>

        </form>

        {{-- Toggle back to Pelamar form --}}
        <p class="text-center text-sm">
            <button type="button" onclick="showPelamarLogin()"
                    class="text-[#7B82C9] font-medium hover:text-[#4B52B0] hover:underline transition-colors bg-transparent border-0 cursor-pointer">
                Back to Pelamar Login
            </button>
        </p>

    </div>

    <script>
        function showHrdLogin() {
            document.getElementById('pelamarLoginCard').classList.add('hidden');
            document.getElementById('hrdLoginCard').classList.remove('hidden');
        }
        function showPelamarLogin() {
            document.getElementById('hrdLoginCard').classList.add('hidden');
            document.getElementById('pelamarLoginCard').classList.remove('hidden');
        }
    </script>

</body>
</html>