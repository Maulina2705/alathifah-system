<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Tahfizh - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 12mm 8mm 12mm;
        }
        * {
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .report-table th, .th-title {
                background-color: #8ea9db !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        body {
            margin: 0;
            padding: 0;
            font-size: 8.5pt;
            color: #000000;
            line-height: 1.25;
            background: #ffffff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .header-container {
            width: 100%;
            margin-bottom: 4px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo-cell {
            width: 78px;
            vertical-align: bottom;
            text-align: left;
            padding: 0;
        }
        .header-logo-img {
            width: 72px;
            height: auto;
            display: block;
            margin-bottom: 2px;
        }
        .header-title-cell {
            text-align: center;
            vertical-align: top;
            padding: 2px 0 0 0;
        }
        .header-title-1 {
            font-size: 13pt;
            font-weight: bold;
            color: #7F007F;
            letter-spacing: 0.3px;
            margin: 0;
            line-height: 1.25;
        }
        .header-title-2 {
            font-size: 13pt;
            font-weight: bold;
            color: #7F007F;
            letter-spacing: 0.5px;
            margin-top: 3px;
            line-height: 1.25;
        }
        .header-spacer-cell {
            width: 78px;
            padding: 0;
        }
        .header-bismillah-cell {
            text-align: right;
            vertical-align: bottom;
            padding-top: 1px;
            padding-bottom: 2px;
        }
        .header-bismillah-img {
            width: 155px;
            height: auto;
            display: inline-block;
            vertical-align: bottom;
        }

        .bio-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 8.5pt;
        }
        .bio-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }
        .bio-label {
            width: 125px;
            color: #000000;
        }
        .bio-sep {
            width: 15px;
            text-align: center;
        }
        .bio-val {
            font-weight: bold;
            color: #000000;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }
        .report-table th, .report-table td {
            border: 1px solid #000000;
            padding: 2.2px 4px;
            vertical-align: middle;
        }
        .report-table th {
            background-color: #8ea9db !important;
            color: #000000 !important;
            font-weight: bold;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .th-title {
            font-size: 9.5pt;
            letter-spacing: 0.5px;
            padding: 3.5px !important;
            background-color: #8ea9db !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .font-bold {
            font-weight: bold;
        }

        .signatures-area {
            width: 100%;
            margin-top: 8px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .signature-table td {
            vertical-align: top;
            padding: 0;
        }
        .signature-box {
            text-align: center;
            width: 44%;
        }
        .signature-spacer {
            height: 48px;
        }
        .sign-underline {
            font-weight: bold;
            text-decoration: underline;
        }
        .sign-id {
            font-size: 8pt;
            margin-top: 1px;
        }
    </style>
</head>
<body>

    @php
        $semName = $semester->name ?? 'Genap';
        if (stripos($semName, 'genap') !== false) {
            $semFormatted = 'II (Genap)';
        } elseif (stripos($semName, 'ganjil') !== false) {
            $semFormatted = 'I (Ganjil)';
        } else {
            $semFormatted = $semName;
        }

        $formattedNis = '-';
        if ($student->nis && $student->nisn) {
            $formattedNis = $student->nis . ' / ' . $student->nisn;
        } elseif ($student->nisn) {
            $formattedNis = $student->nisn;
        } elseif ($student->nis) {
            $formattedNis = $student->nis;
        }

        $academicYearName = str_replace('/', '-', $academicYear->name ?? '2025-2026');

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $parsedDate = \Carbon\Carbon::parse($reportDate);
        $tglIndo = $parsedDate->day . ' ' . ($bulanIndo[$parsedDate->month] ?? $parsedDate->translatedFormat('F')) . ' ' . $parsedDate->year;
    @endphp

    <!-- Header Kop -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td rowspan="2" class="header-logo-cell">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo IRGT">
                    @elseif($setting->logo_path && file_exists(public_path($setting->logo_path)))
                        <img src="{{ public_path($setting->logo_path) }}" class="header-logo-img" alt="Logo">
                    @else
                        <img src="{{ public_path('images/logo_irgt.png') }}" class="header-logo-img" alt="Logo">
                    @endif
                </td>
                <td class="header-title-cell">
                    <div class="header-title-1">{{ $setting->header_title ?? 'LAPORAN PERKEMBANGAN TAHFIZH AL ATHIFA' }}</div>
                    <div class="header-title-2">{{ $setting->school_name ?? (($student->level === 'SD' ? 'SD' : 'SMP') . ' ISLAM RIAU GLOBAL TERPADU') }}</div>
                </td>
                <td class="header-spacer-cell"></td>
            </tr>
            <tr>
                <td colspan="2" class="header-bismillah-cell">
                    @if(!empty($bismillahBase64))
                        <img src="{{ $bismillahBase64 }}" class="header-bismillah-img" alt="Bismillah">
                    @elseif(file_exists(public_path('images/bismillah.png')))
                        <img src="{{ public_path('images/bismillah.png') }}" class="header-bismillah-img" alt="Bismillah">
                    @else
                        <span style="font-size: 13pt; font-weight: bold; color: #000000;">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Student Bio -->
    <table class="bio-table">
        <tr>
            <td class="bio-label">Nama Peserta Didik</td>
            <td class="bio-sep">:</td>
            <td class="bio-val">{{ strtoupper($student->name) }}</td>
            <td style="width: 40px;"></td>
            <td class="bio-label">Semester</td>
            <td class="bio-sep">:</td>
            <td class="bio-val">{{ $semFormatted }}</td>
        </tr>
        <tr>
            <td class="bio-label">NIS / NISN</td>
            <td class="bio-sep">:</td>
            <td class="bio-val">{{ $formattedNis }}</td>
            <td></td>
            <td class="bio-label">Tahun Pelajaran</td>
            <td class="bio-sep">:</td>
            <td class="bio-val">{{ $academicYearName }}</td>
        </tr>
    </table>

    <!-- Main Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th colspan="5" class="th-title" style="background-color: #8ea9db !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">KOMPETENSI KETERAMPILAN</th>
            </tr>
            <tr>
                <th style="width: 5%; background-color: #8ea9db !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">NO</th>
                <th colspan="3" style="width: 81%; background-color: #8ea9db !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">MATERI</th>
                <th style="width: 14%; background-color: #8ea9db !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">NILAI AKHIR</th>
            </tr>
        </thead>
        <tbody>
            @php $sectionNumber = 1; @endphp
            @forelse($sections as $section)
                @php 
                    $scoredItems = $section->items;
                    $itemCount = $scoredItems->count();
                @endphp
                @if($itemCount > 0)
                    @foreach($scoredItems as $index => $item)
                        <tr>
                            @if($index === 0)
                                <td class="text-center font-bold" rowspan="{{ $itemCount }}" style="width: 5%;">
                                    {{ $sectionNumber }}
                                </td>
                                <td class="font-bold" rowspan="{{ $itemCount }}" style="width: 22%; padding-left: 6px;">
                                    {{ $section->name }}
                                </td>
                            @endif
                            <td class="text-center font-bold" style="width: 5%;">
                                {{ $index + 1 }}
                            </td>
                            <td class="text-left" style="width: 54%; padding-left: 6px;">
                                {{ $item->name }}
                            </td>
                            <td class="text-center font-bold" style="width: 14%;">
                                {{ $item->score }}
                            </td>
                        </tr>
                    @endforeach
                    @php $sectionNumber++; @endphp
                @endif
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px; color: #666666;">
                        Belum ada data nilai yang terisi untuk raport ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="signatures-area">
        <table class="signature-table">
            <tr>
                <td class="signature-box">
                    <div>Mengetahui,</div>
                    <div style="font-weight: bold; margin-top: 1px;">{{ $setting->homeroom_label ?? 'Wali Kelas' }}</div>
                    <div class="signature-spacer"></div>
                    <div class="sign-underline">{{ $homeroomName }}</div>
                    <div class="sign-id">
                        @if($homeroomNip && $homeroomNip !== '-')
                            {{ (stripos($homeroomNip, 'niy') === 0 || stripos($homeroomNip, 'nip') === 0) ? $homeroomNip : ('NIY. ' . $homeroomNip) }}
                        @else
                            NIY. -
                        @endif
                    </div>
                </td>
                <td style="width: 12%;"></td>
                <td class="signature-box">
                    <div>{{ $setting->city ?? 'Pekanbaru' }}, {{ $tglIndo }}</div>
                    <div style="font-weight: bold; margin-top: 1px;">Guru Tahfidz</div>
                    <div class="signature-spacer"></div>
                    <div class="sign-underline">{{ $teacherName }}</div>
                    <div class="sign-id">
                        @if($teacherNip && $teacherNip !== '-')
                            {{ (stripos($teacherNip, 'niy') === 0 || stripos($teacherNip, 'nip') === 0) ? $teacherNip : ('NIY. ' . $teacherNip) }}
                        @else
                            NIY. -
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 14px; text-align: center;">
            <div style="display: inline-block; min-width: 250px; text-align: center;">
                <div>Mengetahui,</div>
                <div style="font-weight: bold; margin-top: 1px;">{{ $setting->principal_label ?? 'Kepala Sekolah' }}</div>
                <div class="signature-spacer"></div>
                <div class="sign-underline">{{ $setting->principal_name ?? 'Era Mutia, S.Pd, Gr.' }}</div>
                <div class="sign-id">
                    @php
                        $prinNip = $setting->principal_nip ?? '-';
                    @endphp
                    @if($prinNip && $prinNip !== '-')
                        {{ (stripos($prinNip, 'niy') === 0 || stripos($prinNip, 'nip') === 0) ? $prinNip : ('NIY. ' . $prinNip) }}
                    @else
                        NIY. -
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>
