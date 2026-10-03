@extends('layouts.app', ['title' => 'Approval Raport - Kepala Sekolah'])

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs relative overflow-hidden transition-colors">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-gradient-to-br from-theme-primary/10 to-theme-pink/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-theme-yellow/15 text-amber-800 dark:text-amber-300 font-bold text-[11px] mb-2 border border-theme-yellow/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Persetujuan Akhir & Legalisasi Raport
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Persetujuan Akhir Raport Tahfizh (Kepala Sekolah)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                    Daftar raport santri yang telah lolos review oleh Wali Kelas dan siap untuk disetujui secara resmi oleh Kepala Sekolah (Tahun Pelajaran <span class="font-bold text-slate-700 dark:text-slate-200">{{ $activeAY?->name }}</span>, Semester <span class="font-bold text-slate-700 dark:text-slate-200">{{ $activeSem?->name }}</span>).
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300">
                    Total: {{ $assessments->total() ?? count($assessments) }} Berkas
                </span>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-6">Nama Siswa</th>
                        <th class="py-3 px-6">Jenjang</th>
                        <th class="py-3 px-6">Guru Pembimbing</th>
                        <th class="py-3 px-6 text-center">Progress Nilai</th>
                        <th class="py-3 px-6">Catatan Reviewer</th>
                        <th class="py-3 px-6 text-center">Aksi Approval</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @forelse($assessments as $assess)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $assess->student->name }}</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">NISN: {{ $assess->student->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $assess->student->level === 'SD' ? 'bg-theme-yellow/20 text-amber-800 dark:text-amber-300 border border-theme-yellow/30' : 'bg-theme-primary/10 text-theme-primary dark:text-blue-300 border border-theme-primary/20' }}">
                                    {{ $assess->student->level }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                {{ $assess->teacher?->teacherProfile?->formatted_name_with_degree ?? ($assess->teacher?->name ?? '-') }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="font-extrabold text-xs text-emerald-600 dark:text-emerald-400">{{ $assess->progress_percentage }}%</span>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">{{ $assess->filled_items_count }} / {{ $assess->total_items_count }} diisi</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600 dark:text-slate-400 max-w-xs">
                                <span class="line-clamp-2 italic">{{ $assess->latestReview?->notes ?? 'Telah diperiksa wali kelas' }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('reports.preview', $assess->id) }}" target="_blank"
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition border border-slate-200/50 dark:border-slate-700">
                                        Preview
                                    </a>

                                    <!-- Approve Form -->
                                    <form action="{{ route('principal.approvals.action', $assess->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="APPROVED">
                                        <button type="submit" onclick="return confirm('Setujui raport ini secara resmi (Approved)?')"
                                                class="px-3.5 py-1.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-xs transition flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Setujui
                                        </button>
                                    </form>

                                    <!-- Request Revision -->
                                    <button type="button" onclick="openRevisionModal({{ $assess->id }}, '{{ $assess->student->name }}')"
                                            class="px-3 py-1.5 rounded-xl bg-theme-pink/10 hover:bg-theme-pink/20 text-theme-pink border border-theme-pink/30 font-bold text-xs transition">
                                        Minta Revisi
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Belum ada raport yang berstatus Reviewed menunggu persetujuan.</p>
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

    <!-- Modal Minta Revisi -->
    <div id="revisionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 transition-colors">
            <div class="flex items-center gap-2.5 mb-1">
                <span class="p-1.5 rounded-lg bg-theme-pink/10 text-theme-pink">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Permintaan Revisi Raport</h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4" id="revisionStudentDesc"></p>

            <form id="revisionForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="status" value="REVISION_REQUESTED">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Catatan Koreksi / Alasan</label>
                    <textarea name="notes" rows="3" required placeholder="Tuliskan catatan perbaikan nilai atau materi..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-pink"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeRevisionModal()"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-theme-pink hover:opacity-90 text-white font-bold text-xs shadow-md transition">
                        Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openRevisionModal(id, studentName) {
        document.getElementById('revisionForm').action = '/principal/approvals/' + id;
        document.getElementById('revisionStudentDesc').innerText = 'Raport siswa ' + studentName + ' akan dikembalikan ke status DRAFT.';
        document.getElementById('revisionModal').classList.remove('hidden');
    }
    function closeRevisionModal() {
        document.getElementById('revisionModal').classList.add('hidden');
    }
</script>
@endsection
