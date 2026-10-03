<div class="space-y-6">

    <!-- Top Breadcrumb & Status Info -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs transition-colors">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
            <div>
                <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <span>Penilaian Tahfizh</span>
                    <span>/</span>
                    <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $assessment->student->level }}</span>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $assessment->student->name }}</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $assessment->student->name }}</h2>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mt-1">
                    <span>NISN: <strong class="text-slate-700 dark:text-slate-300">{{ $assessment->student->nisn ?? '-' }}</strong></span>
                    <span>•</span>
                    <span>Guru: <strong class="text-slate-700 dark:text-slate-300">{{ $assessment->teacher?->teacherProfile?->formatted_name_with_degree ?? $assessment->teacher?->name }}</strong></span>
                    <span>•</span>
                    <span>Tahun: <strong class="text-slate-700 dark:text-slate-300">{{ $assessment->academicYear?->name }}</strong> (Semester <strong class="text-slate-700 dark:text-slate-300">{{ $assessment->semester?->name }}</strong>)</span>
                </div>
            </div>

            <!-- Status & Actions -->
            <div class="flex flex-col items-start md:items-end gap-2.5">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Status:</span>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold tracking-wide uppercase
                        @if($assessment->status === 'LOCKED') bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800
                        @elseif($assessment->status === 'APPROVED') bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800
                        @elseif($assessment->status === 'REVIEWED') bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800
                        @elseif($assessment->status === 'SUBMITTED') bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800
                        @else bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 @endif">
                        {{ $assessment->status }}
                    </span>
                </div>

                <div class="flex items-center flex-wrap gap-2">
                    <a href="{{ route('reports.preview', $assessment->id) }}" target="_blank"
                       class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center transition">
                        <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Preview
                    </a>
                    <a href="{{ route('reports.pdf', $assessment->id) }}"
                       class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center transition">
                        <svg class="w-4 h-4 mr-1.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Cetak PDF
                    </a>

                    @if($assessment->isDraft())
                        <button wire:click="$set('showSubmitModal', true)"
                                class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition flex items-center cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Submit Raport
                        </button>
                    @elseif(auth()->user()->isSuperAdmin())
                        <button wire:click="unlockReport"
                                onclick="return confirm('Apakah Anda yakin ingin membuka kunci raport ini dan mengembalikannya ke status DRAFT?')"
                                class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition flex items-center cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            Unlock Raport (Admin)
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Progress Bar & Feedback Toast -->
        <div class="mt-4">
            <div class="flex justify-between items-center text-xs mb-1.5">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-slate-700 dark:text-slate-300">Progress Pengisian:</span>
                    <span class="font-extrabold text-blue-600 dark:text-blue-400">{{ $assessment->progress_percentage }}% Selesai</span>
                    <span class="text-slate-400">({{ $assessment->filled_items_count }} dari {{ $assessment->total_items_count }} indikator dinilai)</span>
                </div>
                <div class="text-[11px] text-slate-400">
                    <span class="italic text-slate-500 dark:text-slate-400 font-medium">Auto-save saat blur</span>
                </div>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $assessment->progress_percentage === 100 ? 'bg-gradient-to-r from-pink-500 to-rose-500' : 'bg-gradient-to-r from-blue-600 to-indigo-600' }}" style="width: {{ $assessment->progress_percentage }}%"></div>
            </div>
        </div>

        @if($feedbackMessage)
            <div class="mt-3 p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200 text-xs font-semibold flex items-center justify-between">
                <span>✓ {{ $feedbackMessage }}</span>
                <button wire:click="$set('feedbackMessage', null)" class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-300">&times;</button>
            </div>
        @endif

        @if($errorMessage)
            <div class="mt-3 p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-semibold flex items-center justify-between">
                <span>⚠ {{ $errorMessage }}</span>
                <button wire:click="$set('errorMessage', null)" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-300">&times;</button>
            </div>
        @endif
    </div>

    <!-- Notice if Locked / Not Draft -->
    @if(!$assessment->canEdit())
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs flex items-center space-x-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <div>
                Raport ini berstatus <strong>{{ $assessment->status }}</strong>. Form nilai dinonaktifkan untuk mengamankan data. Jika perlu perbaikan nilai, hubungi reviewer atau Super Admin untuk melakukan revisi / unlock.
            </div>
        </div>
    @endif

    <!-- Sections and Items List -->
    <div class="space-y-6">
        @forelse($assessment->sections as $section)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
                <!-- Section Header -->
                <div class="bg-slate-50/80 dark:bg-slate-800/60 px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-6 h-6 rounded-lg bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-2xs">
                            {{ $loop->iteration }}
                        </span>
                        <h3 class="text-sm font-extrabold text-slate-800 dark:text-white tracking-wide uppercase">{{ $section->name }}</h3>
                        <span class="text-xs text-slate-400">({{ $section->items->count() }} indikator)</span>
                    </div>

                    @if($assessment->canEdit() || auth()->user()->isSuperAdmin())
                        <div class="flex items-center space-x-2">
                            <button wire:click="openAddItemModal({{ $section->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-pink-50 dark:bg-pink-950/60 hover:bg-pink-100 dark:hover:bg-pink-900/80 text-pink-700 dark:text-pink-300 font-bold text-xs border border-pink-200 dark:border-pink-800 transition cursor-pointer">
                                + Tambah Indikator
                            </button>
                            <button wire:click="deleteSection({{ $section->id }})"
                                    onclick="return confirm('Hapus seluruh materi ini beserta semua indikatornya?')"
                                    class="p-1.5 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-slate-800 text-xs transition cursor-pointer" title="Hapus Materi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Section Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="py-3 px-6 w-12 text-center">No</th>
                                <th class="py-3 px-6">Indikator / Nama Surat</th>
                                <th class="py-3 px-6 w-48 text-center">Nilai (1 - 100)</th>
                                <th class="py-3 px-6 w-44 text-center">Status Cetak</th>
                                @if($assessment->canEdit() || auth()->user()->isSuperAdmin())
                                    <th class="py-3 px-6 w-20 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($section->items as $index => $item)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition" wire:key="item-{{ $item->id }}">
                                    <td class="py-3 px-6 text-center text-xs font-bold text-slate-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3 px-6 text-slate-800 dark:text-slate-200 font-semibold">
                                        {{ $item->name }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <input type="number" min="0" max="100"
                                                   value="{{ $item->score ?? '' }}"
                                                   @disabled(!$assessment->canEdit() && !auth()->user()->isSuperAdmin())
                                                   wire:blur="updateScore({{ $item->id }}, $event.target.value)"
                                                   placeholder="Kosong"
                                                   class="w-24 text-center py-1.5 px-3 rounded-xl border text-sm font-extrabold transition
                                                   {{ $item->score ? 'bg-white dark:bg-slate-800 border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500' : 'bg-slate-50 dark:bg-slate-800/60 border-dashed border-slate-300 dark:border-slate-700 text-slate-400 focus:bg-white dark:focus:bg-slate-800' }}">
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center text-xs">
                                        @if($item->hasScore())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-[10px]">
                                                ✓ Tampil di Raport ({{ $item->score }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-medium bg-slate-100 dark:bg-slate-800 text-slate-400 text-[10px]" title="Nilai belum diisi tidak akan dicetak pada PDF">
                                                Belum Diisi (Disembunyikan)
                                            </span>
                                        @endif
                                    </td>
                                    @if($assessment->canEdit() || auth()->user()->isSuperAdmin())
                                        <td class="py-3 px-6 text-center">
                                            <button wire:click="deleteItem({{ $item->id }})"
                                                    onclick="return confirm('Hapus indikator {{ $item->name }}?')"
                                                    class="text-slate-400 hover:text-rose-500 p-1 rounded-lg transition" title="Hapus Indikator">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-xs text-slate-400">
                                        Belum ada indikator pada materi ini. Klik <strong>+ Tambah Indikator</strong> di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 p-8 text-center space-y-3">
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Belum ada materi atau indikator penilaian untuk siswa ini.</p>
                <button wire:click="openAddSectionModal"
                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition cursor-pointer">
                    + Buat Materi Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Bottom Action Bar -->
    @if($assessment->canEdit() || auth()->user()->isSuperAdmin())
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
            <button wire:click="openAddSectionModal"
                    class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center cursor-pointer">
                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Materi / Section Baru
            </button>

            <div class="flex items-center space-x-3">
                <a href="{{ route('teacher.students.index') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs transition">
                    Kembali ke Daftar Siswa
                </a>

                @if($assessment->isDraft())
                    <button wire:click="$set('showSubmitModal', true)"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition flex items-center cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Submit Raport
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- MODAL: Tambah Indikator -->
    @if($showAddItemModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 animate-in fade-in zoom-in-95">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Tambah Indikator / Surat</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Tambahkan indikator hafalan atau tajwid khusus untuk siswa ini.</p>

                <form wire:submit.prevent="saveItem" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Nama Indikator / Surat</label>
                        <input type="text" wire:model="newItemName" required autofocus
                               placeholder="Contoh: An-Naas, Makharijul Huruf, dll"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        @error('newItemName') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" wire:click="$set('showAddItemModal', false)"
                                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                            Simpan Indikator
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: Tambah Materi -->
    @if($showAddSectionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 animate-in fade-in zoom-in-95">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Tambah Materi / Section</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Tambahkan kelompok materi baru (misal: Tahfizh Juz 29, Adab Qurani).</p>

                <form wire:submit.prevent="saveSection" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Nama Materi</label>
                        <input type="text" wire:model="newSectionName" required autofocus
                               placeholder="Contoh: Tahfizh Juz 29"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        @error('newSectionName') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" wire:click="$set('showAddSectionModal', false)"
                                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                            Simpan Materi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: Submit Raport Confirmation -->
    @if($showSubmitModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 animate-in fade-in zoom-in-95">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Konfirmasi Submit Raport</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                    Setelah raport disubmit, Anda tidak dapat mengubah nilai sampai raport dikembalikan oleh reviewer (Wali Kelas/Admin). Pastikan seluruh nilai yang terisi sudah valid dan sesuai.
                </p>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea wire:model="submissionNotes" rows="2"
                              placeholder="Misal: Sudah melengkapi hafalan Juz 30 surat An-Naba..."
                              class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" wire:click="$set('showSubmitModal', false)"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batalkan
                    </button>
                    <button type="button" wire:click="submitReport"
                            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                        Submit Raport Sekarang
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
