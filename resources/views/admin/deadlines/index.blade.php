@extends('layouts.app', ['title' => 'Batas Waktu Pengisian Raport'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-theme-yellow/15 text-amber-800 dark:text-amber-300 font-bold text-[11px] mb-2 border border-theme-yellow/30">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kontrol Jadwal & Penguncian Nilai
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Batas Waktu (Deadline) Pengisian Raport</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Atur batas waktu submit raport untuk guru per periode semester dan jenjang sekolah.</p>
        </div>

        <button onclick="openDeadlineModal()"
                class="px-5 py-2.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md shadow-theme-primary/20 transition flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Tambah Deadline Baru
        </button>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-6">Judul Batas Waktu</th>
                        <th class="py-3 px-6">Tahun / Semester</th>
                        <th class="py-3 px-6">Jenjang</th>
                        <th class="py-3 px-6">Batas Waktu (WIB)</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Aktif</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @forelse($deadlines as $d)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-6 font-bold text-slate-800 dark:text-white">
                                {{ $d->title }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600 dark:text-slate-400">
                                {{ $d->academicYear?->name }} ({{ $d->semester?->name }})
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    @if($d->level === 'SD') bg-theme-yellow/20 text-amber-800 dark:text-amber-300 border border-theme-yellow/30
                                    @elseif($d->level === 'SMP') bg-theme-primary/10 text-theme-primary dark:text-blue-300 border border-theme-primary/20
                                    @else bg-purple-100 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300 @endif">
                                    {{ $d->level }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700 dark:text-slate-300">
                                <div>{{ $d->deadline_at->format('d F Y, H:i') }} WIB</div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">({{ $d->remaining_human }})</div>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase
                                    @if($d->status_label === 'OVERDUE') bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300
                                    @elseif($d->status_label === 'WARNING') bg-theme-yellow/20 text-amber-800 dark:text-amber-300 border border-theme-yellow/30
                                    @else bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 @endif">
                                    {{ $d->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $d->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500' }}">
                                    {{ $d->is_active ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button type="button"
                                            data-id="{{ $d->id }}"
                                            data-title="{{ $d->title }}"
                                            data-ay="{{ $d->academic_year_id }}"
                                            data-sem="{{ $d->semester_id }}"
                                            data-level="{{ $d->level }}"
                                            data-time="{{ $d->deadline_at->format('Y-m-d\TH:i') }}"
                                            data-active="{{ $d->is_active ? 1 : 0 }}"
                                            onclick="openEditDeadlineFromBtn(this)"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-theme-primary hover:bg-theme-primary/10 transition"
                                            title="Edit Batas Waktu">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.deadlines.destroy', $d->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus deadline ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-theme-pink hover:bg-theme-pink/10 transition" title="Hapus Batas Waktu">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                Belum ada batas waktu deadline yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create Deadline -->
    <div id="deadlineModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 transition-colors">
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white mb-1">Tambah Batas Waktu Baru</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Atur tanggal dan jam penutupan submit raport.</p>

            <form action="{{ route('admin.deadlines.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Judul / Keterangan</label>
                    <input type="text" name="title" required placeholder="Batas Akhir Penilaian Raport..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tahun Pelajaran</label>
                        <select name="academic_year_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Semester</label>
                        <select name="semester_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenjang</label>
                        <select name="level" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            <option value="SEMUA">Semua Jenjang</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Waktu Deadline</label>
                        <input type="datetime-local" name="deadline_at" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeDeadlineModal()"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md transition">
                        Simpan Batas Waktu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Deadline -->
    <div id="editDeadlineModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 transition-colors">
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white mb-1">Edit Batas Waktu Raport</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Ubah judul, waktu penutupan, jenjang, atau status keaktifan.</p>

            <form id="editDeadlineForm" action="" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Judul / Keterangan</label>
                    <input type="text" name="title" id="edit_title" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tahun Pelajaran</label>
                        <select name="academic_year_id" id="edit_academic_year_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Semester</label>
                        <select name="semester_id" id="edit_semester_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenjang</label>
                        <select name="level" id="edit_level" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            <option value="SEMUA">Semua Jenjang</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Waktu Deadline</label>
                        <input type="datetime-local" name="deadline_at" id="edit_deadline_at" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Status Keaktifan</label>
                    <select name="is_active" id="edit_is_active" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        <option value="1">Aktif (Terapkan Batas Waktu)</option>
                        <option value="0">Nonaktif (Bebaskan Pengisian)</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeEditDeadlineModal()"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openDeadlineModal() {
        document.getElementById('deadlineModal').classList.remove('hidden');
    }
    function closeDeadlineModal() {
        document.getElementById('deadlineModal').classList.add('hidden');
    }

    function openEditDeadlineFromBtn(btn) {
        const data = {
            id: btn.getAttribute('data-id'),
            title: btn.getAttribute('data-title'),
            academic_year_id: btn.getAttribute('data-ay'),
            semester_id: btn.getAttribute('data-sem'),
            level: btn.getAttribute('data-level'),
            deadline_at: btn.getAttribute('data-time'),
            is_active: btn.getAttribute('data-active')
        };
        openEditDeadlineModal(data);
    }

    function openEditDeadlineModal(data) {
        const form = document.getElementById('editDeadlineForm');
        form.action = "{{ url('admin/deadlines') }}/" + data.id;

        document.getElementById('edit_title').value = data.title;
        document.getElementById('edit_academic_year_id').value = data.academic_year_id;
        document.getElementById('edit_semester_id').value = data.semester_id;
        document.getElementById('edit_level').value = data.level;
        document.getElementById('edit_deadline_at').value = data.deadline_at;
        document.getElementById('edit_is_active').value = data.is_active;

        document.getElementById('editDeadlineModal').classList.remove('hidden');
    }

    function closeEditDeadlineModal() {
        document.getElementById('editDeadlineModal').classList.add('hidden');
    }
</script>
@endsection
