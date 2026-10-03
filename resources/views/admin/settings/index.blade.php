@extends('layouts.app', ['title' => 'Pengaturan Raport SD & SMP'])

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs relative overflow-hidden transition-colors">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-gradient-to-br from-theme-primary/10 to-theme-yellow/15 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-theme-primary/10 text-theme-primary dark:text-blue-300 font-bold text-[11px] mb-2 border border-theme-primary/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    Pusat Konfigurasi & Format Dokumen
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Pengaturan Format Raport Tahfizh (SD & SMP)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                    Konfigurasi kop resmi sekolah, identitas kepala sekolah & NIP, label penandatangan, kota, serta margin cetak PDF secara terpisah untuk jenjang SD dan SMP.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300">
                    2 Format Jenjang Tersedia
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- SD Settings (Kuning / Amber Accent) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs transition-colors flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                    <div class="flex items-center space-x-2.5">
                        <span class="px-3 py-1 rounded-xl bg-theme-yellow/20 text-amber-800 dark:text-amber-300 font-extrabold text-xs border border-theme-yellow/30">SD</span>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Format Raport SD Islam Riau Global Terpadu</h3>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update', 'SD') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Resmi Sekolah</label>
                        <input type="text" name="school_name" value="{{ old('school_name', $sdSetting->school_name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Judul Raport (Header Kop)</label>
                        <input type="text" name="header_title" value="{{ old('header_title', $sdSetting->header_title) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kota Penerbitan</label>
                            <input type="text" name="city" value="{{ old('city', $sdSetting->city) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tanggal Raport Standar</label>
                            <input type="date" name="default_report_date" value="{{ old('default_report_date', $sdSetting->default_report_date?->format('Y-m-d')) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Kepala Sekolah</label>
                            <input type="text" name="principal_name" value="{{ old('principal_name', $sdSetting->principal_name) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIP Kepala Sekolah</label>
                            <input type="text" name="principal_nip" value="{{ old('principal_nip', $sdSetting->principal_nip) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Label Wali Kelas</label>
                            <input type="text" name="homeroom_label" value="{{ old('homeroom_label', $sdSetting->homeroom_label) }}" required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Label Guru</label>
                            <input type="text" name="teacher_label" value="{{ old('teacher_label', $sdSetting->teacher_label) }}" required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Label Kepsek</label>
                            <input type="text" name="principal_label" value="{{ old('principal_label', $sdSetting->principal_label) }}" required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-yellow">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Ukuran & Margin Kertas (mm)</span>
                        <div class="grid grid-cols-5 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Kertas</label>
                                <input type="text" name="paper_size" value="{{ old('paper_size', $sdSetting->paper_size) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-bold text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Atas</label>
                                <input type="number" name="margin_top" value="{{ old('margin_top', $sdSetting->margin_top) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Bawah</label>
                                <input type="number" name="margin_bottom" value="{{ old('margin_bottom', $sdSetting->margin_bottom) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Kiri</label>
                                <input type="number" name="margin_left" value="{{ old('margin_left', $sdSetting->margin_left) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Kanan</label>
                                <input type="number" name="margin_right" value="{{ old('margin_right', $sdSetting->margin_right) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs text-center">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition">
                            Simpan Pengaturan SD
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SMP Settings (Biru / Primary Accent) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs transition-colors flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                    <div class="flex items-center space-x-2.5">
                        <span class="px-3 py-1 rounded-xl bg-theme-primary/10 text-theme-primary dark:text-blue-300 font-extrabold text-xs border border-theme-primary/20">SMP</span>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Format Raport SMP Islam Riau Global Terpadu</h3>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update', 'SMP') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Resmi Sekolah</label>
                        <input type="text" name="school_name" value="{{ old('school_name', $smpSetting->school_name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Judul Raport (Header Kop)</label>
                        <input type="text" name="header_title" value="{{ old('header_title', $smpSetting->header_title) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kota Penerbitan</label>
                            <input type="text" name="city" value="{{ old('city', $smpSetting->city) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tanggal Raport Standar</label>
                            <input type="date" name="default_report_date" value="{{ old('default_report_date', $smpSetting->default_report_date?->format('Y-m-d')) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Kepala Sekolah</label>
                            <input type="text" name="principal_name" value="{{ old('principal_name', $smpSetting->principal_name) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIP Kepala Sekolah</label>
                            <input type="text" name="principal_nip" value="{{ old('principal_nip', $smpSetting->principal_nip) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Label Wali Kelas</label>
                            <input type="text" name="homeroom_label" value="{{ old('homeroom_label', $smpSetting->homeroom_label) }}" required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Label Guru</label>
                            <input type="text" name="teacher_label" value="{{ old('teacher_label', $smpSetting->teacher_label) }}" required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Label Kepsek</label>
                            <input type="text" name="principal_label" value="{{ old('principal_label', $smpSetting->principal_label) }}" required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Ukuran & Margin Kertas (mm)</span>
                        <div class="grid grid-cols-5 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Kertas</label>
                                <input type="text" name="paper_size" value="{{ old('paper_size', $smpSetting->paper_size) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs font-bold text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Atas</label>
                                <input type="number" name="margin_top" value="{{ old('margin_top', $smpSetting->margin_top) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Bawah</label>
                                <input type="number" name="margin_bottom" value="{{ old('margin_bottom', $smpSetting->margin_bottom) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Kiri</label>
                                <input type="number" name="margin_left" value="{{ old('margin_left', $smpSetting->margin_left) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase">Kanan</label>
                                <input type="number" name="margin_right" value="{{ old('margin_right', $smpSetting->margin_right) }}" required
                                       class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs text-center">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md transition">
                            Simpan Pengaturan SMP
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
