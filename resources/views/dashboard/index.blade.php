@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header & Greeting Banner with Tri-Color Palette (Biru, Pink, Kuning) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 lg:p-8 shadow-xl border border-blue-800/40">
        <!-- Decorative Glow Orbs -->
        <div class="absolute -top-12 -right-12 w-64 h-64 rounded-full bg-pink-500/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 right-48 w-64 h-64 rounded-full bg-amber-400/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -top-12 left-48 w-48 h-48 rounded-full bg-blue-500/20 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-2.5">
                    <span class="inline-flex items-center space-x-1.5 text-xs font-bold px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 backdrop-blur-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-pink-400 animate-ping"></span>
                        <span>Peran: {{ $user->getRoleNames()->first() }}</span>
                    </span>
                    @if($user->teacherProfile?->level)
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-pink-500/20 text-pink-300 border border-pink-400/30">
                        Jenjang {{ $user->teacherProfile->level }}
                    </span>
                    @endif
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight text-white">
                    Selamat Datang, <span class="bg-gradient-to-r from-white via-blue-100 to-amber-200 bg-clip-text text-transparent">{{ $user->name }}</span>
                </h1>
                <p class="text-xs lg:text-sm text-blue-200/80 mt-1 max-w-xl">
                    Sistem Manajemen & E-Raport Perkembangan Tahfizh Al-Athifa SD & SMP Islam Riau Global Terpadu.
                </p>
            </div>

            <!-- Active Term Pill & Quick Admin Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="bg-white/10 dark:bg-slate-800/60 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/15 text-left sm:text-right shadow-inner">
                    <div class="text-[10px] uppercase font-bold tracking-wider text-blue-200">Tahun Pelajaran & Semester</div>
                    <div class="text-sm font-extrabold text-amber-300">
                        {{ $activeAY ? $activeAY->name : '-' }} • Sem. {{ $activeSem ? $activeSem->name : '-' }}
                    </div>
                </div>

                @if($user->isSuperAdmin() || $user->isIt())
                <button type="button" 
                        onclick="openThemeCustomizerModal()"
                        class="px-3.5 py-3 rounded-2xl bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-bold text-xs shadow-lg shadow-pink-500/25 transition-all flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4 4 4 0 014-4h2v8H7zm0 0h10a4 4 0 004-4 4 4 0 00-4-4h-2v8h2m-6 0V3m0 0l-4 4m4-4l4 4"/></svg>
                    <span>Kustom Tema</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Batas Waktu (Deadline) Alert Card (Kuning Amber) -->
    @if($deadline)
    <div class="p-4 rounded-2xl border transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs
        {{ $deadline->isOverdue() 
            ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-900/60 text-rose-900 dark:text-rose-200' 
            : ($deadline->isWarning() 
                ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200' 
                : 'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200') }}">
        
        <div class="flex items-center space-x-3.5">
            <div class="p-2.5 rounded-xl shadow-xs {{ $deadline->isOverdue() ? 'bg-rose-500 text-white' : ($deadline->isWarning() ? 'bg-amber-500 text-slate-900' : 'bg-blue-600 text-white') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block opacity-80">Batas Waktu Input Raport Tahfizh</span>
                <span class="text-sm font-bold">{{ $deadline->title }}: <strong>{{ $deadline->deadline_at->format('d F Y - H:i') }} WIB</strong> ({{ $deadline->remaining_human }})</span>
            </div>
        </div>
        <span class="text-xs font-extrabold px-3 py-1.5 rounded-full uppercase self-start sm:self-center
            {{ $deadline->isOverdue() ? 'bg-rose-600 text-white' : ($deadline->isWarning() ? 'bg-amber-500 text-slate-950 shadow-xs' : 'bg-blue-600 text-white') }}">
            {{ $deadline->status_label }}
        </span>
    </div>
    @endif

    <!-- ============================================================== -->
    <!-- SUPER ADMIN & IT: EXECUTIVE STREAMLINED DASHBOARD -->
    <!-- ============================================================== -->
    @if($user->isSuperAdmin() || $user->isIt())
    <div class="space-y-6">

        <!-- Quick Actions Toolbar (Jalan Pintas Administrasi) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between flex-wrap gap-3 transition-colors">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-theme-primary animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pusat Aksi Cepat & Pengaturan:</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.students.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-theme-primary/10 hover:bg-theme-primary/20 text-theme-primary dark:text-blue-300 border border-theme-primary/20 transition flex items-center gap-1">
                    <span>+ Siswa</span>
                </a>
                <a href="{{ route('admin.teachers.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-theme-pink/10 hover:bg-theme-pink/20 text-theme-pink border border-theme-pink/30 transition flex items-center gap-1">
                    <span>+ Guru & Akun</span>
                </a>
                <a href="{{ route('templates.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-theme-pink/10 hover:bg-theme-pink/20 text-theme-pink border border-theme-pink/30 transition flex items-center gap-1">
                    <span>+ Template</span>
                </a>
                <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-theme-yellow/20 hover:bg-theme-yellow/30 text-amber-800 dark:text-amber-300 border border-theme-yellow/30 transition flex items-center gap-1">
                    <span>🖨️ Antrean Cetak</span>
                </a>
                <a href="{{ route('admin.deadlines.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-50 dark:bg-purple-950/60 hover:bg-purple-100 dark:hover:bg-purple-900/80 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 transition flex items-center gap-1">
                    <span>⏰ Batas Waktu</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 transition flex items-center gap-1">
                    <span>⚙️ Format Raport</span>
                </a>
                <a href="{{ route('admin.academics.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 transition flex items-center gap-1">
                    <span>📅 Tahun & Smt</span>
                </a>
                <a href="{{ route('admin.history.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-cyan-50 dark:bg-cyan-950/60 hover:bg-cyan-100 dark:hover:bg-cyan-900/80 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800 transition flex items-center gap-1">
                    <span>📜 Riwayat Data</span>
                </a>
            </div>
        </div>

        <!-- 4 Primary Metric Highlights (Biru, Pink, Kuning, Putih) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Siswa (Biru) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-blue-100 dark:border-slate-800 shadow-xs relative overflow-hidden group hover:border-blue-300 dark:hover:border-blue-700 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-1">Total Siswa Aktif</span>
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalStudents }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="font-semibold text-blue-600 dark:text-blue-400">SD & SMP</span> Islam Riau Global Terpadu
                </div>
            </div>

            <!-- Total Guru (Pink) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-pink-100 dark:border-slate-800 shadow-xs relative overflow-hidden group hover:border-pink-300 dark:hover:border-pink-700 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-pink-600 dark:text-pink-400 block mb-1">Guru & Pengajar</span>
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalTeachers }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="font-semibold text-pink-600 dark:text-pink-400">{{ count($teachersPending) }} Guru</span> masih dalam status draft
                </div>
            </div>

            <!-- Antrian Cetak (Kuning) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-amber-100 dark:border-slate-800 shadow-xs relative overflow-hidden group hover:border-amber-300 dark:hover:border-amber-700 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block mb-1">Antrean Percetakan</span>
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $statusCounts['MENUNGGU_CETAK'] + $statusCounts['PROSES_CETAK'] }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $statusCounts['MENUNGGU_CETAK'] }}</span> antre, <span class="font-semibold">{{ $statusCounts['PROSES_CETAK'] }}</span> dicetak
                </div>
            </div>

            <!-- Selesai Dicetak (Hijau / Putih) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-emerald-100 dark:border-slate-800 shadow-xs relative overflow-hidden group hover:border-emerald-300 dark:hover:border-emerald-700 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mb-1">Selesai & Locked</span>
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $statusCounts['SELESAI'] + $statusCounts['LOCKED'] }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $statusCounts['APPROVED'] }}</span> menunggu antrean IT
                </div>
            </div>
        </div>

        <!-- Status Pipeline Bar (Alur Pengesahan Raport) -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Pipeline Status Penilaian & Percetakan</h3>
                <span class="text-[11px] text-slate-400">Semester Genap 2025/2026</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block font-medium">1. Draft (Guru)</span>
                    <span class="text-lg font-bold text-slate-700 dark:text-slate-300">{{ $statusCounts['DRAFT'] }}</span>
                </div>
                <div class="p-3 rounded-xl bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/60">
                    <span class="text-[11px] text-blue-600 dark:text-blue-400 block font-medium">2. Submitted</span>
                    <span class="text-lg font-bold text-blue-700 dark:text-blue-300">{{ $statusCounts['SUBMITTED'] }}</span>
                </div>
                <div class="p-3 rounded-xl bg-purple-50/70 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-900/60">
                    <span class="text-[11px] text-purple-600 dark:text-purple-400 block font-medium">3. Reviewed (Wali)</span>
                    <span class="text-lg font-bold text-purple-700 dark:text-purple-300">{{ $statusCounts['REVIEWED'] }}</span>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60">
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 block font-medium">4. Approved (Kepsek)</span>
                    <span class="text-lg font-bold text-emerald-700 dark:text-emerald-300">{{ $statusCounts['APPROVED'] }}</span>
                </div>
                <div class="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60">
                    <span class="text-[11px] text-amber-600 dark:text-amber-400 block font-medium">5. Antrian Cetak</span>
                    <span class="text-lg font-bold text-amber-700 dark:text-amber-300">{{ $statusCounts['MENUNGGU_CETAK'] }}</span>
                </div>
                <div class="p-3 rounded-xl bg-pink-50/70 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-900/60">
                    <span class="text-[11px] text-pink-600 dark:text-pink-400 block font-medium">6. Selesai Cetak</span>
                    <span class="text-lg font-bold text-pink-700 dark:text-pink-300">{{ $statusCounts['SELESAI'] }}</span>
                </div>
            </div>
        </div>

        <!-- Progress Input Nilai SD & SMP (Biru & Pink) -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Progress Input Nilai Raport Tahfizh</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Perbandingan kelengkapan nilai Tahfizh & Tahsin siswa semester ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <!-- SD Progress (Pink / Rose) -->
                <div class="p-4 rounded-2xl bg-pink-50/40 dark:bg-pink-950/20 border border-pink-100 dark:border-pink-900/40">
                    <div class="flex justify-between items-center text-xs font-bold mb-2">
                        <span class="text-slate-800 dark:text-slate-200">SD Islam Riau Global Terpadu</span>
                        <span class="text-pink-600 dark:text-pink-400 font-extrabold">{{ $sdProgress }}% Selesai</span>
                    </div>
                    <div class="w-full bg-slate-200/80 dark:bg-slate-800 rounded-full h-3.5 overflow-hidden p-0.5">
                        <div class="bg-gradient-to-r from-pink-500 to-rose-500 h-full rounded-full transition-all duration-700" style="width: {{ $sdProgress }}%"></div>
                    </div>
                </div>

                <!-- SMP Progress (Biru / Royal) -->
                <div class="p-4 rounded-2xl bg-blue-50/40 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40">
                    <div class="flex justify-between items-center text-xs font-bold mb-2">
                        <span class="text-slate-800 dark:text-slate-200">SMP Islam Riau Global Terpadu</span>
                        <span class="text-blue-600 dark:text-blue-400 font-extrabold">{{ $smpProgress }}% Selesai</span>
                    </div>
                    <div class="w-full bg-slate-200/80 dark:bg-slate-800 rounded-full h-3.5 overflow-hidden p-0.5">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full rounded-full transition-all duration-700" style="width: {{ $smpProgress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monitoring Section (Side by Side) -->
        <div class="grid grid-cols-1 {{ $user->isSuperAdmin() ? 'lg:grid-cols-2' : '' }} gap-6">
            <!-- Guru Belum Submit (Kuning / Amber Accent) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Guru Yang Belum Submit</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Raport masih berstatus Draft pada semester aktif</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            {{ count($teachersPending) }} Guru
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800 mt-2">
                        @forelse($teachersPending as $item)
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold flex items-center justify-center text-[11px]">
                                        {{ substr($item['teacher']->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $item['teacher']->name }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $item['teacher']->email }}</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-bold text-[11px] border border-amber-200 dark:border-amber-800">
                                    {{ $item['pending_count'] }} siswa belum submit
                                </span>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-slate-400">
                                🎉 Luar biasa! Semua guru telah mengirimkan nilai raport.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Audit Logs (Hanya Super Admin) -->
            @if($user->isSuperAdmin())
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Aktivitas Sistem Terbaru</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Audit log rekaman keamanan & operasional</p>
                        </div>
                        <a href="{{ route('admin.audit.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                            Lihat Semua →
                        </a>
                    </div>

                    <div class="space-y-2 mt-3 text-xs">
                        @forelse($recentLogs as $log)
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $log->user?->name ?? 'Sistem' }}</span>:
                                    <span class="text-slate-600 dark:text-slate-400">{{ $log->description }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 whitespace-nowrap ml-2 mt-0.5">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-slate-400">
                                Belum ada catatan aktivitas terbaru.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ============================================================== -->
    <!-- GURU TAHFIZH VIEW -->
    <!-- ============================================================== -->
    @if($user->isGuru())
    <div class="space-y-6">
        <!-- Guru Stats (Biru, Pink, Kuning) -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Total Siswa Bimbingan</span>
                <span class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $guruStats['total'] }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-pink-200 dark:border-pink-900/60 bg-pink-50/20 dark:bg-pink-950/20 shadow-xs">
                <span class="text-xs text-pink-600 dark:text-pink-400 font-semibold block">Sudah Selesai (100%)</span>
                <span class="text-2xl font-extrabold text-pink-700 dark:text-pink-300">{{ $guruStats['completed'] }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-200 dark:border-amber-900/60 bg-amber-50/20 dark:bg-amber-950/20 shadow-xs">
                <span class="text-xs text-amber-600 dark:text-amber-400 font-semibold block">Belum Selesai</span>
                <span class="text-2xl font-extrabold text-amber-700 dark:text-amber-300">{{ $guruStats['incomplete'] }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Draft</span>
                <span class="text-2xl font-extrabold text-slate-600 dark:text-slate-300">{{ $guruStats['draft'] }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-blue-200 dark:border-blue-900/60 bg-blue-50/20 dark:bg-blue-950/20 shadow-xs">
                <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold block">Submitted</span>
                <span class="text-2xl font-extrabold text-blue-700 dark:text-blue-300">{{ $guruStats['submitted'] }}</span>
            </div>
        </div>

        <!-- Student Cards List -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Daftar Siswa Bimbingan Anda</h3>
                <a href="{{ route('teacher.students.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    Kelola Semua Siswa →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($myAssignments as $assign)
                    @php
                        $st = $assign->student;
                        $assessment = $st->assessments->first();
                        $pct = $assessment ? $assessment->progress_percentage : 0;
                        $status = $assessment ? $assessment->status : 'DRAFT';
                    @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900 dark:text-white tracking-tight">{{ $st->name }}</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">NISN: {{ $st->nisn ?? '-' }} | Jenjang: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $st->level }}</span></p>
                                </div>
                                <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase
                                    @if($status === 'LOCKED') bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300
                                    @elseif($status === 'APPROVED') bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300
                                    @elseif($status === 'REVIEWED') bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300
                                    @elseif($status === 'SUBMITTED') bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300
                                    @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 @endif">
                                    {{ $status }}
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Progress Nilai</span>
                                    <span class="font-bold {{ $pct === 100 ? 'text-pink-600 dark:text-pink-400' : 'text-blue-600 dark:text-blue-400' }}">{{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $pct === 100 ? 'bg-pink-500' : 'bg-blue-600' }}" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1">
                                    {{ $assessment ? $assessment->filled_items_count : 0 }} dinilai dari {{ $assessment ? $assessment->total_items_count : 0 }} indikator
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                            @if($assessment)
                                <a href="{{ route('teacher.assessments.edit', $assessment->id) }}"
                                   class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition">
                                    Input Nilai
                                </a>
                                <div class="flex items-center space-x-1.5">
                                    <a href="{{ route('reports.preview', $assessment->id) }}" target="_blank"
                                       class="px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                                        Preview
                                    </a>
                                    <a href="{{ route('reports.pdf', $assessment->id) }}"
                                       class="px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                                        Cetak
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('teacher.assessments.initialize', ['student' => $st->id]) }}"
                                   class="w-full py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs text-center transition shadow-xs">
                                    Pilih Template / Mulai Penilaian
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 text-center">
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada siswa yang ditugaskan kepada Anda pada semester ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Hubungi Administrator untuk melakukan penugasan bimbingan siswa.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================================== -->
    <!-- WALI KELAS VIEW -->
    <!-- ============================================================== -->
    @if($user->isWaliKelas() && !$user->isSuperAdmin() && !$user->isIt())
    <div class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Pemberitahuan Review Raport</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Sebagai Wali Kelas, Anda bertugas mereview raport Tahfizh siswa yang telah disubmit oleh Guru Tahfizh sebelum diajukan ke Kepala Sekolah.</p>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800">
                <div class="flex items-center space-x-3.5">
                    <span class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 font-extrabold flex items-center justify-center text-sm shadow-xs">
                        {{ $homeroomPendingCount }}
                    </span>
                    <span class="text-sm font-bold text-amber-900 dark:text-amber-200">Raport Menunggu Review Anda</span>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('homeroom.reviews.index') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition shadow-xs">
                        Buka Menu Review →
                    </a>
                    <a href="{{ route('reports.homeroom_bulk_pdf') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition flex items-center space-x-1.5 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Download PDF 1 Kelas</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Monitoring Progress Siswa Kelas Saya -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Progress Siswa Kelas Saya</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pantau kemajuan input nilai dan status raport dari seluruh siswa di kelas Anda</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ $homeroomStudents->count() }} Siswa Terdaftar
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 uppercase font-semibold">
                            <th class="p-3 w-10 text-center">No</th>
                            <th class="p-3">Nama Siswa</th>
                            <th class="p-3">NISN / Jenjang</th>
                            <th class="p-3">Guru Pembimbing</th>
                            <th class="p-3">Progress Nilai</th>
                            <th class="p-3">Status Raport</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($homeroomStudents as $idx => $st)
                            @php
                                $assessment = $st->assessments->first();
                                $pct = $assessment ? $assessment->progress_percentage : 0;
                                $status = $assessment ? $assessment->status : 'DRAFT';
                                $teacherName = $st->assignments->first()?->teacher?->name ?? 'Belum Ditentukan';
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                <td class="p-3 text-center text-slate-400 font-medium">{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold text-slate-800 dark:text-slate-200">{{ $st->name }}</td>
                                <td class="p-3 text-slate-600 dark:text-slate-400">{{ $st->nisn ?? '-' }} ({{ $st->level }})</td>
                                <td class="p-3 text-slate-700 dark:text-slate-300">{{ $teacherName }}</td>
                                <td class="p-3">
                                    <div class="w-36">
                                        <div class="flex justify-between text-[11px] mb-1">
                                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $pct }}%</span>
                                            <span class="text-slate-400">{{ $assessment ? $assessment->filled_items_count : 0 }}/{{ $assessment ? $assessment->total_items_count : 0 }}</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full {{ $pct === 100 ? 'bg-pink-500' : 'bg-blue-600' }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        @if($status === 'SELESAI') bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300
                                        @elseif($status === 'PROSES_CETAK') bg-cyan-100 dark:bg-cyan-950/60 text-cyan-800 dark:text-cyan-300
                                        @elseif($status === 'MENUNGGU_CETAK') bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300
                                        @elseif($status === 'APPROVED') bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300
                                        @elseif($status === 'REVIEWED') bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300
                                        @elseif($status === 'SUBMITTED') bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300
                                        @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 @endif">
                                        {{ $assessment?->status_label ?? 'Draft' }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    @if($assessment)
                                        <a href="{{ route('reports.preview', $assessment->id) }}" target="_blank"
                                           class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition">
                                            Preview
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">Belum dinilai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-400">Tidak ada siswa yang ditugaskan di kelas Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================================== -->
    <!-- KEPALA SEKOLAH VIEW (Scoped to SD or SMP) -->
    <!-- ============================================================== -->
    @if($user->isKepalaSekolah() && !$user->isSuperAdmin() && !$user->isIt())
    <div class="space-y-6">
        <!-- Banner Scoped Jenjang -->
        <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/60 rounded-3xl p-5 flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-base shadow-xs">
                    {{ $userLevel ?? 'SD' }}
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-blue-950 dark:text-blue-100">Kepala Sekolah Jenjang {{ $userLevel ?? 'SD' }}</h3>
                    <p class="text-xs text-blue-700 dark:text-blue-300">Data dan statistik yang ditampilkan di bawah ini khusus untuk jenjang {{ $userLevel ?? 'SD' }}.</p>
                </div>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-200 dark:bg-blue-900 text-blue-900 dark:text-blue-200">
                Jenjang {{ $userLevel ?? 'SD' }}
            </span>
        </div>

        <!-- Scoped Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Total Siswa {{ $userLevel ?? 'SD' }}</span>
                <span class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $totalStudents }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-blue-200 dark:border-blue-900/60 bg-blue-50/20 dark:bg-blue-950/20 shadow-xs">
                <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold block">Submitted</span>
                <span class="text-2xl font-extrabold text-blue-700 dark:text-blue-300">{{ $statusCounts['SUBMITTED'] }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-200 dark:border-amber-900/60 bg-amber-50/20 dark:bg-amber-950/20 shadow-xs">
                <span class="text-xs text-amber-600 dark:text-amber-400 font-semibold block">Reviewed (Siap Approval)</span>
                <span class="text-2xl font-extrabold text-amber-700 dark:text-amber-300">{{ $principalPendingCount }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/20 dark:bg-emerald-950/20 shadow-xs">
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold block">Approved</span>
                <span class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-300">{{ $statusCounts['APPROVED'] }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-pink-200 dark:border-pink-900/60 bg-pink-50/20 dark:bg-pink-950/20 shadow-xs">
                <span class="text-xs text-pink-600 dark:text-pink-400 font-semibold block">Selesai / Locked</span>
                <span class="text-2xl font-extrabold text-pink-700 dark:text-pink-300">{{ $statusCounts['SELESAI'] + $statusCounts['LOCKED'] }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Approval Raport Tahfizh</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Sebagai Kepala Sekolah {{ $userLevel ?? 'SD' }}, Anda melakukan persetujuan akhir (Approval) terhadap raport yang telah selesai direview oleh Wali Kelas.</p>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center space-x-3.5">
                    <span class="w-9 h-9 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm shadow-xs">
                        {{ $principalPendingCount }}
                    </span>
                    <span class="text-sm font-bold text-blue-950 dark:text-blue-200">Raport Jenjang {{ $userLevel ?? 'SD' }} Siap untuk Disetujui (Approved)</span>
                </div>
                <a href="{{ route('principal.approvals.index') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs">
                    Buka Menu Approval →
                </a>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
