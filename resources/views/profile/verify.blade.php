@extends('layouts.app', ['title' => 'Verifikasi Biodata Guru'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="flex items-center space-x-3 mb-2">
            <div class="p-2.5 rounded-xl bg-emerald-100 text-emerald-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Verifikasi Biodata Guru</h2>
                <p class="text-xs text-slate-500">Pemeriksaan dan konfirmasi identitas resmi sebelum melakukan submit raport.</p>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs leading-relaxed mt-4">
            <strong>Perhatian Penting:</strong> Nama lengkap dan gelar yang tercantum di sini akan ditarik secara otomatis ke kolom tanda tangan raport siswa resmi. Guru tidak dapat mengetik nama manual pada tiap raport untuk mencegah kekeliruan administrasi.
        </div>

        <form action="{{ route('profile.verify.submit') }}" method="POST" class="mt-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Akun (Database User)</label>
                <input type="text" value="{{ $user->name }}" disabled
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 text-sm font-bold">
                <span class="text-[11px] text-slate-400 mt-0.5 block">Jika terdapat kesalahan penulisan nama akun, hubungi Super Admin.</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Gelar (contoh: S.Pd.I / Lc.)</label>
                    <input type="text" name="title_degree" value="{{ old('title_degree', $profile->title_degree) }}" placeholder="S.Pd.I"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" value="{{ old('nip', $profile->nip) }}" placeholder="199208152019031008"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" name="nik" value="{{ old('nik', $profile->nik) }}" placeholder="1471..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor HP / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" placeholder="0812..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jabatan</label>
                    <input type="text" name="position" value="{{ old('position', $profile->position ?? 'Guru Tahfizh') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jenjang yang Ditangani</label>
                    <select name="level" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                        <option value="SD" {{ old('level', $profile->level) === 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ old('level', $profile->level) === 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="KEDUANYA" {{ old('level', $profile->level) === 'KEDUANYA' ? 'selected' : '' }}>SD & SMP (Keduanya)</option>
                    </select>
                </div>
            </div>

            <!-- Verification Checkbox -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mt-6">
                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox" name="is_confirmed" value="1" required
                           class="mt-1 w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-bold text-slate-800 leading-relaxed">
                        [✓] Saya telah memeriksa dan memastikan biodata saya benar sesuai identitas resmi sekolah.
                    </span>
                </label>
                @error('is_confirmed')
                    <span class="text-xs text-red-600 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:underline">
                    ← Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition">
                    Simpan & Verifikasi Biodata
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
