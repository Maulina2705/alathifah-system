@extends('layouts.app', ['title' => 'Hasil Validasi Excel'])

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Hasil Pemeriksaan & Validasi Excel</h2>
            <p class="text-xs text-slate-500 mt-1">
                Pemeriksaan terhadap data nilai sebelum diimpor ke dalam database (TP {{ $activeAY->name }}, Semester {{ $activeSem->name }}).
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('excel.index') }}"
               class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batalkan
            </a>

            @if($validCount > 0)
                <form action="{{ route('excel.confirm') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Import {{ $validCount }} Data Valid
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-bold block uppercase">Total Baris Ditemukan</span>
            <span class="text-2xl font-black text-slate-800">{{ $totalRows }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
            <span class="text-xs text-emerald-600 font-bold block uppercase">✓ Data Siap Impor (Valid)</span>
            <span class="text-2xl font-black text-emerald-700">{{ $validCount }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-amber-200 bg-amber-50/20 shadow-xs">
            <span class="text-xs text-amber-600 font-bold block uppercase">⚠ Peringatan / Penyesuaian</span>
            <span class="text-2xl font-black text-amber-700">{{ $warningCount }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-red-200 bg-red-50/20 shadow-xs">
            <span class="text-xs text-red-600 font-bold block uppercase">✕ Data Tidak Valid (Dilewati)</span>
            <span class="text-2xl font-black text-red-700">{{ $invalidCount }}</span>
        </div>
    </div>

    <!-- Details Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Detail Baris per Baris</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-4 w-16 text-center">Baris</th>
                        <th class="py-2.5 px-4">Nama pada Excel</th>
                        <th class="py-2.5 px-4">Cocok dengan Database</th>
                        <th class="py-2.5 px-4">Materi / Indikator</th>
                        <th class="py-2.5 px-4 text-center">Nilai</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                        <th class="py-2.5 px-4">Catatan Masalah & Solusi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($details as $d)
                        <tr class="hover:bg-slate-50/60 transition {{ $d['status'] === 'INVALID' ? 'bg-red-50/20' : ($d['status'] === 'WARNING' ? 'bg-amber-50/20' : '') }}">
                            <td class="py-3 px-4 text-center font-bold text-slate-400">
                                {{ $d['row'] }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $d['excel_name'] ?: '-' }}
                                <div class="text-[10px] text-slate-400">NISN: {{ $d['nisn'] ?: '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-bold {{ $d['db_name'] !== '-' ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $d['db_name'] }}
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <span class="text-[10px] text-slate-400 block">{{ $d['materi'] }}</span>
                                <span class="font-medium">{{ $d['indikator'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($d['parsed_score'] !== null)
                                    <span class="px-2 py-0.5 rounded font-black text-xs bg-slate-100 text-slate-800">
                                        {{ $d['parsed_score'] }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Kosong</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full font-extrabold text-[10px] uppercase
                                    @if($d['status'] === 'VALID') bg-emerald-100 text-emerald-800
                                    @elseif($d['status'] === 'WARNING') bg-amber-100 text-amber-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $d['status'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-sm">
                                @if($d['issues'])
                                    <div class="font-semibold {{ $d['status'] === 'INVALID' ? 'text-red-700' : 'text-amber-700' }}">
                                        {{ $d['issues'] }}
                                    </div>
                                    @if($d['solution'])
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            <strong>Solusi:</strong> {{ $d['solution'] }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-emerald-600 font-medium">Data valid & siap disimpan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
