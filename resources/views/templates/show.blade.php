@extends('layouts.app', ['title' => 'Struktur Template - ' . $template->name])

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('templates.index') }}" class="hover:underline">Template Penilaian</a>
                <span>/</span>
                <span class="text-emerald-700 font-bold">{{ $template->level }}</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">{{ $template->name }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ $template->description ?: 'Blueprint struktur penilaian' }}</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('templates.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                ← Kembali
            </a>
            <button onclick="openAddSectionModal()"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Materi / Section
            </button>
        </div>
    </div>

    <!-- Sections and Items List -->
    <div class="space-y-6">
        @forelse($template->sections as $secIndex => $sec)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="bg-slate-50/80 px-6 py-3.5 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-6 h-6 rounded-md bg-emerald-600 text-white font-extrabold flex items-center justify-center text-xs">
                            {{ $secIndex + 1 }}
                        </span>
                        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">{{ $sec->name }}</h3>
                        <span class="text-xs text-slate-400">({{ $sec->items->count() }} indikator)</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button onclick="openAddItemModal({{ $sec->id }}, '{{ $sec->name }}')"
                                class="px-3 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs border border-emerald-200 transition">
                            + Tambah Indikator
                        </button>
                        <form action="{{ route('templates.sections.destroy', $sec->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus seluruh materi ini beserta semua indikatornya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-slate-400 hover:text-red-500 rounded transition" title="Hapus Materi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @forelse($sec->items as $itemIndex => $item)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-800 truncate mr-2">
                                    <strong class="text-slate-400 mr-1">{{ $itemIndex + 1 }}.</strong> {{ $item->name }}
                                </span>
                                <form action="{{ route('templates.items.destroy', $item->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus indikator {{ $item->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition p-0.5">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-4 text-xs text-slate-400">
                                Belum ada indikator pada materi ini. Klik <strong>+ Tambah Indikator</strong> di atas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center space-y-3">
                <p class="text-sm font-semibold text-slate-600">Template ini belum memiliki materi.</p>
                <button onclick="openAddSectionModal()"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                    + Tambah Materi Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah Section -->
    <div id="addSectionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <h3 class="text-base font-extrabold text-slate-800 mb-1">Tambah Materi / Section</h3>
            <p class="text-xs text-slate-500 mb-4">Tambahkan nama kelompok materi (misal: Tahsin Tilawah, Tahfizh Juz 30).</p>

            <form action="{{ route('templates.sections.store', $template->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Materi</label>
                    <input type="text" name="name" required placeholder="Contoh: Tahfizh Juz 29"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeAddSectionModal()"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                        Simpan Materi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Item -->
    <div id="addItemModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <h3 class="text-base font-extrabold text-slate-800 mb-1">Tambah Indikator / Surat</h3>
            <p class="text-xs text-slate-500 mb-4" id="itemSectionSubtitle"></p>

            <form id="addItemForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Indikator / Surat</label>
                    <input type="text" name="name" required placeholder="Contoh: Al-Mulk, Bacaan Mad, dll"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeAddItemModal()"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                        Simpan Indikator
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openAddSectionModal() {
        document.getElementById('addSectionModal').classList.remove('hidden');
    }
    function closeAddSectionModal() {
        document.getElementById('addSectionModal').classList.add('hidden');
    }

    function openAddItemModal(secId, secName) {
        document.getElementById('addItemForm').action = '/templates/sections/' + secId + '/items';
        document.getElementById('itemSectionSubtitle').innerText = 'Materi: ' + secName;
        document.getElementById('addItemModal').classList.remove('hidden');
    }
    function closeAddItemModal() {
        document.getElementById('addItemModal').classList.add('hidden');
    }
</script>
@endsection
