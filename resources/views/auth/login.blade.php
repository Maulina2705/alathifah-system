@extends('layouts.auth')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-7 lg:p-8 border border-slate-100 dark:border-slate-800 transition-colors">
    <!-- Brand Header -->
    <div class="text-center mb-6">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 via-pink-500 to-amber-400 p-0.5 mx-auto flex items-center justify-center shadow-xl shadow-blue-500/20 mb-3">
            <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center">
                <span class="text-2xl font-extrabold bg-gradient-to-r from-blue-400 via-pink-400 to-amber-300 bg-clip-text text-transparent">A</span>
            </div>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight uppercase">Master Raport Tahfizh</h2>
        <p class="text-xs font-bold bg-gradient-to-r from-blue-600 to-pink-600 bg-clip-text text-transparent">SD & SMP Islam Riau Global Terpadu</p>
        <p class="text-[11px] text-slate-400 mt-1">Al-Athifa Tahfizh Assessment & Reporting System</p>
    </div>

    <!-- Login Form -->
    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        @if($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-700 dark:text-rose-300 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <div>
            <label for="login" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Username / Email</label>
            <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-800 dark:text-slate-100 focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-900 transition"
                   placeholder="username / email">
        </div>

        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
            <div class="relative">
                <input type="password" name="password" id="password" required
                       class="w-full pl-4 pr-11 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-800 dark:text-slate-100 focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-900 transition"
                       placeholder="••••••••">
                <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-hidden transition" title="Lihat/Sembunyikan Password">
                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeSlashIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 pt-1">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                <span>Ingat saya</span>
            </label>
            <span class="text-slate-400 text-[11px]">Hubungi Admin jika lupa password</span>
        </div>

        <button type="submit"
                class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition transform active:scale-98 cursor-pointer">
            Masuk ke Sistem
        </button>
    </form>

    <!-- Footer Copyright -->
    <div class="mt-7 pt-5 border-t border-slate-100 dark:border-slate-800 text-center">
        <p class="text-xs text-slate-400 dark:text-slate-500">
            &copy; 2026 MHS-IT Team IRGT. All rights reserved.
        </p>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    }
</script>
@endsection
