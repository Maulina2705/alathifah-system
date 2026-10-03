@extends('layouts.app', ['title' => 'Profil Saya'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <div class="flex items-center space-x-4 pb-6 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl bg-emerald-700 text-white font-extrabold text-2xl flex items-center justify-center">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500">{{ $user->email }}</p>
                <div class="mt-2 flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                        {{ $user->getRoleNames()->first() }}
                    </span>
                    @if($profile && $profile->is_verified)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                            ✓ Terverifikasi ({{ $profile->verified_at?->format('d/m/Y') }})
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            Belum Terverifikasi
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-3 text-xs">
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-400 font-semibold uppercase">Nama dengan Gelar</span>
                <span class="font-bold text-slate-800">{{ $profile?->formatted_name_with_degree ?? $user->name }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-400 font-semibold uppercase">NIP</span>
                <span class="font-bold text-slate-800">{{ $profile?->nip ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-400 font-semibold uppercase">NIK</span>
                <span class="font-bold text-slate-800">{{ $profile?->nik ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-400 font-semibold uppercase">Jabatan</span>
                <span class="font-bold text-slate-800">{{ $profile?->position ?? 'Guru Tahfizh' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-400 font-semibold uppercase">Jenjang</span>
                <span class="font-bold text-slate-800">{{ $profile?->level ?? 'SD' }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-slate-400 font-semibold uppercase">Nomor HP</span>
                <span class="font-bold text-slate-800">{{ $profile?->phone ?? '-' }}</span>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
            <a href="{{ route('profile.verify') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                Perbarui & Verifikasi Biodata →
            </a>
        </div>
    </div>

</div>
@endsection
