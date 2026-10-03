@extends('layouts.app', ['title' => 'Siswa Bimbingan Saya'])

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Siswa Bimbingan Tahfizh Saya</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Daftar siswa yang berada dalam bimbingan Tahfizh Anda untuk semester ini.</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('teacher.students.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="ay" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ $selectedAY == $ay->id ? 'selected' : '' }}>TP {{ $ay->name }}</option>
                @endforeach
            </select>

            <select name="sem" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ $selectedSem == $sem->id ? 'selected' : '' }}>Semester {{ $sem->name }}</option>
                @endforeach
            </select>

            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / NISN..."
                   class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">

            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs">
                Filter
            </button>
        </form>
    </div>

    <!-- Student Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($assignments as $assign)
            @php
                $st = $assign->student;
                $assessment = $st->assessments->first();
                $pct = $assessment ? $assessment->progress_percentage : 0;
                $status = $assessment ? $assessment->status : 'DRAFT';
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Header Card -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider mb-1.5
                                {{ $st->level === 'SD' ? 'bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' }}">
                                {{ $st->level }}
                            </span>
                            <h3 class="font-extrabold text-base text-slate-900 dark:text-white tracking-tight leading-snug">{{ $st->name }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">NISN: {{ $st->nisn ?? '-' }} | NIS: {{ $st->nis ?? '-' }}</p>
                        </div>
                        <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wide
                            @if($status === 'LOCKED') bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800
                            @elseif($status === 'APPROVED') bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800
                            @elseif($status === 'REVIEWED') bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800
                            @elseif($status === 'SUBMITTED') bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800
                            @else bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 @endif">
                            {{ $status }}
                        </span>
                    </div>

                    <!-- Homeroom info -->
                    <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                        Wali Kelas: <strong class="text-slate-700 dark:text-slate-200">{{ $assign->homeroom?->teacherProfile?->formatted_name_with_degree ?? ($assign->homeroom?->name ?? 'Belum Ditentukan') }}</strong>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="text-slate-500 dark:text-slate-400 font-semibold">Progress Nilai</span>
                            <span class="font-bold {{ $pct === 100 ? 'text-pink-600 dark:text-pink-400' : 'text-blue-600 dark:text-blue-400' }}">{{ $pct }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pct === 100 ? 'bg-pink-500' : 'bg-blue-600' }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-1.5 flex justify-between">
                            <span>{{ $assessment ? $assessment->filled_items_count : 0 }} dinilai dari {{ $assessment ? $assessment->total_items_count : 0 }}</span>
                            @if($assessment && $assessment->unfilled_items_count > 0)
                                <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $assessment->unfilled_items_count }} belum diisi</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Action Buttons -->
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    @if($assessment)
                        <a href="{{ route('teacher.assessments.edit', $assessment->id) }}"
                           class="flex-1 text-center py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition">
                            Input Nilai
                        </a>
                        <a href="{{ route('reports.preview', $assessment->id) }}" target="_blank"
                           class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition" title="Preview Raport">
                            Preview
                        </a>
                        <a href="{{ route('reports.pdf', $assessment->id) }}"
                           class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition" title="Cetak Raport">
                            Cetak
                        </a>
                    @else
                        <a href="{{ route('teacher.assessments.initialize', ['student' => $st->id]) }}"
                           class="w-full text-center py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition">
                            Pilih Template / Mulai Penilaian
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 text-center">
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Tidak ada data siswa yang cocok dengan filter saat ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($assignments, 'hasPages') && $assignments->hasPages())
        <div class="mt-6">
            {{ $assignments->links() }}
        </div>
    @endif
</div>
@endsection
