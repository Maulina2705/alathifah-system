@extends('layouts.app', ['title' => 'Import / Export Excel Ledger'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Import & Export Ledger Excel</h2>
        <p class="text-xs text-slate-500 mt-1">
            Unduh format ledger nilai berdasarkan data siswa aktif di sistem, isi nilai secara offline, lalu upload kembali untuk validasi dan import otomatis.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Step 1: Download Template -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 font-extrabold text-lg flex items-center justify-center mb-4">
                    1
                </div>
                <h3 class="font-extrabold text-base text-slate-800">Download Template Ledger</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Sistem akan otomatis mengekspor seluruh siswa Anda beserta materi dan indikator yang sudah diinisialisasi untuk <strong>TP {{ $activeAY?->name }} (Semester {{ $activeSem?->name }})</strong>.
                </p>
                <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600">
                    <span class="font-bold">Format Kolom Excel:</span><br>
                    <code>Nama Siswa | NIS | NISN | Materi | Indikator | Nilai (1-100)</code>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('excel.download') }}"
                   class="w-full py-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-md transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Template Ledger (.xlsx)
                </a>
            </div>
        </div>

        <!-- Step 2: Upload Excel -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 font-extrabold text-lg flex items-center justify-center mb-4">
                    2
                </div>
                <h3 class="font-extrabold text-base text-slate-800">Upload & Validasi Excel</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Unggah file Excel yang telah diisi nilainya. Sistem akan melakukan pengecekan validitas NIS, kecocokan nama, rentang nilai (1-100), dan status kunci raport sebelum mengimpor.
                </p>
            </div>

            <form action="{{ route('excel.preview') }}" method="POST" enctype="multipart/form-data" class="mt-6 pt-4 border-t border-slate-100 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih File (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-200 rounded-xl p-2 cursor-pointer">
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shadow-md shadow-emerald-700/20 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Unggah & Periksa Data (Preview)
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
