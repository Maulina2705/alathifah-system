<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Raport - {{ $assessment->student->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 12mm 8mm 12mm;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            html, body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .a4-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
            }
            .report-table th, .th-title {
                background-color: #8ea9db !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        .a4-sheet {
            width: 210mm;
            min-height: 297mm;
            padding: 8mm 12mm;
            margin: 20px auto 60px auto;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-slate-700 min-h-screen text-slate-900 pb-16">

    <!-- Top Floating Toolbar -->
    <div class="no-print sticky top-0 z-50 bg-slate-900/95 backdrop-blur-md border-b border-slate-800 px-6 py-3 flex flex-wrap items-center justify-between gap-3 text-white shadow-xl">
        <div class="flex items-center space-x-3">
            <a href="javascript:history.back()" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition" title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-sm font-bold tracking-tight">{{ $assessment->student->name }}</h1>
                    @if(isset($totalInScope) && $totalInScope > 1)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $currentPos }} dari {{ $totalInScope }} Siswa
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-400">Status: <span class="text-emerald-400 font-bold">{{ $assessment->status_label ?? $assessment->status }}</span> | Jenjang: {{ $assessment->student->level }}</p>
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            <!-- Navigation Previous & Next -->
            @if(!empty($prevId))
                <a href="{{ route('reports.preview', $prevId) }}"
                   class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Siswa Sebelumnya
                </a>
            @endif

            @if(!empty($nextId))
                <a href="{{ route('reports.preview', $nextId) }}"
                   class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition flex items-center">
                    Siswa Berikutnya
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif

            @if(auth()->user()->isWaliKelas() || auth()->user()->isSuperAdmin() || auth()->user()->isIt())
                <a href="{{ route('reports.homeroom_bulk_pdf', ['ay' => $assessment->academic_year_id, 'sem' => $assessment->semester_id]) }}"
                   class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-900/30 transition flex items-center"
                   title="Unduh satu dokumen PDF berisi raport seluruh siswa di kelas ini">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download PDF 1 Kelas
                </a>
            @endif

            <button onclick="window.print()"
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Browser
            </button>

            <a href="{{ route('reports.pdf', $assessment->id) }}"
               class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-900/40 transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download PDF Resmi
            </a>
        </div>
    </div>

    <!-- Realistic A4 Paper Preview Container -->
    <div class="py-6 px-4 flex justify-center">
        <div class="a4-sheet">
            {!! $reportHtml !!}
        </div>
    </div>

    <!-- Floating Quick Pagination at Bottom -->
    @if(isset($totalInScope) && $totalInScope > 1)
        <div class="no-print fixed bottom-5 left-1/2 -translate-x-1/2 z-40 bg-slate-900/90 backdrop-blur-md px-5 py-2.5 rounded-2xl border border-slate-700/80 shadow-2xl flex items-center space-x-4 text-white">
            @if(!empty($prevId))
                <a href="{{ route('reports.preview', $prevId) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Sebelumnya
                </a>
            @else
                <span class="px-3 py-1.5 rounded-xl bg-slate-800/40 text-slate-500 font-bold text-xs flex items-center gap-1 cursor-not-allowed">
                    Sebelumnya
                </span>
            @endif

            <span class="text-xs font-bold text-slate-300">
                {{ $currentPos }} / {{ $totalInScope }} Siswa
            </span>

            @if(!empty($nextId))
                <a href="{{ route('reports.preview', $nextId) }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1 transition">
                    Berikutnya
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="px-3 py-1.5 rounded-xl bg-slate-800/40 text-slate-500 font-bold text-xs flex items-center gap-1 cursor-not-allowed">
                    Berikutnya
                </span>
            @endif
        </div>
    @endif

</body>
</html>
