@extends('layouts.app', ['title' => 'Mulai Penilaian - ' . $student->name])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-xl font-extrabold text-slate-800">Inisialisasi Penilaian Tahfizh Siswa</h2>
        <p class="text-xs text-slate-500 mt-1">Pilih template standar penilaian untuk dijadikan cetak biru (blueprint) awal. Anda tetap dapat menambah, mengedit, atau menghapus materi/indikator khusus siswa ini nantinya tanpa merusak template utama.</p>

        <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
            <div>
                <span class="text-slate-500">Nama Siswa:</span>
                <span class="font-bold text-slate-800 block text-sm">{{ $student->name }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-500">Jenjang:</span>
                <span class="font-bold text-emerald-700 block text-sm">{{ $student->level }}</span>
            </div>
        </div>

        <form action="{{ route('teacher.assessments.start') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <input type="hidden" name="academic_year_id" value="{{ $activeAY->id }}">
            <input type="hidden" name="semester_id" value="{{ $activeSem->id }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Template Penilaian Awal</label>
                <div class="space-y-2">
                    @foreach($templates as $tpl)
                        <label class="flex items-start p-3.5 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/20 cursor-pointer transition">
                            <input type="radio" name="template_id" value="{{ $tpl->id }}" {{ $loop->first ? 'checked' : '' }}
                                   class="mt-1 text-emerald-600 focus:ring-emerald-500">
                            <div class="ml-3">
                                <span class="font-bold text-sm text-slate-800 block">{{ $tpl->name }}</span>
                                <span class="text-xs text-slate-500">{{ $tpl->description ?? 'Template standar kurikulum Tahfizh' }}</span>
                            </div>
                        </label>
                    @endforeach

                    <label class="flex items-start p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition">
                        <input type="radio" name="template_id" value="" class="mt-1 text-slate-600 focus:ring-slate-500">
                        <div class="ml-3">
                            <span class="font-bold text-sm text-slate-800 block">Kosong (Mulai dari Nol)</span>
                            <span class="text-xs text-slate-500">Buat materi dan indikator sendiri secara manual khusus untuk siswa ini</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                <a href="{{ route('teacher.students.index') }}" class="text-xs font-bold text-slate-500 hover:underline">
                    ← Kembali
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                    Lanjutkan ke Input Nilai →
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
