<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panduan Fitur & Matriks Hak Akses - Master Raport Tahfizh Al-Athifa</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10pt;
            line-height: 1.45;
        }
        .header-box {
            border-bottom: 2.5px solid #047857;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            color: #064e3b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .header-subtitle {
            font-size: 11pt;
            font-weight: 600;
            color: #047857;
            margin-top: 4px;
        }
        .header-meta {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 4px;
        }
        h2 {
            font-size: 12pt;
            color: #0f172a;
            border-left: 4px solid #059669;
            padding-left: 8px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        h3 {
            font-size: 10.5pt;
            color: #065f46;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        p {
            margin: 0 0 8px 0;
            text-align: justify;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 16px;
            font-size: 9pt;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: top;
        }
        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-amber { background-color: #fef3c7; color: #92400e; }
        .badge-purple { background-color: #f3e8ff; color: #6b21a8; }
        .badge-cyan { background-color: #cffafe; color: #155e75; }
        .badge-gray { background-color: #f1f5f9; color: #475569; }
        .check { color: #059669; font-weight: bold; text-align: center; font-size: 11pt; }
        .cross { color: #94a3b8; text-align: center; }
        .workflow-box {
            background-color: #f8fafc;
            border: 1px dashed #059669;
            padding: 10px 14px;
            border-radius: 6px;
            margin: 12px 0;
            font-size: 8.5pt;
        }
        .page-break {
            page-break-after: always;
        }
        ul, ol {
            margin: 0 0 10px 18px;
            padding: 0;
        }
        li {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <div class="header-box">
        <div class="header-title">MASTER RAPORT TAHFIZH AL-ATHIFA</div>
        <div class="header-subtitle">DOKUMEN SPESIFIKASI FITUR & MATRIKS HAK AKSES SISTEM</div>
        <div class="header-meta">
            SD & SMP Islam Riau Global Terpadu (IRGT) Pekanbaru | Terbitan: {{ date('d F Y') }} | Versi Sistem: 2.1 (Multi-Level SD/SMP)
        </div>
    </div>

    <!-- 1. Ringkasan Eksekutif -->
    <h2>1. Ringkasan Sistem</h2>
    <p>
        <strong>Master Raport Tahfizh Al-Athifa</strong> adalah aplikasi web manajemen nilai hafalan dan tilawah serta pencetakan raport resmi terpadu untuk jenjang <strong>SD dan SMP Islam Riau Global Terpadu (IRGT)</strong>. Sistem ini dirancang fleksibel dengan struktur data non-linear berbasis siswa, tahun pelajaran, dan semester tanpa bergantung pada skema kelas manual, memungkinkan tiap siswa memiliki target capaian juz, surat, maupun materi tahsin yang disesuaikan secara individual.
    </p>

    <!-- 2. Alur Kerja (Workflow) Penilaian & Percetakan -->
    <h2>2. Alur Kerja Penilaian & Percetakan Raport</h2>
    <div class="workflow-box">
        <strong>URUTAN PROSES RAPORT TAHFIZH:</strong><br>
        1. <strong>DRAFT</strong>: Guru Tahfizh menginput nilai siswa secara mandiri (manual maupun Excel ledger).<br>
        2. <strong>SUBMITTED</strong>: Guru Tahfizh memverifikasi kelengkapan nilai lalu mengirimkan ke sistem.<br>
        3. <strong>REVIEWED</strong>: Wali Kelas memeriksa seluruh nilai & catatan siswa di kelasnya.<br>
        4. <strong>APPROVED</strong>: Kepala Sekolah (SD/SMP) memberikan persetujuan akhir.<br>
        5. <strong>MENUNGGU ANTRIAN CETAK</strong>: Raport masuk antrian percetakan fisik Staff IT.<br>
        6. <strong>PROSES CETAK</strong>: Staff IT melakukan pencetakan fisik menggunakan kertas A4.<br>
        7. <strong>SELESAI / LOCKED</strong>: Raport telah selesai dicetak & dibagikan, data terkunci aman dari perubahan.
    </div>

    <!-- 3. Matriks Hak Akses Granular -->
    <h2>3. Matriks Hak Akses Berdasarkan Role</h2>
    <p>Sistem membedakan secara tegas tugas dan batas wewenang antara masing-masing pihak:</p>

    <table>
        <thead>
            <tr>
                <th style="width: 38%;">Fitur / Modul Aplikasi</th>
                <th class="text-center" style="width: 12%;">Super Admin</th>
                <th class="text-center" style="width: 12%;">Staff IT</th>
                <th class="text-center" style="width: 12%;">Kepsek</th>
                <th class="text-center" style="width: 12%;">Wali Kelas</th>
                <th class="text-center" style="width: 14%;">Guru Tahfizh</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Manajemen Akun User & Guru</strong></td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Audit Trail & Log Aktivitas Lengkap</strong></td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Data History (Riwayat Guru & Status)</strong></td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Atur Batas Waktu Pengisian (Deadline)</strong></td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Kelola Data Siswa & Upload Excel Siswa</strong></td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Kelola Tahun Pelajaran & Semester</strong></td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Kelola Template & Indikator Penilaian</strong></td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="check">✓</td>
            </tr>
            <tr>
                <td><strong>Kelola Antrian & Update Status Cetak</strong></td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Cetak Massal (Combined PDF A4)</strong></td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="check">✓ (1 Kelas)</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Approval Raport Akhir</strong></td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="check">✓ (Per Jenjang)</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Review Raport Siswa Kelas</strong></td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="check">✓</td>
                <td class="cross">-</td>
            </tr>
            <tr>
                <td><strong>Navigasi Preview Siswa (Next / Prev)</strong></td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="check">✓</td>
            </tr>
            <tr>
                <td><strong>Input Nilai Manual & Import Excel Ledger</strong></td>
                <td class="check">✓</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="check">✓</td>
            </tr>
            <tr>
                <td><strong>Verifikasi Biodata Mandiri Guru</strong></td>
                <td class="cross">-</td>
                <td class="cross">-</td>
                <td class="check">✓</td>
                <td class="check">✓</td>
                <td class="check">✓</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- 4. Rincian Fungsionalitas Tiap Peran -->
    <h2>4. Detail Fitur Berdasarkan Peran (Role)</h2>

    <h3>A. SUPER ADMIN</h3>
    <ul>
        <li><strong>Kontrol Penuh Sistem</strong>: Memegang otoritas tertinggi untuk mengelola akun administrator, guru, staf IT, kepala sekolah, dan data institusi.</li>
        <li><strong>Akses Audit Log Eksklusif</strong>: Satu-satunya peran yang berhak memeriksa catatan log transaksi sensitif, perubahan status raport, dan aktivitas pengguna.</li>
        <li><strong>Unlock & Override</strong>: Berhak membuka kunci (unlock) raport yang terkunci jika terjadi kekeliruan fatal pasca-approval.</li>
    </ul>

    <h3>B. STAFF IT & PERCETAKAN</h3>
    <ul>
        <li><strong>Manajemen Antrian Percetakan</strong>: Mengatur status pencetakan fisik raport (<em>Submitted by Teacher &rarr; Menunggu Antrian Cetak &rarr; Proses Cetak &rarr; Selesai</em>).</li>
        <li><strong>Pencetakan Massal Terpadu</strong>: Menjalankan fitur <em>Generated Combine PDF</em> untuk mencetak seluruh raport dalam satu bundle file PDF format A4.</li>
        <li><strong>Import Data Massal Excel</strong>: Mengupload data siswa dan guru secara massal dengan standardisasi <strong>Auto-Capslock Nama Siswa</strong>.</li>
        <li><strong>Akses Data History</strong>: Memeriksa riwayat penugasan guru pembimbing dan jejak status penilaian dari semester ke semester.</li>
        <li><strong>Batas Waktu (Deadline)</strong>: Membantu mengatur dan memperbarui batas waktu pengisian raport bersama Kepala Sekolah dan Super Admin.</li>
    </ul>

    <h3>C. KEPALA SEKOLAH (SD & SMP)</h3>
    <ul>
        <li><strong>Segmentasi Jenjang Otomatis</strong>: Kepala Sekolah SD hanya melihat data dan statistik SD; Kepala Sekolah SMP hanya melihat data dan statistik SMP.</li>
        <li><strong>Approval Raport</strong>: Memberikan persetujuan akhir terhadap raport yang telah lolos peninjauan oleh Wali Kelas.</li>
        <li><strong>Pengaturan Batas Waktu</strong>: Berhak menentukan jadwal tenggat waktu (deadline) input nilai bagi para guru tahfizh.</li>
    </ul>

    <h3>D. WALI KELAS</h3>
    <ul>
        <li><strong>Monitoring Progress Kelas</strong>: Memantau persentase kelengkapan nilai seluruh siswa di bawah bimbingan kelasnya secara real-time pada dashboard.</li>
        <li><strong>Review Raport & Catatan</strong>: Memeriksa capaian tahfizh dan memberikan catatan wali kelas sebelum diserahkan ke Kepala Sekolah.</li>
        <li><strong>Preview Beruntun (Next & Previous)</strong>: Memeriksa raport siswa satu per satu secara berurutan tanpa harus bolak-balik ke menu utama.</li>
        <li><strong>Download PDF 1 Kelas</strong>: Mengunduh gabungan seluruh raport siswa di kelasnya dalam sekali klik.</li>
    </ul>

    <h3>E. GURU TAHFIZH</h3>
    <ul>
        <li><strong>Verifikasi Biodata Wajib</strong>: Memastikan penulisan nama lengkap beserta gelar kesarjanaan telah valid agar nama yang tercetak pada raport bebas dari kesalahan ketik.</li>
        <li><strong>Struktur Penilaian Fleksibel</strong>: Memilih atau menyusun template penilaian yang berbeda untuk tiap anak sesuai capaian hafalan masing-masing.</li>
        <li><strong>Input Fleksibel (Manual & Excel)</strong>: Mengisi nilai melalui formulir web interaktif atau mengunduh form ledger Excel kemudian menguploadnya kembali.</li>
        <li><strong>Penyembunyian Nilai Kosong</strong>: Indikator atau surat yang belum diuji/belum dinilai otomatis tidak ditampilkan pada raport cetak.</li>
    </ul>

    <!-- 5. Standar Format Percetakan Raport -->
    <h2>5. Standarisasi Format Raport Fisik & PDF</h2>
    <ul>
        <li><strong>Format Kertas</strong>: Standar ISO A4 Portrait dengan margin proporsional (15mm).</li>
        <li><strong>Kop Surat & Ornamen</strong>: Logo resmi Yayasan Riau Global Terpadu di sisi kiri atas dan kaligrafi <em>Bismillah</em> di sisi kanan atas.</li>
        <li><strong>Konsistensi Warna Header Tabel</strong>: Menggunakan warna biru pastel (<em>#8ea9db</em>) dengan pengaturan warna cetak teroptimasi (<em>exact color adjust</em>) baik di browser maupun PDF.</li>
        <li><strong>Format Tanda Tangan</strong>: Rata tengah simetris antara tempat/tanggal (<em>Pekanbaru, [Tanggal]</em>) dan jabatan penandatangan (<em>Guru Tahfidz</em>), dilengkapi tanda tangan Wali Kelas dan Kepala Sekolah.</li>
    </ul>

    <div style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #cbd5e1; font-size: 8pt; color: #94a3b8; text-align: right;">
        Dokumen ini dibuat otomatis oleh Sistem Master Raport Tahfizh Al-Athifa — SD & SMP Islam Riau Global Terpadu.
    </div>

</body>
</html>
