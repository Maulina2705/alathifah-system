@extends('layouts.app', ['title' => 'Daftar Raport Tahfizh'])

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Form -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs transition-colors">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Manajemen & Antrian Cetak Raport Tahfizh</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pantau status pengerjaan guru, antrian percetakan IT, preview raport, dan cetak combine PDF massal.</p>
            </div>

            <!-- Workflow Legend (Biru, Pink, Kuning, Hijau) -->
            <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-bold">
                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">1. Draft</span>
                <span class="text-slate-400">→</span>
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">2. Submitted</span>
                <span class="text-slate-400">→</span>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">3. Antrean Cetak</span>
                <span class="text-slate-400">→</span>
                <span class="px-2.5 py-0.5 rounded-full bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800">4. Proses Cetak</span>
                <span class="text-slate-400">→</span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">5. Selesai</span>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Tahun Pelajaran</label>
                <select name="ay" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tahun</option>
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $ayId == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Semester</label>
                <select name="sem" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semId == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Status Workflow / Cetak</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="DRAFT" {{ $status == 'DRAFT' ? 'selected' : '' }}>Draft Pengisian Guru</option>
                    <option value="SUBMITTED" {{ $status == 'SUBMITTED' ? 'selected' : '' }}>Submitted by Teacher</option>
                    <option value="QUEUED" {{ $status == 'QUEUED' ? 'selected' : '' }}>Menunggu Antrian Cetak</option>
                    <option value="PRINTING" {{ $status == 'PRINTING' ? 'selected' : '' }}>Proses Cetak</option>
                    <option value="COMPLETED" {{ $status == 'COMPLETED' ? 'selected' : '' }}>Selesai Cetak</option>
                    <option value="REVIEWED" {{ $status == 'REVIEWED' ? 'selected' : '' }}>Reviewed (Wali Kelas)</option>
                    <option value="APPROVED" {{ $status == 'APPROVED' ? 'selected' : '' }}>Approved (Kepsek)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Jenjang</label>
                <select name="level" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jenjang</option>
                    <option value="SD" {{ $level == 'SD' ? 'selected' : '' }}>SD</option>
                    <option value="SMP" {{ $level == 'SMP' ? 'selected' : '' }}>SMP</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Action Form -->
    <form action="{{ route('reports.bulk_pdf') }}" method="POST" id="bulkPdfForm" target="_blank">
        @csrf
        <input type="hidden" name="ay" value="{{ $ayId }}">
        <input type="hidden" name="sem" value="{{ $semId }}">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="hidden" name="level" value="{{ $level }}">
        <input type="hidden" name="search" value="{{ $search }}">

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
            <!-- Table Header Bar -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center space-x-3 text-xs text-slate-600 dark:text-slate-300">
                    <input type="checkbox" id="selectAll" class="rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                    <label for="selectAll" class="font-bold cursor-pointer">Pilih Semua di Halaman Ini</label>
                    <span id="selectedCountBadge" class="hidden px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 font-extrabold text-[11px] border border-blue-200 dark:border-blue-800">
                        0 Dipilih
                    </span>
                </div>

                <div class="flex items-center flex-wrap gap-2">
                    @if(auth()->user()->isItOrSuperAdmin())
                        <!-- IT & Super Admin Bulk Status Updater -->
                        <div class="flex items-center space-x-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-1">
                            <select id="bulkStatusSelect" class="text-xs font-semibold text-slate-700 dark:text-slate-200 bg-transparent border-0 focus:ring-0 py-1 pl-2 pr-6">
                                <option value="">-- Ubah Status Cetak --</option>
                                <option value="SUBMITTED">Submitted by Teacher</option>
                                <option value="QUEUED">Menunggu Antrian Cetak</option>
                                <option value="PRINTING">Proses Cetak</option>
                                <option value="COMPLETED">Selesai Cetak</option>
                                <option value="DRAFT">Kembalikan ke Draft</option>
                            </select>
                            <button type="button" onclick="submitBulkStatus()"
                                    class="px-3 py-1.5 rounded-lg bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white font-bold text-xs transition">
                                Update Status
                            </button>
                        </div>
                    @endif

                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Massal (Generate PDF)</span>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4 w-10 text-center"></th>
                            <th class="py-3 px-4">Nama Siswa</th>
                            <th class="py-3 px-4">Jenjang</th>
                            <th class="py-3 px-4">Guru Tahfidz</th>
                            <th class="py-3 px-4">Tahun / Semester</th>
                            <th class="py-3 px-4 text-center">Progress</th>
                            <th class="py-3 px-4 text-center">Status Cetak</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($assessments as $assess)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox" name="selected_ids[]" value="{{ $assess->id }}"
                                           class="student-checkbox rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $assess->student->name }}</div>
                                    <div class="text-[11px] text-slate-400">NISN: {{ $assess->student->nisn ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase
                                        {{ $assess->student->level === 'SD' ? 'bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' }}">
                                        {{ $assess->student->level }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $assess->teacher?->teacherProfile?->formatted_name_with_degree ?? ($assess->teacher?->name ?? '-') }}
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-600 dark:text-slate-400">
                                    {{ $assess->academicYear?->name }} ({{ $assess->semester?->name }})
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="w-24 mx-auto">
                                        <div class="flex justify-between text-[10px] font-bold mb-0.5 text-slate-600 dark:text-slate-300">
                                            <span>{{ $assess->progress_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-full rounded-full {{ $assess->progress_percentage === 100 ? 'bg-pink-500' : 'bg-blue-600' }}" style="width: {{ $assess->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wide border
                                        {{ $assess->status_badge_color }}">
                                        {{ $assess->status_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        @if(auth()->user()->isItOrSuperAdmin())
                                            <!-- Quick IT Status Toggle -->
                                            <button type="button" onclick="openStatusModal({{ $assess->id }}, '{{ addslashes($assess->student->name) }}', '{{ $assess->status }}')"
                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-pink-500 hover:bg-pink-50 dark:hover:bg-slate-800 transition" title="Update Status Cetak (IT)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            </button>
                                        @endif

                                        <a href="{{ route('teacher.assessments.edit', $assess->id) }}"
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-800 transition" title="Input / Edit Nilai">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <a href="{{ route('reports.preview', $assess->id) }}" target="_blank"
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-slate-800 transition" title="Preview Raport">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        <a href="{{ route('reports.pdf', $assess->id) }}"
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-800 transition" title="Download PDF Resmi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-xs text-slate-400">
                                    Tidak ada data raport yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($assessments->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $assessments->links() }}
                </div>
            @endif
        </div>
    </form>

    <!-- Modal Single Status Update (IT & Admin) -->
    <div id="statusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Update Status Cetak Raport</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4" id="statusModalDesc"></p>

            <form id="statusModalForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Pilih Status Baru</label>
                    <select name="status" id="modalStatusSelect" required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        <option value="DRAFT">1. Draft Pengisian Guru</option>
                        <option value="SUBMITTED">2. Submitted by Teacher (Siap Dicetak)</option>
                        <option value="QUEUED">3. Menunggu Antrian Cetak</option>
                        <option value="PRINTING">4. Proses Cetak Fisik</option>
                        <option value="COMPLETED">5. Selesai Dicetak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Misal: Sudah cetak rangkap 2..."
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeStatusModal()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Bulk Status Form -->
    <form id="bulkStatusForm" action="{{ route('reports.bulk_status') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="bulk_status" id="hiddenBulkStatus">
        <div id="hiddenSelectedIdsContainer"></div>
    </form>

</div>

<script>
    const selectAllCheckbox = document.getElementById('selectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const badge = document.getElementById('selectedCountBadge');

    function updateBadge() {
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
        if (checkedCount > 0) {
            badge.innerText = checkedCount + ' Dipilih';
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    selectAllCheckbox?.addEventListener('change', function() {
        studentCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBadge();
    });

    studentCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBadge);
    });

    function openStatusModal(id, name, currentStatus) {
        document.getElementById('statusModalForm').action = '/reports/' + id + '/status';
        document.getElementById('statusModalDesc').innerText = 'Siswa: ' + name;
        document.getElementById('modalStatusSelect').value = currentStatus;
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }

    function submitBulkStatus() {
        const statusVal = document.getElementById('bulkStatusSelect').value;
        if (!statusVal) {
            alert('Silakan pilih status baru terlebih dahulu.');
            return;
        }

        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang minimal satu siswa yang ingin diubah statusnya.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin mengubah status ' + checkedBoxes.length + ' raport terpilih menjadi: ' + statusVal + '?')) {
            return;
        }

        const form = document.getElementById('bulkStatusForm');
        document.getElementById('hiddenBulkStatus').value = statusVal;
        const container = document.getElementById('hiddenSelectedIdsContainer');
        container.innerHTML = '';

        checkedBoxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });

        form.submit();
    }
</script>
@endsection
