@extends('layouts.app', ['title' => 'Audit Trail & Log'])

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs relative overflow-hidden transition-colors">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-gradient-to-br from-theme-pink/10 to-theme-yellow/15 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-theme-pink/10 text-theme-pink dark:text-pink-300 font-bold text-[11px] mb-2 border border-theme-pink/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Keamanan & Jejak Aktivitas
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Audit Trail & Log Aktivitas Sistem</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                    Rekam jejak setiap perubahan nilai santri, alur workflow persetujuan, percobaan login, serta modifikasi data penting.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.audit.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="action" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-theme-pink">
                    <option value="">Semua Tindakan</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>

                <input type="text" name="search" value="{{ $search }}" placeholder="Cari keterangan, IP, nama..."
                       class="px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-theme-pink">

                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs shadow-xs transition">
                    Saring
                </button>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-6 w-36">Waktu & Tanggal</th>
                        <th class="py-3 px-6">Pengguna & IP</th>
                        <th class="py-3 px-6">Tindakan</th>
                        <th class="py-3 px-6">Siswa Terkait</th>
                        <th class="py-3 px-6">Keterangan Aktivitas</th>
                        <th class="py-3 px-6">Perubahan Nilai / Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3 px-6 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                <div class="font-bold text-slate-700 dark:text-slate-300">{{ $log->created_at->format('d/m/Y H:i') }} WIB</div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3 px-6">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $log->user?->name ?? 'Sistem / Anonim' }}</span>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">
                                    Role: {{ $log->user?->getRoleNames()->first() ?? '-' }}
                                    @if($log->ip_address)
                                        | <span class="font-mono text-slate-500">{{ $log->ip_address }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase
                                    @if(str_contains($log->action, 'SCORE')) bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300
                                    @elseif(str_contains($log->action, 'SUBMIT') || str_contains($log->action, 'APPROVE')) bg-theme-primary/10 text-theme-primary dark:text-blue-300 border border-theme-primary/20
                                    @elseif(str_contains($log->action, 'FAILED_LOGIN') || str_contains($log->action, 'BLOCKED_LOGIN') || str_contains($log->action, 'DELETE')) bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800
                                    @elseif(str_contains($log->action, 'UNLOCK')) bg-theme-yellow/20 text-amber-800 dark:text-amber-300 border border-theme-yellow/30
                                    @elseif(str_contains($log->action, 'LOGIN') || str_contains($log->action, 'LOGOUT')) bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300
                                    @else bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 @endif">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3 px-6 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $log->student?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-6 text-slate-600 dark:text-slate-300 max-w-xs">
                                {{ $log->description }}
                            </td>
                            <td class="py-3 px-6 text-[11px]">
                                @if($log->old_values || $log->new_values)
                                    <div class="font-mono bg-slate-50 dark:bg-slate-800 p-2 rounded-xl border border-slate-100 dark:border-slate-700 max-w-xs overflow-x-auto text-[10px]">
                                        @if($log->old_values)
                                            <span class="text-rose-600 dark:text-rose-400 font-bold">Sebelum:</span> {{ json_encode($log->old_values, JSON_UNESCAPED_UNICODE) }}<br>
                                        @endif
                                        @if($log->new_values)
                                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sesudah:</span> {{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                Tidak ada log aktivitas yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
