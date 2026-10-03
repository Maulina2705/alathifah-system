@extends('layouts.app', ['title' => 'Template Penilaian Tahfizh'])

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-theme-primary/10 text-theme-primary dark:text-blue-300 font-bold text-[11px] mb-2 border border-theme-primary/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Kurikulum & Blueprint Nilai
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Template Penilaian Tahfizh</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                Template penilaian berfungsi sebagai cetak biru kurikulum. Guru dapat membuat template standar, lalu melakukan kustomisasi pada tiap santri tanpa merusak template acuan utama.
            </p>
        </div>

        <button onclick="openCreateTemplateModal()"
                class="px-5 py-2.5 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md shadow-theme-primary/20 transition flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Buat Template Baru
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($templates as $tpl)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider mb-1.5
                                @if($tpl->level === 'SD') bg-theme-yellow/20 text-amber-800 dark:text-amber-300 border border-theme-yellow/30
                                @elseif($tpl->level === 'SMP') bg-theme-primary/10 text-theme-primary dark:text-blue-300 border border-theme-primary/20
                                @else bg-theme-pink/15 text-theme-pink border border-theme-pink/30 @endif">
                                {{ $tpl->level }}
                            </span>
                            <h3 class="font-extrabold text-base text-slate-800 dark:text-white tracking-tight group-hover:text-theme-primary transition-colors">{{ $tpl->name }}</h3>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $tpl->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $tpl->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">
                        {{ $tpl->description ?: 'Tidak ada deskripsi tambahan.' }}
                    </p>

                    <!-- Sections Summary -->
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
                        <div class="font-bold text-[11px] text-slate-400 dark:text-slate-500 uppercase">Materi Termasuk:</div>
                        @foreach($tpl->sections->take(3) as $sec)
                            <div class="flex items-center justify-between text-xs">
                                <span class="truncate font-medium">• {{ $sec->name }}</span>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500">({{ $sec->items->count() }} item)</span>
                            </div>
                        @endforeach
                        @if($tpl->sections->count() > 3)
                            <div class="text-[11px] text-theme-primary font-semibold">+ {{ $tpl->sections->count() - 3 }} materi lainnya...</div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    <a href="{{ route('templates.show', $tpl->id) }}"
                       class="flex-1 text-center py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs transition">
                        Kelola Materi & Indikator
                    </a>
                    <form action="{{ route('templates.duplicate', $tpl->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Duplikasi Template">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white dark:bg-slate-900 p-10 rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 text-center">
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Belum ada template penilaian.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Create Template -->
    <div id="createTemplateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 transition-colors">
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white mb-1">Buat Template Penilaian Baru</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Buat cetak biru kurikulum tahfizh baru.</p>

            <form method="POST" action="{{ route('templates.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Template</label>
                    <input type="text" name="name" required placeholder="Contoh: Tahfizh Juz 29 Standar"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-theme-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenjang</label>
                    <select name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-theme-primary">
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SEMUA">Semua Jenjang</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Keterangan materi kurikulum..."
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-theme-primary"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeCreateTemplateModal()"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-theme-primary hover:opacity-90 text-white font-bold text-xs shadow-md transition">
                        Simpan & Atur Materi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openCreateTemplateModal() {
        document.getElementById('createTemplateModal').classList.remove('hidden');
    }
    function closeCreateTemplateModal() {
        document.getElementById('createTemplateModal').classList.add('hidden');
    }
</script>
@endsection
