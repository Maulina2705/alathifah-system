<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Master Raport Tahfizh Al-Athifa' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Anti-flicker Early Theme Script -->
    <script>
        (function() {
            try {
                const mode = localStorage.getItem('alathifah_theme_mode') || 'light';
                const isDark = mode === 'dark' || (mode === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                const customTheme = localStorage.getItem('alathifah_custom_theme');
                if (customTheme) {
                    const p = JSON.parse(customTheme);
                    const r = document.documentElement;
                    if (p.primary) {
                        r.style.setProperty('--theme-primary', p.primary);
                        r.style.setProperty('--theme-primary-hover', p.primaryHover || p.primary);
                        r.style.setProperty('--theme-primary-light', p.primary + '18');
                        r.style.setProperty('--theme-primary-border', p.primary + '33');
                    }
                    if (p.pink) {
                        r.style.setProperty('--theme-pink', p.pink);
                        r.style.setProperty('--theme-pink-hover', p.pinkHover || p.pink);
                        r.style.setProperty('--theme-pink-light', p.pink + '18');
                        r.style.setProperty('--theme-pink-border', p.pink + '33');
                    }
                    if (p.yellow) {
                        r.style.setProperty('--theme-yellow', p.yellow);
                        r.style.setProperty('--theme-yellow-hover', p.yellowHover || p.yellow);
                        r.style.setProperty('--theme-yellow-light', p.yellow + '18');
                        r.style.setProperty('--theme-yellow-border', p.yellow + '33');
                    }
                }
            } catch(e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .arabic-font {
            font-family: 'Amiri', serif;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col transition-colors duration-200">

    <!-- Topbar & Sidebar Layout -->
    <div class="flex min-h-screen" x-data="{ mobileMenuOpen: false }">
        <!-- Sidebar Backdrop on Mobile -->
        <div x-show="mobileMenuOpen" 
             x-cloak 
             @click="mobileMenuOpen = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden transition-opacity">
        </div>

        <!-- Sidebar -->
        <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-slate-900 dark:bg-slate-950 text-slate-100 flex flex-col shadow-2xl lg:shadow-xl border-r border-slate-800 dark:border-slate-800/80 flex-shrink-0 transition-transform duration-300 ease-in-out">
            
            <!-- App Brand with Tri-Color Accent (Biru, Pink, Kuning) -->
            <div class="p-5 border-b border-slate-800/90 dark:border-slate-800 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 via-pink-500 to-amber-400 p-0.5 shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                        <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center text-white font-extrabold text-xl">
                            <span class="bg-gradient-to-r from-blue-400 via-pink-400 to-amber-300 bg-clip-text text-transparent">A</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5">
                            <h1 class="text-sm font-extrabold tracking-wide text-white uppercase leading-tight">Master Raport</h1>
                            <span class="inline-block w-2 h-2 rounded-full bg-pink-500 animate-pulse"></span>
                        </div>
                        <p class="text-xs bg-gradient-to-r from-blue-400 to-amber-300 bg-clip-text text-transparent font-bold tracking-wider">Tahfizh Al-Athifa</p>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button @click="mobileMenuOpen = false" class="lg:hidden p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- User Brief with Role Badge -->
            @auth
            <div class="px-5 py-4 bg-slate-850/60 dark:bg-slate-900/50 border-b border-slate-800/80">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-pink-500 text-white font-extrabold flex items-center justify-center text-sm shadow-md ring-2 ring-amber-400/30">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                            </span>
                            @if(auth()->user()->teacherProfile?->level)
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-pink-500/20 text-pink-300 border border-pink-500/30">
                                {{ auth()->user()->teacherProfile->level }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            @php
                $user = auth()->user();
                $isSuperAdmin = $user && $user->isSuperAdmin();
                $isIt = $user && $user->isIt();
                $isGuru = $user && $user->isGuru();
                $isWali = $user && $user->isWaliKelas();
                $isKepsek = $user && $user->isKepalaSekolah();
                $activeAY = \App\Models\AcademicYear::where('is_active', true)->first();
                $activeSem = \App\Models\Semester::where('is_active', true)->first();
            @endphp

            <!-- Quick Search Bar (Khusus Super Admin agar mudah filter menu yang banyak) -->
            @if($isSuperAdmin)
            <div class="px-4 pt-3 pb-1">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" 
                           id="sidebarMenuSearch" 
                           placeholder="Cari menu (Ctrl+K)..." 
                           oninput="filterSidebarMenus(this.value)"
                           class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-800/80 dark:bg-slate-900 border border-slate-700/60 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
            </div>
            @endif

            <!-- Navigation Links -->
            <nav id="sidebarNavContainer" class="flex-1 px-3 py-3 space-y-1 overflow-y-auto text-xs font-medium">

                <!-- 1. DASHBOARD (All Roles) -->
                <a href="{{ route('dashboard') }}" 
                   data-nav-item
                   data-nav-title="dashboard beranda utama"
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-3 opacity-90 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </div>
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('dashboard') ? 'bg-amber-300' : 'bg-transparent' }}"></span>
                </a>

                <!-- ================= SUPER ADMIN DEDICATED STREAMLINED SECTIONS ================= -->
                @if($isSuperAdmin)
                    <!-- KELOMPOK 1: MASTER DATA AKADEMIK -->
                    <div data-nav-group class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-blue-400 flex items-center justify-between">
                            <span>Master Data</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        </div>
                        <a href="{{ route('admin.students.index') }}" 
                           data-nav-item data-nav-title="data siswa santri murid"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.students.*') ? 'bg-slate-800 text-blue-400 font-bold border-l-2 border-blue-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-blue-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Data Siswa
                        </a>
                        <a href="{{ route('admin.teachers.index') }}" 
                           data-nav-item data-nav-title="data guru akun user staf pengajar"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.teachers.*') ? 'bg-slate-800 text-blue-400 font-bold border-l-2 border-blue-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-pink-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Data Guru & Akun
                        </a>
                        <a href="{{ route('admin.academics.index') }}" 
                           data-nav-item data-nav-title="tahun pelajaran semester akademik"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.academics.*') ? 'bg-slate-800 text-blue-400 font-bold border-l-2 border-blue-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-amber-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Tahun & Semester
                        </a>
                    </div>

                    <!-- KELOMPOK 2: KURIKULUM & PENILAIAN -->
                    <div data-nav-group class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-pink-400 flex items-center justify-between">
                            <span>Kurikulum & Nilai</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span>
                        </div>
                        <a href="{{ route('templates.index') }}" 
                           data-nav-item data-nav-title="template penilaian materi juz kurikulum"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('templates.*') ? 'bg-slate-800 text-pink-400 font-bold border-l-2 border-pink-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-pink-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Template Penilaian
                        </a>
                        <a href="{{ route('excel.index') }}" 
                           data-nav-item data-nav-title="excel ledger import export nilai tahfizh"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('excel.*') ? 'bg-slate-800 text-pink-400 font-bold border-l-2 border-pink-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-emerald-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                            Excel Ledger Nilai
                        </a>
                    </div>

                    <!-- KELOMPOK 3: PUSAT PERCETAKAN -->
                    <div data-nav-group class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-amber-400 flex items-center justify-between">
                            <span>Pusat Percetakan</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        </div>
                        <a href="{{ route('reports.index') }}" 
                           data-nav-item data-nav-title="antrian cetak raport bulk pdf print status"
                           class="flex items-center justify-between px-3 py-2 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-amber-400 font-bold border-l-2 border-amber-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3 text-amber-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Antrian Cetak & Raport</span>
                            </div>
                            <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">CETAK</span>
                        </a>
                    </div>

                    <!-- KELOMPOK 4: APPROVAL WORKFLOW -->
                    <div data-nav-group class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-purple-400 flex items-center justify-between">
                            <span>Verifikasi & Pengesahan</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                        </div>
                        <a href="{{ route('homeroom.reviews.index') }}" 
                           data-nav-item data-nav-title="review wali kelas periksa raport"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('homeroom.reviews.*') ? 'bg-slate-800 text-purple-400 font-bold border-l-2 border-purple-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-blue-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Review Wali Kelas
                        </a>
                        <a href="{{ route('principal.approvals.index') }}" 
                           data-nav-item data-nav-title="approval kepala sekolah pengesahan raport"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('principal.approvals.*') ? 'bg-slate-800 text-purple-400 font-bold border-l-2 border-purple-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-emerald-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Approval Kepsek
                        </a>
                    </div>

                    <!-- KELOMPOK 5: SISTEM & KONFIGURASI -->
                    <div data-nav-group class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                            <span>Sistem & Keamanan</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                        </div>
                        <a href="{{ route('admin.deadlines.index') }}" 
                           data-nav-item data-nav-title="batas waktu deadline input nilai raport"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.deadlines.*') ? 'bg-slate-800 text-amber-400 font-bold border-l-2 border-amber-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-amber-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Batas Waktu (Deadline)
                        </a>
                        <a href="{{ route('admin.settings.index') }}" 
                           data-nav-item data-nav-title="pengaturan raport sd smp kop tanda tangan margin kertas"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'bg-slate-800 text-blue-400 font-bold border-l-2 border-blue-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-blue-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                            Format Raport SD/SMP
                        </a>
                        <a href="{{ route('admin.history.index') }}" 
                           data-nav-item data-nav-title="riwayat data history log semester"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.history.*') ? 'bg-slate-800 text-blue-400 font-bold border-l-2 border-blue-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-cyan-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0zM3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                            Data History
                        </a>
                        <a href="{{ route('admin.audit.index') }}" 
                           data-nav-item data-nav-title="audit trail log aktivitas riwayat keamanan super admin"
                           class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.audit.*') ? 'bg-slate-800 text-pink-400 font-bold border-l-2 border-pink-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-pink-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            Audit Trail & Log
                        </a>
                    </div>
                @endif

                <!-- ================= IT & PERCETAKAN DEDICATED SECTIONS ================= -->
                @if($isIt && !$isSuperAdmin)
                    <div class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-amber-400">Tim Percetakan</div>
                        <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-amber-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Antrian Cetak & Raport
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.students.*') ? 'bg-slate-800 text-blue-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Data Siswa
                        </a>
                        <a href="{{ route('excel.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('excel.*') ? 'bg-slate-800 text-emerald-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                            Import / Export Excel
                        </a>
                        <a href="{{ route('admin.deadlines.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.deadlines.*') ? 'bg-slate-800 text-amber-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Batas Waktu (Deadline)
                        </a>
                        <a href="{{ route('admin.history.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.history.*') ? 'bg-slate-800 text-cyan-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0zM3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                            Data History
                        </a>
                    </div>
                @endif

                <!-- ================= GURU TAHFIZH SECTIONS ================= -->
                @if($isGuru && !$isSuperAdmin)
                    <div class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-pink-400">Guru Tahfizh</div>
                        <a href="{{ route('teacher.students.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('teacher.students.*') ? 'bg-slate-800 text-pink-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Siswa Bimbingan Saya
                        </a>
                        <a href="{{ route('templates.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('templates.*') ? 'bg-slate-800 text-pink-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Template Penilaian
                        </a>
                        <a href="{{ route('excel.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('excel.*') ? 'bg-slate-800 text-emerald-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                            Excel Ledger Nilai
                        </a>
                    </div>
                @endif

                <!-- ================= WALI KELAS SECTIONS ================= -->
                @if($isWali && !$isSuperAdmin)
                    <div class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-blue-400">Wali Kelas</div>
                        <a href="{{ route('homeroom.reviews.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('homeroom.reviews.*') ? 'bg-slate-800 text-blue-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Review Raport Siswa
                        </a>
                        <a href="{{ route('reports.homeroom_bulk_pdf') }}" target="_blank" class="flex items-center px-3 py-2 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-amber-300">
                            <svg class="w-4 h-4 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download Raport 1 Kelas (PDF)
                        </a>
                    </div>
                @endif

                <!-- ================= KEPALA SEKOLAH SECTIONS ================= -->
                @if($isKepsek && !$isSuperAdmin)
                    <div class="pt-3">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-purple-400">Kepala Sekolah</div>
                        <a href="{{ route('principal.approvals.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('principal.approvals.*') ? 'bg-slate-800 text-purple-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Approval Raport
                        </a>
                        <a href="{{ route('admin.deadlines.index') }}" class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.deadlines.*') ? 'bg-slate-800 text-amber-400 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Batas Waktu (Deadline)
                        </a>
                    </div>
                @endif

                <!-- PROFILE & GENERAL -->
                <div data-nav-group class="pt-3">
                    <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Akun & Layanan</div>
                    <a href="{{ route('profile.verify') }}" 
                       data-nav-item data-nav-title="verifikasi biodata profil guru nip nik gelar"
                       class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('profile.verify') ? 'bg-slate-800 text-blue-400 font-bold border-l-2 border-blue-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 text-blue-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Verifikasi Biodata
                    </a>
                    <a href="{{ route('profile.show') }}" 
                       data-nav-item data-nav-title="profil akun ubah password saya"
                       class="flex items-center px-3 py-2 rounded-xl transition {{ request()->routeIs('profile.show') ? 'bg-slate-800 text-blue-400 font-bold border-l-2 border-blue-500 pl-2.5' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                        Profil Saya
                    </a>
                    @if($isSuperAdmin || $isIt)
                    <a href="{{ route('docs.download') }}" target="_blank" 
                       data-nav-item data-nav-title="panduan fitur buku petunjuk sistem pdf"
                       class="flex items-center px-3 py-2 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-pink-300">
                        <svg class="w-4 h-4 mr-3 text-pink-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Panduan Fitur (PDF)
                    </a>
                    @endif
                </div>
            </nav>

            <!-- Bottom: Quick Theme Status & Logout -->
            <div class="p-3 border-t border-slate-800/90 dark:border-slate-800 bg-slate-900/90 dark:bg-slate-950 flex flex-col gap-2">
                <!-- Theme Palette Indicator Badge -->
                <div class="px-2 py-1 flex items-center justify-between text-[11px] text-slate-400">
                    <span class="flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 ring-1 ring-blue-300"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-pink-500 ring-1 ring-pink-300"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 ring-1 ring-amber-200"></span>
                        <span class="ml-1 text-[10px]">Tema Al-Athifa</span>
                    </span>
                    <button onclick="window.AlAthifaTheme.toggleMode()" 
                            class="p-1 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition"
                            title="Beralih Dark / Light Mode">
                        <span class="dark:hidden">☀️ Light</span>
                        <span class="hidden dark:inline">🌙 Dark</span>
                    </button>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 rounded-xl transition">
                        <svg class="w-4 h-4 mr-2.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar (Elevated & Tri-color Accent) -->
            <header class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/90 dark:border-slate-800 px-4 lg:px-6 py-3 flex items-center justify-between shadow-xs sticky top-0 z-30 transition-colors duration-200">
                <!-- Left Topbar: Mobile Toggle & Semester Info -->
                <div class="flex items-center space-x-3">
                    <!-- Mobile Hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="lg:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Active Term Badges (Biru & Pink/Kuning) -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center text-xs font-bold px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                            TP {{ $activeAY ? $activeAY->name : 'N/A' }}
                        </span>
                        <span class="inline-flex items-center text-xs font-bold px-3 py-1 rounded-full bg-pink-50 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800 shadow-2xs">
                            Sem. {{ $activeSem ? $activeSem->name : 'N/A' }}
                        </span>
                    </div>
                </div>

                <!-- Right Topbar: Deadline, Theme Customizer (Admin/IT), and Dark Mode Toggle -->
                <div class="flex items-center space-x-2.5">
                    @php
                        $activeDeadline = \App\Models\Deadline::where('is_active', true)->first();
                    @endphp
                    @if($activeDeadline)
                        <div class="hidden md:flex items-center space-x-2 text-xs px-3 py-1 rounded-xl {{ $activeDeadline->isOverdue() ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : ($activeDeadline->isWarning() ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800') }}">
                            <span class="font-bold text-[11px] uppercase tracking-wider">Deadline:</span>
                            <span class="font-bold">
                                {{ $activeDeadline->deadline_at->format('d M H:i') }} ({{ $activeDeadline->remaining_human }})
                            </span>
                        </div>
                    @endif

                    <!-- Theme Customizer Trigger Button (Khusus Super Admin & IT) -->
                    @if($isSuperAdmin || $isIt)
                    <button type="button"
                            onclick="openThemeCustomizerModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-50 via-pink-50 to-amber-50 dark:from-slate-800 dark:via-slate-800 dark:to-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:border-pink-300 dark:hover:border-pink-500 shadow-xs hover:shadow-md transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4 4 4 0 014-4h2v8H7zm0 0h10a4 4 0 004-4 4 4 0 00-4-4h-2v8h2m-6 0V3m0 0l-4 4m4-4l4 4"/></svg>
                        <span class="hidden sm:inline">Kustom Tema</span>
                    </button>
                    @endif

                    <!-- Dark / Light Mode Switcher (For All Users) -->
                    <button type="button" 
                            onclick="window.AlAthifaTheme.toggleMode()"
                            class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all cursor-pointer"
                            aria-label="Toggle Dark Mode"
                            title="Ganti Mode Gelap / Terang">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg class="w-4 h-4 hidden dark:block text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/></svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg class="w-4 h-4 block dark:hidden text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                    </button>
                </div>
            </header>

            <!-- Alerts / Flashes -->
            <main class="flex-1 p-4 lg:p-6 max-w-7xl w-full mx-auto">
                @if(session('success'))
                    <div class="mb-5 p-4 rounded-2xl bg-blue-50/90 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800/80 text-blue-900 dark:text-blue-200 flex items-start space-x-3 shadow-xs">
                        <div class="p-1 rounded-lg bg-blue-500 text-white flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="text-sm font-semibold">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-4 rounded-2xl bg-rose-50/90 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-900 dark:text-rose-200 flex items-start space-x-3 shadow-xs">
                        <div class="p-1 rounded-lg bg-rose-500 text-white flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div class="text-sm font-semibold">{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-5 p-4 rounded-2xl bg-amber-50/90 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 text-amber-900 dark:text-amber-200 flex items-start space-x-3 shadow-xs">
                        <div class="p-1 rounded-lg bg-amber-500 text-white flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="text-sm font-semibold">{{ session('warning') }}</div>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- THEME CUSTOMIZER MODAL (Khusus Super Admin & IT) -->
    <!-- ============================================================== -->
    @if($isSuperAdmin || $isIt)
    <div id="themeCustomizerModal" 
         class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 text-slate-800 dark:text-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 via-pink-500 to-amber-400 p-0.5 flex items-center justify-center shadow-md">
                        <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[14px] flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4 4 4 0 014-4h2v8H7zm0 0h10a4 4 0 004-4 4 4 0 00-4-4h-2v8h2m-6 0V3m0 0l-4 4m4-4l4 4"/></svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Studio Kustomisasi Tema</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Khusus Super Admin & IT</p>
                    </div>
                </div>
                <button onclick="closeThemeCustomizerModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="py-5 space-y-5">
                <!-- 1. Mode Tampilan (Light / Dark) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Mode Tampilan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" 
                                onclick="window.AlAthifaTheme.setMode('light')" 
                                data-theme-mode-btn="light"
                                class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold transition hover:border-blue-400">
                            <span>☀️ Mode Terang (Light)</span>
                        </button>
                        <button type="button" 
                                onclick="window.AlAthifaTheme.setMode('dark')" 
                                data-theme-mode-btn="dark"
                                class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold transition hover:border-blue-400">
                            <span>🌙 Mode Gelap (Dark)</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Pilihan Palet Warna Preset -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Preset Warna Siap Pakai</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <!-- Preset 1: Al-Athifah Klasik (Biru, Pink, Kuning) -->
                        <button type="button" 
                                onclick="applyPresetTheme('classic')"
                                class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-blue-500 text-left transition flex items-center justify-between group">
                            <div>
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Al-Athifah Klasik</div>
                                <div class="text-[10px] text-slate-400">Biru, Pink & Kuning</div>
                            </div>
                            <div class="flex items-center -space-x-1">
                                <span class="w-3.5 h-3.5 rounded-full bg-blue-600 ring-2 ring-white dark:ring-slate-900"></span>
                                <span class="w-3.5 h-3.5 rounded-full bg-pink-500 ring-2 ring-white dark:ring-slate-900"></span>
                                <span class="w-3.5 h-3.5 rounded-full bg-amber-400 ring-2 ring-white dark:ring-slate-900"></span>
                            </div>
                        </button>

                        <!-- Preset 2: Ocean Sapphire & Rose -->
                        <button type="button" 
                                onclick="applyPresetTheme('ocean')"
                                class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-sky-500 text-left transition flex items-center justify-between group">
                            <div>
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Ocean Sapphire</div>
                                <div class="text-[10px] text-slate-400">Sky Blue, Rose & Gold</div>
                            </div>
                            <div class="flex items-center -space-x-1">
                                <span class="w-3.5 h-3.5 rounded-full bg-sky-600 ring-2 ring-white dark:ring-slate-900"></span>
                                <span class="w-3.5 h-3.5 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-900"></span>
                                <span class="w-3.5 h-3.5 rounded-full bg-yellow-400 ring-2 ring-white dark:ring-slate-900"></span>
                            </div>
                        </button>

                        <!-- Preset 3: Royal Indigo & Fuchsia -->
                        <button type="button" 
                                onclick="applyPresetTheme('indigo')"
                                class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-indigo-500 text-left transition flex items-center justify-between group">
                            <div>
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Royal Indigo</div>
                                <div class="text-[10px] text-slate-400">Indigo, Fuchsia & Amber</div>
                            </div>
                            <div class="flex items-center -space-x-1">
                                <span class="w-3.5 h-3.5 rounded-full bg-indigo-600 ring-2 ring-white dark:ring-slate-900"></span>
                                <span class="w-3.5 h-3.5 rounded-full bg-fuchsia-500 ring-2 ring-white dark:ring-slate-900"></span>
                                <span class="w-3.5 h-3.5 rounded-full bg-amber-400 ring-2 ring-white dark:ring-slate-900"></span>
                            </div>
                        </button>

                        <!-- Preset 4: Cyber Twilight -->
                        <button type="button" 
                                onclick="applyPresetTheme('cyber')"
                                class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-blue-600 text-left transition flex items-center justify-between group">
                            <div>
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Cyber Twilight</div>
                                <div class="text-[10px] text-slate-400">Cobalt, Neon Pink & Lime</div>
                            </div>
                            <div class="flex items-center -space-x-1">
                                <span class="w-3.5 h-3.5 rounded-full bg-blue-700 ring-2 ring-white dark:ring-slate-900"></span>
                                <span class="w-3.5 h-3.5 rounded-full bg-pink-600 ring-2 ring-white dark:ring-slate-900"></span>
                                <span class="w-3.5 h-3.5 rounded-full bg-yellow-300 ring-2 ring-white dark:ring-slate-900"></span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 3. Custom Color Pickers Manual -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Kustomisasi Warna Bebas</label>
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Primary Blue -->
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1.5">Warna Utama (Biru)</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" id="customPrimaryColor" value="#2563eb" onchange="previewCustomColors()" class="w-8 h-8 rounded-lg border-0 cursor-pointer p-0 bg-transparent">
                                <span id="customPrimaryHex" class="text-[11px] font-mono font-bold text-slate-500">#2563eb</span>
                            </div>
                        </div>

                        <!-- Accent Pink -->
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1.5">Warna Aksen (Pink)</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" id="customPinkColor" value="#ec4899" onchange="previewCustomColors()" class="w-8 h-8 rounded-lg border-0 cursor-pointer p-0 bg-transparent">
                                <span id="customPinkHex" class="text-[11px] font-mono font-bold text-slate-500">#ec4899</span>
                            </div>
                        </div>

                        <!-- Warning Yellow -->
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1.5">Warna Sorotan (Kuning)</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" id="customYellowColor" value="#f59e0b" onchange="previewCustomColors()" class="w-8 h-8 rounded-lg border-0 cursor-pointer p-0 bg-transparent">
                                <span id="customYellowHex" class="text-[11px] font-mono font-bold text-slate-500">#f59e0b</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Preview Card -->
                <div class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Pratinjau Elemen UI</div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" class="px-3 py-1.5 rounded-xl bg-theme-primary text-white text-xs font-bold shadow-xs">
                            Tombol Utama
                        </button>
                        <span class="px-2.5 py-1 rounded-full bg-theme-pink text-white text-[10px] font-bold">
                            Badge Aksen Pink
                        </span>
                        <span class="px-2.5 py-1 rounded-full bg-theme-yellow text-slate-900 text-[10px] font-bold">
                            Peringatan Kuning
                        </span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <button type="button" 
                        onclick="resetToDefaultTheme()"
                        class="text-xs font-semibold text-rose-500 hover:text-rose-600 dark:hover:text-rose-400">
                    Reset ke Standar
                </button>
                <div class="flex items-center space-x-2">
                    <button type="button" 
                            onclick="closeThemeCustomizerModal()" 
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                        Batal
                    </button>
                    <button type="button" 
                            onclick="saveCustomThemeColors()" 
                            class="px-5 py-2 rounded-xl text-xs font-bold bg-theme-primary hover:bg-theme-primary-hover text-white shadow-md shadow-blue-500/20 transition">
                        Simpan Tema
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                if (window.toastSuccess) window.toastSuccess(@json(session('success')));
            @endif
            @if(session('error'))
                if (window.toastError) window.toastError(@json(session('error')));
            @endif
            @if(session('warning'))
                if (window.toastWarning) window.toastWarning(@json(session('warning')));
            @endif

            // Keyboard shortcut Ctrl+K to focus sidebar search
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const input = document.getElementById('sidebarMenuSearch');
                    if (input) {
                        input.focus();
                        input.select();
                    }
                }
            });
        });

        // Sidebar Menu Live Filter Function (For Super Admin)
        function filterSidebarMenus(query) {
            const q = query.trim().toLowerCase();
            const items = document.querySelectorAll('#sidebarNavContainer [data-nav-item]');
            const groups = document.querySelectorAll('#sidebarNavContainer [data-nav-group]');

            if (!q) {
                items.forEach(el => el.classList.remove('hidden'));
                groups.forEach(el => el.classList.remove('hidden'));
                return;
            }

            items.forEach(el => {
                const title = el.getAttribute('data-nav-title') || el.innerText.toLowerCase();
                if (title.toLowerCase().includes(q)) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });

            // Hide empty group headers
            groups.forEach(group => {
                const visibleInGroup = group.querySelectorAll('[data-nav-item]:not(.hidden)');
                if (visibleInGroup.length === 0) {
                    group.classList.add('hidden');
                } else {
                    group.classList.remove('hidden');
                }
            });
        }

        // Theme Customizer Modal Handlers
        function openThemeCustomizerModal() {
            const modal = document.getElementById('themeCustomizerModal');
            if (!modal) return;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Populate current color inputs
            if (window.AlAthifaTheme) {
                const current = window.AlAthifaTheme.getCustomColors();
                const pri = document.getElementById('customPrimaryColor');
                const pnk = document.getElementById('customPinkColor');
                const yel = document.getElementById('customYellowColor');
                if (pri && current.primary) pri.value = current.primary;
                if (pnk && current.pink) pnk.value = current.pink;
                if (yel && current.yellow) yel.value = current.yellow;

                updateHexLabels();
            }
        }

        function closeThemeCustomizerModal() {
            const modal = document.getElementById('themeCustomizerModal');
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function updateHexLabels() {
            const pri = document.getElementById('customPrimaryColor')?.value;
            const pnk = document.getElementById('customPinkColor')?.value;
            const yel = document.getElementById('customYellowColor')?.value;
            if (pri) document.getElementById('customPrimaryHex').innerText = pri;
            if (pnk) document.getElementById('customPinkHex').innerText = pnk;
            if (yel) document.getElementById('customYellowHex').innerText = yel;
        }

        function previewCustomColors() {
            updateHexLabels();
            const pri = document.getElementById('customPrimaryColor').value;
            const pnk = document.getElementById('customPinkColor').value;
            const yel = document.getElementById('customYellowColor').value;

            window.AlAthifaTheme.applyCustomColors({
                primary: pri,
                pink: pnk,
                yellow: yel
            });
        }

        function applyPresetTheme(key) {
            window.AlAthifaTheme.applyPreset(key);
            const current = window.AlAthifaTheme.getCustomColors();
            document.getElementById('customPrimaryColor').value = current.primary;
            document.getElementById('customPinkColor').value = current.pink;
            document.getElementById('customYellowColor').value = current.yellow;
            updateHexLabels();
        }

        function saveCustomThemeColors() {
            previewCustomColors();
            closeThemeCustomizerModal();
            if (window.toastSuccess) {
                window.toastSuccess('Tema kustom Anda berhasil disimpan!');
            }
        }

        function resetToDefaultTheme() {
            window.AlAthifaTheme.reset();
            closeThemeCustomizerModal();
        }
    </script>
</body>
</html>
