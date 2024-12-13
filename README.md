# Sistem Manajemen Rumah Sakit

Proyek ini adalah **Sistem Manajemen Rumah Sakit** yang dibangun menggunakan PHP, HTML, CSS, dan JavaScript. Sistem ini mencakup modul untuk mengelola berbagai operasi rumah sakit, seperti pendaftaran pasien, manajemen dokter, manajemen obat, manajemen poli dan lain-lain. Sistem ini menggunakan database MySQL untuk penyimpanan data.

---

## Fitur

### Modul Admin
- Fungsionalitas login untuk akses yang aman.
- Mengelola(menambah, memperbarui, atau menghapus) data dokter, pasien, poli, dan obat.
- Dapat diakses melalui folder `admin`.

### Modul Dokter
- Fungsionalitas login untuk akses yang aman.
- Dapat diakses melalui folder `dokter`.

### Modul Pasien
- Mendaftar sebagai pasien baru.
- Fungsionalitas login untuk akses yang aman.
- Dapat diakses melalui folder `pasien`.

---

## Struktur Folder

```
web_hospital-main/
|-- admin/            # Fungsionalitas dan tampilan admin
|-- assets/           # File CSS, JS, dan gambar untuk index.php
|-- db/               # File database
|-- dokter/           # Fungsionalitas dan tampilan dokter
|-- koneksi/          # File koneksi database
|-- pasien/           # Fungsionalitas dan tampilan pasien
|-- index.php         # Halaman utama
```

---

## Instalasi

1. Download file web_hospital-main tersebut.

2. Kemudian extract filenya.

3. Jalankan server lokal (misalnya, XAMPP) dan tempatkan folder proyek di direktori root server (misalnya, `htdocs` untuk XAMPP).

4. Buka file yang sudah anda extract di code editor anda gunakan seperti Visual Studio Code.

5. Import file `db/db_hospital2.sql` ke dalam database MySQL Anda.

6.Perbarui file `koneksi/koneksi.php` dengan kredensial database Anda:
     ```php
     $host = "localhost";
     $user = "root";
     $pass = "your_password";
     $db = "your_database_name";
     ```
7. Akses aplikasi melalui browser web:
   ```
   http://localhost/web_hospital-main/index.php
   ```
---

## Penggunaan

1. **Admin**: Login melalui `/admin/login.php` untuk mengelola catatan rumah sakit.
2. **Dokter**: Login melalui `/dokter/login.php` untuk melihat janji temu pasien.
3. **Pasien**: Daftar atau login melalui `/pasien/login.php` untuk mengakses informasi medis.

---

## Teknologi yang Digunakan

- **Backend**: PHP
- **Frontend**: HTML, CSS, JavaScript, Bootsrap
- **Database**: MySQL
- **Server**: Apache (via XAMPP)

---

## Kontak

Untuk pertanyaan atau dukungan, silakan hubungi:
- **Nama**: Zacky Fahd Annahdli
- **NIM**: A11.2021.13422
- **Kelas**: WD03
- **Email**: zackyfahdanahdli@gmail.com
- **Email Mhs**: 111202113422@mhs.dinus.ac.id
