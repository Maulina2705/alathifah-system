@extends('layouts.app', ['title' => 'Tahun Pelajaran & Semester'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Card & Form Tambah -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-theme-primary/10 text-theme-primary dark:text-blue-300 font-bold text-[11px] mb-2 border border-theme-primary/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Kalender Akademik
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Tahun Pelajaran & Semester</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola kalender akademik dan tentukan periode semester aktif untuk penginputan raport santri.</p>
        </div>

        <form action="{{ route('admin.academics.year.store') }}" method="POST" class="flex items-center space-x-2">
            @csrf
            <input type="text" name="name" required placeholder="Contoh: 2026/2027"
                   class="px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-theme-primary">
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md transition whitespace-nowrap">
                + Tambah Tahun
            </button>
        </form>
    </div>

    <!-- Academic Years Cards -->
    <div class="space-y-4">
        @foreach($academicYears as $ay)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border {{ $ay->is_active ? 'border-theme-primary/50 dark:border-blue-500 ring-2 ring-theme-primary/20' : 'border-slate-200/80 dark:border-slate-800' }} p-6 shadow-xs transition-colors">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-3.5">
                        <span class="w-11 h-11 rounded-2xl {{ $ay->is_active ? 'bg-theme-primary text-white shadow-md shadow-theme-primary/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }} font-extrabold text-sm flex items-center justify-center">
                            {{ substr($ay->name, 2, 2) }}
                        </span>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Tahun Pelajaran {{ $ay->name }}</h3>
                            <span class="text-xs font-bold {{ $ay->is_active ? 'text-theme-primary dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">
                                {{ $ay->is_active ? '● Periode Tahun Berjalan (Aktif)' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>

                    @if(!$ay->is_active)
                        <form action="{{ route('admin.academics.year.active', $ay->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition">
                                Set Sebagai Tahun Aktif
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Semesters in this Year -->
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($ay->semesters as $sem)
                        <div class="p-4 rounded-xl border {{ $sem->is_active ? 'border-theme-primary/40 bg-theme-primary/5 dark:bg-theme-primary/10' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40' }} flex items-center justify-between transition-colors">
                            <div>
                                <span class="font-bold text-xs text-slate-800 dark:text-white block">Semester {{ $sem->name }}</span>
                                <span class="text-[11px] font-semibold {{ $sem->is_active ? 'text-theme-primary dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">
                                    {{ $sem->is_active ? '✓ Semester Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            @if(!$sem->is_active)
                                <form action="{{ route('admin.academics.semester.active', $sem->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-xs transition">
                                        Aktifkan
                                    </button>
                                </form>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-extrabold text-[10px] uppercase">
                                    Sedang Berjalan
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
