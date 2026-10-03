@extends('layouts.app', ['title' => 'Riwayat Data & Workflow'])

@section('content')
<div class="space-y-6">

    <!-- Header & Summary -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs relative overflow-hidden transition-colors">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-gradient-to-br from-theme-primary/10 to-theme-pink/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-theme-primary/10 text-theme-primary dark:text-blue-300 font-bold text-[11px] mb-2 border border-theme-primary/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0zM3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                    Pusat Riwayat & Audit Data
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Data History & Rekam Jejak Raport</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                    Lacak rekam jejak guru pembimbing, wali kelas, serta riwayat perubahan status raport santri dari masa ke masa lintas semester.
                </p>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1.5 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 self-start">
                <a href="{{ route('admin.history.index', array_merge(request()->query(), ['tab' => 'assignments'])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $tab === 'assignments' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Riwayat Pembimbing
                </a>
                <a href="{{ route('admin.history.index', array_merge(request()->query(), ['tab' => 'assessments'])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $tab === 'assessments' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Riwayat Status Raport
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.history.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Tahun Pelajaran</label>
                <select name="ay" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    <option value="">Semua Tahun Pelajaran</option>
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $ayId == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Semester</label>
                <select name="sem" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semId == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Cari Siswa / Guru</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama siswa / NISN / Guru..."
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-theme-primary">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md shadow-theme-primary/20 transition">
                    Saring Data
                </button>
                @if($ayId || $semId || $search)
                    <a href="{{ route('admin.history.index', ['tab' => $tab]) }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($tab === 'assignments')
        <!-- Tab 1: Riwayat Penugasan Guru & Wali Kelas -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
            <div class="p-4 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wide">
                    Riwayat Penugasan Guru Pembimbing & Wali Kelas (Total: {{ $assignments->total() }} Data)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">Nama Siswa</th>
                            <th class="py-3.5 px-6">Jenjang</th>
                            <th class="py-3.5 px-6">Tahun / Semester</th>
                            <th class="py-3.5 px-6">Guru Tahfidz Pembimbing</th>
                            <th class="py-3.5 px-6">Wali Kelas</th>
                            <th class="py-3.5 px-6">Waktu Penugasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                        @forelse($assignments as $assign)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-800 dark:text-white">
                                    {{ $assign->student?->name }}
                                    <div class="text-[11px] font-normal text-slate-400 dark:text-slate-500 font-mono">NISN: {{ $assign->student?->nisn ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase
                                        {{ $assign->student?->level === 'SD' ? 'bg-theme-yellow/20 text-amber-800 dark:text-amber-300 border border-theme-yellow/30' : 'bg-theme-primary/10 text-theme-primary dark:text-blue-300 border border-theme-primary/20' }}">
                                        {{ $assign->student?->level }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-xs text-slate-600 dark:text-slate-300 font-semibold">
                                    {{ $assign->academicYear?->name }} - {{ $assign->semester?->name }}
                                </td>
                                <td class="py-3.5 px-6 text-xs font-bold text-theme-primary dark:text-blue-400">
                                    {{ $assign->teacher?->teacherProfile?->formatted_name_with_degree ?? ($assign->teacher?->name ?? 'Belum Ditugaskan') }}
                                </td>
                                <td class="py-3.5 px-6 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $assign->homeroom?->teacherProfile?->formatted_name_with_degree ?? ($assign->homeroom?->name ?? '-') }}
                                </td>
                                <td class="py-3.5 px-6 text-xs text-slate-400 dark:text-slate-500">
                                    {{ $assign->created_at->format('d/m/Y H:i') }} WIB
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                    Tidak ada data riwayat penugasan sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Tab 2: Riwayat Status & Workflow Raport -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
            <div class="p-4 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wide">
                    Riwayat Status Raport & Catatan Workflow (Total: {{ $assessments->total() }} Raport)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">Nama Siswa</th>
                            <th class="py-3.5 px-6">Tahun / Semester</th>
                            <th class="py-3.5 px-6">Guru Tahfidz</th>
                            <th class="py-3.5 px-6 text-center">Status Terakhir</th>
                            <th class="py-3.5 px-6">Riwayat Workflow & Catatan</th>
                            <th class="py-3.5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                        @forelse($assessments as $assess)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-800 dark:text-white">
                                    {{ $assess->student?->name }}
                                    <div class="text-[11px] font-normal text-slate-400 dark:text-slate-500">NISN: {{ $assess->student?->nisn ?? '-' }} | {{ $assess->student?->level }}</div>
                                </td>
                                <td class="py-3.5 px-6 text-xs text-slate-600 dark:text-slate-300 font-semibold">
                                    {{ $assess->academicYear?->name }} - {{ $assess->semester?->name }}
                                </td>
                                <td class="py-3.5 px-6 text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {{ $assess->teacher?->teacherProfile?->formatted_name_with_degree ?? ($assess->teacher?->name ?? '-') }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wide
                                        @if($assess->status === 'APPROVED' || $assess->status === 'SELESAI') bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300
                                        @elseif($assess->status === 'REVIEWED') bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300
                                        @elseif($assess->status === 'SUBMITTED') bg-theme-primary/10 text-theme-primary dark:text-blue-300 border border-theme-primary/20
                                        @elseif($assess->status === 'LOCKED') bg-slate-200 text-slate-800 dark:bg-slate-800 dark:text-slate-300
                                        @else bg-theme-yellow/20 text-amber-800 dark:text-amber-300 border border-theme-yellow/30 @endif">
                                        {{ $assess->status_label ?? $assess->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-xs text-slate-600 dark:text-slate-300 space-y-1">
                                    @if($assess->latestSubmission)
                                        <div class="text-[11px]">
                                            <span class="font-bold text-theme-primary">Submitted:</span> {{ $assess->latestSubmission->submitted_at?->format('d/m/Y H:i') }}
                                            @if($assess->latestSubmission->notes) <span class="italic text-slate-400">"{{ $assess->latestSubmission->notes }}"</span> @endif
                                        </div>
                                    @endif
                                    @if($assess->latestReview)
                                        <div class="text-[11px]">
                                            <span class="font-bold text-amber-600 dark:text-amber-400">Reviewed (Wali):</span> {{ $assess->latestReview->reviewed_at?->format('d/m/Y H:i') }}
                                            @if($assess->latestReview->notes) <span class="italic text-slate-400">"{{ $assess->latestReview->notes }}"</span> @endif
                                        </div>
                                    @endif
                                    @if($assess->latestApproval)
                                        <div class="text-[11px]">
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Approved (Kepsek):</span> {{ $assess->latestApproval->approved_at?->format('d/m/Y H:i') }}
                                            @if($assess->latestApproval->notes) <span class="italic text-slate-400">"{{ $assess->latestApproval->notes }}"</span> @endif
                                        </div>
                                    @endif
                                    @if(!$assess->latestSubmission && !$assess->latestReview && !$assess->latestApproval)
                                        <span class="text-slate-400 dark:text-slate-500 italic">Belum ada riwayat workflow</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    <a href="{{ route('reports.preview', $assess->id) }}" target="_blank"
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition inline-flex items-center gap-1 border border-slate-200/60 dark:border-slate-700/60">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Preview
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                    Tidak ada data riwayat raport sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assessments->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $assessments->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
