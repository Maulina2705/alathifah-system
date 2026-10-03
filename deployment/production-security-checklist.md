# 🛡️ Panduan & Checklist Keamanan Hosting (Production Hardening)
## Sistem e-Rapor & Asesmen Tahfizh Al-Athifah

Panduan ini wajib diterapkan sebelum meluncurkan sistem ke hosting publik (VPS / Cloud / cPanel) untuk menjamin data santri dan nilai terlindungi dari berbagai ancaman siber (*Data Breach*, *Brute-force*, *Ransomware*, *SQL Injection*, dan *Unauthorized Access*).

---

### 1. Checklist File `.env` Produksi (Wajib)

Pastikan variabel-variabel berikut diatur dengan benar di server hosting:

```env
# 1. Nonaktifkan mode debug (JANGAN PERNAH true di server publik)
APP_ENV=production
APP_DEBUG=false

# 2. URL Domain Resmi dengan HTTPS
APP_URL=https://raport.alathifah.sch.id

# 3. Kunci Enkripsi Aplikasi (Generate jika belum: php artisan key:generate)
APP_KEY=base64:...

# 4. Keamanan Cookie Sesi (Hanya dikirim lewat koneksi HTTPS aman)
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

# 5. Database MySQL (Gunakan user MySQL terpisah, jangan gunakan user 'root'!)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alathifah_db
DB_USERNAME=alathifah_user
DB_PASSWORD=GunakanPasswordAcakPanjangMinimal16Karakter!
```

---

### 2. Hak Akses File & Direktori Linux (Permissions Hardening)

Jalankan perintah ini di terminal server VPS Linux (Ubuntu/Debian):

```bash
# 1. Atur kepemilikan file ke user web server (misal www-data)
sudo chown -R www-data:www-data /var/www/alathifah

# 2. Atur izin standar folder (755) dan file (644)
sudo find /var/www/alathifah -type d -exec chmod 755 {} \;
sudo find /var/www/alathifah -type f -exec chmod 644 {} \;

# 3. Berikan akses tulis hanya pada storage dan bootstrap/cache
sudo chmod -R 775 /var/www/alathifah/storage
sudo chmod -R 775 /var/www/alathifah/bootstrap/cache

# 4. Kunci file konfigurasi .env agar hanya bisa dibaca oleh sistem (600 atau 640)
sudo chmod 600 /var/www/alathifah/.env
```

---

### 3. Perlindungan Server Web (Nginx / Apache)

1. **Document Root**:
   * Arahkan *Document Root* ke folder `/public`, contoh: `/var/www/alathifah/public`.
   * **DILARANG KERAS** mengarahkan Document Root ke folder induk root project karena seluruh kode sumber dan file `.env` dapat diakses langsung oleh penyerang lewat browser.
2. **Nginx Configuration**:
   * Gunakan template konfigurasi yang sudah disiapkan di file: [`deployment/nginx-security.conf`](file:///f:/1.%20MHS%20FOLDER/Project%20Gabut/masteralathifah/deployment/nginx-security.conf).
   * Konfigurasi ini telah memblokir akses ke `.env`, `.git`, mematikan *server tokens*, dan menerapkan *rate limiting* anti DDoS.

---

### 4. Backup Database Otomatis Harian (Disaster Recovery)

Untuk mencegah kehilangan data jika server mengalami masalah atau serangan *ransomware*, buat cron job backup harian:

1. Buka cron editor:
   ```bash
   crontab -e
   ```
2. Tambahkan baris backup otomatis setiap pukul 02:00 malam:
   ```bash
   0 2 * * * mysqldump -u alathifah_user -p'PASSWORD_ANDA' alathifah_db | gzip > /var/backups/alathifah/db_$(date +\%Y\%m\%d_\%H\%M\%S).sql.gz
   ```
3. *Rekomendasi tambahan*: Sinkronkan folder `/var/backups/alathifah/` ke penyimpanan cloud eksternal (Google Drive / Amazon S3 / Rclone).

---

### 5. Optimasi & Cache Laravel Sebelum Go-Live

Setiap kali melakukan update atau pertama kali deploy, jalankan perintah berikut:

```bash
# Optimasi dependency (tanpa modul testing dev)
composer install --optimize-autoloader --no-dev

# Build aset tampilan produksi
npm run build

# Cache konfigurasi, rute, dan blade views
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

### 6. Proteksi Keamanan yang Telah Aktif di Program Saat Ini

| Fitur Keamanan | Implementasi | Fungsi |
|---|---|---|
| **Security Headers** | `App\Http\Middleware\SecurityHeaders` | Anti Clickjacking (`X-Frame-Options`), Anti MIME Sniffing, HSTS, Disable Sensor. |
| **Login Rate Limiter** | `throttle:15,1` pada rute `POST /login` | Menahan serangan Brute Force tebak password otomatis. |
| **Audit Log Intrusi** | `AuditLog::create(['action' => 'FAILED_LOGIN'])` | Merekam percobaan login gagal & IP penyerang ke database. |
| **Role Guarding (RBAC)** | `middleware('role:...')` di `routes/web.php` | Mengunci menu Admin, IT, Kepsek, Wali Kelas, dan Guru dari manipulasi URL. |
| **PDF DoS Defense** | `throttle:20,1` pada `POST /reports/bulk-pdf` | Mencegah lonjakan beban CPU server dari request cetak massal liar. |
| **Apache .htaccess Shield** | File `public/.htaccess` | Memblokir request langsung ke berkas tersembunyi (`.env`, `.git`, `.log`, `.sql`). |
