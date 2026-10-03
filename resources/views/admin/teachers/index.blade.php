@extends('layouts.app', ['title' => 'Data Guru & User'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-theme-primary/10 text-theme-primary dark:text-blue-300 font-bold text-[11px] mb-2 border border-theme-primary/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Master Data Pengguna
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Data Guru & Akun Pengguna</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola akun guru tahfizh, wali kelas, kepala sekolah, dan super admin.</p>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            <a href="{{ route('admin.teachers.download_template') }}"
               class="px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center transition" title="Unduh Format Excel Guru">
                <svg class="w-4 h-4 mr-1.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Template
            </a>
            <button onclick="openImportTeacherModal()"
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs flex items-center shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5 text-theme-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Import Excel
            </button>
            <button onclick="openUserModal()"
                    class="px-4 py-2.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md shadow-theme-primary/20 transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah User
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-6">Nama Lengkap & NIP</th>
                        <th class="py-3 px-6">Email</th>
                        <th class="py-3 px-6">Role / Peran</th>
                        <th class="py-3 px-6">Jenjang</th>
                        <th class="py-3 px-6 text-center">Verifikasi</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @forelse($users as $u)
                        @php
                            $p = $u->teacherProfile;
                            $roleName = $u->getRoleNames()->first();
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $p?->formatted_name_with_degree ?? $u->name }}</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">NIP: {{ $p?->nip ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600 dark:text-slate-400">
                                {{ $u->email }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    @if($roleName === 'SUPER ADMIN') bg-theme-pink/15 text-theme-pink border border-theme-pink/30
                                    @elseif($roleName === 'GURU') bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400
                                    @elseif($roleName === 'WALI KELAS') bg-theme-primary/10 text-theme-primary dark:text-blue-300 border border-theme-primary/20
                                    @elseif($roleName === 'IT') bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300
                                    @else bg-theme-yellow/20 text-amber-800 dark:text-amber-300 border border-theme-yellow/30 @endif">
                                    {{ $roleName }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                {{ $p?->level ?? 'SD' }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if($p && $p->is_verified)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                                        Belum
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $u->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button onclick='editUser(@json($u), @json($p), "{{ $roleName }}")'
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-theme-primary hover:bg-theme-primary/10 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button onclick="openResetPasswordModal({{ $u->id }}, '{{ $u->name }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition" title="Reset Password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-xs text-slate-400">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal User Form -->
    <div id="userModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto transition-colors">
            <h3 id="modalUserTitle" class="text-base font-extrabold text-slate-800 dark:text-white mb-1">Tambah Akun Baru</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Buat akun untuk guru atau staf sekolah.</p>

            <form id="userForm" method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-4">
                @csrf
                <div id="methodUserContainer"></div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="userName" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Email</label>
                        <input type="email" name="email" id="userEmail" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Peran (Role)</label>
                        <select name="role" id="userRole" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="passwordFieldGroup">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Password</label>
                    <input type="password" name="password" id="userPassword"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary" placeholder="Minimal 6 karakter">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIP</label>
                        <input type="text" name="nip" id="userNip" placeholder="1992..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Gelar (contoh: S.Pd.I)</label>
                        <input type="text" name="title_degree" id="userDegree" placeholder="S.Pd.I"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenjang</label>
                        <select name="level" id="userLevel" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="KEDUANYA">SD & SMP (Keduanya)</option>
                        </select>
                    </div>
                    <div id="statusFieldGroup" class="hidden">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Status Akun</label>
                        <select name="is_active" id="userActive"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-theme-primary">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeUserModal()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md transition">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div id="resetPasswordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 transition-colors">
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white mb-1">Reset Password Pengguna</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4" id="resetUserDesc"></p>

            <form id="resetPasswordForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Password Baru</label>
                    <input type="password" name="new_password" required minlength="6" placeholder="Minimal 6 karakter"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeResetPasswordModal()"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Excel Guru -->
    <div id="importTeacherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 transition-colors">
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white mb-1">Import Data Guru & Staf</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Unggah file Excel berisi data guru sesuai format template sistem.</p>

            <form action="{{ route('admin.teachers.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                           class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-theme-primary/10 file:text-theme-primary dark:file:bg-theme-primary/20 hover:file:bg-theme-primary/20 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl">
                </div>

                <div class="bg-theme-yellow/15 rounded-xl p-3 border border-theme-yellow/30 text-xs text-amber-900 dark:text-amber-200 space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Ketentuan Import:
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-1">
                        <li>Kolom wajib: Nama, Email, Role (GURU, WALI_KELAS, KEPALA_SEKOLAH, SUPER_ADMIN).</li>
                        <li>Password default jika kosong: <span class="font-mono font-bold">password123</span>.</li>
                        <li>Pastikan email belum terdaftar di sistem.</li>
                    </ul>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeImportTeacherModal()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openUserModal() {
        document.getElementById('modalUserTitle').innerText = 'Tambah Akun Baru';
        document.getElementById('userForm').action = "{{ route('admin.teachers.store') }}";
        document.getElementById('methodUserContainer').innerHTML = '';
        document.getElementById('userName').value = '';
        document.getElementById('userEmail').value = '';
        document.getElementById('userRole').value = 'GURU';
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordFieldGroup').classList.remove('hidden');
        document.getElementById('userNip').value = '';
        document.getElementById('userDegree').value = '';
        document.getElementById('userLevel').value = 'SD';
        document.getElementById('statusFieldGroup').classList.add('hidden');
        document.getElementById('userModal').classList.remove('hidden');
    }

    function editUser(u, p, roleName) {
        document.getElementById('modalUserTitle').innerText = 'Edit Data Akun';
        document.getElementById('userForm').action = '/admin/teachers/' + u.id;
        document.getElementById('methodUserContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('userName').value = u.name;
        document.getElementById('userEmail').value = u.email;
        document.getElementById('userRole').value = roleName;
        document.getElementById('passwordFieldGroup').classList.add('hidden');
        document.getElementById('userPassword').required = false;
        document.getElementById('userNip').value = p ? (p.nip || '') : '';
        document.getElementById('userDegree').value = p ? (p.title_degree || '') : '';
        document.getElementById('userLevel').value = p ? p.level : 'SD';
        document.getElementById('userActive').value = u.is_active ? '1' : '0';
        document.getElementById('statusFieldGroup').classList.remove('hidden');
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function openResetPasswordModal(id, name) {
        document.getElementById('resetPasswordForm').action = '/admin/teachers/' + id + '/reset-password';
        document.getElementById('resetUserDesc').innerText = 'Setel password baru untuk pengguna: ' + name;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
    }

    function openImportTeacherModal() {
        document.getElementById('importTeacherModal').classList.remove('hidden');
    }

    function closeImportTeacherModal() {
        document.getElementById('importTeacherModal').classList.add('hidden');
    }
</script>
@endsection
