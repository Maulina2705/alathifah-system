@extends('layouts.app', ['title' => 'Master Data Siswa'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Master Data Siswa</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola data siswa SD & SMP serta penugasan Guru Pembimbing Tahfizh & Wali Kelas.</p>
        </div>

        <div class="flex items-center space-x-2 flex-wrap gap-y-2">
            <a href="{{ route('admin.students.download_template') }}"
               class="px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center transition" title="Unduh Format Excel Siswa">
                <svg class="w-4 h-4 mr-1.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Template Excel
            </a>
            <button onclick="openImportStudentModal()"
                    class="px-4 py-2.5 rounded-xl bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white font-bold text-xs flex items-center shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Import Excel
            </button>
            <button onclick="openStudentModal()"
                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition flex items-center cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Siswa
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs transition-colors">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NISN, NIS..."
                   class="px-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500 flex-1 min-w-[200px]">

            <select name="level" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Jenjang</option>
                <option value="SD" {{ $level === 'SD' ? 'selected' : '' }}>SD</option>
                <option value="SMP" {{ $level === 'SMP' ? 'selected' : '' }}>SMP</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs">
                Filter
            </button>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-6">Nama Siswa</th>
                        <th class="py-3 px-6">NIS / NISN</th>
                        <th class="py-3 px-6">Jenjang</th>
                        <th class="py-3 px-6">L/P</th>
                        <th class="py-3 px-6">Guru Tahfizh (Aktif)</th>
                        <th class="py-3 px-6">Wali Kelas (Aktif)</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($students as $st)
                        @php
                            $currAssign = $st->assignments->first();
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-6 font-bold text-slate-800 dark:text-slate-200">
                                {{ $st->name }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500 dark:text-slate-400">
                                <div>NISN: <strong class="text-slate-700 dark:text-slate-300">{{ $st->nisn ?? '-' }}</strong></div>
                                <div>NIS: {{ $st->nis ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase
                                    {{ $st->level === 'SD' ? 'bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' }}">
                                    {{ $st->level }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                {{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                {{ $currAssign?->teacher?->teacherProfile?->formatted_name_with_degree ?? ($currAssign?->teacher?->name ?? 'Belum Ditugaskan') }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600 dark:text-slate-400">
                                {{ $currAssign?->homeroom?->teacherProfile?->formatted_name_with_degree ?? ($currAssign?->homeroom?->name ?? '-') }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button onclick='editStudent(@json($st), @json($currAssign))'
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.students.destroy', $st->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus siswa {{ $st->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                Tidak ada data siswa yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <!-- Student Modal Form -->
    <div id="studentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Tambah Siswa Baru</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Lengkapi data pokok siswa dan atur penugasan guru bimbingan.</p>

            <form id="studentForm" method="POST" action="{{ route('admin.students.store') }}" class="space-y-4">
                @csrf
                <div id="methodContainer"></div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap Siswa</label>
                    <input type="text" name="name" id="modalName" required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NISN (Wajib Unik)</label>
                        <input type="text" name="nisn" id="modalNisn" required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIS</label>
                        <input type="text" name="nis" id="modalNis"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenjang</label>
                        <select name="level" id="modalLevel" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenis Kelamin</label>
                        <select name="gender" id="modalGender" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Assignment Section -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <span class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Penugasan Semester Aktif ({{ $activeAY?->name }} - {{ $activeSem?->name }})</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Guru Tahfizh</label>
                            <select name="teacher_user_id" id="modalTeacher"
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->teacherProfile?->formatted_name_with_degree ?? $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Wali Kelas</label>
                            <select name="homeroom_user_id" id="modalHomeroom"
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Wali Kelas --</option>
                                @foreach($homerooms as $h)
                                    <option value="{{ $h->id }}">{{ $h->teacherProfile?->formatted_name_with_degree ?? $h->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeStudentModal()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                        Simpan Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Siswa via Excel -->
    <div id="importStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Import Siswa via Excel</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Unggah file Excel berisi data siswa. Sistem akan otomatis menambahkan siswa baru dan memperbarui data jika NISN sudah terdaftar.</p>

            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Pilih File (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                           class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 dark:file:bg-blue-950/60 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 border border-slate-200 dark:border-slate-700 rounded-xl p-2 cursor-pointer">
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 text-[11px] text-slate-500 dark:text-slate-400">
                    <p class="font-bold text-slate-700 dark:text-slate-300 mb-1">Petunjuk Pengisian:</p>
                    <p>Unduh terlebih dahulu format resmi melalui tombol <strong>Template Excel</strong> di atas sebelum mengisi data.</p>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeImportStudentModal()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                        Upload & Proses Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openImportStudentModal() {
        document.getElementById('importStudentModal').classList.remove('hidden');
    }
    function closeImportStudentModal() {
        document.getElementById('importStudentModal').classList.add('hidden');
    }

    function openStudentModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Siswa Baru';
        document.getElementById('studentForm').action = "{{ route('admin.students.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('modalName').value = '';
        document.getElementById('modalNisn').value = '';
        document.getElementById('modalNis').value = '';
        document.getElementById('modalLevel').value = 'SD';
        document.getElementById('modalGender').value = 'L';
        document.getElementById('modalTeacher').value = '';
        document.getElementById('modalHomeroom').value = '';
        document.getElementById('studentModal').classList.remove('hidden');
    }

    function editStudent(st, assign) {
        document.getElementById('modalTitle').innerText = 'Edit Data Siswa';
        document.getElementById('studentForm').action = '/admin/students/' + st.id;
        document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('modalName').value = st.name;
        document.getElementById('modalNisn').value = st.nisn || '';
        document.getElementById('modalNis').value = st.nis || '';
        document.getElementById('modalLevel').value = st.level;
        document.getElementById('modalGender').value = st.gender;
        document.getElementById('modalTeacher').value = assign ? assign.teacher_user_id : '';
        document.getElementById('modalHomeroom').value = assign ? assign.homeroom_user_id : '';
        document.getElementById('studentModal').classList.remove('hidden');
    }

    function closeStudentModal() {
        document.getElementById('studentModal').classList.add('hidden');
    }
</script>
@endsection
