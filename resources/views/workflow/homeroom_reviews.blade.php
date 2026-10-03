@extends('layouts.app', ['title' => 'Review Raport - Wali Kelas'])

@section('content')
<div class="space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs transition-colors">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Review Raport Tahfizh (Wali Kelas)</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Daftar raport yang telah disubmit oleh Guru Tahfizh dan menunggu pemeriksaan Anda untuk Tahun Pelajaran {{ $activeAY?->name }} (Semester {{ $activeSem?->name }}).
        </p>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-6">Nama Siswa</th>
                        <th class="py-3.5 px-6">Jenjang</th>
                        <th class="py-3.5 px-6">Guru Tahfizh</th>
                        <th class="py-3.5 px-6 text-center">Progress Nilai</th>
                        <th class="py-3.5 px-6">Catatan Pengajuan</th>
                        <th class="py-3.5 px-6 text-center">Aksi Review</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($assessments as $assess)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $assess->student->name }}</div>
                                <div class="text-[11px] text-slate-400">NISN: {{ $assess->student->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase
                                    {{ $assess->student->level === 'SD' ? 'bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' }}">
                                    {{ $assess->student->level }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                {{ $assess->teacher?->teacherProfile?->formatted_name_with_degree ?? ($assess->teacher?->name ?? '-') }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="font-extrabold text-xs text-blue-600 dark:text-blue-400">{{ $assess->progress_percentage }}%</span>
                                <div class="text-[10px] text-slate-400">{{ $assess->filled_items_count }} / {{ $assess->total_items_count }} diisi</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600 dark:text-slate-400 max-w-xs">
                                {{ $assess->latestSubmission?->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('reports.preview', $assess->id) }}" target="_blank"
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                                        Preview
                                    </a>

                                    <!-- Approve Form -->
                                    <form action="{{ route('homeroom.reviews.action', $assess->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="REVIEWED">
                                        <button type="submit" onclick="return confirm('Setujui raport ini dan teruskan ke Kepala Sekolah?')"
                                                class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition">
                                            ✓ Setujui (Reviewed)
                                        </button>
                                    </form>

                                    <!-- Reject / Return Form -->
                                    <button type="button" onclick="openRejectModal({{ $assess->id }}, '{{ addslashes($assess->student->name) }}')"
                                            class="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 font-bold text-xs transition">
                                        Kembalikan
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                                Tidak ada raport yang menunggu review saat ini.
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

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Kembalikan Raport ke Guru</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4" id="rejectModalDesc"></p>

            <form id="rejectModalForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="status" value="REJECTED">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Alasan Penolakan / Catatan Revisi</label>
                    <textarea name="notes" rows="3" required placeholder="Jelaskan bagian nilai yang perlu diperbaiki oleh Guru Tahfizh..."
                              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition">
                        Kembalikan untuk Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openRejectModal(id, studentName) {
        document.getElementById('rejectModalForm').action = '/homeroom/reviews/' + id;
        document.getElementById('rejectModalDesc').innerText = 'Siswa: ' + studentName;
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection
