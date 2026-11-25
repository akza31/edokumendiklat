# E-Dokumen PKL — Native PHP + SQLite

Starter aplikasi mandiri tanpa Laravel, Composer, atau MariaDB. Target runtime PHP 8.3+ dengan ekstensi `pdo_sqlite`.

## Isi tahap awal
- Login dan logout berbasis session.
- Role: `admin`, `student`, `ci`, `management`, `auditor`.
- Dashboard ringkas.
- CRUD master institusi pendidikan.
- SQLite dengan foreign keys, WAL, dan busy timeout.
- CSRF token, password hashing, session regeneration, security headers, prepared statements, dan audit log dasar.
- Database disimpan di `storage/`, di luar web root.

## Persyaratan
- PHP 8.3 atau lebih baru.
- Ekstensi PHP `pdo_sqlite` aktif.
- Apache 2 atau Nginx.
- Tidak membutuhkan Composer, Node.js, atau MariaDB.

Periksa ekstensi:
```bash
php -m | grep -i sqlite
```
Jika belum ada pada Ubuntu:
```bash
sudo apt update
sudo apt install php-sqlite3
sudo systemctl restart apache2
```

## Instalasi
1. Ekstrak folder proyek di lokasi non-public, misalnya `/var/www/edokumen-pkl`.
2. Pastikan web server menggunakan `public/` sebagai DocumentRoot.
3. Atur permission `storage/` agar dapat ditulis user Apache.
4. Buat akun admin pertama melalui CLI:

```bash
cd /var/www/edokumen-pkl
php bin/create-admin.php admin@contoh.local 'GantiPasswordKuat!'
```

5. Buka aplikasi melalui domain/virtual host.

## Apache 2
DocumentRoot harus mengarah ke:
`/var/www/edokumen-pkl/public`

Contoh VirtualHost:
```apache
<VirtualHost *:80>
    ServerName edokumen.example.local
    DocumentRoot /var/www/edokumen-pkl/public

    <Directory /var/www/edokumen-pkl/public>
        AllowOverride None
        Require all granted
        Options -Indexes
        DirectoryIndex index.php
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/edokumen-error.log
    CustomLog ${APACHE_LOG_DIR}/edokumen-access.log combined
</VirtualHost>
```

Aktifkan `mod_rewrite` bila belum aktif:
```bash
sudo a2enmod rewrite
sudo systemctl reload apache2
```

## Keamanan dan backup
- Jangan arahkan DocumentRoot ke root proyek.
- Jangan meletakkan database atau dokumen mahasiswa di `public/`.
- Gunakan HTTPS sebelum digunakan pengguna.
- Backup SQLite dengan SQLite Online Backup API (`VACUUM INTO`) atau saat aplikasi dihentikan; jangan hanya menyalin file database ketika WAL masih aktif.
- Backup juga direktori dokumen privat setelah modul upload diaktifkan.
- Uji restore sebelum pilot.

## Batas tahap ini
Ini adalah fondasi native tahap 1, bukan seluruh cakupan PRD. Modul orientasi/post-test, sertifikat QR, logbook, kompetensi, supervisi, penilaian CI, laporan PDF/Excel, arsip read-only, dan manajemen dokumen belum diimplementasikan.
